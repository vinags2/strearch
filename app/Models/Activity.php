<?php

namespace App\Models;

use App\Traits\CommonModelFunctions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Scope;

class Activity extends Model
{
    use CommonModelFunctions, HasFactory;

    protected $guarded = [];

    protected $appends = ['moving_time_as_string', 'start_date_as_timestamp'];

    protected $hidden = ['resource_state', 'user_id', 'athlete_id', 'athlete_resource_state', 'elapsed_time', 'type',
        'workout_type', 'start_date', 'timezone', 'UTC_offset', 'location_city', 'location_state', 'location_country', 'achievement_count',
        'kudos_count', 'comment_count',  'athlete_count', 'photo_count', 'map', 'trainer', 'commute', 'manual', 'private',
        'visibility', 'flagged', 'gear_id', 'start_latlong', 'end_latlong', 'average_temperature',
        'has_heartrate', 'heartrate_opt_out', 'display_hide_heartrate_option', 'elev_high', 'elev_low', 'upload_id',
        'external_id', 'upload_id_str', 'from_accepted_tag', 'pr_count', 'total_photo_count', 'has_kudoed', 'created_at',
        'updated_at'];

    public function athlete(): BelongsTo
    {
        return $this->belongsTo(Athlete::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function segment_efforts(): HasMany
    {
        return $this->hasMany(SegmentEffort::class)->orderBy('start_date_local', 'desc');
    }
}
