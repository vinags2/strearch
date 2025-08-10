<script setup lang="ts">
import ActivityCharts from '@/components/my/ActivityCharts.vue';
import Filters from '@/components/my/Filters.vue';
import Layout from '@/layouts/my/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ref } from 'vue';

import ActivitiesTable from '@/components/my/ActivitiesTable.vue';
import StatsTable from '@/components/my/StatsTable.vue';

const props = defineProps({
    autoUpdateActivities: {
        type: Boolean,
        default: true,
    },
    showViewInStravaAsText: {
        type: Boolean,
        default: false,
    },
});

const showStatsTable = ref(false);
const showActivitiesTable = ref(true);
const showCharts = ref(false);

function showRightComponent(component: string = 'ActivitiesTable') {
    showActivitiesTable.value = component == 'ActivitiesTable';
    showStatsTable.value = component == 'StatsTable';
    showCharts.value = component == 'ChartsTable';
}
</script>

<template>
    <Head title="Activities"></Head>

    <Layout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="relative min-h-[100vh] flex-1 rounded-xl py-4 md:min-h-min">
                <div class="justify-left flex flex-row items-center pb-8">
                    <Filters></Filters>
                    <Button class="ml-8 h-9" severity="info" @click="showRightComponent('ActivitiesTable')" label="Activities"></Button>
                    <Button class="ml-4 h-9" severity="info" @click="showRightComponent('StatsTable')" label="Statistics"></Button>
                    <Button class="ml-4 h-9" severity="info" @click="showRightComponent('ChartsTable')" label="Charts"></Button>
                </div>
                <div v-show="showActivitiesTable">
                    <ActivitiesTable
                        :auto-update-activities="props.autoUpdateActivities"
                        :show-view-in-strava-as-text="props.showViewInStravaAsText"
                    ></ActivitiesTable>
                </div>
                <div v-show="showStatsTable" class="pt-4">
                    <StatsTable v-if="showStatsTable"></StatsTable>
                </div>
                <div v-show="showCharts" class="pt-4">
                    <ActivityCharts v-if="showCharts"></ActivityCharts>
                </div>
            </div>
        </div>
    </Layout>
</template>
