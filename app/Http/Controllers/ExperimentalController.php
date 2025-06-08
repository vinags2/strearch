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

class ExperimentalController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $athlete = $this->my('athlete');

        return Inertia::render('Experimental', []);
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

        return response()->json([
            'athlete' => $athlete ?? false,
            'numberOfActivities' => Activity::count(),
            'numberOfVirtualActivities' => Activity::where('type', 'like', 'Virtual%')->count(),
            'date_of_last_activities_strava_update' => Setting::last_activities_update_from_strava(),
            'numberOfFilters' => Filter::count(),
            'date_of_last_athlete_strava_update' => $athlete->updated_at,
        ],
            201
        );
    }
}
