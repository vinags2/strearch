<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch, computed } from 'vue';
import { getActivities, getFilters, save_filter, save_filters, delete_filter, set_active_filter } from '@/functions/StrearchAPI.js'
import { strearch_data, debug } from '@/functions/Flags.js'
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import MultiSelect from 'primevue/multiselect';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { Button } from '@/components/ui/button';
import { Binoculars, SearchX, CircleX, Save, Check } from 'lucide-vue-next';
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';

const props = defineProps({
});

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 12) {
        fill_tableData(newValue.data)
    } else if (newValue.code == 14) {
        getFilterFromDB(newValue.data)
    } else if (newValue.code == 17) {
        console.log(newValue.data)
    }
})

const activeFilter = ref<any>()

watch(activeFilter, (newValue, oldValue) => {
    filterName.value = newValue?.name ?? 'a new filter name'
    warningVisible.value = newValue ? true : false
})

const tableData = ref<any>([])
const lastUpdate = ref<any>()
const filters = ref();
const loading = ref(true);
const rowCount = ref(0)
const allFilters = ref<any>([])
const visible = ref(false);
const filterName = ref<string>()
const filterNameNotEmpty = computed(() => {
    return filterName.value != ''
}) 

const sportTypes = ref(['Ride', 'VirtualRide', 'Run', 'Walk', 'Workout'])
const warningVisible = ref(true)
const selectedActivity = ref<any>()
var activeAllFilterIndex = -1

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

