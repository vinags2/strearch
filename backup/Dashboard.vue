<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AppFooter from '@/components/AppFooter.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { Table, TableCell, TableRow } from '@/components/ui/table'
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';
import { getStats } from '@/functions/StrearchAPI.js'
import { strearch_data, debug } from '@/functions/Flags.js'

const props = defineProps({
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    appVersion: {
        type: String,
        required: true,
    },
    quote: {
        type: Object,
        default: {message: 'no quote found', author: 'no author'}
    }
});

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 8) {
        fill_fields(newValue.data)
    }
}) 

const athlete = ref({first_name: 'Joe', last_name: 'Bloggs', updated_at: '1/1/1900'})
const numberOfActivities = ref(0)
const numberOfVirtualActivities = ref(0)
const date_of_last_activities_strava_update = ref('1/1/1900')
const numberOfFilters = ref(0)

onMounted(() => {
    get_stats()
})

// function convert2LocaleString(dateToConvert: any) {
//     const dj = new Date(dateToConvert)
//     return dj.toLocaleString()
// }

function get_stats() {
    getStats()
}

function fill_fields(data: any) {
    athlete.value.first_name = data.athlete.first_name
    athlete.value.last_name = data.athlete.last_name
    athlete.value.updated_at = data.date_of_last_athlete_strava_update
    numberOfActivities.value = data.numberOfActivities
    numberOfVirtualActivities.value = data.numberOfVirtualActivities
    date_of_last_activities_strava_update.value = data.date_of_last_activities_strava_update
    numberOfFilters.value = data.numberOfFilters
}

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <p class="md:px-32 py-8 w-full text-xl font-semibold tracking-tight">Statistics</p>

                <Table>
                    <TableRow>
                        <TableCell class="">Current athlete</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ athlete.first_name }} {{ athlete.last_name}}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Date of last athlete download</TableCell>
                        <TableCell>
                            <DownloadFromStravaWithDialog @newdownload="get_stats" download-what="athlete"></DownloadFromStravaWithDialog>
                        </TableCell>
                        <TableCell>{{ athlete.updated_at }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Number of activities downloaded</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ numberOfActivities }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Number of virtual activities downloaded</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ numberOfVirtualActivities }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Number of IRL activities downloaded</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ numberOfActivities - numberOfVirtualActivities }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Date of last activities download</TableCell>
                        <TableCell>
                            <DownloadFromStravaWithDialog @newdownload="get_stats" download-what="activities"></DownloadFromStravaWithDialog>
                        </TableCell>
                        <TableCell>{{ date_of_last_activities_strava_update }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Number of filters created</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ numberOfFilters }}</TableCell>
                    </TableRow>
                </Table>
            </div>
            <AppFooter :php-version="props.phpVersion" :laravel-version="props.laravelVersion" :app-version="props.appVersion" :quote="props.quote" ></AppFooter>
        </div>
    </AppLayout>
</template>
