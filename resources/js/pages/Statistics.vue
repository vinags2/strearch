<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import DownloadFromStrava from '@/components/DownloadFromStrava.vue';
import { getStats } from '@/functions/StrearchAPI.js';
import Layout from '@/layouts/my/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { onMounted, ref } from 'vue';

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
    },
});

const tableData = ref<any>([]);
const loading = ref(true);

onMounted(() => {
    get_stats();
});

async function get_stats() {
    loading.value = true;
    const stats = await getStats();
    if (stats.error) {
        loading.value = false;
        return;
    }
    fill_tableData(stats.data);
}

function fill_tableData(data: any) {
    tableData.value = [];
    tableData.value.push({ col1: 'Current athlete', col2: 'bold', col3: data.athlete.first_name + ' ' + data.athlete.last_name });
    tableData.value.push({ col1: '', col2: '', col3: '' });
    tableData.value.push({ col1: 'Date of last athlete download', col2: '1', col3: data.date_of_last_athlete_strava_update });
    tableData.value.push({ col1: 'Date of last activities download', col2: '10', col3: data.date_of_last_activities_strava_update });
    tableData.value.push({ col1: 'Number of activities', col2: 'bold, columns', col3: 'All', col4: 'Virtual', col5: 'IRL' });
    tableData.value.push({
        col1: 'Total',
        col2: 'columns',
        col3: data.allActivities['count'],
        col4: data.allVirtualActivities['count'],
        col5: data.allActivities['count'] - data.allVirtualActivities['count'],
    });
    tableData.value.push({
        col1: 'This year',
        col2: 'columns',
        col3: data.thisYearActivities['count'],
        col4: data.thisYearVirtualActivities['count'],
        col5: data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'],
    });
    tableData.value.push({
        col1: 'Last year',
        col2: 'columns',
        col3: data.lastYearActivities['count'],
        col4: data.lastYearVirtualActivities['count'],
        col5: data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'],
    });

    tableData.value.push({ col1: 'Distance (km)', col2: 'bold, columns', col3: 'All', col4: 'Virtual', col5: 'IRL' });
    tableData.value.push({
        col1: 'Total',
        col2: 'columns',
        col3: data.allActivities['totalDistance'],
        col4: data.allVirtualActivities['totalDistance'],
        col5: data.allActivities['totalDistance'] - data.allVirtualActivities['totalDistance'],
    });
    tableData.value.push({
        col1: 'This year',
        col2: 'columns',
        col3: data.thisYearActivities['totalDistance'],
        col4: data.thisYearVirtualActivities['totalDistance'],
        col5: data.thisYearActivities['totalDistance'] - data.thisYearVirtualActivities['totalDistance'],
    });
    tableData.value.push({
        col1: 'Last year',
        col2: 'columns',
        col3: data.lastYearActivities['totalDistance'],
        col4: data.lastYearVirtualActivities['totalDistance'],
        col5: data.lastYearActivities['totalDistance'] - data.lastYearVirtualActivities['totalDistance'],
    });

    tableData.value.push({ col1: 'Average distance per activity (km)', col2: 'bold, columns', col3: 'All', col4: 'Virtual', col5: 'IRL' });
    tableData.value.push({
        col1: 'Total',
        col2: 'columns',
        col3: data.allActivities['averageDistance'],
        col4: data.allVirtualActivities['averageDistance'],
        col5: (
            (data.allActivities['totalDistance'] - data.allVirtualActivities['totalDistance']) /
            (data.allActivities['count'] - data.allVirtualActivities['count'])
        ).toFixed(),
    });
    tableData.value.push({
        col1: 'This year',
        col2: 'columns',
        col3: data.thisYearActivities['averageDistance'],
        col4: data.thisYearVirtualActivities['averageDistance'],
        col5: (
            (data.thisYearActivities['totalDistance'] - data.thisYearVirtualActivities['totalDistance']) /
            (data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'])
        ).toFixed(),
    });
    tableData.value.push({
        col1: 'Last year',
        col2: 'columns',
        col3: data.lastYearActivities['averageDistance'],
        col4: data.lastYearVirtualActivities['averageDistance'],
        col5: (
            (data.lastYearActivities['totalDistance'] - data.lastYearVirtualActivities['totalDistance']) /
            (data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'])
        ).toFixed(),
    });

    tableData.value.push({ col1: 'Climbing (m)', col2: 'bold, columns', col3: 'All', col4: 'Virtual', col5: 'IRL' });
    tableData.value.push({
        col1: 'Total',
        col2: 'columns',
        col3: data.allActivities['totalAscent'],
        col4: data.allVirtualActivities['totalAscent'],
        col5: data.allActivities['totalAscent'] - data.allVirtualActivities['totalAscent'],
    });
    tableData.value.push({
        col1: 'This year',
        col2: 'columns',
        col3: data.thisYearActivities['totalAscent'],
        col4: data.thisYearVirtualActivities['totalAscent'],
        col5: data.thisYearActivities['totalAscent'] - data.thisYearVirtualActivities['totalAscent'],
    });
    tableData.value.push({
        col1: 'Last year',
        col2: 'columns',
        col3: data.lastYearActivities['totalAscent'],
        col4: data.lastYearVirtualActivities['totalAscent'],
        col5: data.lastYearActivities['totalAscent'] - data.lastYearVirtualActivities['totalAscent'],
    });

    tableData.value.push({ col1: 'Average climbing per activity (m)', col2: 'bold, columns', col3: 'All', col4: 'Virtual', col5: 'IRL' });
    tableData.value.push({
        col1: 'Total',
        col2: 'columns',
        col3: data.allActivities['averageAscent'],
        col4: data.allVirtualActivities['averageAscent'],
        col5: (
            (data.allActivities['totalAscent'] - data.allVirtualActivities['totalAscent']) /
            (data.allActivities['count'] - data.allVirtualActivities['count'])
        ).toFixed(),
    });
    tableData.value.push({
        col1: 'This year',
        col2: 'columns',
        col3: data.thisYearActivities['averageAscent'],
        col4: data.thisYearVirtualActivities['averageAscent'],
        col5: (
            (data.thisYearActivities['totalAscent'] - data.thisYearVirtualActivities['totalAscent']) /
            (data.thisYearActivities['count'] - data.thisYearVirtualActivities['count'])
        ).toFixed(),
    });
    tableData.value.push({
        col1: 'Last year',
        col2: 'columns',
        col3: data.lastYearActivities['averageAscent'],
        col4: data.lastYearVirtualActivities['averageAscent'],
        col5: (
            (data.lastYearActivities['totalAscent'] - data.lastYearVirtualActivities['totalAscent']) /
            (data.lastYearActivities['count'] - data.lastYearVirtualActivities['count'])
        ).toFixed(),
    });

    tableData.value.push({ col1: 'Filters', col2: 'bold', col3: '' });
    tableData.value.push({ col1: 'Number of filters created', col2: '', col3: data.numberOfFilters });
    loading.value = false;
}
</script>

<template>
    <Head title="Statistics" />

    <Layout>
        <div class="mb-16 mt-8 text-center text-gray-500" v-if="tableData[2]?.col3 == 'No athlete data'">
            No data has been found. Please download your athlete data from Strava.
        </div>
        <div v-else>
            <p class="w-full py-8 text-xl font-semibold tracking-tight md:px-32">Statistics</p>

            <DataTable
                :value="tableData"
                :loading="loading"
                size="small"
                stripedRows
                style="width: 50%"
                scrollable
                scrollHeight="550px"
                :virtualScrollerOptions="{ itemSize: 44 }"
                :pt="{
                    thead: { style: 'display: none' },
                }"
            >
                <Column field="col1" header="Code">
                    <template #body="{ data }">
                        <div v-if="data.col2 && data.col2.includes('bold')" class="flex items-center gap-2 font-bold">
                            {{ data.col1 }}
                        </div>
                        <div v-else>
                            {{ data.col1 }}
                        </div>
                    </template>
                </Column>

                <Column field="col2" header="Download">
                    <template #body="{ data }">
                        <div v-if="data.col2 && (data.col2 == 1 || data.col2 == 10)" class="flex items-center gap-2">
                            <DownloadFromStrava @newdownload="get_stats" :flag="Number(data.col2)"></DownloadFromStrava>
                        </div>
                    </template>
                </Column>

                <Column field="col3" header="Name">
                    <template #body="{ data }">
                        <div v-if="data.col2 && data.col2.includes('columns')" class="columns-3">
                            <div :class="{ 'font-bold': data.col2.includes('bold') }">{{ data.col3 }}</div>
                            <div :class="{ 'font-bold': data.col2.includes('bold') }">{{ data.col4 }}</div>
                            <div :class="{ 'font-bold': data.col2.includes('bold') }">{{ data.col5 }}</div>
                        </div>
                        <div v-else>{{ data.col3 }}</div>
                    </template>
                </Column>
            </DataTable>
        </div>
        <AppFooter
            :php-version="props.phpVersion"
            :laravel-version="props.laravelVersion"
            :app-version="props.appVersion"
            :quote="props.quote"
        ></AppFooter>
    </Layout>
</template>