function fill_tableData(data:any) {
    lastUpdate.value = data.date_of_last_activities_strava_update
    tableData.value = []
    for(const element of data.activities) {
      element.start_date_local = new Date(element.start_date_local_as_timestamp);
    }
    // tableData.value = data.activities.data // use this when using paginator() in ActivityController.
    tableData.value = data.activities // use this when using get()() in ActivityController.
    getFilters()
    loading.value = false;
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

initFilters();

const deleteFilter = () => {
    // console.log('allFilters = ', allFilters.value)
    // console.log('allFilters[0] = ', allFilters.value[0])
    // console.log('activeFilter = ', activeFilter.value)
    // console.log('filters = ', filters.value)
    // console.log('activeAllFilterIndex = ', activeAllFilterIndex)
    // alert('This functionality is under development');
    // return
    delete_filter(allFilters.value[activeAllFilterIndex].id)
    get_activities()
    // setAllFiltersToInactive()
    // allFilters.value.forEach((el:any) => {
    //     if (el.id == 33) {
    //         el.active = 1
    //     } else {
    //         el.active = 0
    //     }
    // })
    // initFilters();
};

const saveFilter = (i = 0) => {
    visible.value = false
    // alert('The functionality is under development'); return
    if (debug) console.log('called from saveFilter in activities.vue', allFilters.value[i].name, allFilters.value[i])
    save_filter(allFilters.value[i]);
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
    visible.value = false
    var newFilterAdded = false
    var arrayOfAllFilters = []
    for (let i = 0; i < allFilters.value.length; i++) {
        if (i == activeAllFilterIndex) {
            arrayOfAllFilters.push(newAllFilterElement())
            newFilterAdded = true
        } else {
            arrayOfAllFilters.push(allFilters.value[i])
        }
        // saveFilter(i)
    }
    if (!newFilterAdded) arrayOfAllFilters.push(newAllFilterElement())
    if (debug) console.log(arrayOfAllFilters)
    save_filters(arrayOfAllFilters)
    // allFilters.value = arrayOfAllFilters
    get_activities()
    // visible.value = false
    // // alert('The functionality is under development'); return
    // save_filters(JSON.stringify(allFilters.value));
};

// working versions of saveFilter(s)
// const saveFilter = (i = 0) => {
//     visible.value = false
//     // alert('The functionality is under development'); return
//     console.log('called from saveFilter in activities.vue', allFilters.value[i].name)
//     save_filter(allFilters.value[i].name, allFilters.value[i]);
// };

// const saveFilters = () => {
//     visible.value = false
//     for (let i = 0; i < allFilters.value.length; i++) {
//         saveFilter(i)
//     }
//     // visible.value = false
//     // // alert('The functionality is under development'); return
//     // save_filters(JSON.stringify(allFilters.value));
// };

function onFilter(event:any) {
  rowCount.value = event.filteredValue.length;
}

// const restoreFilter = () => {
//     getFilters()
// };

// function extractActiveFilter() {
//    let activeFilterAsArray =  JSON.parse(allFilters.value.filter((el:any) => el.active == 1)[0].filter || '{}') 
//    return activeFilterAsArray;
   
// }

function setAllFiltersToInactive() {
    allFilters.value.map((el:any) => el.active = 0)
}

function setNewActiveFilter() {
    // setAllFiltersToInactive()
    // console.log('activefilter ID = ', activeFilter.value?.id)
    allFilters.value.forEach((el:any) => {
        if (el.id == activeFilter.value?.id) {
            el.active = 1
        } else {
            el.active = 0
        }
    })
    setActiveFilter()
    // allFilters.value.map((el:any) => el.active = el.id == 34 ? 1 : 0)
    // convertStringToDate(lsf)
    // filters.value = allFilters.value.filter((el:any) => el.active = 1)

}
function onChangeOfFilter() {
    if (debug) console.log('onChange of Filter called')
    // return
    setNewActiveFilter()
    // filterName.value = activeFilter.value.name
    // filters.value = activeFilter.value.filter
    // console.log('allFilters.value = ', allFilters.value)
    // console.log('activeFilter.filter.value = ', activeFilter.value?.filter)
    // console.log('filters.value = ', filters.value)
}

function convertAPIdatatoJStypes() {
    allFilters.value.forEach((el:any) => {
        el.filter = JSON.parse(el.filter)
        convertConstraintStringsToDates(el.filter.start_date_local.constraints)
        // el.filter.start_date_local.constraints.forEach(element => {
        //     if (element.value) {
        //         element.value = new Date(element.value)
        //     }
        // });

    })
    // filters.value = lsf
}

function filterNameExists(event:any) {
    // console.log('filterNameExists called')
    warningVisible.value = false
    allFilters.value.forEach(element => {
        // console.log(element.name, filterName.value)
        if (element.name == filterName.value) {
            warningVisible.value = true
        }
    });
}

function convertConstraintStringsToDates(constraints: any) {
    constraints.forEach(element => {
        if (element.value) {
            element.value = new Date(element.value)
        }
    });
    // filters.value = lsf
}

function setActiveFilter() {
    // console.log('filters.value before filtering active = ', filters.value)
    // filters.value = []
    initFilters()
    var i = 0
    var selectedFilter = -1;
    activeAllFilterIndex = -1
    allFilters.value.forEach((el:any) => {
        // console.log(el)
        if (el.active == 1) {
            selectedFilter = i;
            filters.value = el.filter
            activeAllFilterIndex = i
        }
        i++;
    })
    activeFilter.value = selectedFilter == -1 ? null : allFilters.value[selectedFilter]
    // if (activeFilter.value ) set_active_filter(allFilters.value[activeAllFilterIndex].id)
    if (activeFilter.value ) {
        set_active_filter(allFilters.value[activeAllFilterIndex].id)
    } else {
        set_active_filter(-1)
    }
    // filterName.value = activeFilter.value.name
    // console.log('1 allFilters = ', allFilters.value)
    // console.log('2 activeFilter = ', activeFilter.value)
    // console.log('3 filters = ', filters.value)
    // console.log('4 i = ', i, ' selectedFilter = ', selectedFilter)
    // console.log('filters.value after filtering active = ', filters.value)
}

function getFilterFromDB(data:any) {
    // console.log('(from getFilterFromDB) data.filters = ',data.filters)
    allFilters.value = data.filters
    convertAPIdatatoJStypes()
    setActiveFilter()

    // allFilters.value =  JSON.parse(allFilters.value || '{}') 
    // console.log('(from getFilterFromDB) allFilters.value = ', allFilters.value)
    // let lsf = extractActiveFilter();
    // convertStringToDate(lsf)
    // filters.value = lsf
    if (debug) {
        let tmp = allFilters.value
        // console.log('typeof allFilters.value = ', typeof allFilters.value)
        // console.log('allFilters.value = ', allFilters.value)
        // console.log('allFilters.value[0] = ', allFilters.value[0])
        // console.log('allFilters.value[0].id = ', allFilters.value[0].id)
        // console.log('allFilters.value[0].filter = ', allFilters.value[0].filter)
        // console.log('typeof allFilters.value[0].filter = ', typeof allFilters.value[0].filter)
        // var tmp = JSON.parse(allFilters.value[0].filter)
        if (debug) console.log('allFilters.value = ', tmp)
        // tmp.start_date_local.constraints.forEach(element => {
        //     if (element.value) {
        //         element.value = new Date(element.value)
        //     }
        // });
        // console.log('String changed to DATE object in JSON.parsed allFilters.value[0].filter = ', tmp)
        // console.log('activeFilter.filter.value = ', activeFilter.value?.filter)
        if (debug) console.log('filters.value = ', filters.value)
    }
}

</script>

<template>
    <Head title="Activities" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                    <p class="md:px-32 w-full text-xl font-semibold tracking-tight">Activities</p>
                    <div class="flex pt-8">
                        <div class="">
                            <Select @change="onChangeOfFilter" v-model="activeFilter" :options="allFilters" showClear optionLabel="name" data-key="id" placeholder="Select a filter" class="w-full md:w-56" />
                        </div>
                        <div class="pl-8">
                            <a href="" @click.prevent="visible = true"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><Save color="white"></Save>Save filter</Button></a>
                        </div>
                        <div class="pl-8" v-if="activeAllFilterIndex >= 0">
                            <a href="" @click.prevent="deleteFilter()"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><CircleX color="red"></CircleX>Delete filter</Button></a>
                        </div>
                        <!-- <div>
                            <a href="" @click.prevent="restoreFilter()"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><SearchX color="white"></SearchX>Restore filter</Button></a>
                        </div> -->
                    </div>
                    <div>
                        {{ activeFilter?.value }}
                    </div>

                <DataTable @filter="onFilter" class="pt-4" v-model:filters="filters" filterDisplay="menu" :loading="loading" :globalFilterFields="['name']" :value="tableData" size="small" stripedRows scrollable scrollHeight="580px" :virtualScrollerOptions="{ itemSize: 44 }">
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

            </div>
            <div>
            </div>
        <Dialog v-model:visible="visible" modal header="Filter name" :closable="false">
            <span class="text-surface-500 dark:text-surface-400 block mb-2">Enter a name for the filter</span>
            <div class="flex items-center gap-4 mb-4">
                <label for="filtername" class="font-semibold w-24">Filter name</label>
                <InputText @value-change="filterNameExists" v-model="filterName" id="filtername" class="flex-auto" autocomplete="off" type="search" />
            </div>
            <div class="flex items-center gap-4 mb-4">
                <span class="text-red-500 text-sm" v-if="warningVisible">Warning: this filter already exists and will be overwritten</span>
            </div>
            <div class="flex justify-end gap-2">
                <a href="" @click.prevent="visible = false"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><CircleX color="red"></CircleX>Cancel</Button></a>
                <a href="" @click.prevent="saveFilters" v-if="filterNameNotEmpty"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><Check color="green"></Check>OK</Button></a>
            </div>
        </Dialog>
        </div>
    </AppLayout>
</template>
