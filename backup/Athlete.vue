<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref, watch } from 'vue';
import { Table, TableCell, TableRow } from '@/components/ui/table'
import DownloadFromStravaWithDialog from '@/components/DownloadFromStravaWithDialog.vue';
import { getAthlete } from '@/functions/StrearchAPI.js'
import { strearch_data, debug } from '@/functions/Flags.js'
import Button from "primevue/button"

const props = defineProps({
});

watch(strearch_data, (newValue, oldValue) => {
    if (newValue.code == 10) {
        fill_fields(newValue.data)
    }
}) 

const athlete = ref({ first_name: 'Joe', last_name: 'Bloggs', updated_at: '1/1/1900', bio: 'a bio', location: 'somewhere',
    id: 0, profile_picture: '', sex: 'M', join_date: '1/1/2000', premium_member: false, username: 'uname', weight: '0', })

onMounted(() => {
    get_athlete()
})

const last_update_from_strava = computed(() => {
    return convert2LocaleString(athlete.value.updated_at)
})

function convert2LocaleString(dateToConvert: any) {
    const dj = new Date(dateToConvert)
    return dj.toLocaleString()
}

function get_athlete() {
    getAthlete()
}

function fill_fields(data: any) {
    athlete.value.first_name = data.athlete.first_name
    athlete.value.last_name = data.athlete.last_name
    athlete.value.updated_at = data.date_of_last_athlete_strava_update
    athlete.value.id = data.athlete.id
    athlete.value.username = data.athlete.username
    athlete.value.bio = data.athlete.bio
    athlete.value.premium_member = data.athlete.summit == 1
    athlete.value.join_date = data.athlete.strava_created_at
    athlete.value.weight = data.athlete.weight + ' kg'
    athlete.value.location = data.athlete.city + ' ' + data.athlete.state + ' ' + data.athlete.country
    athlete.value.profile_picture = data.athlete.profile_picture_large
}

</script>

<template>
    <Head title="Athlete Details" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4  bg-gradient-to-r from-cyan-200 to-blue-300">
            <div class="px-8 py-4 relative min-h-[100vh] flex-1 rounded-xl md:min-h-min">
                <p class="md:px-32 py-8 w-full text-xl font-semibold tracking-tight">Athlete Details</p>

                <Table>
                    <TableRow>
                        <TableCell class="">Current athlete</TableCell>
                        <TableCell></TableCell>
                        <TableCell>{{ athlete.first_name }} {{ athlete.last_name}}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Strava ID</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.id }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Username</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.username }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Bio</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.bio }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Premium member</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.premium_member }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Member since</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.join_date }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Weight</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.weight }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Profile picture</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell><img :src="athlete.profile_picture" /></TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Location</TableCell>
                        <TableCell>
                        </TableCell>
                        <TableCell>{{ athlete.location }}</TableCell>
                    </TableRow>
                    <TableRow>
                        <TableCell>Date of last download</TableCell>
                        <TableCell>
                            <DownloadFromStravaWithDialog @newdownload="get_athlete" download-what="athlete"></DownloadFromStravaWithDialog>
                        </TableCell>
                        <TableCell>{{ athlete.updated_at }}</TableCell>
                    </TableRow>
                </Table>
            </div>
        </div>
    </AppLayout>
</template>
