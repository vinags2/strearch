import { ref } from 'vue';
// format of strava_data: {'code': an integer, 'data', the activities as an array of objects}
// 'code' can take the following values:
// 1 = successful return of data
// 2 = prepare to get data from Strava
// 3 = missing authorization data to access Strava
// 4 = Too many attempts trying to authorise, or other error, with Strava
// 6 = Athlete data has been saved to the database
// 7 = Athlete data has NOT been saved to the database
// 8 = Activities data has been saved to the database
// 9 = Activities data has NOT been saved to the database
// 10 = Activities have been downloaded from Strava and saved to the database.
export const strava_data = ref({code:0, data:['no data']})

// format of strearch_data: {'code': an integer, 'data', the activities as an array of objects}
// 'code' can take the following values:
// 8 = Stats downloaded from Strearch
// 9 = Stats was unable to be downloaded from Strearch
// 10 = Athlete data has been uploaded from the DB
// 11 = Athlete data was NOT uploaded from the DB
// 12 = Activities data has been uploaded from the DB
// 13 = Activities data was NOT uploaded from the DB
// 14 = Filter data has been uploaded from the DB
// 15 = Filter data was NOT uploaded from the DB
// 16 = Filters data has been uploaded from the DB
// 17 = Filters data was NOT uploaded from the DB
// 18 = Filter deleted from the DB
// 19 = Filter NOT deleted from the DB
// 20 = Filter set active
// 21 = Filter NOT set active
export const strearch_data = ref({code:0, data:'no data'})

// (do not) show debug messages
export let debug = true