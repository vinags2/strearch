import axios from 'axios';
import { strearch_data, strava_data, debug } from '@/functions/Flags.js'

export async function saveAccessToken (saved_access_token:string) {
    const route_to_save_token = route('token.save', saved_access_token)
    try {
        await axios.post(route_to_save_token);
        if ( debug ) console.log('Strava Authorisation Token saved successfully')
    } catch (error) {
        if ( debug ) { 
            console.log('There was an error saving the access token to the DB. Error = ', error)
            console.log('The route used to save the access token = ', route_to_save_token)
            console.log('error = ', error)
        }
    }
};

export async function getStats () {
    try {
        const request = await axios.get(route('stats'));
        if ( debug ) console.log('Successfully uploaded the statistics from the DB. Data = ', request.data)
        strearch_data.value = {'code': 8, 'data': request.data }
    } catch (error) {
        if ( debug ) {
            console.log('There was an error getting the stats from the DB. Error = ', error)
            console.log('The url used was ', route('stats'))
            console.log('error = ', error)
        }
        strearch_data.value = {'code': 9, 'data': 'Unable to download stats from the DB'}
    }
}

export async function getChartData () {
    try {
        const request = await axios.get(route('chartData.get'));
        if ( debug ) console.log('Successfully uploaded the Chart data from Laravel. Data = ', request.data)
        strearch_data.value = {'code': 22, 'data': request.data }
    } catch (error) {
        if ( debug ) {
            console.log('There was an error getting the Chart data from Laravel. Error = ', error)
            console.log('The url used was ', route('chartData.get'))
            console.log('error = ', error)
        }
        strearch_data.value = {'code': 23, 'data': 'Unable to download Chart data from Laravel'}
    }
}

export async function getAthlete () {
    try {
        const request = await axios.get(route('athlete.get'));
        if ( debug ) console.log('Successfully uploaded the athlete data from the DB. Data = ', request.data)
        strearch_data.value = {'code': 10, 'data': request.data }
    } catch (error) {
        if ( debug ) {
            console.log('There was an error getting the athlete data from the DB. Error = ', error)
            console.log('The url used was ', route('athlete.get'))
            console.log('error = ', error)
        }
        strearch_data.value = {'code': 11, 'data': 'Unable to download athlete data from the DB'}
    }
}

export function save_athlete(athlete:any) {
    axios
        .post(route('athlete.save',athlete.id), athlete)
        .then ((response) => {
            if ( debug ) {
                console.log('Athlete data saved to the database')
                console.log('url = ', route('athlete.save', athlete.id), athlete)
                console.log('response = ', response)
            }
            strava_data.value = {'code': 6, 'data': ['Athlete data has been saved to the database']}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('Athlete data was NOT saved to the database')
                console.log('url = ', route('athlete.save', athlete.id), athlete)
                console.log('error = ', error)
            }
            strava_data.value = {'code': 7, 'data': ['Athlete data has NOT been saved to the database']}
        })
}

export function save_activities(activities:any, filtered_activities: boolean = false) {
    let save_route = filtered_activities ? route('activities.filtered.save') : route('activities.save')
    console.log('route = ', save_route)
    axios
        .post(save_route, activities)
        .then ((response) => {
            if ( debug ) {
                console.log('Activities data saved to the database')
                console.log('url = ', save_route, activities)
                console.log('response = ', response)
            }
            strava_data.value = {'code': 8, 'data': ['Activities data has been saved to the database']}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('Activities data was NOT saved to the database')
                console.log('url = ', route('activities.save'), activities)
                console.log('error = ', error)
            }
            strava_data.value = {'code': 9, 'data': ['Activities data has NOT been saved to the database']}
        })
}