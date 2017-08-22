<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DeviceTest extends TestCase
{
    /**
     * Test that vpn is updated when device is assigned to new group
     * Device is not isolated
     */
    public function testShouldUpdateVpnForNewGroup()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group1 = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->states('not_isolated')->create();

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->group_id = $group1->id;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group1->ip . '/112', [], 'add']);
    }

    /**
     * Same as previous but with previous group (should make two requests)
     * Device is not isolated
     */
    public function testShouldUpdateVpnForNewAndPrevGroup()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group1 = factory(\App\Group::class)->create();
        $group2 = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->states('not_isolated')->create(['group_id' => $group1->id]);

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->group_id = $group2->id;
        $device->save();

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group2->ip . '/112', [], 'add']);
        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group1->ip . '/112', [], 'remove']);
    }

    /**
     * Test that vpn is not updated when device is assigned to new group
     * Device is isolated
     */
    public function testShouldNotUpdateVpnForNewGroup()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group1 = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->create();

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->group_id = $group1->id;
        $device->save();

        \Vpn::shouldNotHaveReceived('setGroups');
    }

    /**
     * Same as previous but with previous group (should make two requests)
     * Device is isolated
     */
    public function testShouldNotUpdateVpnForNewAndPrevGroup()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        $group1 = factory(\App\Group::class)->create();
        $group2 = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->create(['group_id' => $group1->id]);

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $device->group_id = $group2->id;
        $device->save();

        \Vpn::shouldNotHaveReceived('setGroups');
    }

}
