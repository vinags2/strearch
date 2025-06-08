<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch, computed } from 'vue';

import { getActivities, getFilters, save_filter, save_filters, delete_filter, set_active_filter } from '@/functions/StrearchAPI.js'
import { strearch_data, debug } from '@/functions/Flags.js'

import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import MultiSelect from 'primevue/multiselect';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import { Button } from '@/components/ui/button';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip"

import { Binoculars, CircleX, Save, Check, TestTubes } from 'lucide-vue-next';

import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 12) {
        fill_tableData(newValue.data)
    } else if (newValue.code == 14) {
        getFilterFromDB(newValue.data)
    } 
})

const activeFilter = ref<any>()

watch(activeFilter, (newValue, oldValue) => {
    filterName.value = newValue?.name ?? 'a new filter name'
    warningVisible.value = newValue ? true : false
})

const tableData = ref<any>([])
const statsTableData = ref<any>([])
const lastUpdate = ref<any>()
const filters = ref();
const loading = ref(true);
const rowCount = ref(0)
const allFilters = ref<any>([])

const askForFilterName = ref(false);
const filterName = ref<string>()
const filterNameNotEmpty = computed(() => { return filterName.value != '' }) 

const sportTypes = ref(['Ride', 'VirtualRide', 'Run', 'Walk', 'Workout'])
const warningVisible = ref(true)
const activeAllFilterIndex = ref(-1)
const showExperimental = ref(false)

const formatDate = (value:Date) => {
    return value.toLocaleDateString('en-AU', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    })
};

onMounted(() => {
    get_activities()
})

function get_activities() {
    loading.value = true;
    getActivities()
}

function activity_url(id:any) {
    return "https://www.strava.com/activities/" + id
}

function stringToDate(data:any) {
    for(const element of data.activities) {
      element.start_date_local = new Date(element.start_date_local_as_timestamp);
    }
}

function fill_tableData(data:any) {
    lastUpdate.value = data.date_of_last_activities_strava_update
    tableData.value = []
    stringToDate(data)
    // tableData.value = data.activities.data // use this when using paginator() in ActivityController.
    tableData.value = data.activities // use this when using get()() in ActivityController.
    getFilters()
    loading.value = false;
}

function convertMovingTimeToString(mt:number) {
        
        let d = Math.floor(mt / 86400);
        mt = mt - d * 86400;
        let h = Math.floor(mt / 3600);
        let m = Math.floor(mt % 3600 / 60);
        let s = Math.floor(mt % 3600 % 60);


        let dDisplay:string = d.toString();
        let hDisplay:string = h.toString();
        let mDisplay = m.toString();
        let sDisplay = s.toString();

        let display =  d > 0 ? dDisplay + 'd ' : ''
        display = h > 0 ? display + hDisplay + 'h ' : display
        display = m > 0 ? display + mDisplay + 'm ' : display
        display = s > 0 ? display + sDisplay + 's ' : display
        return display
}

