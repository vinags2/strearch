<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import ActivityDetails from '@/components/my/ActivityDetails.vue';
import SegmentsTable from '@/components/my/SegmentsTable.vue';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';

import { BadgePlus, Bike, Delete, Download } from 'lucide-vue-next';

import DownloadFromStrava from '@/components/DownloadFromStrava.vue';

import { getStravaData } from '@/functions/StravaAPI';

import { useStrearchData } from '@/stores/StrearchStore';
import { storeToRefs } from 'pinia';

const props = defineProps({
    showViewInStravaAsText: {
        type: Boolean,
        default: false,
    },
    autoUpdateActivities: {
        type: Boolean,
        default: true,
    },
});

// Initialize PrimeVue dialog state
const dialogVisible = ref(false);
const closeDialog = () => {
    dialogVisible.value = false;
};

const form = useForm({ activityId: 0 });

const deleteActivity = (e: Event) => {
    e.preventDefault();

    form.delete(route('activity.delete', currentId.value), {
        preserveScroll: true,
        onSuccess: () => processSuccessfulDeletion(),
        onError: () => (deletionFailed.value = true),
        onFinish: () => form.reset(),
    });
};

function processSuccessfulDeletion() {
    get_activities();
    deletionSucceeded.value = true;
}

const deletionSucceeded = ref(false);
const deletionFailed = ref(false);
const reDownloadOccurred = ref(false);

async function downloadAnActivity() {
    const flag = await getStravaData(6, currentId.value);
    showSuccessBeforeClosing(flag != 0);
    closeDialog();
}

function showSuccessBeforeClosing(success: boolean = true) {
    if (success) {
        deletionSucceeded.value = true;
        reDownloadOccurred.value = true;
        get_activities();
    } else {
        deletionFailed.value = true;
        reDownloadOccurred.value = true;
    }
}

const autoUpdateComplete = ref(!props.autoUpdateActivities);

const strearchData = useStrearchData();
const { activities, sportTypes, deviceNames, loading: loading, lastUpdate, justTheFilter } = storeToRefs(useStrearchData());

strearchData.initActivities();

function get_activities() {
    autoUpdateComplete.value = true;
    strearchData.initActivities(true);
}

function onFilter(event: any) {
    strearchData.updateFilteredActivities(event.filteredValue);
}

function activity_url(id: any) {
    return 'https://www.strava.com/activities/' + id;
}

