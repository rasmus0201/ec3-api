<?php

use App\Models\{Device, Sensor};
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_has_sensors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Device::class);
            $table->foreignIdFor(Sensor::class);
            $table->timestamps();

            $table->unique(['device_id', 'sensor_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('device_has_sensors');
    }
};
