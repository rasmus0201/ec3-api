<?php

namespace Database\Seeders;

use App\Models\SensorData;
use Illuminate\Database\Seeder;

class SensorDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SensorData::truncate();
        SensorData::factory()->count(1000)->create();
    }
}
