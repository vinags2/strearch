<?php

namespace App\Models;

use App\Models\Scopes\MyScope;
use App\Traits\Utilities;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use Utilities;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'analysis_id' => 'array',
            'auto_update_activities' => 'boolean',
        ];
    }

    protected function lastActivitiesUpdateFromStrava(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Carbon::parse($value)->format('j/n/Y g:i:s a'),
        );
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new MyScope);
    }

    public static function active_sort()
    {
        $sort = Setting::first(['sort_column', 'sort_direction'])?->toArray() ??
            ['sort_column' => 'start_date_local', 'sort_direction' => 'desc'];

        $sort = Setting::save_sort($sort);

        return $sort;
    }

    public static function current_analysis_id()
    {
        return Setting::first('analysis_id')->analysis_id ?? [false, 0, 0];
    }

    public static function current_timeperiod()
    {
        return Setting::first('time_period')->time_period ?? 0;
    }

    private static function save_sort($sort)
    {

        $sort_column = request()->input('sort');
        if ($sort_column) {
            $sort_direction = Setting::sort_direction($sort);
            Setting::updateOrCreate(
                ['user_id' => auth()->user()->id],
                [
                    'sort_column' => $sort_column,
                    'sort_direction' => $sort_direction,
                ]
            );
            $sort['sort_column'] = $sort_column;
            $sort['sort_direction'] = $sort_direction;
        }

        return $sort;
    }

    private static function sort_direction($sort)
    {

        return $sort['sort_direction'] == 'asc' ? 'desc' : 'asc';

    }

    public static function auto_update_activities()
    {
        return Setting::first('auto_update_activities')?->auto_update_activities;
    }

    public static function last_activities_update_from_strava()
    {
        return Setting::first('last_activities_update_from_strava')?->last_activities_update_from_strava;
    }

    public static function last_activities_update_from_strava_as_unix_timestamp()
    {
        return strtotime(str_replace('/', '-', Setting::last_activities_update_from_strava())) - 12 * 60 * 60;
    }
}
