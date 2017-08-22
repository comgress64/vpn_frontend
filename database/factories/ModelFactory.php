<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'fname' => $faker->firstName,
        'lname' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
    ];
});

$factory->state(App\User::class, 'user', function (Faker\Generator $faker) {
    return [
        'role' => 'user',
    ];
});

$factory->state(App\User::class, 'superadmin', function (Faker\Generator $faker) {
    return [
        'role' => 'superadmin',
    ];
});

$factory->define(App\Group::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});

$factory->define(App\GroupPort::class, function (Faker\Generator $faker) {
    return [
        'port' => $faker->numberBetween(20, 20000),
    ];
});

$factory->define(App\Device::class, function (Faker\Generator $faker) {
    $user = factory(\App\User::class)->create();
    return [
        'user_id' => $user->id,
        'creator_id' => $user->id,
    ];
});

$factory->state(App\Device::class, 'not_isolated', function (Faker\Generator $faker) {
    return [
        'isolated' => false,
    ];
});
