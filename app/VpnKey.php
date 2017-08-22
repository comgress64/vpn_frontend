<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VpnKey extends Model
{
    protected $fillable = [
        'device_id', 'key', 'valid_till', 'key_version',
    ];

    public function getExpiredAttribute() {
        return $this->valid_till < \Carbon\Carbon::now();
    }

    public function device() {
        return $this->belongsTo(Device::class);
    }
}