function calculate_stats(data:any) {
    var stats = {
        distance:0,
        ascent:0,
        numberOfActivities:0,
        average_distance:0,
        average_ascent:0,
        minimum_distance:1000000,
        minimum_ascent:1000000,
        maximum_distance:0,
        maximum_ascent:0,
        moving_time:0,
        moving_time_as_string:'',
        minimum_moving_time_as_string:'',
        maximum_moving_time_as_string:'',
        average_moving_time:'',
        minimum_moving_time:1000000,
        maximum_moving_time:0,
        average_cadence:<any>0,
        average_heartrate:<any>0,
        average_speed:<any>0,
        average_pace:<any>0,
        average_watts:<any>0,
        average_relative_effort:<any>0,
        times_cadence_recorded:0,
        times_heartrate_recorded:0,
        times_watts_recorded:0,
        minimum_cadence:<any>1000000,
        minimum_heartrate:<any>100000,
        minimum_speed:<any>100000,
        minimum_pace:<any>100000,
        minimum_watts:<any>100000,
        maximum_cadence:<any>0,
        maximum_heartrate:<any>0,
        maximum_speed:<any>0,
        maximum_pace:<any>0,
        maximum_watts:<any>0,
        relative_effort:<any>0,
        maximum_relative_effort:<any>0,
        minimum_relative_effort:<any>1000000,
        times_relative_effort_recorded:0,
    }
    for (const element of data) {
        stats.distance += element.distance
        stats.ascent += element.total_elevation_gain
        stats.moving_time += element.moving_time
        stats.numberOfActivities += 1
        stats.minimum_moving_time = stats.minimum_moving_time < element.moving_time ? stats.minimum_moving_time : element.moving_time
        stats.maximum_moving_time = stats.maximum_moving_time > element.moving_time ? stats.maximum_moving_time : element.moving_time
        stats.minimum_distance = stats.minimum_distance < element.distance ? stats.minimum_distance : element.distance
        stats.maximum_distance = stats.maximum_distance > element.distance ? stats.maximum_distance : element.distance
        stats.minimum_ascent = stats.minimum_ascent < element.total_elevation_gain ? stats.minimum_ascent : element.total_elevation_gain
        stats.maximum_ascent = stats.maximum_ascent > element.total_elevation_gain ? stats.maximum_ascent : element.total_elevation_gain
        stats.average_cadence += element.average_cadence
        stats.average_heartrate += element.average_heartrate
        stats.average_speed += element.average_speed
        stats.average_watts += element.average_watts
        stats.average_relative_effort += element.suffer_score
        stats.times_cadence_recorded += element.average_cadence == null ? 0 : 1
        stats.times_heartrate_recorded += element.average_heartrate == null ? 0 : 1
        stats.times_watts_recorded += element.average_watts == null ? 0 : 1
        stats.times_relative_effort_recorded += element.suffer_score == null ? 0 : 1
        if (element.average_cadence) stats.minimum_cadence = stats.minimum_cadence < element.average_cadence ? stats.minimum_cadence : element.average_cadence
        if (element.average_cadence) stats.maximum_cadence = stats.maximum_cadence > element.average_cadence ? stats.maximum_cadence : element.average_cadence
        if (element.average_heartrate) stats.minimum_heartrate = stats.minimum_heartrate < element.average_heartrate ? stats.minimum_heartrate : element.average_heartrate
        if (element.average_heartrate) stats.maximum_heartrate = stats.maximum_heartrate > element.average_heartrate ? stats.maximum_heartrate : element.average_heartrate
        if (element.average_speed) stats.maximum_speed = stats.maximum_speed > element.average_speed ? stats.maximum_speed : element.average_speed
        if (element.average_speed) stats.minimum_speed = stats.minimum_speed < element.average_speed ? stats.minimum_speed : element.average_speed
        if (element.average_watts) stats.minimum_watts = stats.minimum_watts < element.average_watts ? stats.minimum_watts : element.average_watts
        if (element.average_watts) stats.maximum_watts = stats.maximum_watts > element.average_watts ? stats.maximum_watts : element.average_watts
        if (element.suffer_score) stats.minimum_relative_effort = stats.minimum_relative_effort < element.suffer_score ? stats.minimum_relative_effort : element.suffer_score
        if (element.suffer_score) stats.maximum_relative_effort = stats.maximum_relative_effort > element.suffer_score ? stats.maximum_relative_effort : element.suffer_score
    }
    stats.average_ascent = stats.ascent / stats.numberOfActivities
    stats.average_distance = stats.distance / stats.numberOfActivities
    stats.moving_time_as_string = convertMovingTimeToString(stats.moving_time)
    stats.average_moving_time = convertMovingTimeToString(stats.moving_time/stats.numberOfActivities)
    stats.minimum_moving_time_as_string = convertMovingTimeToString(stats.minimum_moving_time)
    stats.maximum_moving_time_as_string = convertMovingTimeToString(stats.maximum_moving_time)
    stats.average_cadence = stats.times_cadence_recorded  > 0 ? Math.floor(stats.average_cadence / stats.times_cadence_recorded) : null
    stats.average_heartrate = stats.times_heartrate_recorded  > 0 ? Math.floor(stats.average_heartrate / stats.times_heartrate_recorded) : null
    stats.average_pace = stats.average_speed == 0 ? null : (stats.numberOfActivities / stats.average_speed * 60).toFixed(2)
    stats.average_speed = Math.floor(stats.average_speed / stats.numberOfActivities)
    stats.average_watts = stats.times_watts_recorded  > 0 ? Math.floor(stats.average_watts / stats.times_watts_recorded) : null
    stats.average_relative_effort = stats.times_relative_effort_recorded  > 0 ? Math.floor(stats.average_relative_effort / stats.times_relative_effort_recorded) : null
    stats.minimum_pace = stats.minimum_speed == 0 ? null : 1 / stats.minimum_speed * 60
    stats.maximum_pace = stats.maximum_speed == 0 ? null : 1 / stats.maximum_speed * 60
    return stats
}

