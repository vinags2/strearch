<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';

import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

import { useStrearchData } from '@/stores/StrearchStore.js';
import { storeToRefs } from 'pinia';

const strearchData = useStrearchData();
const { filteredActivities, loading: loading } = storeToRefs(strearchData);

const statsTableData = ref<any>([]);

const numberOfActivities = ref(0);

onMounted(() => fill_statsTableData(filteredActivities.value));

watch(filteredActivities, () => {
    fill_statsTableData(filteredActivities.value);
});

function convertMovingTimeToString(mt: number) {
    const d = Math.floor(mt / 86400);
    mt = mt - d * 86400;
    const h = Math.floor(mt / 3600);
    const m = Math.floor((mt % 3600) / 60);
    const s = Math.floor((mt % 3600) % 60);

    const dDisplay: string = d.toString();
    const hDisplay: string = h.toString();
    const mDisplay = m.toString();
    const sDisplay = s.toString();

    let display = d > 0 ? dDisplay + 'd ' : '';
    display = h > 0 ? display + hDisplay + 'h ' : display;
    display = m > 0 ? display + mDisplay + 'm ' : display;
    display = s > 0 ? display + sDisplay + 's ' : display;
    return display;
}

function calculate_stats(data: any) {
    const stats = {
        distance: 0,
        ascent: 0,
        numberOfActivities: 0,
        average_distance: 0,
        average_ascent: 0,
        minimum_distance: 1000000,
        minimum_ascent: 1000000,
        maximum_distance: 0,
        maximum_ascent: 0,
        moving_time: 0,
        moving_time_as_string: '',
        minimum_moving_time_as_string: '',
        maximum_moving_time_as_string: '',
        average_moving_time: '',
        minimum_moving_time: 1000000,
        maximum_moving_time: 0,
        average_cadence: <any>0,
        average_heartrate: <any>0,
        average_speed: <any>0,
        average_pace: <any>0,
        average_watts: <any>0,
        weighted_average_watts: <any>0,
        average_relative_effort: <any>0,
        times_cadence_recorded: 0,
        times_heartrate_recorded: 0,
        times_watts_recorded: 0,
        weighted_times_watts_recorded: 0,
        minimum_cadence: <any>1000000,
        minimum_heartrate: <any>100000,
        minimum_speed: <any>100000,
        minimum_pace: <any>100000,
        minimum_watts: <any>100000,
        weighted_minimum_watts: <any>100000,
        maximum_cadence: <any>0,
        maximum_heartrate: <any>0,
        maximum_speed: <any>0,
        maximum_pace: <any>0,
        maximum_watts: <any>0,
        weighted_maximum_watts: <any>0,
        relative_effort: <any>0,
        maximum_relative_effort: <any>0,
        minimum_relative_effort: <any>1000000,
        times_relative_effort_recorded: 0,
    };
    for (const element of data) {
        stats.distance += element.distance;
        stats.ascent += element.total_elevation_gain;
        stats.moving_time += element.moving_time;
        stats.numberOfActivities += 1;
        stats.minimum_moving_time = stats.minimum_moving_time < element.moving_time ? stats.minimum_moving_time : element.moving_time;
        stats.maximum_moving_time = stats.maximum_moving_time > element.moving_time ? stats.maximum_moving_time : element.moving_time;
        stats.minimum_distance = stats.minimum_distance < element.distance ? stats.minimum_distance : element.distance;
        stats.maximum_distance = stats.maximum_distance > element.distance ? stats.maximum_distance : element.distance;
        stats.minimum_ascent = stats.minimum_ascent < element.total_elevation_gain ? stats.minimum_ascent : element.total_elevation_gain;
        stats.maximum_ascent = stats.maximum_ascent > element.total_elevation_gain ? stats.maximum_ascent : element.total_elevation_gain;
        stats.average_cadence += element.average_cadence;
        stats.average_heartrate += element.average_heartrate;
        stats.average_speed += element.average_speed;
        stats.average_watts += element.average_watts;
        stats.weighted_average_watts += element.weighted_average_watts;
        stats.average_relative_effort += element.suffer_score;
        stats.times_cadence_recorded += element.average_cadence == null ? 0 : 1;
        stats.times_heartrate_recorded += element.average_heartrate == null ? 0 : 1;
        stats.times_watts_recorded += element.average_watts == null ? 0 : 1;
        stats.weighted_times_watts_recorded += element.weighted_average_watts == null ? 0 : 1;
        stats.times_relative_effort_recorded += element.suffer_score == null ? 0 : 1;
        if (element.average_cadence)
            stats.minimum_cadence = stats.minimum_cadence < element.average_cadence ? stats.minimum_cadence : element.average_cadence;
        if (element.average_cadence)
            stats.maximum_cadence = stats.maximum_cadence > element.average_cadence ? stats.maximum_cadence : element.average_cadence;
        if (element.average_heartrate)
            stats.minimum_heartrate = stats.minimum_heartrate < element.average_heartrate ? stats.minimum_heartrate : element.average_heartrate;
        if (element.average_heartrate)
            stats.maximum_heartrate = stats.maximum_heartrate > element.average_heartrate ? stats.maximum_heartrate : element.average_heartrate;
        if (element.average_speed) stats.maximum_speed = stats.maximum_speed > element.average_speed ? stats.maximum_speed : element.average_speed;
        if (element.average_speed) stats.minimum_speed = stats.minimum_speed < element.average_speed ? stats.minimum_speed : element.average_speed;
        if (element.average_watts) stats.minimum_watts = stats.minimum_watts < element.average_watts ? stats.minimum_watts : element.average_watts;
        if (element.average_watts) stats.maximum_watts = stats.maximum_watts > element.average_watts ? stats.maximum_watts : element.average_watts;
        if (element.weighted_average_watts)
            stats.weighted_minimum_watts =
                stats.weighted_minimum_watts < element.weighted_average_watts ? stats.weighted_minimum_watts : element.weighted_average_watts;
        if (element.weighted_average_watts)
            stats.weighted_maximum_watts =
                stats.weighted_maximum_watts > element.weighted_average_watts ? stats.weighted_maximum_watts : element.weighted_average_watts;
        if (element.suffer_score)
            stats.minimum_relative_effort =
                stats.minimum_relative_effort < element.suffer_score ? stats.minimum_relative_effort : element.suffer_score;
        if (element.suffer_score)
            stats.maximum_relative_effort =
                stats.maximum_relative_effort > element.suffer_score ? stats.maximum_relative_effort : element.suffer_score;
    }
    stats.average_ascent = stats.ascent / stats.numberOfActivities;
    stats.average_distance = stats.distance / stats.numberOfActivities;
    stats.moving_time_as_string = convertMovingTimeToString(stats.moving_time);
    stats.average_moving_time = convertMovingTimeToString(stats.moving_time / stats.numberOfActivities);
    stats.minimum_moving_time_as_string = convertMovingTimeToString(stats.minimum_moving_time);
    stats.maximum_moving_time_as_string = convertMovingTimeToString(stats.maximum_moving_time);
    stats.average_cadence = stats.times_cadence_recorded > 0 ? Math.floor(stats.average_cadence / stats.times_cadence_recorded) : null;
    stats.average_heartrate = stats.times_heartrate_recorded > 0 ? Math.floor(stats.average_heartrate / stats.times_heartrate_recorded) : null;
    stats.average_pace = stats.average_speed == 0 ? null : ((stats.numberOfActivities / stats.average_speed) * 60).toFixed(2);
    stats.average_speed = Math.floor(stats.average_speed / stats.numberOfActivities);
    stats.average_watts = stats.times_watts_recorded > 0 ? Math.floor(stats.average_watts / stats.times_watts_recorded) : null;
    stats.weighted_average_watts =
        stats.weighted_times_watts_recorded > 0 ? Math.floor(stats.weighted_average_watts / stats.weighted_times_watts_recorded) : null;
    stats.average_relative_effort =
        stats.times_relative_effort_recorded > 0 ? Math.floor(stats.average_relative_effort / stats.times_relative_effort_recorded) : null;
    stats.minimum_pace = stats.minimum_speed == 0 ? null : (1 / stats.minimum_speed) * 60;
    stats.maximum_pace = stats.maximum_speed == 0 ? null : (1 / stats.maximum_speed) * 60;
    numberOfActivities.value = stats.numberOfActivities;
    return stats;
}

