<script setup lang="ts">
import { ref } from 'vue';

import { useStrearchData } from '@/stores/StrearchStore';
import { ChartNoAxesCombined } from 'lucide-vue-next';
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

// strearchData.getSegmentEffortsForActivity(props.activityId);

const currentTitle = ref('');
const column3Header = ref('');
const column3Field = ref('');
const column4Field = ref('');
const showColumn1 = ref(true);

function showSegmentsForActivity(flag: boolean, segmentId: number | null = null, segmentName: string | null = null) {
    if (flag) {
        currentTitle.value = props.activityTitle;
        strearchData.getSegmentEffortsForActivity(props.activityId);
        column3Header.value = 'Time';
        column3Field.value = 'start_time';
        column4Field.value = 'name';
        showColumn1.value = true;
    } else {
        currentTitle.value = 'All efforts for the segment "' + segmentName + '"';
        strearchData.getAllEffortsForASegment(segmentId);
        column3Header.value = 'Date';
        column3Field.value = 'formatted_start_date_local';
        column4Field.value = 'activity_name';
        showColumn1.value = false;
        // strearchData.segmentEfforts = [];
    }
}

showSegmentsForActivity(true);
</script>
<template>
    <div class="">
        <p class="w-full py-6 text-xl font-semibold tracking-tight md:px-32">{{ currentTitle }}</p>
    </div>
    <DataTable
        :value="segmentEfforts"
        :loading="loading"
        filterDisplay="menu"
        stripedRows
        scrollable
        scrollHeight="400px"
        :virtualScrollerOptions="{ itemSize: 44 }"
        ref="dt"
    >
        <template #empty> No segments found for this activity. </template>
        <template #loading> Loading segment data. Please wait. </template>

        <Column v-if="false" field="start_date_as_timestamp" sortable header="Date" frozen></Column>
        <Column v-if="showColumn1" frozen style="min-width: 60px">
            <template #body="{ data }">
                <div class="">
                    <div
                        class="text-sm text-[#FC5200]"
                        v-tooltip.top="{
                            value: 'View all efforts for this segment',
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <a
                            @click="showSegmentsForActivity(false, data.segment_id, data.name)"
                            style="background-color: transparent; border: none"
                            class="cursor-pointer"
                        >
                            <ChartNoAxesCombined color="blue" stroke-width="1" :size="20"></ChartNoAxesCombined>
                        </a>
                    </div>
                </div>
            </template>
        </Column>

        <Column :field="column3Field" :header="column3Header" sortable frozen> </Column>
        <Column :field="column4Field" header="Title" sortable frozen> </Column>
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