function fill_statsTableData(data:any) {
    const stats = calculate_stats(data)
    statsTableData.value = []
    statsTableData.value.push({col1: 'Number of activities', col2:'', col3: stats.numberOfActivities})
    statsTableData.value.push({col1: 'Distance', col2:'bold', col3: ''})
    if (stats.distance == 0) {
        statsTableData.value.push({col1: 'total', col2:'', col3: null})
        statsTableData.value.push({col1: 'average', col2:'', col3: null})
        statsTableData.value.push({col1: 'minimum', col2:'', col3: null})
        statsTableData.value.push({col1: 'maximum', col2:'', col3: null})
    } else {
        statsTableData.value.push({col1: 'total', col2:'', col3: Math.round(stats.distance).toLocaleString() + ' km'})
        statsTableData.value.push({col1: 'average', col2:'', col3: Math.round(stats.average_distance).toLocaleString() + ' km'})
        statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_distance).toLocaleString() + ' km'})
        statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_distance).toLocaleString() + ' km'})
    }
    statsTableData.value.push({col1: 'Ascent', col2:'bold', col3: ''})
    if (stats.ascent == 0) {
        statsTableData.value.push({col1: 'total', col2:'', col3: null})
        statsTableData.value.push({col1: 'average', col2:'', col3: null})
        statsTableData.value.push({col1: 'minimum', col2:'', col3: null})
        statsTableData.value.push({col1: 'maximum', col2:'', col3: null})
    } else {
        statsTableData.value.push({col1: 'total', col2:'', col3: Math.round(stats.ascent).toLocaleString() + ' m'})
        statsTableData.value.push({col1: 'average', col2:'', col3: Math.round(stats.average_ascent).toLocaleString() + ' m'})
        statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_ascent).toLocaleString() + ' m'})
        statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_ascent).toLocaleString() + ' m'})
    }
    statsTableData.value.push({col1: 'Moving time', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'total', col2:'', col3: stats.moving_time_as_string})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_moving_time})
    statsTableData.value.push({col1: 'minimum', col2:'', col3: stats.minimum_moving_time_as_string})
    statsTableData.value.push({col1: 'maximum', col2:'', col3: stats.maximum_moving_time_as_string})
    statsTableData.value.push({col1: 'Cadence', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_cadence})
    statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_cadence) > 1000 ? null : Math.round(stats.minimum_cadence).toLocaleString()})
    statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_cadence) == 0 ? null : Math.round(stats.maximum_cadence).toLocaleString()})
    statsTableData.value.push({col1: 'Heartrate', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_heartrate})
    statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_heartrate).toLocaleString()})
    statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_heartrate).toLocaleString()})
    statsTableData.value.push({col1: 'Watts', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_watts})
    statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_watts) > 1000 ? null : Math.round(stats.minimum_watts).toLocaleString()})
    statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_watts) == 0 ? null : Math.round(stats.maximum_watts).toLocaleString()})
    statsTableData.value.push({col1: 'Speed', col2:'bold', col3: ''})
    if (stats.average_speed == 0) {
        statsTableData.value.push({col1: 'average', col2:'', col3: null})
        statsTableData.value.push({col1: 'slowest', col2:'', col3: null})
        statsTableData.value.push({col1: 'fastest', col2:'', col3: null})
    } else {
        statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_speed + ' kph'})
        statsTableData.value.push({col1: 'slowest', col2:'', col3: Math.round(stats.minimum_speed).toLocaleString() + ' kph'})
        statsTableData.value.push({col1: 'fastest', col2:'', col3: Math.round(stats.maximum_speed).toLocaleString() + ' kph'})
    }
    statsTableData.value.push({col1: 'Pace', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_pace ? stats.average_pace + ' mins/km': null})
    statsTableData.value.push({col1: 'slowest', col2:'', col3: stats.minimum_pace < 1000 ? stats.minimum_pace.toFixed(2) + ' mins/km' : null})
    statsTableData.value.push({col1: 'fastest', col2:'', col3: stats.maximum_pace ? stats.maximum_pace.toFixed(2) + ' mins/km' : null})
    statsTableData.value.push({col1: 'Relative Effort', col2:'bold', col3: ''})
    statsTableData.value.push({col1: 'average', col2:'', col3: stats.average_relative_effort})
    statsTableData.value.push({col1: 'minimum', col2:'', col3: Math.round(stats.minimum_relative_effort).toLocaleString()})
    statsTableData.value.push({col1: 'maximum', col2:'', col3: Math.round(stats.maximum_relative_effort).toLocaleString()})
}

const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
        start_date_local: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        start_date_local_as_timestamp: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
        sport_type: { value: null, matchMode: FilterMatchMode.IN },
        distance: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        average_speed: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        average_cadence: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        average_watts: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        average_heartrate: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        max_heartrate: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        total_elevation_gain: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
        suffer_score: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
    };
};

