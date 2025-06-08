<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';
import { getAthlete } from '@/functions/StrearchAPI.js'
import { strearch_data } from '@/functions/Flags.js'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';

const props = defineProps({
});

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 10) {
        fill_fields(newValue.data)
    }
}) 

const tableData = ref<any>([])

onMounted(() => {
    get_athlete()
})

function get_athlete() {
    getAthlete()
}

function fill_fields(data: any) {
    tableData.value = []
    tableData.value.push({col1: 'Current athlete', col2:'', col3: data.athlete.first_name + ' ' + data.athlete.last_name})
    tableData.value.push({col1: 'Strava ID', col2:'', col3: data.athlete.id})
    tableData.value.push({col1: 'Username', col2:'', col3: data.athlete.username})
    tableData.value.push({col1: 'Bio', col2:'', col3: data.athlete.bio})
    tableData.value.push({col1: 'Premium member', col2:'', col3: data.athlete.summit == 1})
    tableData.value.push({col1: 'Member since', col2:'', col3: data.athlete.strava_created_at})
    tableData.value.push({col1: 'Weight', col2:'', col3: data.athlete.weight + ' kg'})
    tableData.value.push({col1: 'Profile picture', col2:'image', col3: data.athlete.profile_picture_large})
    tableData.value.push({col1: 'Location', col2:'', col3: data.athlete.city + ' ' + data.athlete.state + ' ' + data.athlete.country})
    tableData.value.push({col1: 'Date of last download', col2:'athlete', col3: data.date_of_last_athlete_strava_update})
}

</script>

<template>
    <Head title="Athlete Details" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                    <p class="md:px-32 py-8 w-full text-xl font-semibold tracking-tight">Athlete Details</p>

                <DataTable :value="tableData" size="small" stripedRows style="width: 50%"
                    :pt="{
                        thead: { style: 'display: none' },
                        bodyRow: { class: '!bg-cyan-100' },
                    }"
                >
                    <Column field="col1" header="Code"></Column>
                    <Column field="col2" header="Download">
                        <template #body="{ data }">
                            <div v-if="data.col2 && (data.col2 != 'image')" class="flex items-center gap-2">
                            <DownloadFromStravaWithDialog @newdownload="get_athlete" :download-what="data.col2"></DownloadFromStravaWithDialog>
                            </div>
                        </template>
                    </Column>
                    <Column field="col3" header="Name" >
                        <template #body="{ data }">
                            <div v-if="data.col2 && (data.col2 == 'image')" class="flex items-center gap-2">
                                <img :src="data.col3"/>
                            </div>
                            <div v-else>{{ data.col3 }}</div>
                        </template>
                    </Column>
                </DataTable>


            </div>
        </div>
    </AppLayout>
</template>
