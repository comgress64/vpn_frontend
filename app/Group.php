<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'comment',
    ];

    protected $hidden = [
        'groupPort',
    ];

    public static function boot() {
        parent::boot();

        self::created(function($model) {
            $hexId = (string) dechex($model->id);
            $hexId = str_pad($hexId, 4, '0', STR_PAD_LEFT);
            $model->ip = "fd7d:6fbd:828c:022c:0000:0000:$hexId:0000";
            $model->save();
        });

        self::deleting(function($model) {
            event(new \App\Events\GroupDeleted($model));
        });
    }

    public function groupPorts()
    {
        return $this->hasMany(GroupPort::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'admin_user_groups');
    }

    public function scopeForUser($query, $user) {
        if (!$user->isSuperadmin()) {
            $query->whereHas('users', function($squery) use ($user) {
                $squery->where('users.id', $user->id);
            });
        }
    }

    public function getPortsAttribute() {
        return $this->groupPorts->map(function($port) {
            return $port->port;
        });
    }

    public function syncPorts($ports) {
        $currentPorts = $this->ports->all();

        $addedPorts = collect($ports)->diff($currentPorts)->all();
        $removedPorts = collect($currentPorts)->diff($ports)->all();

        // FIXME: remove only removed, add only added, instead of deleting and resaving all
        $ports = collect($ports)->map(function($port) {
            return new GroupPort(['port' => $port]);
        })->all();
        $this->groupPorts()->delete();
        $this->groupPorts()->saveMany($ports);

        event(new \App\Events\GroupUpdatedPorts($this, $addedPorts, $removedPorts));
    }
}