// initFilters();

const forTesting = () => {
    console.log('allFilters = ', allFilters.value)
    console.log('allFilters[0] = ', allFilters.value[0])
    console.log('activeFilter = ', activeFilter.value)
    console.log('filters = ', filters.value)
    console.log('activeAllFilterIndex = ', activeAllFilterIndex.value)
}

const deleteFilter = () => {
    if (window.confirm("Are you sure you wish to delete the filter '"+allFilters.value[activeAllFilterIndex.value].name+"'?")) {
        delete_filter(allFilters.value[activeAllFilterIndex.value].id)
        get_activities()
    }
};

function newAllFilterElement() {
    return {
        active: 1,
        created_at: "2025-06-07T08:18:48.000000Z",
        filter: filters.value,
        id: 100000,
        name: filterName.value,
        type_of_name_search: 0,
        updated_at: "2025-06-07T08:18:48.000000Z",
        user_id: 0,
    }
}

const saveFilters = () => {
    askForFilterName.value = false
    var newFilterAdded = false
    var arrayOfAllFilters = []
    for (let i = 0; i < allFilters.value.length; i++) {
        if (i == activeAllFilterIndex.value) {
            arrayOfAllFilters.push(newAllFilterElement())
            newFilterAdded = true
        } else {
            arrayOfAllFilters.push(allFilters.value[i])
        }
    }
    if (!newFilterAdded) arrayOfAllFilters.push(newAllFilterElement())
    if (debug) console.log(arrayOfAllFilters)
    save_filters(arrayOfAllFilters)
    get_activities()
};

function onFilter(event:any) {
  rowCount.value = event.filteredValue.length;
  fill_statsTableData(event.filteredValue)
  if (debug) {
    console.log('event from onFilter event = ', event)
    console.log('activities from onFilter event = ', event.filteredValue)
  }
}

function setNewActiveFilter() {
    allFilters.value.forEach((el:any) => {
        if (el.id == activeFilter.value?.id) {
            el.active = 1
        } else {
            el.active = 0
        }
    })
    setActiveFilter()
}
function onChangeOfFilter() {
    if (debug) console.log('onChange of Filter called')
    setNewActiveFilter()
}

function convertAPIdatatoJStypes() {
    allFilters.value.forEach((el:any) => {
        el.filter = JSON.parse(el.filter)
        convertConstraintStringsToDates(el.filter.start_date_local.constraints)
    })
}

function filterNameExists(event:any) {
    warningVisible.value = false
    allFilters.value.forEach((element:any) => {
        if (element.name == filterName.value) {
            warningVisible.value = true
        }
    });
}

function convertConstraintStringsToDates(constraints: any) {
    constraints.forEach((element:any) => {
        if (element.value) {
            element.value = new Date(element.value)
        }
    });
}

function setActiveFilter() {
    initFilters()
    var i = 0
    var selectedFilter = -1;
    activeAllFilterIndex.value = -1
    allFilters.value.forEach((el:any) => {
        if (el.active == 1) {
            selectedFilter = i;
            filters.value = el.filter
            activeAllFilterIndex.value = i
        }
        i++;
    })
    activeFilter.value = selectedFilter == -1 ? null : allFilters.value[selectedFilter]
    if (activeFilter.value ) {
        set_active_filter(allFilters.value[activeAllFilterIndex.value].id)
    } else {
        set_active_filter(-1)
    }
}

function getFilterFromDB(data:any) {
    allFilters.value = data.filters
    convertAPIdatatoJStypes()
    setActiveFilter()

    if (debug) {
        let tmp = allFilters.value
        if (debug) console.log('allFilters.value = ', tmp)
        if (debug) console.log('filters.value = ', filters.value)
    }
}

</script>