const formatDate = (value: Date) => {
    return value.toLocaleDateString('en-AU', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatTime = (value: Date) => {
    return value.toLocaleTimeString('en-AU', {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const viewInStravaHeader = computed(() => (props.showViewInStravaAsText ? '' : 'View on Strava'));
const currentTitle = ref('an activity');
const currentId = ref(0);
const currentActivity = ref(0);
// const showAlert = ref(false);
const alertIsVisible = ref(false);
const segmentTableIsVisible = ref(false);
function showAlert(title: string, id: number) {
    currentTitle.value = title;
    currentId.value = id;
    deletionFailed.value = false;
    deletionSucceeded.value = false;
    alertIsVisible.value = true;
    return true;
}
function showSegmentTable(activity) {
    currentTitle.value =
        'Segments for "' + activity.name + '" on ' + formatDate(activity.start_date_local) + ' at ' + formatTime(activity.start_date_local);
    currentId.value = activity.id;
    currentActivity.value = activity;
    segmentTableIsVisible.value = true;
    return true;
}
const dt = ref();
const exportCSV = () => {
    dt.value.exportCSV();
};
const tip = 'Export activities as a CSV file';
function myExportFunction(data) {
    if (data.data && data.data instanceof Date) {
        return formatDate(data.data);
    } else return data.data;
}
const t1 = ref(justTheFilter.value.start_date_local);
function changeDateFilter() {
    // if (!justTheFilter.value.start_date_local) {
    justTheFilter.value.start_date_local = { constraints: [] };
    // }
    justTheFilter.value.start_date_local = {
        operator: FilterOperator.AND,
        constraints: [
            {
                value: new Date('2024-01-01T00:00:00'),
                matchMode: FilterMatchMode.DATE_BEFORE,
            },
            {
                // value: new Date('2024-01-01T00:00:00'),
                value: new Date('2023-11-30T00:00:00'),
                matchMode: FilterMatchMode.DATE_AFTER,
            },
            // {
            //     value: new Date('2024-12-31T23:59:59'),
            //     matchMode: 'lte',
            // },
        ],
    };
    t1.value = justTheFilter.value.start_date_local;
}
</script>
<template>
    <!-- <div class="mt-4">justTheFilter = {{ justTheFilter }}</div>
    <div class="mt-4">start_date_local's year = {{ justTheFilter.start_date_local?.constraints[0].value?.getFullYear() }}</div>
    <div class="mt-4">t1 = {{ t1 }}</div>
    <button @click="changeDateFilter()" class="p-button p-component mb-4 mt-4">Modify Date Filter</button> -->

    <DataTable
        :value="activities"
        v-model:filters="justTheFilter"
        @filter="onFilter"
        :loading="loading"
        size="small"
        class="ml-8 mr-8 pt-4"
        filterDisplay="menu"
        stripedRows
        scrollable
        scrollHeight="550px"
        :virtualScrollerOptions="{ itemSize: 44 }"
        ref="dt"
        :exportFunction="myExportFunction"
    >
        <template #empty> No activities found. </template>
        <template #loading> Loading activity data. Please wait. </template>

        <Column field="col12" :header="viewInStravaHeader" frozen style="min-width: 60px">
            <template #body="{ data }">
                <div class="grid grid-cols-5 content-between items-center gap-4">
                    <div v-if="props.showViewInStravaAsText" class="text-sm text-[#FC5200]">
                        <a :href="activity_url(data.id)" target="_blank">View on Strava</a>
                    </div>
                    <div
                        v-else
                        v-tooltip.top="{
                            value: 'View on Strava',
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <a :href="activity_url(data.id)" target="_blank"> <Bike color="#FC5200" stroke-width="1" :size="20"></Bike> </a>
                    </div>
                    <div
                        class="ml-2"
                        v-tooltip.top="{
                            value: 'Edit',
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <a @click="showAlert(data.name, data.id)" style="background-color: transparent; border: none" class="cursor-pointer">
                            <Delete color="blue" stroke-width="1" :size="20"></Delete>
                        </a>
                    </div>
                    <div
                        class="ml-4"
                        v-tooltip.top="{
                            value: 'Segments and Details',
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <a @click="showSegmentTable(data)" style="background-color: transparent; border: none" class="cursor-pointer">
                            <BadgePlus color="blue" stroke-width="1" :size="20"></BadgePlus>
                        </a>
                    </div>
                </div>
            </template>
        </Column>

        <Column v-if="false" field="start_date_as_timestamp" sortable header="Date" frozen></Column>

        <Column
            field="start_date_local"
            filterField="start_date_local"
            dataType="date"
            sortable
            sortField="start_date_local"
            style="min-width: 150px"
            header="Date"
            frozen
        >
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

        <Column
            field="sport_type"
            header="Type"
            style="min-width: 100px"
            sortable
            filterField="sport_type"
            :showFilterMatchModes="false"
            :filterMenuStyle="{ width: '14rem' }"
        >
            <template #filter="{ filterModel }">
                <MultiSelect v-model="filterModel.value" :options="[...sportTypes]" optionLabel="" placeholder="Any">
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <span>{{ slotProps.option }}</span>
                        </div>
                    </template>
                </MultiSelect>
            </template>
        </Column>

        <Column field="moving_time_as_string" sortable header="Moving time" style="text-align: right; min-width: 100px"></Column>

        <Column
            field="average_speed"
            header="Avg speed"
            filterField="average_speed"
            dataType="numeric"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by speed" />
            </template>
        </Column>

        <Column field="distance" header="Distance" filterField="distance" dataType="numeric" sortable style="text-align: right; min-width: 100px">
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by distance" />
            </template>
        </Column>

        <Column
            field="total_elevation_gain"
            filterField="total_elevation_gain"
            dataType="numeric"
            header="Ascent"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by ascent" />
            </template>
        </Column>

        <Column
            field="average_cadence"
            filterField="average_cadence"
            dataType="numeric"
            header="Avg cadence"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by cadence" />
            </template>
        </Column>

        <Column
            field="average_watts"
            filterField="average_watts"
            dataType="numeric"
            header="Avg watts"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by watts" />
            </template>
        </Column>

        <Column
            field="weighted_average_watts"
            filterField="weighted_average_watts"
            dataType="numeric"
            header="Avg weighted watts"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by weighted watts" />
            </template>
        </Column>

        <Column
            field="average_heartrate"
            filterField="average_heartrate"
            dataType="numeric"
            header="Avg HR"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by heart rate" />
            </template>
        </Column>

        <Column
            field="max_heartrate"
            filterField="max_heartrate"
            dataType="numeric"
            header="Max HR"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by heart rate" />
            </template>
        </Column>

        <Column
            field="suffer_score"
            filterField="suffer_score"
            dataType="numeric"
            header="Relative effort"
            sortable
            style="text-align: right; min-width: 100px"
        >
            <template #filter="{ filterModel, filterCallback }">
                <InputText v-model="filterModel.value" type="number" @input="filterCallback()" placeholder="Search by relative effort" />
            </template>
        </Column>

        <Column
            field="device_name"
            header="Device"
            style="min-width: 150px"
            sortable
            filterField="device_name"
            :showFilterMatchModes="false"
            :filterMenuStyle="{ width: '14rem' }"
        >
            <template #filter="{ filterModel }">
                <MultiSelect v-model="filterModel.value" :options="[...deviceNames]" optionLabel="" placeholder="Any">
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <span>{{ slotProps.option }}</span>
                        </div>
                    </template>
                </MultiSelect>
            </template>
        </Column>

        <template #footer>
            <div class="flex justify-between">
                <div class="">
                    <span v-if="autoUpdateComplete" class="">Update Actitivies from Strava</span>
                    <span v-else class="animate-pulse">Auto-updating actitivies from Strava</span>
                    <DownloadFromStrava @newdownload="get_activities" :with-dialog="autoUpdateComplete" :flag="10"></DownloadFromStrava>
                </div>
                <div class="mt-1">Activities current to {{ lastUpdate }}</div>
                <div>
                    Export
                    <Button
                        label="Export"
                        @click="exportCSV()"
                        style="background-color: transparent; border: none"
                        v-tooltip.top="{
                            value: tip,
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <Download color="blue" :size="16" />
                    </Button>
                </div>
                <div v-if="autoUpdateComplete" class="">
                    <span class="">Re-download all activities from Strava</span>
                    <DownloadFromStrava @newdownload="get_activities" :flag="23"></DownloadFromStrava>
                </div>
                <div v-else></div>
            </div>
        </template>
    </DataTable>
    <Dialog v-model:visible="alertIsVisible" modal :closable="false" header="Edit Activity" style="background-color: #f0f8ff">
        <div v-if="deletionSucceeded">
            <div class="mt-2" v-if="reDownloadOccurred">The re-download of the activity:</div>
            <div class="mt-2" v-else>The deletion of the activity:</div>
            <div class="mt-2 italic">"{{ currentTitle }}"</div>
            <div class="mt-2">was successful.</div>
            <div class="mt-2 text-red-500">[Note that the activity in Strava was not altered.]</div>
        </div>
        <div v-else-if="deletionFailed">
            <div class="mt-2" v-if="reDownloadOccurred">The re-download of the activity:</div>
            <div class="mt-2" v-else>The deletion of the activity:</div>
            <div class="mt-2 italic">"{{ currentTitle }}"</div>
            <div class="mt-2">FAILED.</div>
        </div>
        <div v-else>
            <div class="mt-2">You may delete the local copy of the activity:</div>
            <div class="mt-2 italic">"{{ currentTitle }}"</div>
            <div class="mt-2">or re-download it from Strava.</div>
            <div class="mt-2 text-red-500">[The activity in Strava is not altered.]</div>
        </div>
        <div class="mt-4 grid grid-cols-3 content-between gap-4">
            <Button @click="alertIsVisible = false" size="small" severity="info">Close</Button>
            <Button v-show="!deletionFailed && !deletionSucceeded" @click="downloadAnActivity" size="small" severity="info">Download</Button>
            <form class="space-y-6" @submit="deleteActivity">
                <Button v-show="!deletionFailed && !deletionSucceeded" size="small" severity="danger" type="submit" :disabled="form.processing"
                    >Delete</Button
                >
            </form>
        </div>
    </Dialog>
    <Dialog
        v-model:visible="segmentTableIsVisible"
        modal
        :closable="true"
        :showHeader="false"
        style="background-color: #f0f8ff; width: 90%; max-width: 1200px"
    >
        <ActivityDetails :activity="currentActivity"></ActivityDetails>
        <SegmentsTable :activityId="currentId" activityTitle="Current Activity's Segments"></SegmentsTable>
        <div class="justify-left flex flex-row items-center pb-1 pt-2">
            <Button @click="segmentTableIsVisible = false" size="small" severity="info">Close</Button>
        </div>
    </Dialog>
</template>
