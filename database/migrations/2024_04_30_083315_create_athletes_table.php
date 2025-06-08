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
        Schema::create('athletes', function (Blueprint $table) {
            $table->id()
                ->comment('the strava id');
            $table->unsignedBigInteger('user_id')
                ->index();
            $table->string('username')
                ->nullable();
            $table->unsignedTinyInteger('resource_state')
                ->comment('1. meta, 2. summary, 3. detailed');
            $table->string('first_name')
                ->nullable();
            $table->string('last_name')
                ->nullable();
            $table->string('bio')
                ->comment('eg An OFIL')
                ->nullable();
            $table->string('city')
                ->nullable();
            $table->string('state')
                ->nullable();
            $table->string('country')
                ->nullable();
            $table->char('sex', length: 1)
                ->nullable();
            $table->boolean('summit')
                ->comment('if premium member, this value is true')
                ->default(false);
            $table->datetime('strava_created_at')
                ->nullable();
            $table->datetime('strava_updated_at')
                ->nullable();
            $table->unsignedTinyInteger('badge_type_id')
                ->nullable();
            $table->double('weight')
                ->comment('weight in kg')
                ->nullable();
            $table->string('profile_picture_medium')
                ->comment('a URL to the medium profile picture')
                ->nullable();
            $table->string('profile_picture_large')
                ->comment('a URL to the large profile picture')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('athletes');
    }
};
