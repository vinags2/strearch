<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Athlete;
use App\Models\Filter;
use App\Models\Setting;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StatisticsController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Statistics', [
            'laravelVersion' => app()->version(),
            'phpVersion' => phpversion(),
            'appVersion' => config('app.version'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage by an API call.
     */
    public function store($id)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Athlete $athlete)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Athlete $athlete)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Athlete $athlete)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Athlete $athlete)
    {
        //
    }

    /**
     * Return the statistics and athlete data for an API call
     */
    public function stats()
    {
        $athlete = $this->my('athlete');
        $total = round(Activity::sum('distance') / 1000, 0);
        $totalDistanceOfVirtualActivities = round(Activity::where('type', 'like', 'Virtual%')->sum('distance') / 2000, 0);
        $allActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')->first();
        $thisYearActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')
            ->whereRaw("strftime('%Y', start_date_local) = strftime('%Y', 'now')")
            ->first();
        $lastYearActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')
            ->whereRaw("cast(strftime('%Y', start_date_local) as int) = cast(strftime('%Y', 'now') - 1 as int)")
            ->first();
        $allVirtualActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')
            ->where('sport_type', 'VirtualRide')
            ->first();
        $thisYearVirtualActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')
            ->whereRaw("strftime('%Y', start_date_local) = strftime('%Y', 'now')")
            ->where('sport_type', 'VirtualRide')
            ->first();
        $lastYearVirtualActivities = activity::selectRaw('count(*) as c, sum(distance)/1000 as d, avg(distance)/1000 as a, sum(total_elevation_gain) as e, avg(total_elevation_gain) as f')
            ->whereRaw("cast(strftime('%Y', start_date_local) as int) = cast(strftime('%Y', 'now') - 1 as int)")
            ->where('sport_type', 'VirtualRide')
            ->first();

        return response()->json([
            'athlete' => $athlete ?? false,
            'allActivities' => [
                'count' => $allActivities->c,
                'totalDistance' => round($allActivities->d),
                'averageDistance' => round($allActivities->a),
                'totalAscent' => round($allActivities->e),
                'averageAscent' => round($allActivities->f),
            ],
            'thisYearActivities' => [
                'count' => $thisYearActivities->c,
                'totalDistance' => round($thisYearActivities->d),
                'averageDistance' => round($thisYearActivities->a),
                'totalAscent' => round($thisYearActivities->e),
                'averageAscent' => round($thisYearActivities->f),
            ],
            'lastYearActivities' => [
                'count' => $lastYearActivities->c,
                'totalDistance' => round($lastYearActivities->d),
                'averageDistance' => round($lastYearActivities->a),
                'totalAscent' => round($lastYearActivities->e),
                'averageAscent' => round($lastYearActivities->f),
            ],
            'allVirtualActivities' => [
                'count' => $allVirtualActivities->c,
                'totalDistance' => round($allVirtualActivities->d),
                'averageDistance' => round($allVirtualActivities->a),
                'totalAscent' => round($allVirtualActivities->e),
                'averageAscent' => round($allVirtualActivities->f),
            ],
            'thisYearVirtualActivities' => [
                'count' => $thisYearVirtualActivities->c,
                'totalDistance' => round($thisYearVirtualActivities->d),
                'averageDistance' => round($thisYearVirtualActivities->a),
                'totalAscent' => round($thisYearVirtualActivities->e),
                'averageAscent' => round($thisYearVirtualActivities->f),
            ],
            'lastYearVirtualActivities' => [
                'count' => $lastYearVirtualActivities->c,
                'totalDistance' => round($lastYearVirtualActivities->d),
                'averageDistance' => round($lastYearVirtualActivities->a),
                'totalAscent' => round($lastYearVirtualActivities->e),
                'averageAscent' => round($lastYearVirtualActivities->f),
            ],
            'numberOfFilters' => Filter::count(),
            'date_of_last_athlete_strava_update' => $athlete->updated_at ?? 'No athlete data',
            'date_of_last_activities_strava_update' => Setting::last_activities_update_from_strava(),
        ],
            201
        );
    }
}
