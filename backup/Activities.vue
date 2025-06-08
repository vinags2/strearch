<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import AppFooter from '@/components/AppFooter.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { getActivities } from '@/functions/StrearchAPI.js'
import { strearch_data } from '@/functions/Flags.js'
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
// import MultiSelect from 'primevue/multiselect';
import Column from 'primevue/column';
import InputText from 'primevue/inputtext';
// import Button from 'primevue/button';
import { Button } from '@/components/ui/button';
import { Binoculars, SearchX } from 'lucide-vue-next';
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
// import { Icon } from 'lucide-vue-next';

// import Button from '@/components/ui/button/Button.vue';

const props = defineProps({
});

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 12) {
        fill_tableData(newValue.data)
    }
}) 

const tableData = ref<any>([])
const lastUpdate = ref<any>()
const filters = ref();

const sportTypes = ref([
    {name: 'Ride'},
    {name: 'VirtualRide'},
    {name: 'Run'}
])

onMounted(() => {
    get_activities()
})

function get_activities() {
    getActivities()
}

function activity_url(id:any) {
    return "https://www.strava.com/activities/" + id
}

function fill_tableData(data:any) {
    lastUpdate.value = data.date_of_last_activities_strava_update
    tableData.value = []
    for(const element of data.activities) {
      element.start_date_local_as_date = new Date(element.start_date_local);
    }
    // tableData.value = data.activities.data // use this when using paginator() in ActivityController.
    tableData.value = data.activities // use this when using get()() in ActivityController.
}

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        start_date_local: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        start_date_local_as_timestamp: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        start_date_local_as_date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        // 'country.name': { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        // representative: { value: null, matchMode: FilterMatchMode.IN },
        // date: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        sport_type: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        distance: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        // status: { operator: FilterOperator.OR, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        // activity: { value: [0, 100], matchMode: FilterMatchMode.BETWEEN },
        // verified: { value: null, matchMode: FilterMatchMode.EQUALS }
    };
};

initFilters();

const clearFilter = () => {
    initFilters();
};

const saveFilter = () => {
    window.localStorage.setItem('filters', JSON.stringify(filters.value));
};

const restoreFilter = () => {
    filters.value = JSON.parse(window.localStorage.getItem('filters')|| '{}');
    console.log('filters=', JSON.parse(window.localStorage.getItem('filters')|| '{}'));
};

</script>

<template>
    <Head title="Activities" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                    <p class="md:px-32 w-full text-xl font-semibold tracking-tight">Activities</p>
                    <div class="flex items-end">
                        <div class="">Activities current to {{ lastUpdate }}</div>
                        <div class="px-8">
                            <DownloadFromStravaWithDialog @newdownload="get_activities" download-what="activities"></DownloadFromStravaWithDialog>
                            <span class="">Update Actitivies from Strava</span>

                        </div>
                        <div class="px-8">
                            <DownloadFromStravaWithDialog @newdownload="get_activities" download-what="all activities"></DownloadFromStravaWithDialog>
                            <span class="">Re-download all activities from Strava</span>

                        </div>
                        <div class="px-8">
                              <a href="" @click.prevent="clearFilter()"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><SearchX color="white"></SearchX>Clear filters</Button></a>
                        </div>

                    </div>
                    <div>
                            <a href="" @click.prevent="saveFilter()"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><SearchX color="white"></SearchX>Save filter</Button></a>
                            <a href="" @click.prevent="restoreFilter()"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><SearchX color="white"></SearchX>Restore filter</Button></a>
                    </div>

                <DataTable class="pt-4" v-model:filters="filters" filterDisplay="menu" :globalFilterFields="['name']" :value="tableData" size="small" stripedRows scrollable scrollHeight="600px" :virtualScrollerOptions="{ itemSize: 44 }"
                    :pt="{
                        bodyRow: { class: '!bg-cyan-100' },
                    }"
                >
                    <Column v-if="false" field="start_date_local_as_timestamp" sortable header="Date"></Column>
                    <Column field="start_date_local" filterField="start_date_local_as_date" dataType="date" sortable sortField="start_date_local_as_timestamp" style="min-width: 150px" header="Date">
                        <template #filter="{ filterModel }">
                            <DatePicker v-model="filterModel.value" dateFormat="mm/dd/yy" placeholder="mm/dd/yyyy" />
                        </template>
                    </Column>
                    <Column field="name" header="Title" style="min-width: 250px" sortable>
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by name" />
                        </template>
                    </Column>
                    <Column field="sport_type" header="Type" style="min-width: 100px" sortable filterField="sport_type" :showFilterMatchModes="false" :filterMenuStyle="{ width: '14rem' }" >
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by sport type" />
                        </template>
                    </Column>
                    
                    <Column field="moving_time_as_string" sortable header="Moving time" style="text-align: right;min-width: 100px"></Column>
                    <Column field="average_speed" header="Avg speed" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="distance" header="Distance" filterField="distance" dataType="numeric" sortable style="text-align: right;min-width: 100px">
                        <template #filter="{ filterModel, filterCallback }">
                            <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by distance" />
                        </template>
                    </Column>
                    <Column field="total_elevation_gain" header="Ascent" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="average_cadence" header="Avg cadence" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="average_watts" header="Avg watts" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="average_heartrate" header="Avg HR" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="max_heartrate" header="Max HR" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="suffer_score" header="Relative effort" sortable style="text-align: right;min-width: 100px"></Column>
                    <Column field="col12" header="" >
                        <template #body="{ data }">
                            <div>
                                <a :href="activity_url(data.id)" target="_blank"> <Binoculars color="blue" stroke-width="1" size=20></Binoculars> </a>
                            </div>
                        </template>
                    </Column>
                </DataTable>

            </div>
            {{ filters }}
        </div>
    </AppLayout>
</template>
