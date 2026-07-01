<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->id()
                ->comment('Strava segment id');
            $table->string('name')
                ->nullable()
                ->index();
            $table->string('activity_type')
                ->comment('May take one of the following values: Ride, Run')
                ->default('Ride')
                ->nullable();
            $table->double('distance')
                ->nullable()
                ->index();
            $table->double('average_grade')
                ->nullable();
            $table->double('maximum_grade')
                ->nullable();
            $table->double('elevation_high')
                ->comment('The segments\'s highest elevation, in meters')
                ->nullable();
            $table->double('elevation_low')
                ->comment('The segments\'s lowest elevation, in meters')
                ->nullable();
            $table->string('elevation_profile')
                ->nullable();
            $table->boolean('is_private')
                ->nullable()
                ->default(false);
            $table->boolean('is_hazardous')
                ->nullable()
                ->default(false);
            $table->boolean('is_starred')
                ->nullable()
                ->default(false);
            $table->double('total_elevation_gain')
                ->comment('The total elevation gain of the segment, in metres')
                ->nullable();
            $table->unsignedBigInteger('map_id')
                ->nullable();
            $table->string('map_polyline')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('segment_efforts');
    }
};
