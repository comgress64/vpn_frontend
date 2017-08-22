<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\GetKeyException;
use App\Exceptions\GetDeviceStatusException;

class Device extends Model
{
    protected $fillable = [
        'user_id', 'group_id', 'name', 'comment', 'creator_id', 'isolated',
    ];

    protected $casts = [
        'isolated' => 'boolean',
    ];

    public static function boot() {
        parent::boot();

        self::creating(function($model) {
            if (is_null($model->isolated)) {
                $model->isolated = true;
            }
        });

        self::created(function($model) {
            $model->getVpnKey();
        });

        self::saving(function($model) {
            if ($model->id) {
                $prevGroupId = $model->getOriginal('group_id');
                if ($model->group_id != $prevGroupId) {
                    event(new \App\Events\DeviceGroupChanged($model, $prevGroupId));
                }

                $prevIsolated = $model->getOriginal('isolated');
                if ($model->isolated != $prevIsolated) {
                    event(new \App\Events\DeviceIsolationChanged($model));
                }
            }
        });

        self::deleting(function($model) {
            if ($model->vpnKey) {
                $model->vpnKey->delete();
            }

            event(new \App\Events\DeviceDeleted($model));
        });
    }

    public function group() {
        return $this->belongsTo(Group::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vpnKey() {
        return $this->hasOne(VpnKey::class);
    }

    public function scopeForUser($query, $user) {
        if (!$user->isSuperadmin()) {
            $query->whereHas('group.users', function($squery) use ($user) {
                $squery->where('users.id', $user->id);
            })->orWhere('creator_id', $user->id);
        }
    }

    public function getStatus() {
        $result = \Vpn::getStatus($this->id);

        if (empty($result)) {
            throw new GetDeviceStatusException('Error fetching status');
        }

        if ($result['status'] == 'error') {
            throw new GetDeviceStatusException($result['error_description']);
        }
        return $result['status'];
    }

    public function getVpnKey() {
        $download_url = config('app.url') . "/vpn/download";
        $latest_key_version = 0;

        // Check that same not expired key already exists
        $key = $this->vpnKey;
        if ($key) {
            $latest_key_version = $key->key_version;
            if ($key->expired) {
                $key->delete();
            }
            else {
                $hash = md5($this->id . $key->key . $key->valid_till);
                return [
                    'device_id' => $this->id,
                    'status' => 'OK',
                    'key_url' => $download_url . "/$hash",
                    'key_valid_till' => $key->valid_till,
                    'key_version' => $latest_key_version,
                ];
            }
        }

        // Use special group for superadmins, that gives access to whole network
        if ($this->user && $this->user->isSuperadmin() && (!$this->user->ownDevice || $this->user->ownDevice->id == $this->id)) {
            $group = env('VPN_GLOBAL_GROUP');
        }
        else {
            $group = $this->group->ip;
        }

        // Get new key from vpn api
        $result = \Vpn::getKey($this->id, $group);
        if ($result['status'] != 'ok') {
            throw new GetKeyException($result['error_description']);
        }

        // Download key contents from vpn api and remember it for 30 days
        $key = \Vpn::downloadKey($result['key_url']);
        $valid_till = \Carbon\Carbon::now()->addYears(1)->format('Y-m-d H:i:s');
        $vpn_key = VpnKey::create([
            'device_id' => $this->id,
            'key' => $key,
            'valid_till' => $valid_till,
            'key_version' => $latest_key_version + 1,
        ]);

        if (isset($result['ip'])) {
            $this->ip = $result['ip'];
            $this->save();
        }

        $hash = md5($this->id . $key . $valid_till);
        return [
            'device_id' => $this->id,
            'status' => 'OK',
            'key_url' => $download_url . "/$hash",
            'key_valid_till' => $valid_till,
            'key_version' => $vpn_key->key_version,
        ];
    }
}
