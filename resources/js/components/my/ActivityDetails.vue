<script setup lang="ts">
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { ref } from 'vue';

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
const props = defineProps({
    activity: {
        type: Object,
        default: () => ({}),
    },
});
const tableData = ref<any>([]);
const currentTitle = ref(
    props.activity.name + ' - ' + formatDate(props.activity.start_date_local) + ' at ' + formatTime(props.activity.start_date_local),
);

tableData.value = [];
tableData.value.push({ col1: 'Title', col2: '', col3: props.activity.name });
tableData.value.push({
    col1: 'Date and time',
    col2: '',
    col3: formatDate(props.activity.start_date_local) + ' at ' + formatTime(props.activity.start_date_local),
});
tableData.value.push({ col1: 'Type', col2: '', col3: props.activity.sport_type });
tableData.value.push({ col1: 'Moving time', col2: '', col3: props.activity.moving_time_as_string });
if (props.activity.average_speed) {
    tableData.value.push({ col1: 'Average speed', col2: '', col3: props.activity.average_speed + ' kph' });
}
if (props.activity.distance) {
    tableData.value.push({ col1: 'Distance', col2: '', col3: props.activity.distance + ' km' });
}
if (props.activity.total_elevation_gain) {
    tableData.value.push({ col1: 'Ascent', col2: '', col3: props.activity.total_elevation_gain + ' m' });
}
if (props.activity.average_cadence) {
    tableData.value.push({ col1: 'Average cadence', col2: '', col3: props.activity.average_cadence + ' rpm' });
}
if (props.activity.average_watts) {
    tableData.value.push({ col1: 'Average watts', col2: '', col3: props.activity.average_watts });
}
if (props.activity.weighted_average_watts) {
    tableData.value.push({ col1: 'Average weighted watts', col2: '', col3: props.activity.weighted_average_watts });
}
if (props.activity.average_heartrate) {
    tableData.value.push({ col1: 'Average heart rate', col2: '', col3: props.activity.average_heartrate });
}
if (props.activity.max_heartrate) {
    tableData.value.push({ col1: 'Maximum heart rate', col2: '', col3: props.activity.max_heartrate });
}
if (props.activity.suffer_score) {
    tableData.value.push({ col1: 'Relative effort', col2: '', col3: props.activity.suffer_score });
}
if (props.activity.device_name) {
    tableData.value.push({ col1: 'Recording device', col2: '', col3: props.activity.device_name });
}
</script>

<template>
    <div class="px-8">
        <p class="w-full py-8 text-xl font-semibold tracking-tight md:px-32">{{ currentTitle }}</p>

        <DataTable
            :value="tableData"
            size="small"
            stripedRows
            style="width: 50%"
            :pt="{
                thead: { style: 'display: none' },
            }"
        >
            <Column field="col1" header="Code"></Column>
            <Column field="col3" header="Name"> </Column>
        </DataTable>
    </div>
    <!-- </div> -->
</template>
