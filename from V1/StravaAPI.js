import { ref } from 'vue';
import axios from 'axios';

// format of strava_data: {'code': an integer, 'data', the activities as an array of objects}
// 'code' can take the following values:
// 1 = successful return of data
// 2 = prepare to get data from Strava
// 3 = missing authorization data to access Strava
export const strava_data = ref(null)
export const saved_access_token = ref(null)

export function getDataFromStrava(route_to_save_token, client_id, client_secret, refresh_token, url, parameters = null) {

    if (!client_id || !client_secret || !refresh_token) {
        strava_data.value = {'code': 3, 'data': 'Missing authorization codes to access Strava'}
        console.log('Missing authorization codes to access Strava')
        return
    }
    var API_number_of_calls = 0

    const getStravaData = async () => {
            API_number_of_calls++
            if (API_number_of_calls > 2) {
                strava_data.value = {'code': 4, 'data': 'Too many attempts trying to authorise with Strava'}
                console.log('Too many attempts trying to authorise with Strava')
            } else {
            try {
                const request = await axios.get(createUrl(url, parameters, saved_access_token.value));
                strava_data.value = {'code': 1, 'data': request.data}
            } catch (error) {
                console.log('Access denied. Incorrect access token.')
                authoriseWithStrava()
            }
    }
      };

    const authoriseWithStrava = async () => {
        try {
            const request = await axios.post('https://www.strava.com/api/v3/oauth/token?client_id='+client_id+'&client_secret='+client_secret+'&grant_type=refresh_token&refresh_token='+refresh_token);
            saved_access_token.value = request.data.access_token
            saveAccessToken()
            getStravaData()
        } catch (error) {
            console.log('Error re-authorising with Strava.', error)
            strava_data.value = {'code': 4, 'data': 'Error re-authorising with Strava'}
        }
      };

    const saveAccessToken = async () => {
        try {
            await axios.post(route_to_save_token + saved_access_token.value);
        } catch (error) {
            console.log('There was an error saving the access token', Error)
        }
      };

    authoriseWithStrava()

}

function createUrl(url, parameters) {
   return url + '?access_token=' + saved_access_token.value + (parameters ? '&' + parameters : '')
}