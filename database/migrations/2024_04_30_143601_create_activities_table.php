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
        Schema::create('activities', function (Blueprint $table) {
            $table->id()
                ->comment('Strava activity id');
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->unsignedTinyInteger('resource_state')
                ->comment('1. meta, 2. summary, 3. detailed');
            $table->unsignedBigInteger('athlete_id')
                ->comment('linked to Athletes Id')
                ->index();
            $table->unsignedTinyInteger('athlete_resource_state');
            $table->string('name')
                ->nullable();
            $table->double('distance')
                ->nullable()
                ->index();
            $table->unsignedInteger('moving_time')
                ->comment('Time in seconds')
                ->nullable()
                ->index();
            $table->unsignedInteger('elapsed_time')
                ->comment('Time in seconds')
                ->nullable();
            $table->double('total_elevation_gain')
                ->nullable()
                ->index();
            $table->string('type')
                ->comment('eg Ride')
                ->nullable()
                ->index();
            $table->string('sport_type')
                ->comment('eg Ride')
                ->nullable();
            $table->string('workout_type')
                ->comment('eg Ride')
                ->nullable();
            $table->dateTime('start_date')
                ->nullable()
                ->index();
            $table->dateTime('start_date_local')
                ->nullable()
                ->index()
                ->comment('eg 2024-04-29T15:42:43Z');
            $table->string('timezone')
                ->nullable()
                ->comment('eg (GMT+10:00) Australia/Sydney');
            $table->double('UTC_offset')
                ->nullable()
                ->comment('UTC offset in seconds');
            $table->string('location_city')
                ->nullable();
            $table->string('location_state')
                ->nullable();
            $table->string('location_country')
                ->nullable();
            $table->unsignedInteger('achievement_count')
                ->nullable()
                ->comment('the number of PRs, etc');
            $table->unsignedInteger('kudos_count')
                ->nullable()
                ->default(0);
            $table->unsignedInteger('comment_count')
                ->nullable()
                ->default(0);
            $table->unsignedInteger('athlete_count')
                ->nullable()
                ->comment('The number of athletes that did the same activity')
                ->default(0);
            $table->unsignedInteger('photo_count')
                ->nullable()
                ->default(0);
            $table->json('map')
                ->nullable()
                ->comment('serialized array of id, summary_polyline, and resource_state');
            $table->boolean('trainer')
                ->nullable()
                ->default(false);
            $table->boolean('commute')
                ->nullable()
                ->default(false);
            $table->boolean('manual')
                ->nullable()
                ->default(false);
            $table->boolean('private')
                ->nullable()
                ->default(false);
            $table->string('visibility')
                ->nullable();
            $table->boolean('flagged')
                ->nullable()
                ->default(false);
            $table->string('gear_id')
                ->nullable()
                ->index();
            $table->json('start_latlong')
                ->comment('serialized array of latitude and longitude')
                ->nullable();
            $table->json('end_latlong')
                ->comment('serialized array of latitude and longitude')
                ->nullable();
            $table->double('average_speed')
                ->comment('average speed in m/s. Convert to kph by multiplying by 3.6.')
                ->nullable();
            $table->double('max_speed')
                ->comment('max speed in m/s. Convert to kph by multiplying by 3.6.')
                ->nullable();
            $table->double('average_cadence')
                ->comment('average cadence')
                ->nullable();
            $table->double('average_temperature')
                ->comment('average temperature')
                ->nullable();
            $table->double('average_watts')
                ->comment('average watts')
                ->nullable();
            $table->double('max_watts')
                ->comment('maximum watts')
                ->nullable();
            $table->double('weighted_average_watts')
                ->comment('weighted average watts')
                ->nullable();
            $table->double('kilojoules')
                ->comment('kilojoules')
                ->nullable();
            $table->boolean('device_watts')
                ->nullable()
                ->comment('is wattage recorded from a power metre?')
                ->default(false);
            $table->boolean('has_heartrate')
                ->nullable()
                ->comment('Was the athlete wearing a HR monitor?')
                ->default(false);
            $table->double('average_heartrate')
                ->nullable()
                ->comment('average heartrate')
                ->default(0);
            $table->double('max_heartrate')
                ->nullable()
                ->comment('max heartrate')
                ->default(0);
            $table->boolean('heartrate_opt_out')
                ->nullable()
                ->comment('do not what this field is')
                ->default(false);
            $table->boolean('display_hide_heartrate_option')
                ->nullable()
                ->default(true);
            $table->double('elev_high')
                ->nullable()
                ->comment('elevation')
                ->default(0);
            $table->double('elev_low')
                ->nullable()
                ->comment('elevation')
                ->default(0);
            $table->unsignedInteger('upload_id')
                ->nullable();
            $table->string('upload_id_str')
                ->nullable();
            $table->string('external_id')
                ->comment('the name of the file that was uploaded')
                ->nullable();
            $table->boolean('from_accepted_tag')
                ->nullable()
                ->comment('do not what this field is')
                ->default(false);
            $table->unsignedInteger('pr_count')
                ->nullable()
                ->comment('the number of PRs')
                ->default(0);
            $table->unsignedInteger('total_photo_count')
                ->nullable()
                ->default(0)
                ->comment('the number of photos uploaded');
            $table->boolean('has_kudoed')
                ->nullable()
                ->comment('do not what this field is')
                ->default(false);
            $table->double('suffer_score')
                ->nullable()
                ->comment('suffer score')
                ->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
