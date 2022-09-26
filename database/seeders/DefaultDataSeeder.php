<?php

namespace Database\Seeders;

use App\Models\{Device, Location, Sensor};
use Illuminate\Database\Seeder;

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Device 1 (stm32)
        // Setup default locations
        $location = Location::create([
            'name' => 'MU8-Z29',
            'lat' => '55.38080481126275',
            'long' => '10.411834955435646',
            'timezone' => 'Europe/Copenhagen',
        ]);

        // Setup default devices
        $device = Device::create([
            'name' => 'Enhed #1',
            'location_id' => $location->id,
        ]);

        // Setup default sensors
        $sensor1 = Sensor::create(['name' => 'temperature']);
        $sensor2 = Sensor::create(['name' => 'humidity']);
        $sensor3 = Sensor::create(['name' => 'light']);
        $sensor4 = Sensor::create(['name' => 'sound']);

        $device->sensors()->attach([
            $sensor1->id,
            $sensor2->id,
            $sensor3->id,
            $sensor4->id,
        ]);


        // Device 2 (rpi3)
        // Setup default locations
        $location = Location::create([
            'name' => 'Server-rum',
            'lat' => '25.000018128764673',
            'long' => '-71.00018207031547',
            'timezone' => 'America/Araguaina',
        ]);

        // Setup default devices
        $device = Device::create([
            'name' => 'Enhed #2',
            'location_id' => $location->id,
        ]);

        $sensor5 = Sensor::create(['name' => 'ultraSonic']);
        $sensor6 = Sensor::create(['name' => 'webcam']);

        $device->sensors()->attach([
            $sensor1->id,
            $sensor2->id,
            $sensor5->id,
            $sensor6->id,
        ]);
    }
}
