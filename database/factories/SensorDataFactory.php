<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SensorData;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;

$sensors = ['humidity', 'temperature', 'light', 'sound', 'vibration'];

$factory->define(SensorData::class, function (Faker $faker) use ($sensors) {
    return [
        'sensor' => Arr::random($sensors),
        'value' => rand(0, 1023),
        'sensored_at' => $faker->dateTimeBetween('-10 days', 'now')
    ];
});
