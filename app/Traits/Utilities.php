<?php

namespace App\Traits;

use App\Models\Strava;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

trait Utilities
{
    public function NotDoneYet(string $message = 'This section')
    {
        return Inertia::render('NotDoneYet', [
            'message' => $message,
        ]);
    }

    // not used any more, and possibly could be deleted.
    private function getAccessToken()
    {
        $strava = Strava::first() ?? new Strava;

        return $strava->authentication_token ?? 'no token saved';
    }

    private function getClientId()
    {
        return env('STRAVA_CLIENT_ID');
    }

    private function getClientSecret()
    {
        return env('STRAVA_CLIENT_SECRET');
    }

    private function getRefreshToken()
    {
        $strava = Strava::first() ?? new Strava;

        return $strava->refresh_token ?? 'no refresh token saved';
    }

    private function getAthleteURL()
    {
        return env('STRAVA_ATHLETE_URL');
    }

    private function getActivitiesURL()
    {
        return env('STRAVA_ACTIVITIES_URL');
    }

    private function me()
    {
        return auth()->user();
    }

    private function my($model, $get = true)
    {
        switch ($model) {
            case 'activities': return $get ? $this->me()->activities : $this->me()->activities();
            case 'setting': return $get ? $this->me()->setting : $this->me()->setting();
            case 'filters': return $get ? $this->me()->filters : $this->me()->filters();
            case 'id': return $this->me()->id;
            default: return $get ? $this->me()->athlete : $this->me()->athlete();
        }
    }

    public static function CurrentYear()
    {
        return now()->format('Y');
    }

    public function getSportTypes()
    {
        $sportTypes = DB::table('activities')->select('sport_type')->distinct()->get()->sortBy('sport_type');

        $objectValues = [];
        foreach ($sportTypes as $object) {
            $objectValues[] = $object->sport_type;
        }

        return $objectValues;
    }
}
