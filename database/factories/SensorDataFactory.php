<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class SensorDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $sensors = [1, 2, 3, 4];

        return [
            'sensor_id' => Arr::random($sensors),
            'value' => rand(0, 1023),
            'sensored_at' => faker()->dateTimeBetween('-10 days', 'now')
        ];
    }
}
