This is what is called when retrieving and saving a number of activities:

- The user starts in ActivitiesTable.vue
- Which contains the component DownloadFromStrava.vue
- The user clicks on the 'Download' icon
- which calls getdata()
- which calls getStravaData(10) which is located in the StravaAPI.ts
- which calls getActivitiesData(false)
- which, in batches of 200, downloads the activities from Strava and calls save_activities(false) to save the data to the DB
  [NB save_activities is located in StrearchAPI.ts]
- upon completion of the download, ActivitiesTable.vue calls newdownload which refreshes the activities from the DB

This is what is called when retrieving and saving justs one activity:

- The user starts in ActivitiesTable.vue
- The user clicks on the edit/delete icon of the activity to re-download
- The user clicks on the 'Download' button
- which calls 'downloadAnAcitivy'
- which calls getStravaData(6, activityID) which is located in the StravaAPI.ts
- which calls getAnActivity(activityID)
- which downloads the activity and calls save_activity(activityID) which saves the activity to the DB
  (NB save_activity is located in StrearchAPI.ts)
- the activities are refreshed from the DB, from the showSuccessBeforeClosing function

To include the segments in the DB:

- create a column 'segments_downloaded' as a boolean with default value false
- when downloading multiple activities:
- after retrieving the activities both from Strava and the DB, call a strearch API to download all segments from all activities where
  'segments_downloaded' is false
- this will happen in the background which will speed up the application from the user's perspective, but will slow down the retrieval of the segments

During testing, just download segments when re-downloading one activity. That is, when calling getStravaData(6, id).