<template>
    <Head title="Activities" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                    <div class="flex pb-8">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <div class="">
                                        <Select @change="onChangeOfFilter" v-model="activeFilter" :options="allFilters" showClear optionLabel="name" data-key="id" placeholder="Select a filter" style="background-color: #E0FFFF" />
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Select a filter</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <div class="pl-8">
                                        <a href="" @click.prevent="askForFilterName = true"><Button unstyled style="background-color: #E0FFFF"><Save color="black"></Save></Button></a>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Save Filter</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <div class="pl-1" v-show="activeAllFilterIndex >= 0">
                                        <a href="" @click.prevent="deleteFilter()"><Button unstyled style="background-color: #E0FFFF"><CircleX color="red"></CircleX></Button></a>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>Delete Filter</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger asChild>
                                    <div class="pl-8" v-if="showExperimental">
                                        <a href="" @click.prevent="forTesting"><Button unstyled style="background-color: #E0FFFF; color:black"><TestTubes color="black"></TestTubes>For testing</Button></a>
                                    </div>
                                </TooltipTrigger>
                                <TooltipContent>
                                    <p>This button is used for testing purposes, and normally outputs to the console.</p>
                                </TooltipContent>
                            </Tooltip>
                        </TooltipProvider>
                    </div>
                    <Tabs value="0" unstyled
                >
                        <TabList class="flex flex-row">
                            <Tab value="0" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Activities</Tab>
                            <Tab value="3" disabled>&nbsp;&nbsp;</Tab>
                            <Tab value="1" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Statistics</Tab>
                            <Tab value="3" disabled>&nbsp;&nbsp;</Tab>
                            <Tab value="2" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Charts</Tab>
                        </TabList>
                        <TabPanels>

                        <TabPanel value="0">
              

                <DataTable @filter="onFilter" class="pt-4" v-model:filters="filters" filterDisplay="menu" :loading="loading" :globalFilterFields="['name']" :value="tableData" size="small" stripedRows scrollable scrollHeight="580px" :virtualScrollerOptions="{ itemSize: 44 }" >
                    <template #empty> No activities found. </template>
                    <template #loading> Loading activity data. Please wait. </template>
            
                    <Column field="col12" header="" >
                        <template #body="{ data }">
                            <div>
                                <a :href="activity_url(data.id)" target="_blank"> <Binoculars color="blue" stroke-width="1" size=20></Binoculars> </a>
                            </div>
                        </template>
                    </Column>
                    <Column v-if="false" field="start_date_local_as_timestamp" sortable header="Date"></Column>
                    <Column field="start_date_local" filterField="start_date_local" dataType="date" sortable sortField="start_date_local" style="min-width: 150px" header="Date">
                        <template #body="{ data }">
                            {{ formatDate(data.start_date_local) }}
                        </template>
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
                            <div>
                                In total there are {{ rowCount }} activities.
                            </div>
                            <div>
                                Activities current to {{ lastUpdate }}
                            </div>
                        </div>
                    </template>
                </DataTable>
                    <div class="flex justify-between">
                        <div class="">
                            <span class="">Update Actitivies from Strava</span>
                            <DownloadFromStravaWithDialog @newdownload="get_activities" download-what="activities"></DownloadFromStravaWithDialog>

                        </div>
                        <div class="">
                            <span class="">Re-download all activities from Strava</span>
                            <DownloadFromStravaWithDialog @newdownload="get_activities" download-what="all activities"></DownloadFromStravaWithDialog>

                        </div>
                    </div>
                </TabPanel>
                <TabPanel value="1">

                    <div class="pt-4">
                    <DataTable :value="statsTableData" size="small" stripedRows style="width: 40%;" scrollable scrollHeight="580px"
                    >
                        <Column field="col1" header="Statistic" style="width: 50%">
                            <template #body="{ data }">
                                <div v-if="data.col2 && data.col2 == 'bold'" class="flex font-bold items-center gap-2">
                                    {{ data.col1 }}
                                </div>
                                <div v-else>
                                    {{ data.col1 }}
                                </div>
                            </template>
                        </Column>
                        <Column field="col2" header="" >
                            <template #body="{ data }">
                            </template>
                        </Column>
                        <Column field="col3" header="Value" style="width: 50%">
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
                </TabPanel>
                <TabPanel value="2">
                    <div>Also still to be done</div>
                </TabPanel>
                </TabPanels>
        </Tabs>
            </div>
            <div>
            </div>
        <Dialog v-model:visible="askForFilterName" modal header="Filter name" :closable="false">
            <span class="text-surface-500 dark:text-surface-400 block mb-2">Enter a name for the filter</span>
            <div class="flex items-center gap-4 mb-4">
                <label for="filtername" class="font-semibold w-24">Filter name</label>
                <InputText @value-change="filterNameExists" v-model="filterName" id="filtername" class="flex-auto" autocomplete="off" type="search" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <span class="text-red-500 text-sm" v-if="warningVisible">Warning: this filter already exists and will be overwritten</span>
            </div>
            <div class="flex justify-end gap-2">
                <a href="" @click.prevent="askForFilterName = false"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><CircleX color="red"></CircleX>Cancel</Button></a>
                <a href="" @click.prevent="saveFilters" v-if="filterNameNotEmpty"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><Check color="green"></Check>OK</Button></a>
            </div>
        </Dialog>
        </div>
    </AppLayout>
</template>
