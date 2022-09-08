<?php

namespace Database\Seeders;

use App\Models\SensorMeasurement;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate random sensor data
        SensorMeasurement::factory()->count(1000)->create();
    }
}
