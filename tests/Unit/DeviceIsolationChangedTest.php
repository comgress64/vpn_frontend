<?php

namespace Tests\Unit;

use Tests\TestCase;

class DeviceIsolationChangedTest extends TestCase
{
    /**
     * Test that when device becomes not isolated, vpn is updated
     * Not user's own device
     * Request to main group without ports
     */
    public function testNotUsersNotIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group) {
            $port->group_id = $group->id;
            $port->save();
        });
        $device = factory(\App\Device::class)->create(['group_id' => $group->id]);

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->isolated = false;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group->ip . '/112', [], 'add']);
    }

    /**
     * Test that when device becomes not isolated, vpn is updated
     * User's own device
     * Request to main group without ports and to user's groups with ports
     */
    public function testUsersNotIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('user')->create();

        $group1 = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group1) {
            $port->group_id = $group1->id;
            $port->save();
        });

        $group2 = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group2) {
            $port->group_id = $group2->id;
            $port->save();
        });

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $user->syncGroups([$group1->id, $group2->id]);

        $device = \App\Device::create([
            'group_id' => $group1->id,
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'isolated' => false,
        ]);

        $user->load('ownDevice');

        event(new \App\Events\DeviceIsolationChanged($user->ownDevice));

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group1->ip . '/112', [], 'add']);
        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group2->ip . '/112', $group2->ports->all(), 'add']);
    }

    /**
     * Test that when device becomes isolated, vpn is updated
     * Not user's own device
     * Request to main group without ports with remove
     */
    public function testNotUsersIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group) {
            $port->group_id = $group->id;
            $port->save();
        });
        $device = factory(\App\Device::class)->states('not_isolated')->create(['group_id' => $group->id]);

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->isolated = true;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group->ip . '/112', [], 'remove']);
    }

    /**
     * Test that when device becomes isolated, vpn is updated
     * User's own device
     * Request to main group without ports and to user's groups with ports with remove
     */
    public function testUsersIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('user')->create();

        $group1 = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group1) {
            $port->group_id = $group1->id;
            $port->save();
        });

        $group2 = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group2) {
            $port->group_id = $group2->id;
            $port->save();
        });

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $user->syncGroups([$group1->id, $group2->id]);

        event(new \App\Events\UserCreated($user));

        $user->load('ownDevice');

        $device = $user->ownDevice;
        $device->isolated = true;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group1->ip . '/112', [], 'remove']);
        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group2->ip . '/112', $group2->ports->all(), 'remove']);
    }

    /**
     * Test that when device becomes not isolated, vpn is updated
     * User's own device SUPERADMIN
     * Request to global group without ports
     */
    public function testSuperadminNotIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('superadmin')->create();

        $group = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group) {
            $port->group_id = $group->id;
            $port->save();
        });

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device = \App\Device::create([
            'group_id' => null,
            'user_id' => $user->id,
            'creator_id' => $user->id,
            'isolated' => false,
        ]);

        $user->load('ownDevice');

        event(new \App\Events\DeviceIsolationChanged($user->ownDevice));

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, env('VPN_GLOBAL_GROUP') . '/48', [], 'add']);
    }

    /**
     * Test that when device becomes isolated, vpn is updated
     * User's own device SUPERADMIN
     * Request to global group without ports
     */
    public function testSuperadminIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('superadmin')->create();

        $group = factory(\App\Group::class)->create();
        factory(\App\GroupPort::class, 3)->make()->each(function($port) use ($group) {
            $port->group_id = $group->id;
            $port->save();
        });

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        event(new \App\Events\UserCreated($user));

        $user->load('ownDevice');

        $device = $user->ownDevice;
        $device->isolated = true;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, env('VPN_GLOBAL_GROUP') . '/48', [], 'remove']);
    }
}
