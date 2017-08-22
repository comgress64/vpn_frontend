<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname', 'lname', 'email', 'password', 'password_confirmation',
        'phone', 'mobile', 'role', 'max_devices',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'userPermissions', 'api_token',
    ];

    protected $appends = [
        'fullname', 'no_devices_restriction',
    ];

    public static function boot() {
        parent::boot();

        self::saving(function($model) {
            unset($model->password_confirmation);

            if (empty($model->api_token)) {
                $model->generateApiToken();
            }
        });

        self::deleting(function($model) {
            $model->userPermissions()->delete();
            $model->groups()->detach();
            if ($model->ownDevice) {
                $model->ownDevice->delete();
            }
        });
    }

    public function scopeAdmins($query) {
        $query->whereRole('admin');
    }

    public function ownDevice() {
        return $this->hasOne(Device::class)->orderBy('id', 'ASC');
    }

    public function createdDevices() {
        return $this->hasMany(Device::class, 'creator_id');
    }

    public function groups() {
        return $this->belongsToMany(Group::class, 'admin_user_groups');
    }

    public function userPermissions() {
        return $this->hasMany(UserPermission::class);
    }

    public function getFullnameAttribute() {
        return implode(' ', [$this->attributes['fname'], $this->attributes['lname']]);
    }

    public function setPasswordAttribute($data)
    {
        if ($data == '') {
            return;
        }

        $this->attributes['password'] = bcrypt($data);
    }

    public function isUser() {
        return $this->role == 'user';
    }

    public function isAdmin() {
        return $this->role == 'admin';
    }

    public function isSuperadmin() {
        return $this->role == 'superadmin';
    }

    public function getPermissionsAttribute() {
        return $this->userPermissions->pluck('permission');
    }

    public function getGroupIdsAttribute() {
        return $this->groups->pluck('id');
    }

    public function getCanCreateDevicesAttribute() {
        return $this->isSuperadmin() ||
            $this->noDevicesRestriction ||
            $this->createdDevices->count() < $this->max_devices;
    }

    public function getNoDevicesRestrictionAttribute() {
        return $this->max_devices == -1;
    }

    public function hasPermission($permission) {
        return $this->permissions->contains($permission);
    }

    public function addPermission($permission) {
        $this->userPermissions()->create([
            'permission' => $permission,
        ]);
    }

    public function syncPermissions($permissions) {
        $this->userPermissions()->whereNotIn('permission', $permissions)->delete();
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                $this->addPermission($permission);
            }
        }
    }

    public function syncGroups($groupIds) {
        $currentIds = $this->groupIds;

        $addedIds = collect($groupIds)->diff($currentIds);
        $removedIds = collect($currentIds)->diff($groupIds);

        $this->groups()->sync($groupIds);

        if (!empty($this->ownDevice)) {
            event(new \App\Events\UserUpdatedGroups($this, $addedIds, $removedIds));
        }
    }

    public function generateApiToken() {
        do {
            $api_token = str_random(60);
        }
        while (self::where('api_token', $api_token)->exists());

        $this->api_token = $api_token;
    }

}
