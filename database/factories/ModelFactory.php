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

$faker = Faker\Factory::create("ko_KR");

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function () use ($faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Post::class, function ()  use ($faker) {
    return [
        'title' => $faker->sentence,
        'content' => $faker->paragraph,
    ];
});