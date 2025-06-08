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

const athlete = ref({first_name: 'Joe', last_name: 'Bloggs', updated_at: '1/1/1900'})
const numberOfActivities = ref(0)
const numberOfVirtualActivities = ref(0)
const date_of_last_activities_strava_update = ref('1/1/1900')
const numberOfFilters = ref(0)
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
    tableData.value.push({col1: 'Current athlete', col2:'', col3: data.athlete.first_name + ' ' + data.athlete.last_name})
    tableData.value.push({col1: 'Date of last athlete download', col2:'athlete', col3: data.date_of_last_athlete_strava_update})
    tableData.value.push({col1: 'Number of activities downloaded', col2:'', col3: data.numberOfActivities})
    tableData.value.push({col1: 'Number of virtual activities downloaded', col2:'', col3: data.numberOfVirtualActivities})
    tableData.value.push({col1: 'Number of IRL activities downloaded', col2:'', col3: data.numberOfActivities - data.numberOfVirtualActivities})
    tableData.value.push({col1: 'Date of last activities download', col2:'activities', col3: data.date_of_last_activities_strava_update})
    tableData.value.push({col1: 'Number of filters created', col2:'', col3: data.numberOfFilters})
}

</script>

<template>
    <Head title="Experimental" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <p class="md:px-32 py-8 w-full text-xl font-semibold tracking-tight">Experimental Page</p>

                <DataTable :value="tableData" size="small" stripedRows style="width: 50%"
                    :pt="{
                        thead: { style: 'display: none' },
                        bodyRow: { class: '!bg-cyan-100' },
                    }"
                >
                    <Column field="col1" header="Code"></Column>
                    <Column field="col2" header="Download" >
                        <template #body="{ data }">
                            <div v-if="data.col2" class="flex items-center gap-2">
                            <DownloadFromStravaWithDialog @newdownload="get_stats" :download-what="data.col2"></DownloadFromStravaWithDialog>
                            </div>
                        </template>
                    </Column>
                    <Column field="col3" header="Name" ></Column>
                </DataTable>

            </div>
            <AppFooter :php-version="props.phpVersion" :laravel-version="props.laravelVersion" :app-version="props.appVersion" :quote="props.quote" ></AppFooter>
        </div>
    </AppLayout>
</template>
