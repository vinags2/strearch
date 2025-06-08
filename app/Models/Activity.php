<?php

namespace App\Models;

use App\Models\Scopes\MyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['moving_time_as_string', 'start_date_local_as_timestamp'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new MyScope);
    }

    protected function startDateLocal(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $dt = Carbon::parse($value)->format('D j M Y'),
        );
    }

    protected function averageCadence(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value, 0) : null,
        );
    }

    protected function averageHeartrate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value, 0) : null,
        );
    }

    protected function sufferScore(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value, 0) : null,
        );
    }

    protected function maxHeartrate(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value, 0) : null,
        );
    }

    protected function averageWatts(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? round($value, 0) : null,
        );
    }

    protected function averageSpeed(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => round($value * 3.6, 1),
        );
    }

    protected function distance(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => round($value / 1000, 2),
        );
    }

    public function getMovingTimeAsStringAttribute()
    {
        $d = $this->moving_time;
        $h = floor($d / 3600);
        $m = floor($d % 3600 / 60);
        $s = floor($d % 3600 % 60);

        $hDisplay = strval($h);
        $mDisplay = $m <= 9 ? '0'.strval($m) : strval($m);
        $sDisplay = $s <= 9 ? '0'.strval($s) : strval($s);

        return $hDisplay.':'.$mDisplay.':'.$sDisplay;
    }

    public function startDateLocalAsTimestamp(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtotime(str_replace('/', '-', $this->start_date_local)) * 1000, // - 12 * 60 * 60
        );

    }
}
