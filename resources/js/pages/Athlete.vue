<script setup lang="ts">
import DownloadFromStrava from '@/components/DownloadFromStrava.vue';
import { API_data } from '@/functions/Flags.js';
import { getAthlete } from '@/functions/StrearchAPI.js';
import Layout from '@/layouts/my/Layout.vue';
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { onMounted, ref, watch } from 'vue';

watch(API_data, (newValue) => {
    if (newValue.code == 11) {
        fill_fields(newValue.data.data);
    }
});

const tableData = ref<any>([]);
const loading = ref(true);

onMounted(() => {
    get_athlete();
});

function get_athlete() {
    loading.value = true;
    getAthlete();
}

function fill_fields(data: any) {
    tableData.value = [];
    tableData.value.push({ col1: 'Current athlete', col2: '', col3: data.athlete.first_name + ' ' + data.athlete.last_name });
    tableData.value.push({ col1: 'Strava ID', col2: '', col3: data.athlete.id });
    tableData.value.push({ col1: 'Username', col2: '', col3: data.athlete.username });
    tableData.value.push({ col1: 'Bio', col2: '', col3: data.athlete.bio });
    tableData.value.push({ col1: 'Premium member', col2: '', col3: data.athlete.summit == 1 });
    tableData.value.push({ col1: 'Member since', col2: '', col3: data.athlete.strava_created_at });
    tableData.value.push({ col1: 'Weight', col2: '', col3: data.athlete.weight + ' kg' });
    tableData.value.push({ col1: 'Profile picture', col2: 'image', col3: data.athlete.profile_picture_large });
    tableData.value.push({ col1: 'Location', col2: '', col3: data.athlete.city + ' ' + data.athlete.state + ' ' + data.athlete.country });
    tableData.value.push({ col1: 'Date of last download', col2: 'athlete', col3: data.date_of_last_athlete_strava_update });
    loading.value = false;
}
</script>

<template>
    <Head title="Athlete Details" />

    <Layout>
        <!-- <div class="flex-col  bg-gradient-to-r from-cyan-200 to-blue-300 flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300"> -->
        <!-- <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min"> -->
        <div v-if="tableData[1]?.col3 == null" class="mt-8 text-center text-gray-500">
            <p>No athlete data found. Please download your athlete data from Strava.</p>
            <DownloadFromStrava @newdownload="get_athlete" download-what="athlete"></DownloadFromStrava>
        </div>
        <div v-else class="px-8">
            <p class="w-full py-8 text-xl font-semibold tracking-tight md:px-32">Athlete Details</p>

            <DataTable
                :value="tableData"
                :loading="loading"
                size="small"
                stripedRows
                style="width: 50%"
                :pt="{
                    thead: { style: 'display: none' },
                }"
            >
                <Column field="col1" header="Code"></Column>
                <Column field="col2" header="Download">
                    <template #body="{ data }">
                        <div v-if="data.col2 && data.col2 != 'image'" class="flex items-center gap-2">
                            <DownloadFromStrava @newdownload="get_athlete" :download-what="data.col2"></DownloadFromStrava>
                        </div>
                    </template>
                </Column>
                <Column field="col3" header="Name">
                    <template #body="{ data }">
                        <div v-if="data.col2 && data.col2 == 'image'" class="flex items-center gap-2">
                            <img :src="data.col3" />
                        </div>
                        <div v-else>{{ data.col3 }}</div>
                    </template>
                </Column>
            </DataTable>
        </div>
        <!-- </div> -->
    </Layout>
</template>