function fill_statsTableData(data: any) {
    const stats = calculate_stats(data);
    statsTableData.value = [];
    statsTableData.value.push({ col1: 'Number of activities', col2: '', col3: stats.numberOfActivities });
    statsTableData.value.push({ col1: 'Distance', col2: 'bold', col3: '' });
    if (stats.distance == 0) {
        statsTableData.value.push({ col1: 'total', col2: '', col3: null });
        statsTableData.value.push({ col1: 'average', col2: '', col3: null });
        statsTableData.value.push({ col1: 'minimum', col2: '', col3: null });
        statsTableData.value.push({ col1: 'maximum', col2: '', col3: null });
    } else {
        statsTableData.value.push({ col1: 'total', col2: '', col3: Math.round(stats.distance).toLocaleString() + ' km' });
        statsTableData.value.push({ col1: 'average', col2: '', col3: Math.round(stats.average_distance).toLocaleString() + ' km' });
        statsTableData.value.push({ col1: 'minimum', col2: '', col3: Math.round(stats.minimum_distance).toLocaleString() + ' km' });
        statsTableData.value.push({ col1: 'maximum', col2: '', col3: Math.round(stats.maximum_distance).toLocaleString() + ' km' });
    }
    statsTableData.value.push({ col1: 'Ascent', col2: 'bold', col3: '' });
    if (stats.ascent == 0) {
        statsTableData.value.push({ col1: 'total', col2: '', col3: null });
        statsTableData.value.push({ col1: 'average', col2: '', col3: null });
        statsTableData.value.push({ col1: 'minimum', col2: '', col3: null });
        statsTableData.value.push({ col1: 'maximum', col2: '', col3: null });
    } else {
        statsTableData.value.push({ col1: 'total', col2: '', col3: Math.round(stats.ascent).toLocaleString() + ' m' });
        statsTableData.value.push({ col1: 'average', col2: '', col3: Math.round(stats.average_ascent).toLocaleString() + ' m' });
        statsTableData.value.push({ col1: 'minimum', col2: '', col3: Math.round(stats.minimum_ascent).toLocaleString() + ' m' });
        statsTableData.value.push({ col1: 'maximum', col2: '', col3: Math.round(stats.maximum_ascent).toLocaleString() + ' m' });
    }
    statsTableData.value.push({ col1: 'Moving time', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'total', col2: '', col3: stats.moving_time_as_string });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_moving_time });
    statsTableData.value.push({ col1: 'minimum', col2: '', col3: stats.minimum_moving_time_as_string });
    statsTableData.value.push({ col1: 'maximum', col2: '', col3: stats.maximum_moving_time_as_string });
    statsTableData.value.push({ col1: 'Cadence', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_cadence });
    statsTableData.value.push({
        col1: 'minimum',
        col2: '',
        col3: Math.round(stats.minimum_cadence) > 1000 ? null : Math.round(stats.minimum_cadence).toLocaleString(),
    });
    statsTableData.value.push({
        col1: 'maximum',
        col2: '',
        col3: Math.round(stats.maximum_cadence) == 0 ? null : Math.round(stats.maximum_cadence).toLocaleString(),
    });
    statsTableData.value.push({ col1: 'Heartrate', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_heartrate });
    statsTableData.value.push({ col1: 'minimum', col2: '', col3: Math.round(stats.minimum_heartrate).toLocaleString() });
    statsTableData.value.push({ col1: 'maximum', col2: '', col3: Math.round(stats.maximum_heartrate).toLocaleString() });
    statsTableData.value.push({ col1: 'Watts', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_watts });
    statsTableData.value.push({
        col1: 'minimum',
        col2: '',
        col3: Math.round(stats.minimum_watts) > 1000 ? null : Math.round(stats.minimum_watts).toLocaleString(),
    });
    statsTableData.value.push({
        col1: 'maximum',
        col2: '',
        col3: Math.round(stats.maximum_watts) == 0 ? null : Math.round(stats.maximum_watts).toLocaleString(),
    });
    statsTableData.value.push({ col1: 'Weighted watts', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.weighted_average_watts });
    statsTableData.value.push({
        col1: 'minimum',
        col2: '',
        col3: Math.round(stats.weighted_minimum_watts) > 1000 ? null : Math.round(stats.weighted_minimum_watts).toLocaleString(),
    });
    statsTableData.value.push({
        col1: 'maximum',
        col2: '',
        col3: Math.round(stats.weighted_maximum_watts) == 0 ? null : Math.round(stats.weighted_maximum_watts).toLocaleString(),
    });
    statsTableData.value.push({ col1: 'Speed', col2: 'bold', col3: '' });
    if (stats.average_speed == 0) {
        statsTableData.value.push({ col1: 'average', col2: '', col3: null });
        statsTableData.value.push({ col1: 'slowest', col2: '', col3: null });
        statsTableData.value.push({ col1: 'fastest', col2: '', col3: null });
    } else {
        statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_speed + ' kph' });
        statsTableData.value.push({ col1: 'slowest', col2: '', col3: Math.round(stats.minimum_speed).toLocaleString() + ' kph' });
        statsTableData.value.push({ col1: 'fastest', col2: '', col3: Math.round(stats.maximum_speed).toLocaleString() + ' kph' });
    }
    statsTableData.value.push({ col1: 'Pace', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_pace ? stats.average_pace + ' mins/km' : null });
    statsTableData.value.push({ col1: 'slowest', col2: '', col3: stats.minimum_pace < 1000 ? stats.minimum_pace.toFixed(2) + ' mins/km' : null });
    statsTableData.value.push({ col1: 'fastest', col2: '', col3: stats.maximum_pace ? stats.maximum_pace.toFixed(2) + ' mins/km' : null });
    statsTableData.value.push({ col1: 'Relative Effort', col2: 'bold', col3: '' });
    statsTableData.value.push({ col1: 'average', col2: '', col3: stats.average_relative_effort });
    statsTableData.value.push({ col1: 'minimum', col2: '', col3: Math.round(stats.minimum_relative_effort).toLocaleString() });
    statsTableData.value.push({ col1: 'maximum', col2: '', col3: Math.round(stats.maximum_relative_effort).toLocaleString() });
}
</script>
<template>
    <div v-if="numberOfActivities > 0">
        <DataTable
            :value="statsTableData"
            size="small"
            stripedRows
            style="width: 40%"
            scrollable
            scrollHeight="580px"
            :loading="loading"
            class="ml-8"
        >
            <Column field="col1" header="Statistic" style="width: 50%">
                <template #body="{ data }">
                    <div v-if="data.col2 && data.col2 == 'bold'" class="flex items-center gap-2 font-bold">
                        {{ data.col1 }}
                    </div>
                    <div v-else>
                        {{ data.col1 }}
                    </div>
                </template>
            </Column>

            <Column field="col2" header="">
                <template #body=""> </template>
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
    <div v-else class="mt-8 text-center text-gray-500">
        No activities found. Please download some activities or change the filter to see statistics.
    </div>
</template>
