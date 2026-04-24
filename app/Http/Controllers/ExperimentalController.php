<?php

namespace App\Http\Controllers;

use App\Models\Activity;
// use App\Models\Filter;
use App\Models\Setting;
use App\Traits\Utilities;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class ExperimentalController extends Controller
{
    use Utilities;

    private $sort_column;

    private $sort_direction;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {

        // $response = Http::get('https://www.strava.com/api/v3/activities/15654812865?access_token=d1268023dbf6c1e4c1d017551c317ac4d41181fe');
        // $resJson = $response->json();
        // dd($resJson['segment_efforts'], $resJson['segment_efforts'][1]['name'], $resJson);

        // $metaData = (new StravaController)->stravaMetaData();
        // dd($metaData->original, $metaData->original['client_id']);

        // $activities = $this->getActivities();

        (new GetDataFromStravaController)->getAnActivity(15654812865);

        return Inertia::render('Experimental', [
            'aProp' => ['key' => 'value from controller'],
        ]);

    }

    // public function index_with_change_of_filter(Request $request): Response
    // {
    //     Filter::set_active($request->input('selected'));

    //     return $this->index(1);
    // }

    // private function getFilter()
    // {
    //     return Filter::active_filter();
    // }

    private function getActivities()
    {
        // $filter = $this->getFilter();

        $activities = $this->my('activities', false); // ->where($filter);
        $activities = $this->addOrderBy($activities);

        return $activities->get();
        // return $activities->paginate(50);
    }

    private function addOrderBy($activities)
    {
        $sort_info = Setting::active_sort();

        $this->sort_column = $sort_info['sort_column'];
        $this->sort_direction = $sort_info['sort_direction'];

        return $activities->orderBy($sort_info['sort_column'], $sort_info['sort_direction']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store an array of activities from an API call
     */
    public function store(Request $request)
    {
        foreach ($request->input() as $activity) {
            $this->store_one_activity($activity);
        }

        (new SettingController)->saveDateOfActivitiesStore();

        return response()->json(['status' => 'Saving was successful'], 201);
    }

    private function store_one_activity($activity)
    {
        $r = Activity::updateOrCreate(
            ['id' => $activity['id']],
            [
                'name' => $activity['name'],
                'id' => $activity['id'],
                'user_id' => $this->my('id'),
                'name' => $activity['name'],
                'athlete_id' => $activity['athlete']['id'] ?? $activity['athlete_id'],
                'athlete_resource_state' => $activity['athlete']['resource_state'] ?? $activity['athlete_resource_state'],
                'achievement_count' => $activity['achievement_count'] ?? null,
                'total_photo_count' => $activity['total_photo_count'] ?? null,
                'resource_state' => $activity['resource_state'] ?? null,
                'distance' => $activity['distance'] ?? null,
                'elapsed_time' => $activity['elapsed_time'] ?? null,
                'total_elevation_gain' => $activity['total_elevation_gain'] ?? null,
                'type' => $activity['type'] ?? null,
                'sport_type' => $activity['sport_type'] ?? null,
                'workout_type' => $activity['workout_type'] ?? null,
                'start_date' => $activity['start_date'] ?? null,
                'start_date_local' => $activity['start_date_local'] ?? null,
                'timezone' => $activity['timezone'] ?? null,
                'UTC_offset' => $activity['UTC_offset'] ?? null,
                'location_city' => $activity['location_city'] ?? null,
                'location_country' => $activity['location_country'] ?? null,
                'location_state' => $activity['location_state'] ?? null,
                'kudos_count' => $activity['kudos_count'] ?? null,
                'comment_count' => $activity['comment_count'] ?? null,
                'athlete_count' => $activity['athlete_count'] ?? null,
                'photo_count' => $activity['photo_count'] ?? null,
                'map' => 'Use $casts and serialize for this field',
                'trainer' => $activity['trainer'] ?? null,
                'commute' => $activity['commute'] ?? null,
                'manual' => $activity['manual'] ?? null,
                'private' => $activity['private'] ?? null,
                'visibility' => $activity['visibility'] ?? null,
                'flagged' => $activity['flagged'] ?? null,
                'gear_id' => $activity['gear_id'] ?? null,
                'start_latlong' => 'Use $casts and serialize for this field',
                'end_latlong' => 'Use $casts and serialize for this field',
                'average_speed' => $activity['average_speed'] ?? null,
                'max_speed' => $activity['max_speed'] ?? null,
                'average_cadence' => $activity['average_cadence'] ?? null,
                'average_temperature' => $activity['average_temperature'] ?? null,
                'average_watts' => $activity['average_watts'] ?? null,
                'max_watts' => $activity['max_watts'] ?? null,
                'weighted_average_watts' => $activity['weighted_average_watts'] ?? null,
                'kilojoules' => $activity['kilojoules'] ?? null,
                'device_watts' => $activity['device_watts'] ?? null,
                'has_heartrate' => $activity['has_heartrate'] ?? null,
                'average_heartrate' => $activity['average_heartrate'] ?? null,
                'max_heartrate' => $activity['max_heartrate'] ?? null,
                'heartrate_opt_out' => $activity['heartrate_opt_out'] ?? null,
                'display_hide_heartrate_option' => $activity['display_hide_heartrate_option'] ?? null,
                'elev_high' => $activity['elev_high'] ?? null,
                'elev_low' => $activity['elev_low'] ?? null,
                'upload_id' => $activity['upload_id'] ?? null,
                'upload_id_str' => $activity['upload_id_str'] ?? null,
                'external_id' => $activity['external_id'] ?? null,
                'from_accepted_tag' => $activity['from_accepted_tag'] ?? null,
                'pr_count' => $activity['pr_count'] ?? null,
                'has_kudoed' => $activity['has_kudoed'] ?? null,
                'suffer_score' => $activity['suffer_score'] ?? null,
                'moving_time' => $activity['moving_time'] ?? null,
                'device_name' => $activity['device_name'] ?? null,
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Activity $activity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        //
    }

    public function api_get()
    {
        $activities = $this->getActivities();

        return response()->json([
            'activities' => $activities ?? false,
            'date_of_last_activities_strava_update' => Setting::last_activities_update_from_strava(),
        ],
            201
        );
    }
}
