import axios from 'axios';
// values for the "flag" parameter used in async_axios function:
// 1 = successful retrieved athlete data from Strava *** athlete data download from Strava ***
// 2 = the access token saved to the DB
// 3 = getting authorization data to access Strava from Laravel
// 4 = Too many attempts trying to authorise, or other error, with Strava
// 5 = Too many attempts trying to authorise, or other error, with Strava
// 6 = successful retrieved an activity from Strava  *** an activity data download from Strava ***
// 8 = Stats uploaded from Laravel
// 9 = Activities data has been saved to the database
// 10 = A (Strava) page of Activities has been downloaded from Strava  *** activities data download from Strava ***
// 11 = Athlete data has been uploaded from the DB
// 12 = Activities data has been uploaded from the DB
// 13 = Filtered Activities data has been saved to the database
// 14 = Filter data has been uploaded from the DB
// 16 = Filters data has been saved to the DB
// 18 = Filter deleted from the DB
// 22 = Chart Data uploaded from Laravel
// 23 = All activities downloaded from Strava

interface WatchData {
    code: number;
    data: any;
    error?: boolean;
}
// (do not) show debug messages in the console
export let debug = true;
let butStillLogErrors = true;

export const whatWeAreDownloading = (flag: number = 0) => {
    switch (flag) {
        case 1:
            return 'Athlete';
        case 6:
            return 'Activity';
        case 10:
            return 'Activities';
        case 23:
            return 'All activities';
        default:
            return 'Unknown';
    }
};

// error messages when there are errors with API calls to Laravel or Strava
export const error_message = (flag: number = 0, error: boolean = false) => {
    if (error) {
        switch (flag) {
            case 1:
                return 'There was an error retrieving the athlete data from Strava.';
            case 2:
                return 'There was an error saving the access token to Laravel.';
            case 3:
                return 'There was an error retrieving the authorization data for Strava.';
            case 4:
            case 5:
                return 'There was an error authorizing with Strava.';
            case 6:
                return 'There was an error retrieving an activity from Strava.';
            case 8:
                return 'There was an error uploading the Stats data from Laravel.';
            case 9:
                return 'There was an error saving the activities to Laravel.';
            case 10:
                return 'There was an error retrieving the activities data from Strava.';
            case 11:
                return 'There was an error retrieving the athlete data from Strava.';
            case 12:
                return 'There was an error retrieving the activities data from Laravel.';
            case 13:
                return 'There was an error saving the filtered activities data to Laravel.';
            case 14:
                return 'There was an error retrieving the filters from Laravel.';
            case 16:
                return 'There was an error saving the filters to Laravel.';
            case 18:
                return 'There was an error deleting a filter from Laravel.';
            case 22:
                return 'There was an error uploading the Chart data from Laravel.';
            default:
                return 'There was an error downloading the data.';
        }
    } else {
        switch (flag) {
            case 1:
                return 'Athlete data from Strava retrieved successfully.';
            case 2:
                return 'The access token has been saved to Laravel successfully.';
            case 3:
                return 'Authorization data for Strava retrieved successfully.';
            case 4:
            case 5:
                return 'Successfully authorized with Strava.';
            case 6:
                return 'Successfully retrieved an activity from Strava.';
            case 8:
                return 'The Stats data has been uploaded from Laravel successfully.';
            case 9:
                return 'The activities have been saved to Laravel successfully.';
            case 10:
                return 'Activities data from Strava retrieved successfully.';
            case 11:
                return 'Athlete data from Strava retrieved successfully.';
            case 12:
                return 'Activities data from Laravel retrieved successfully.';
            case 13:
                return 'The filtered activities data has been saved to Laravel.';
            case 14:
                return 'The filters have been uploaded from Laravel.';
            case 16:
                return 'The filters have been saved to Laravel.';
            case 18:
                return 'A filter has been deleted from Laravel.';
            case 22:
                return 'The Chart data has been uploaded from Laravel successfully.';
            default:
                return 'An unknown flag was returned.(' + flag + ')';
        }
    }
};

export function log2Console({ flag, error = false }: any) {
    if (debug || (butStillLogErrors && error)) {
        console.log(error_message(flag, error));
    }
}

export function sendErrorNotification(flag: number, error_code: string = '', error_response: any = '') {
    log2Console({ flag: flag, error: true });
    const route_to_notify_error = route('error.notification');
    try {
        axios.post(route('error.notification'), {
            flag: flag,
            error_code: error_code,
            error_response: JSON.stringify(error_response),
            error_message: error_message(flag, true),
        });
        if (debug) console.log('Error notification to the developer sent successfully');
    } catch (error) {
        if (debug) {
            console.log('There was an error sending the error notification to the developer. Error = ', error);
            console.log('The route used to send the error notification is = ', route_to_notify_error);
            console.log('error = ', error);
        }
    }
}

// Get/Post data using axios, and report errors if they occur
export async function async_axios({ url, flag, method = 'get', post_data = '' }: any) {
    let error = false;
    let data;
    try {
        if (method == 'get') {
            data = await axios.get(url);
        } else {
            data = await axios.post(url, post_data);
        }
        log2Console({ flag: flag });
        return { error: error, data: data.data };
    } catch (axios_error: any) {
        error = true;
        sendErrorNotification(flag, axios_error.status, axios_error.response.data);
        return { error: error, data: axios_error };
    }
}
