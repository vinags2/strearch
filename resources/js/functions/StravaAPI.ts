import { API_data, async_axios, sendErrorNotifiction } from '@/functions/Flags.js';
import { saveAccessToken, save_activities, save_activity, save_athlete } from '@/functions/StrearchAPI.js';

let athlete_url: string;
let activity_url: string;
let activities_url: string;
let saved_access_token: string;
let saved_refresh_token: string;
let client_id: string;
let client_secret: string;
let date_of_last_activity_update: number;

async function getStravaMetaData() {
    const ret = await async_axios({ url: route('dfsd'), flag: 3 });
    if (ret.error) {
        return false;
    }
    athlete_url = ret.data.athlete_url;
    activity_url = ret.data.activity_url;
    activities_url = ret.data.activities_url;
    client_id = ret.data.client_id;
    client_secret = ret.data.client_secret;
    saved_refresh_token = ret.data.refresh_token;
    date_of_last_activity_update = ret.data.date_of_last_activity_update;
    return true;
}

async function getActivitiesData(all: boolean = false) {
    let pageNo = 1;
    let moreData = true;
    const per_page = 200;
    let ret: any;
    const after = all ? 1 : date_of_last_activity_update;
    do {
        let parameters = `after=${after}&per_page=${per_page}&page=${pageNo++}`;
        let full_url = createUrl({ url: activities_url, parameters: parameters });
        ret = await async_axios({ url: full_url, flag: 10 });
        if (ret?.error) {
            moreData = false;
        } else {
            moreData = ret?.data.length > 0;
            if (moreData) await save_activities(ret.data);
        }
        // pageNo < 30 is there to prevent infinite loops in case Strava API does not return an end of data.
        // The implication is that if there are more than 6000 activities, the code will not work correctly.
    } while (moreData && pageNo < 30);
    return !ret.error;
}

async function getAthleteData() {
    let full_url = createUrl({ url: athlete_url });
    const ret = await async_axios({ url: full_url, flag: 1 });
    if (!ret.error) {
        await save_athlete(ret.data);
    }
    return !ret.error;
}

async function getAnActivity(id: number) {
    let full_url = createUrl({ url: activity_url, id: id });
    const ret = await async_axios({ url: full_url, flag: 6, feedback: true });
    if (!ret.error) {
        await save_activity(ret.data);
    }
    return !ret.error;
}

async function authoriseWithStrava(downloadWhat: string = 'athlete') {
    if (!(await getStravaMetaData())) return false;

    let full_url =
        'https://www.strava.com/api/v3/oauth/token?client_id=' +
        client_id +
        '&client_secret=' +
        client_secret +
        '&grant_type=refresh_token&refresh_token=' +
        saved_refresh_token;

    let ret = await async_axios({ url: full_url, flag: 4, method: 'post' });
    if (ret.error) {
        return false;
    }

    saved_access_token = ret.data.access_token;
    saved_refresh_token = ret.data.refresh_token;

    await saveAccessToken(saved_access_token, saved_refresh_token);
    return true;
}

// downloadWhat can equal 'athlete', 'activities', 'all activities', 'activity'
export async function getStravaData(downloadWhat: string, id: number = 0) {
    let error_string: string = 'athlete';
    if (!(await authoriseWithStrava(downloadWhat))) {
        API_data.value = { code: 3, data: ['There was an error authorising with Strava.'], error: true };
        return;
    }
    let API_number_of_calls = 0;
    while (++API_number_of_calls < 3) {
        if (downloadWhat == 'athlete') {
            if (await getAthleteData()) {
                API_data.value = { code: 1, data: ['Athlete data downloaded from Strava successfully'], error: false };
                return;
            }
        } else if (downloadWhat == 'activity') {
            if (await getAnActivity(id)) {
                API_data.value = { code: 1, data: ['Activity data downloaded from Strava successfully'], error: false };
                return;
            }
        } else {
            error_string = 'activities';
            if (await getActivitiesData(downloadWhat == 'All Activities')) {
                API_data.value = { code: 10, data: ['Activities data downloaded from Strava successfully'], error: false };
                return;
            }
        }
    }
    API_data.value = { code: 5, data: ['There was an error downloading the ' + error_string + ' data.'], error: true };
    sendErrorNotifiction(5, '0', ['Too many attempts trying to authorise with Strava']);
}

function createUrl({ url = '', parameters = '', id = 0 }) {
    return url + (id == 0 ? '' : '/' + id) + '?access_token=' + saved_access_token + (parameters ? '&' + parameters : '');
}
