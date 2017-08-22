<?php

namespace Tests\Unit;

use Tests\TestCase;

class GroupPortsUpdatedTest extends TestCase
{
    /**
     * Test that vpn is updated when group gains new port
     * Device is not isolated
     */
    public function testShouldUpdateVpnWithNewPort()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $group = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->states('not_isolated')->create(['group_id' => $group->id]);

        $group->syncPorts([10]);

        \Vpn::shouldHaveReceived('setGroups', [$device->id, $group->ip . '/112', [10], 'add']);
    }

    /**
     * Test that vpn is not updated when group gains new port
     * Same as above, but device is isolated
     */
    public function testShouldNotUpdateVpnWithNewPort()
    {
        \Vpn::shouldReceive('getKey')->andReturn(['status' => 'ok', 'key_url' => 'url']);
        \Vpn::shouldReceive('downloadKey')->andReturn('key');

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        $group = factory(\App\Group::class)->create();
        $device = factory(\App\Device::class)->create(['group_id' => $group->id]);

        $group->syncPorts([10]);

        \Vpn::shouldNotHaveReceived('setGroups');
    }
}
