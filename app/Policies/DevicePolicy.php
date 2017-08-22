<?php

namespace App\Policies;

use App\User;
use App\Device;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    public function before($user, $ability) {
        if ($user->isSuperadmin()) {
            return true;
        }
        if (!$user->hasPermission('manage_devices')) {
            return false;
        }
    }

    protected function hasAccessToDevice(User $user, Device $device) {
        return $user->groups->map(function($group) {
            return $group->devices;
        })->flatten()->pluck('id')->contains($device->id);
    }

    /**
     * Determine whether the user can view the device.
     *
     * @param  \App\User  $user
     * @param  \App\Device  $device
     * @return mixed
     */
    public function view(User $user, Device $device)
    {
        return $this->hasAccessToDevice($user, $device);
    }

    /**
     * Determine whether the user can create devices.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the device.
     *
     * @param  \App\User  $user
     * @param  \App\device  $device
     * @return mixed
     */
    public function update(User $user, Device $device)
    {
        return $this->hasAccessToDevice($user, $device);
    }

    /**
     * Determine whether the user can delete the device.
     *
     * @param  \App\User  $user
     * @param  \App\Device  $device
     * @return mixed
     */
    public function delete(User $user, Device $device)
    {
        return $this->hasAccessToDevice($user, $device);
    }
}
