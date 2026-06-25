<script setup lang="ts">
import { ref } from 'vue';

import { useStrearchData } from '@/stores/StrearchStore';
import { storeToRefs } from 'pinia';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';

const props = defineProps({
    activityTitle: {
        type: String,
        default: 'Activity Segments',
    },
    activityId: {
        type: Number,
        default: 0,
    },
});

const strearchData = useStrearchData();
const { segmentEfforts, loading: loading } = storeToRefs(useStrearchData());

strearchData.getSegmentEffortsForActivity(props.activityId);

const currentTitle = ref(props.activityTitle);
</script>
<template>
    <DataTable
        :value="segmentEfforts"
        :loading="loading"
        filterDisplay="menu"
        stripedRows
        scrollable
        scrollHeight="550px"
        :virtualScrollerOptions="{ itemSize: 44 }"
        ref="dt"
    >
        <template #empty> No segments found for this activity. </template>
        <template #loading> Loading segment data. Please wait. </template>

        <Column v-if="false" field="start_date_as_timestamp" sortable header="Date" frozen></Column>

        <Column field="start_time" header="Time" sortable frozen> </Column>
        <Column field="name" header="Title" sortable frozen> </Column>
        <Column field="moving_time_as_string" sortable header="Moving time" style="text-align: right; min-width: 100px"></Column>

        <Column field="distance" header="Distance" filterField="distance" dataType="numeric" sortable style="text-align: right; min-width: 100px">
        </Column>
        <Column
            field="average_cadence"
            filterField="average_cadence"
            dataType="numeric"
            header="Avg cadence"
            sortable
            style="text-align: right; min-width: 100px"
        >
        </Column>

        <Column
            field="average_watts"
            filterField="average_watts"
            dataType="numeric"
            header="Avg watts"
            sortable
            style="text-align: right; min-width: 100px"
        >
        </Column>

        <Column
            field="average_heartrate"
            filterField="average_heartrate"
            dataType="numeric"
            header="Avg HR"
            sortable
            style="text-align: right; min-width: 100px"
        >
        </Column>

        <Column
            field="max_heartrate"
            filterField="max_heartrate"
            dataType="numeric"
            header="Max HR"
            sortable
            style="text-align: right; min-width: 100px"
        >
        </Column>
        <Column field="pr_rank" filterField="pr_rank" dataType="numeric" header="PR Rank" sortable style="text-align: right; min-width: 100px">
        </Column>
    </DataTable>
</template>
