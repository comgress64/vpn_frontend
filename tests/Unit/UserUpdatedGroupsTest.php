<?php

namespace Tests\Unit;

use Tests\TestCase;

class UserUpdatedGroupsTest extends TestCase
{
    /**
     * Test that vpn requests are made when user updates its groups
     */
    public function testNotIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('user')->create();

        $group1 = factory(\App\Group::class)->create();
        $group2 = factory(\App\Group::class)->create();

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $user->syncGroups([$group1->id]);

        event(new \App\Events\UserCreated($user));

        $user->load('ownDevice');
        $user->load('groups');

        $user->syncGroups([$group2->id]);

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group1->ip . '/112', [], 'remove']);
        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group2->ip . '/112', [], 'add']);
    }

    /**
     * Test that vpn requests are not made when user updates its groups
     * Device is isolated
     */
    public function testIsolated()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $user = factory(\App\User::class)->states('user')->create();

        $group1 = factory(\App\Group::class)->create();
        $group2 = factory(\App\Group::class)->create();

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $user->syncGroups([$group1->id]);

        event(new \App\Events\UserCreated($user));

        $user->load('ownDevice');
        $user->load('groups');

        $device = $user->ownDevice;
        $device->isolated = true;
        $device->save();

        $user->load('ownDevice');

        $user->syncGroups([$group2->id]);

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group1->ip . '/112', [], 'remove']);
        \Vpn::shouldNotHaveReceived('setGroups', [$user->ownDevice->id, $group2->ip . '/112', [], 'add']);
    }

}
