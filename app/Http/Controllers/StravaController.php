<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Strava;
use App\Traits\Utilities;
use Illuminate\Http\Request;

class StravaController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($token)
    {
        $strava = Strava::first() ?? new Strava;
        $strava->authentication_token = $token;
        $strava->save();

        return response()->json(['status' => 'Token saved successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Strava $strava)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Strava $strava)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Strava $strava)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Strava $strava)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function download()
    {
        return $this->NotDoneYet('Downloading from Strava');
    }

    /**
     * Return the data needed to download data from Strava
     */
    public function stravaMetaData()
    {
        return response()->json(
            ['client_id' => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'refresh_token' => $this->getRefreshToken(),
                'athlete_url' => 'https://www.strava.com/api/v3/athlete',
                'activities_url' => 'https://www.strava.com/api/v3/athlete/activities',
                'date_of_last_activity_update' => Setting::last_activities_update_from_strava_as_unix_timestamp()],
            201
        );
    }
}
