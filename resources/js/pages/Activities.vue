<script setup lang="ts">

import Layout from '@/layouts/my/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Filters from '@/components/my/Filters.vue';
// import { defineAsyncComponent, ref } from 'vue'
import { ref } from 'vue'

// const ActivitiesTable = defineAsyncComponent(() =>
//   import('@/components/my/ActivitiesTable.vue')
// )
// const StatsTable = defineAsyncComponent(() =>
//   import('@/components/my/StatsTable.vue')
// )
  import ActivitiesTable from '@/components/my/ActivitiesTable.vue'
import StatsTable from '@/components/my/StatsTable.vue';

const props = defineProps({
    autoUpdateActivities: {
        type: Boolean,
        default: true,
    },
})


const showStatsTable = ref(false)
const showActivitiesTable = ref(true)

const statsTableOn = () => {
    showActivitiesTable.value = false
    showStatsTable.value = true
}

const activitiesTableOn = () => {
    showStatsTable.value = false
    showActivitiesTable.value = true
}

</script>

<template>
    <Head title="Activities"></Head>

    <Layout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <div class="flex flex-row pb-8 justify-left items-center">
                    <Filters></Filters>
                    <Button class="ml-8 h-9" severity="info" @click="activitiesTableOn" label="Activities"></Button>
                    <Button class="ml-4 h-9" severity="info" @click="statsTableOn" label="Statistics"></Button>
                </div>
                <div v-show="showActivitiesTable" >
                    <ActivitiesTable :auto-update-activities=props.autoUpdateActivities></ActivitiesTable>
                </div>
                <div v-show="showStatsTable" class="pt-4">
                    <StatsTable v-if="showStatsTable" ></StatsTable>
                </div>
            </div>
        </div>
    </Layout>
</template>
