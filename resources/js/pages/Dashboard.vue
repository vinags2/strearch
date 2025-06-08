<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AppFooter from '@/components/AppFooter.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { getStats } from '@/functions/StrearchAPI.js'
import { strearch_data } from '@/functions/Flags.js'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';


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
        fill_tableData(newValue.data)
    }
}) 

const tableData = ref<any>([])

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

function fill_tableData(data:any) {
    tableData.value = []
    tableData.value.push({col1: 'Current athlete', col2:'bold', col3: data.athlete.first_name + ' ' + data.athlete.last_name})
    tableData.value.push({col1: '', col2:'', col3: ''})
    tableData.value.push({col1: 'Date of last athlete download', col2:'athlete', col3: data.date_of_last_athlete_strava_update})
    tableData.value.push({col1: 'Date of last activities download', col2:'activities', col3: data.date_of_last_activities_strava_update})
    tableData.value.push({col1: 'Number of activities', col2:'bold', col3: ''})
    tableData.value.push({col1: 'All, Virtual, IRL', col2:'columns',
        col3: data.allActivities['count'],
        col4: data.allVirtualActivities['count'],
        col5: (data.allActivities['count'] - data.allVirtualActivities['count'])})
    tableData.value.push({col1: 'This year\'s All, Virtual, IRL', col2:'columns',
        col3: data.thisYearActivities['count'], 
        col4: data.thisYearVirtualActivities['count'],
        col5: (data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'])})
    tableData.value.push({col1: 'Last year\'s All, Virtual, IRL', col2:'columns',
        col3: data.lastYearActivities['count'],
        col4: data.lastYearVirtualActivities['count'],
        col5: (data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'])})

    tableData.value.push({col1: 'Distance (km)', col2:'bold', col3: ''})
    tableData.value.push({col1: 'All, Virtual, IRL', col2:'columns',
        col3: data.allActivities['totalDistance'],
        col4: data.allVirtualActivities['totalDistance'],
        col5: (data.allActivities['totalDistance'] - data.allVirtualActivities['totalDistance'])})
    tableData.value.push({col1: 'This year\'s All, Virtual, IRL', col2:'columns',
        col3: data.thisYearActivities['totalDistance'],
        col4: data.thisYearVirtualActivities['totalDistance'],
        col5: (data.thisYearActivities['totalDistance'] - data.thisYearVirtualActivities['totalDistance'])})
    tableData.value.push({col1: 'Last year\'s All, Virtual, IRL', col2:'columns',
        col3: data.lastYearActivities['totalDistance'],
        col4: data.lastYearVirtualActivities['totalDistance'],
        col5: (data.lastYearActivities['totalDistance'] - data.lastYearVirtualActivities['totalDistance'])})

    tableData.value.push({col1: 'Average distance per activity (km)', col2:'bold', col3: ''})
    tableData.value.push({col1: 'All, Virtual, IRL', col2:'columns',
        col3: data.allActivities['averageDistance'],
        col4: data.allVirtualActivities['averageDistance'],
        col5: ((data.allActivities['totalDistance'] - data.allVirtualActivities['totalDistance']) / 
                (data.allActivities['count'] - data.allVirtualActivities['count'])).toFixed()})
    tableData.value.push({col1: 'This year\'s All, Virtual, IRL', col2:'columns',
        col3: data.thisYearActivities['averageDistance'],
        col4: data.thisYearVirtualActivities['averageDistance'],
        col5: ((data.thisYearActivities['totalDistance'] - data.thisYearVirtualActivities['totalDistance']) / 
                (data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'])).toFixed()})
    tableData.value.push({col1: 'Last year\'s All, Virtual, IRL', col2:'columns',
        col3: data.lastYearActivities['averageDistance'],
        col4: data.lastYearVirtualActivities['averageDistance'],
        col5: ((data.lastYearActivities['totalDistance'] - data.lastYearVirtualActivities['totalDistance']) / 
                (data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'])).toFixed()})

    tableData.value.push({col1: 'Climbing (m)', col2:'bold', col3: ''})
    tableData.value.push({col1: 'All, Virtual, IRL', col2:'columns',
        col3: data.allActivities['totalAscent'],
        col4: data.allVirtualActivities['totalAscent'],
        col5: (data.allActivities['totalAscent'] - data.allVirtualActivities['totalAscent'])})
    tableData.value.push({col1: 'This year\'s All, Virtual, IRL', col2:'columns',
        col3: data.thisYearActivities['totalAscent'],
        col4: data.thisYearVirtualActivities['totalAscent'],
        col5: (data.thisYearActivities['totalAscent'] - data.thisYearVirtualActivities['totalAscent'])})
    tableData.value.push({col1: 'Last year\'s All, Virtual, IRL', col2:'columns',
        col3: data.lastYearActivities['totalAscent'],
        col4: data.lastYearVirtualActivities['totalAscent'],
        col5: (data.lastYearActivities['totalAscent'] - data.lastYearVirtualActivities['totalAscent'])})

    tableData.value.push({col1: 'Average climbing per activity (m)', col2:'bold', col3: ''})
    tableData.value.push({col1: 'All, Virtual, IRL', col2:'columns',
        col3: data.allActivities['averageAscent'],
        col4: data.allVirtualActivities['averageAscent'],
        col5: ((data.allActivities['totalAscent'] - data.allVirtualActivities['totalAscent']) / 
                (data.allActivities['count'] - data.allVirtualActivities['count'])).toFixed()})
    tableData.value.push({col1: 'This year\'s All, Virtual, IRL', col2:'columns',
        col3: data.thisYearActivities['averageAscent'],
        col4: data.thisYearVirtualActivities['averageAscent'],
        col5: ((data.thisYearActivities['totalAscent'] - data.thisYearVirtualActivities['totalAscent']) / 
                (data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'])).toFixed()})
    tableData.value.push({col1: 'Last year\'s All, Virtual, IRL', col2:'columns',
        col3: data.lastYearActivities['averageAscent'],
        col4: data.lastYearVirtualActivities['averageAscent'],
        col5: ((data.lastYearActivities['totalAscent'] - data.lastYearVirtualActivities['totalAscent']) / 
                (data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'])).toFixed()})

    tableData.value.push({col1: 'Filters', col2:'bold', col3: ''})
    tableData.value.push({col1: 'Number of filters created', col2:'', col3: data.numberOfFilters})
}

</script>

<template>
    <Head title="Statistics" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <p class="md:px-32 py-4 w-full text-xl font-semibold tracking-tight">Statistics</p>

                <DataTable :value="tableData" size="small" stripedRows style="width: 50%"
                    :pt="{
                        thead: { style: 'display: none' },
                    }"
                >
                    <Column field="col1" header="Code">
                        <template #body="{ data }">
                            <div v-if="data.col2 && data.col2 == 'bold'" class="flex font-bold items-center gap-2">
                                {{ data.col1 }}
                            </div>
                            <div v-else>
                                {{ data.col1 }}
                            </div>
                        </template>
                    </Column>
                    <Column field="col2" header="Download" >
                        <template #body="{ data }">
                            <div v-if="data.col2 && (data.col2 == 'athlete' || data.col2 == 'activities')" class="flex items-center gap-2">
                                <DownloadFromStravaWithDialog @newdownload="get_stats" :download-what="data.col2"></DownloadFromStravaWithDialog>
                            </div>
                        </template>
                    </Column>
                    <Column field="col3" header="Name" >
                        <template #body="{ data }">
                            <div v-if="data.col2 && data.col2 == 'columns'" class="columns-3">
                                <div>{{ data.col3 }}</div>
                                <div>{{ data.col4 }}</div>
                                <div>{{ data.col5 }}</div>
                            </div>
                            <div v-else>{{ data.col3 }}</div>
                        </template>
                    </Column>
                </DataTable>

            </div>
            <AppFooter :php-version="props.phpVersion" :laravel-version="props.laravelVersion" :app-version="props.appVersion" :quote="props.quote" ></AppFooter>
        </div>
    </AppLayout>
</template>
