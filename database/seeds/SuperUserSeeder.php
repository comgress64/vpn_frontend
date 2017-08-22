<?php

use Illuminate\Database\Seeder;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::find(1);

        if (!$user) {
            $user = \App\User::create([
                'id' => 1,
                'email' => 'vpn@vpn.com',
                'fname' => 'vpn',
                'lname' => 'vpn',
                'password' => 'vpn',
                'max_devices' => 100,
            ]);
            $user->api_token = 'testuser';
            $user->save();
            event(new \App\Events\UserCreated($user));
        }
    }
}
