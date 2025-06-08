import axios from 'axios';
import { saveAccessToken, save_athlete, save_activities } from '@/functions/StrearchAPI.js'
import { strava_data, debug } from '@/functions/Flags.js'

export function getStravaData(downloadWhat: string, parameters = '') {

    var client_id = ''
    var client_secret = ''
    var refresh_token = ''
    var athlete_url = ''
    var activities_url = ''
    var saved_access_token = ''
    var date_of_last_activity_update = 0

    const getStravaMetaData = async () => {
        try {
            const request = await axios.get(route('dfsd'));
            if ( debug ) console.log('Successfully uploaded the data from the DB needed for downloading from Strava, such as the client id. '
                +'data = ', request.data)
            client_id = request.data.client_id
            client_secret = request.data.client_secret
            refresh_token = request.data.refresh_token
            athlete_url = request.data.athlete_url
            activities_url = request.data.activities_url
            date_of_last_activity_update = request.data.date_of_last_activity_update
        } catch (error) {
            if ( debug ) console.log('There was an error getting the data from the DB needed for downloading from Strava, such as the client id ' +
                'Error = ', error)
            strava_data.value = {'code': 3, 'data': ['Missing authorization codes to access Strava']}
        }
    }

    var API_number_of_calls = 0

    const getAthleteData = async () => {
            API_number_of_calls++
            if (API_number_of_calls > 2) {
                strava_data.value = {'code': 4, 'data': ['Too many attempts trying to authorise with Strava']}
                if ( debug ) console.log('Too many attempts trying to authorise with Strava')
                return
            } else {

                let full_url = createUrl(athlete_url, parameters, saved_access_token)
                try {
                    const request = await axios.get(full_url);
                    if ( debug ) {
                        console.log('Data retrieved from Strava successfully')
                        console.log('url = ', full_url)
                        console.log('Data = ', request.data)
                    }
                    strava_data.value = {'code': 1, 'data': request.data}
                    save_athlete(request.data)
                } catch (error) {
                    if ( debug ) console.log('Data was NOT retrieved from Strava successfully.')
                    if ( debug ) console.log('Error = ', error)
                    authoriseWithStrava()
                }
            }
      };

    const getActivitiesData = async (all: boolean) => {
            API_number_of_calls++
            if (API_number_of_calls > 2) {
                strava_data.value = {'code': 4, 'data': ['Too many attempts trying to authorise with Strava']}
                if ( debug ) console.log('Too many attempts trying to authorise with Strava')
            } else {
                let pageNo = 1
                let moreData = true
                do {
                    // parameters = 'after='+'2025-03-01 16:57:45'+'&per_page=200&page='+pageNo
                    // parameters = 'after='+last_update_from_strava.value+'&per_page=200&page='
                    parameters = all ? 'after=1&per_page=200&page='+pageNo++ : 'after='+date_of_last_activity_update+'&per_page=200&page='+pageNo++
                    // parameters = 'after='+'1740808620'+'&per_page=200&page='+pageNo++
                    let full_url = createUrl(activities_url, parameters, saved_access_token)
                    try {
                        const request = await axios.get(full_url);
                        if ( debug ) {
                            console.log('Data retrieved from Strava successfully - page ', pageNo - 1)
                            console.log('url = ', full_url)
                            console.log('data = ', request.data)
                            console.log('pageNo = ', pageNo - 1, 'moreData = ', moreData)
                        }
                        moreData = request?.data.length > 0
                        // strava_data.value = {'code': 1, 'data': request.data}
                        save_activities(request.data)
                    } catch (error) {
                        if ( debug ) console.log('Data was NOT retrieved from Strava successfully.')
                        if ( debug ) console.log('Error = ', error)
                        authoriseWithStrava()
                    }
                } while (moreData && (pageNo < 30))
                strava_data.value = {'code': 10, 'data': ['new activities have been downloaded from Strava and saved to the DB']}
            }
      };


    const authoriseWithStrava = async () => {
        await getStravaMetaData()

        let full_url = 'https://www.strava.com/api/v3/oauth/token?client_id='+client_id+'&client_secret='+client_secret+'&grant_type=refresh_token&refresh_token='+refresh_token
        try {
            const request = await axios.post(full_url);
            saved_access_token = request.data.access_token
            if ( debug ) {
                console.log('Authorised with Strava successfully')
            }
            saveAccessToken(saved_access_token)
            if (downloadWhat == 'athlete') {
                getAthleteData()
            } else {
                getActivitiesData(true)
            }
        } catch (error) {
            if ( debug ) {
                console.log('Error re-authorising with Strava.', error)
                console.log('url = ', full_url)
            }
            strava_data.value = {'code': 4, 'data': ['Error re-authorising with Strava']}
        }
      };

    authoriseWithStrava()

}

function createUrl(url:string, parameters:string, saved_access_token:string) {
   return url + '?access_token=' + saved_access_token + (parameters ? '&' + parameters : '')
}