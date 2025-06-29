<script setup lang="ts">

import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { defineAsyncComponent, ref } from 'vue'
const ActivitiesTable = defineAsyncComponent(() =>
  import('@/components/my/ActivitiesTable.vue')
)
const StatsTable = defineAsyncComponent(() =>
  import('@/components/my/StatsTable.vue')
)

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';

import Filters from '@/components/my/Filters.vue';

const showStatsTable = ref(false)

</script>

<template>
    <Head title="Activities" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <div class="flex pb-8">
                    <Filters></Filters>
                </div>
                <Tabs value="0" unstyled >
                    <TabList class="flex flex-row">
                        <Tab value="0" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Activities</Tab>
                        <Tab value="3" disabled>&nbsp;&nbsp;</Tab>
                        <Tab value="1" @click="showStatsTable = true" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Statistics</Tab>
                        <Tab value="3" disabled>&nbsp;&nbsp;</Tab>
                        <Tab value="2" class="px-2 py-1 bg-blue-400 rounded-md text-white decoration-double">Charts</Tab>
                    </TabList>
                    <TabPanels>
                        <TabPanel value="0">
                            <ActivitiesTable></ActivitiesTable>
                        </TabPanel>
                        <TabPanel  value="1">
                            <div class="pt-4"> <StatsTable v-if="showStatsTable" ></StatsTable> </div>
                        </TabPanel>
                        <TabPanel value="2">
                            <div class="mt-8">Charts and Analyses will go here.</div>
                        </TabPanel>
                    </TabPanels>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
