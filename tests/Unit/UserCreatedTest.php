<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserCreatedTest extends TestCase
{
    /**
     * Test that vpn requests are made for first group without ports
     * and for the rest of groups with ports
     */
    public function testVpnRequests()
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
        factory(\App\GroupPort::class, 2)->make()->each(function($port) use ($group2) {
            $port->group_id = $group2->id;
            $port->save();
        });

        $user->syncGroups([$group1->id, $group2->id]);

        \Vpn::shouldReceive('setGroups')->andReturn(false);
        \Vpn::shouldReceive('getData')->andReturn([]);

        event(new \App\Events\UserCreated($user));

        $user->load('ownDevice');

        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group1->ip . '/112', []]);
        \Vpn::shouldHaveReceived('setGroups', [$user->ownDevice->id, $group2->ip . '/112', $group2->ports->all(), 'add']);
    }
}
