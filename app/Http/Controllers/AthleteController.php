<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Traits\Utilities;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AthleteController extends Controller
{
    use Utilities;

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render('Athlete', []);
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
        $athlete = Athlete::updateOrCreate(
            ['id' => $id],
            [
                'id' => $id,
                'user_id' => $this->my('id'),
                'username' => request()->input('username'),
                'resource_state' => request()->input('resource_state'),
                'first_name' => request()->input('firstname'),
                'last_name' => request()->input('lastname'),
                'bio' => request()->input('bio'),
                'city' => request()->input('city'),
                'state' => request()->input('state'),
                'country' => request()->input('country'),
                'sex' => request()->input('sex'),
                'summit' => request()->input('summit'),
                'strava_created_at' => request()->input('created_at'),
                'strava_updated_at' => request()->input('updated_at'),
                'badge_type_id' => request()->input('badge_type_id'),
                'weight' => request()->input('weight'),
                'profile_picture_medium' => request()->input('profile'),
                'profile_picture_large' => request()->input('profile_medium'),
                'follower_count' => request()->input('follower_count'),
            ]
        );

        return response()->json(['athlete_id' => $id], 201);
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

    public function api_get()
    {
        $athlete = $this->my('athlete');

        return response()->json([
            'athlete' => $athlete ?? false,
            'date_of_last_athlete_strava_update' => $athlete->updated_at ?? 'no athlete data',
        ],
            //     400
            // );
            200
        );
    }
}
