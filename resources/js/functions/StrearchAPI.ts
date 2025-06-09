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

export function set_active_filter(id:number) {
    axios
        .get(route('filter.setactive',id))
        .then ((response) => {
            if ( debug ) {
                console.log('Filter set to active successrully')
                console.log('response = ', response)
                console.log('response = ', response)
            }
            strearch_data.value = {'code': 20, 'data': 'Filter successully set active'}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('Filter NOT deleted successrully')
                console.log('url = ', route('filter.setactive', id))
                console.log('error = ', error)
            }
            strearch_data.value = {'code': 21, 'data': 'Filter has NOT been set active'}
        })
}

export function delete_filter(id:number) {
    axios
        .get(route('filter.delete',id))
        .then ((response) => {
            if ( debug ) {
                console.log('Filter deleted successrully')
                console.log('response = ', response)
            }
            strearch_data.value = {'code': 18, 'data': 'Filter successully deleted from the database'}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('Filter NOT deleted successrully')
                console.log('url = ', route('filter.delete', id))
                console.log('error = ', error)
            }
            strearch_data.value = {'code': 19, 'data': 'Filter has NOT been deleted from the database'}
        })
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

export async function save_filters(filters:any) {
    // JSON.stringify(allFilters.value[0])
    var arrayOfAllFilters = <any>[]
    filters.forEach((element:any) => {
        let combined = {
            filterName: element.name,
            filters: JSON.stringify(element.filter),
            active: element.active,
        }
        arrayOfAllFilters.push(combined)
    });
    await axios
        .post(route('filters.save'), arrayOfAllFilters)
        .then ((response) => {
            if ( debug ) {
                console.log('filters data saved to the database')
                console.log('url = ', route('filters.save'), arrayOfAllFilters)
                console.log('response = ', response)
            }
            strava_data.value = {'code': 16, 'data': ['filters data has been saved to the database']}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('filters data was NOT saved to the database')
                console.log('url = ', route('filters.save'), arrayOfAllFilters)
                console.log('error = ', error)
            }
            strava_data.value = {'code': 17, 'data': ['filters data has NOT been saved to the database']}
        })
}

export async function save_filter(filters:any) {
    // JSON.stringify(allFilters.value[0])
    let combined = {
        filterName: filters.name,
        filters: JSON.stringify(filters.filter),
        active: filters.active,
    }
    await axios
        .post(route('filter.save'), combined)
        .then ((response) => {
            if ( debug ) {
                console.log('filter data saved to the database')
                console.log('url = ', route('filter.save'), combined)
                console.log('response = ', response)
            }
            strava_data.value = {'code': 14, 'data': ['filter data has been saved to the database']}
        })
        .catch((error) => {
            if ( debug ) {
                console.log('filters data was NOT saved to the database')
                console.log('url = ', route('filter.save'), combined)
                console.log('error = ', error)
            }
            strava_data.value = {'code': 15, 'data': ['filter data has NOT been saved to the database']}
        })
}

export function save_activities(activities:any) {
    axios
        .post(route('activities.save'), activities)
        .then ((response) => {
            if ( debug ) {
                console.log('Activities data saved to the database')
                console.log('url = ', route('activities.save'), activities)
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

export async function getFilters () {
    try {
        const request = await axios.get(route('filters.get'));
        if ( debug ) console.log('Successfully uploaded the filters data from the DB. Data = ', request.data)
        strearch_data.value = {'code': 14, 'data': request.data }
    } catch (error) {
        if ( debug ) {
            console.log('There was an error getting the filters data from the DB. Error = ', Error)
            console.log('The url used was ', route('filters.get'))
            console.log('error = ', error)
        }
        strearch_data.value = {'code': 15, 'data': 'Unable to download activities data from the DB'}
    }
}

export async function getActivities () {
    try {
        const request = await axios.get(route('activities.get'));
        if ( debug ) console.log('Successfully uploaded the activities data from the DB. Data = ', request.data)
        strearch_data.value = {'code': 12, 'data': request.data }
    } catch (error) {
        if ( debug ) {
            console.log('There was an error getting the activities data from the DB. Error = ', Error)
            console.log('The url used was ', route('activities.get'))
            console.log('error = ', error)
        }
        strearch_data.value = {'code': 13, 'data': 'Unable to download activities data from the DB'}
    }
}