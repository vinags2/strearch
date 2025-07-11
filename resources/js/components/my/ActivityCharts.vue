<script setup  lang="ts">

import { onMounted, watch, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Chart from 'primevue/chart';
import Select from 'primevue/select';
import Button from 'primevue/button'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

import { strearch_data } from '@/functions/Flags';
import { getChartData } from '@/functions/StrearchAPI';
import { useStrearchData } from "@/stores/StrearchStore.js"
import { storeToRefs } from 'pinia'

const strearchData = useStrearchData()
const { filteredActivitiesFlag } = storeToRefs(strearchData)
watch(filteredActivitiesFlag, () => { getChartData() })


const dataset = ref()
const chartOptions = ref()
const analyses = ref()
const chartData = ref()
const timeperiod = ref()
const activeanalysisids = ref()

watch(strearch_data, (newValue) => {
    if (newValue.code == 22) {
        create_chart(newValue.data)
    }
}) 

function initChart() { getChartData() }

function create_chart(data:any) {
    dataset.value = data.dataset
    chartOptions.value = data.chartOptions
    analyses.value = data.analyses
    chartData.value = data.chartData
    timeperiod.value = data.timeperiod
    activeanalysisids.value = data.activeanalysisids
    form.time_period = timeperiod.value
    get_active_analysis()
    selectedTimePeriod.value = timePeriods[form.time_period].name
}

const form = useForm({
    filter_selected: 16,
    analysis_selected: 0,
    analysis2_selected: 0,
    time_period:<number>0,
    second_analysis: false
})

const timePeriods = [
    {id: 0, name: 'Year'},
    {id: 1, name: 'Half Year'},
    {id: 2, name: 'Every 3 months'},
    {id: 3, name: 'Month'},
]

function saveForm() {
    form.post(route('chartData.save'))
    setTimeout(function () {
        initChart()
    }, 90);
}

const selectedTimePeriod = ref('year')

onMounted(() => {
    initChart()
});

const heading_classes = {
    'normal' : "w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm",
}

function get_active_analysis() {
    form.second_analysis = activeanalysisids.value[0]
    form.analysis_selected = activeanalysisids.value[1]
    form.analysis2_selected = activeanalysisids.value[2]
}

</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
        <form @submit.prevent="saveForm">
            <div class="flex items-center">
                <label class="ml-2 mr-6">Analyse:</label>
                    <Select id="analysisnames" v-model="form.analysis_selected" :options="analyses" optionLabel="name" optionValue="id" class="mr-6">
                    </Select>
                    <input v-model="form.second_analysis" class="mr-2" type="checkbox">
                    and
                    <Select :disabled="form.second_analysis == false" id="analysis2names" v-model="form.analysis2_selected" :options="analyses" optionLabel="name" optionValue="id" class="mr-6 ml-6">
                    </Select>
                <label class="ml-2">Per:</label>
                    <Select id="timeperiod" v-model="form.time_period" :options="timePeriods" optionLabel="name" optionValue="id" class="ml-6" >
                    </Select>
                <Button type="submit" severity="info" :disabled="form.processing" class="ml-6"> Apply </Button>
            </div>
        </form>
        <Chart type="line" class="mt-6 bg-cyan-50 border-solid border-2 border-cyan-200"
            id="my-chart-id"
            :options="chartOptions"
            :data="chartData"
        />
        <div class="mt-8 text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Tabulated data per {{ selectedTimePeriod }}</div>
        <DataTable :value="dataset" size="small" stripedRows scrollable scrollHeight="580px">
            <Column field="groupby" header="Period"> </Column>
            <Column field="Distance" header="Avg Distance"> </Column>
            <Column field="Speed" header="Avg Speed"> </Column>
            <Column field="AverageHR" header="Avg HR"> </Column>
            <Column field="Watts" header="Avg Watts"> </Column>
            <Column field="Climbing" header="Avg Climbing"> </Column>
            <Column field="TotalDistance" header="Total Distance"> </Column>
            <Column field="TotalClimbing" header="Total Climbing"> </Column>
            <Column field="HRtoClimbing" header="HR:Climbing"> </Column>
            <Column field="HRtoWatts" header="HR:Watts"> </Column>
            <Column field="HRtoSpeed" header="HR:Speed"> </Column>
            <Column field="HRtoDistance" header="HR:Distance"> </Column>
            <Column field="ClimbingToDistance" header="% Climbing:Distance"> </Column>
        </DataTable>
    </div>
</template>
