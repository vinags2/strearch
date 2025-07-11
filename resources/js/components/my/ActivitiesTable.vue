<script setup lang="ts">

import { ref } from 'vue';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import MultiSelect from 'primevue/multiselect';
import InputText from 'primevue/inputtext';

import { Binoculars } from 'lucide-vue-next';

import DownloadFromStrava from '@/components/DownloadFromStrava.vue';

import { useStrearchData } from '@/stores/StrearchStore';
import { storeToRefs } from 'pinia'

const props = defineProps({
    autoUpdateActivities: {
        type: Boolean,
        default: true,
    },
})

const autoUpdateComplete = ref(!props.autoUpdateActivities)

const strearchData = useStrearchData()
const { activities, sportTypes, loading: loading, lastUpdate, justTheFilter} = storeToRefs(strearchData)
strearchData.initActivities()

function get_activities() {
    autoUpdateComplete.value = true
    strearchData.initActivities(true)
}

function onFilter(event:any) {
    strearchData.updateFilteredActivities(event.filteredValue)
}

function activity_url(id:any) { return "https://www.strava.com/activities/" + id }

const formatDate = (value:Date) => {
    return value.toLocaleDateString('en-AU', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
};

</script>
<template>
    <DataTable
        :value="activities"
        v-model:filters="justTheFilter"
        @filter="onFilter" 
       :loading="loading"  
        size="small"
        class="pt-4"
        filterDisplay="menu"
        stripedRows scrollable scrollHeight="550px" :virtualScrollerOptions="{ itemSize: 44 }"
    >
        <template #empty> No activities found. </template>
        <template #loading> Loading activity data. Please wait. </template>

        <Column field="col12" header="" frozen>
            <template #body="{ data }">
                <div>
                    <a :href="activity_url(data.id)" target="_blank"> <Binoculars color="blue" stroke-width="1" :size=20></Binoculars> </a>
                </div>
            </template>
        </Column>

        <Column v-if="false" field="start_date_local_as_timestamp" sortable header="Date" frozen></Column>

        <Column field="start_date_local" filterField="start_date_local" dataType="date" sortable sortField="start_date_local" style="min-width: 150px" header="Date" frozen>
            <template #body="{ data }">
                {{ formatDate(data.start_date_local) }}
            </template>
            <template #filter="{ filterModel }">
                <DatePicker v-model="filterModel.value" dateFormat="dd/mm/yy" placeholder="dd/mm/yy" />
            </template>
        </Column>

        <Column field="name" header="Title" style="min-width: 250px" sortable frozen>
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="text" @input="filterCallback()" placeholder="Search by name" />
            </template>
        </Column>

        <Column field="sport_type" header="Type" style="min-width: 100px" sortable filterField="sport_type" :showFilterMatchModes="false" :filterMenuStyle="{ width: '14rem' }" >
            <template #filter="{ filterModel }">
                <MultiSelect v-model="filterModel.value" :options="sportTypes" optionLabel="" placeholder="Any">
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <span>{{ slotProps.option }}</span>
                        </div>
                    </template>
                </MultiSelect>
            </template>
        </Column>

        <Column field="moving_time_as_string" sortable header="Moving time" style="text-align: right;min-width: 100px"></Column>

        <Column field="average_speed" header="Avg speed" filterField="average_speed" dataType="numeric" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by speed" />
            </template>
        </Column>

        <Column field="distance" header="Distance" filterField="distance" dataType="numeric" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by distance" />
            </template>
        </Column>

        <Column field="total_elevation_gain" filterField="total_elevation_gain" dataType="numeric" header="Ascent" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by ascent" />
            </template>
        </Column>

        <Column field="average_cadence" filterField="average_cadence" dataType="numeric" header="Avg cadence" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by cadence" />
            </template>
        </Column>

        <Column field="average_watts" filterField="average_watts" dataType="numeric" header="Avg watts" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by watts" />
            </template>
        </Column>

        <Column field="average_heartrate" filterField="average_heartrate" dataType="numeric" header="Avg HR" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by heart rate" />
            </template>
        </Column>

        <Column field="max_heartrate" filterField="max_heartrate" dataType="numeric" header="Max HR" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by heart rate" />
            </template>
        </Column>

        <Column field="suffer_score" filterField="suffer_score" dataType="numeric" header="Relative effort" sortable style="text-align: right;min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by relative effort" />
            </template>
        </Column>

        <template #footer> 
            <div class="flex justify-between">
                <div class="">
                    <span v-if="autoUpdateComplete" class="">Update Actitivies from Strava</span>
                    <span v-else class="animate-pulse">Auto-updating actitivies from Strava</span>
                    <DownloadFromStrava @newdownload="get_activities" :with-dialog="autoUpdateComplete" download-what="activities"></DownloadFromStrava>
                </div>
                <div> Activities current to {{ lastUpdate }} </div>
                <div v-if="autoUpdateComplete" class="">
                    <span class="">Re-download all activities from Strava</span>
                    <DownloadFromStrava @newdownload="get_activities" download-what="all activities"></DownloadFromStrava>
                </div>
                <div v-else></div>
            </div>
        </template>
    </DataTable>
</template>