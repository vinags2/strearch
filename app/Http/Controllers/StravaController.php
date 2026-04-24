<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Strava;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
    public function store($access_token, $refresh_token)
    {
        // Validate the access token and refresh token
        if (empty($access_token) || empty($refresh_token)) {
            return response()->json(['error' => 'Invalid access token or refresh token'], 400);
        }

        Strava::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'authentication_token' => $access_token,
                'refresh_token' => $refresh_token,
            ]
        );

        return response()->json(['status' => 'Token saved successfully'], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function authorization_stage2(Request $request)
    {
        dd($request->all());

        return response()->json(['expires at' => $request->input('expires_at'), 'all' => $request->all()], 201);
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
                'athlete_url' => $this->getAthleteURL(),
                'activity_url' => $this->getActivityURL(),
                'activities_url' => $this->getActivitiesURL(),
                'date_of_last_activity_update' => Setting::last_activities_update_from_strava_as_unix_timestamp()],
            201
            // ['The Strava API is not yet implemented in this version of Strearch.'],
            // 501
        );
    }

    public function sendErrorNotification(Request $request)
    {
        Mail::to(env('MAIL_TO_FOR_ERRORS', 'retired@gregvinall.com'))
            ->send(new \App\Mail\ErrorNotification(
                $request->input('flag'),
                $request->input('error_code'),
                is_array($request->input('error_response')) ? implode(', ', $request->input('error_response')) : $request->input('error_response'),
                $request->input('error_message')
            ));

        return response()->json(['status' => 'Error notification sent'], 200);
    }
}
