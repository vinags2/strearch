<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Chart from 'primevue/chart';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Select from 'primevue/select';
import { onMounted, ref, watch } from 'vue';

import { getChartData } from '@/functions/StrearchAPI';
import { useStrearchData } from '@/stores/StrearchStore.js';
import { storeToRefs } from 'pinia';

const strearchData = useStrearchData();
const { filteredActivitiesFlag } = storeToRefs(strearchData);
watch(filteredActivitiesFlag, () => {
    updateOrCreateChart();
});

const dataset = ref([]);
const chartOptions = ref();
const analyses = ref();
const chartData = ref();
const timeperiod = ref();
const activeanalysisids = ref();

function initChart() {
    updateOrCreateChart();
}

async function updateOrCreateChart() {
    const ret = await getChartData();
    create_chart(ret.data);
}

function create_chart(data: any) {
    dataset.value = data.dataset;
    chartOptions.value = data.chartOptions;
    analyses.value = data.analyses;
    chartData.value = data.chartData;
    timeperiod.value = data.timeperiod;
    activeanalysisids.value = data.activeanalysisids;
    form.time_period = timeperiod.value;
    get_active_analysis();
    selectedTimePeriod.value = timePeriods[form.time_period].name;
}

const form = useForm({
    filter_selected: 16,
    analysis_selected: 0,
    analysis2_selected: 0,
    time_period: <number>0,
    second_analysis: false,
});

const timePeriods = [
    { id: 0, name: 'Year' },
    { id: 1, name: 'Half Year' },
    { id: 2, name: 'Every 3 months' },
    { id: 3, name: 'Month' },
];

function saveForm() {
    form.post(route('chartData.save'), {
        onSuccess: () => initChart(),
    });
}

const selectedTimePeriod = ref('year');

onMounted(() => {
    initChart();
});

function get_active_analysis() {
    form.second_analysis = activeanalysisids.value[0];
    form.analysis_selected = activeanalysisids.value[1];
    form.analysis2_selected = activeanalysisids.value[2];
}
</script>

<template>
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
        <div v-if="dataset?.length > 0">
            <form @submit.prevent="saveForm">
                <div class="ml-8 flex items-center">
                    <label class="ml-2 mr-6">Analyse:</label>
                    <Select id="analysisnames" v-model="form.analysis_selected" :options="analyses" optionLabel="name" optionValue="id" class="mr-6">
                    </Select>
                    <input v-model="form.second_analysis" class="mr-2" type="checkbox" />
                    and
                    <Select
                        :disabled="form.second_analysis == false"
                        id="analysis2names"
                        v-model="form.analysis2_selected"
                        :options="analyses"
                        optionLabel="name"
                        optionValue="id"
                        class="ml-6 mr-6"
                    >
                    </Select>
                    <label class="ml-2">Per:</label>
                    <Select id="timeperiod" v-model="form.time_period" :options="timePeriods" optionLabel="name" optionValue="id" class="ml-6">
                    </Select>
                    <Button type="submit" severity="info" :disabled="form.processing" class="ml-6"> Apply </Button>
                </div>
            </form>
            <Chart
                v-if="true"
                type="line"
                class="ml-8 mr-8 mt-6 border-2 border-solid border-cyan-200 bg-cyan-50"
                id="my-chart-id"
                :options="chartOptions"
                :data="chartData"
            />
            <div class="ml-8 mt-8 text-2xl/7 font-bold text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Tabulated data per {{ selectedTimePeriod }}
            </div>
            <DataTable :value="dataset" size="small" stripedRows scrollable scrollHeight="580px" class="ml-8 mr-8 pt-4">
                <template #empty> No data available.</template>
                <template #loading> Loading data. Please wait. </template>

                <Column field="groupby" header="Period" frozen sortable> </Column>
                <Column field="Distance" header="Avg Distance" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="Speed" header="Avg Speed" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="AverageHR" header="Avg Heart Rate" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="Watts" header="Avg Watts" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="Climbing" header="Avg Climbing" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="TotalDistance" header="Total Distance" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="TotalClimbing" header="Total Climbing" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="HRtoClimbing" header="Ratio HR to Climbing" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="HRtoWatts" header="Ratio HR to Watts" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="HRtoSpeed" header="Ratio HR to Speed" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="HRtoDistance" header="Ratio HR to Distance" sortable style="text-align: right; min-width: 100px"> </Column>
                <Column field="ClimbingToDistance" header="% Climbing to Distance" sortable style="text-align: right; min-width: 100px"> </Column>
            </DataTable>
        </div>
        <div v-else class="mt-8 text-center text-gray-500">
            No activities found. Please download some activities or change the filter to see the chart.
        </div>
    </div>
</template>
