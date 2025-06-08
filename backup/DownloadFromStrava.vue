<script setup>
import AuthenticatedLayoutNoAthlete from '@/Layouts/AuthenticatedLayoutNoAthlete.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, watch, ref } from 'vue';
import { getStravaData } from '@/Functions/StravaAPI.js'
import { strava_data } from '@/Functions/Flags.js'

const props = defineProps({
    athlete: {
        type: [Object, Boolean],
        default: [],
    },
    client_id: {
        type: String,
        default: 'no client id'
    },
    client_secret: {
        type: String,
        default: 'no client secret'
    },
    refresh_token: {
        type: String,
        default: 'no refresh token'
    },
    url: {
        type: String,
        default: 'https://dummy.com'
    },
});

onMounted(() => {
    get_athlete_from_database()
});

const date_joined = ref('')
const last_update_from_strava = ref('')
const weight_as_string = ref('')
var url_for_token_save = route('token.save', 1)
const download_text = ref({line1:'You need to download your athlete profile from Strava', line2:"Click 'Download' to continue", show_download:true})

watch(strava_data, (newValue, oldValue) => {
    if (newValue.code == 2) {
        download_text.value.line1 = 'There was an error retrieving the athlete profile from Strava.'
        download_text.value.line2 = 'Contact the developer of strearch to configure the Strava authorization codes correctly.'
        download_text.value.show_download = false
    } else if (newValue.code == 1) {
        console.log('athete data retrieved from Strava successfully')
    } else if (newValue.code == 4) {
        download_text.value.line1 = 'There is a problem trying to access Strava.'
        download_text.value.line2 = 'Contact the developer of strearch to investigate the issue.'
        download_text.value.show_download = false
    }
}) 

function prepare_athlete_for_display() {
    props.athlete.profile_medium = props.athlete.profile_picture_large
    display_created_at_date(props.athlete.created_at)
    display_weight_as_string(props.athlete.weight)
    display_updated_at_date(props.athlete.updated_at)
}

function get_athlete_from_database() {
    if (props.athlete) {
        prepare_athlete_for_display()
        console.log('athete data retrieved from database successfully')
        return true
    } else {
        console.log('No athlete data found in the database')
        return false
    }
}

function get_athlete_from_Strava() {
    getStravaData(url_for_token_save, props.client_id, props.client_secret, props.refresh_token, props.url)
}

function display_created_at_date(data) {
    const dj = new Date(data)
    date_joined.value = dj.toDateString()
}

function display_updated_at_date(data) {
    const dj = new Date(data)
    last_update_from_strava.value = dj.toLocaleString()
}

function display_weight_as_string(data) {
    weight_as_string.value = data + ' kg'
}

</script>

<template>
    <AuthenticatedLayout v-if="props.athlete.id">
        <div class="md:px-32 py-8 w-full">
            <div class="shadow overflow-hidden rounded border-b border-gray-200">
                <table class="bg-white">
                <thead class="bg-blue-800 text-white">
                    <tr>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Item</th>
                    <th class="w-1/3 text-left py-3 px-4 uppercase font-semibold text-sm">Value</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <tr>
                    <td class="w-1/4 text-left py-3 px-4">Strava ID</td>
                    <td class="w-1/3 text-left py-3 px-4">{{  props.athlete.id }}</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="w-1/4 text-left py-3 px-4">Username</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.username }}</td>
                </tr>
                <tr>
                    <td class="w-1/4 text-left py-3 px-4">First name</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.first_name }}</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="w-1/4 text-left py-3 px-4">Last name</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.last_name }}</td>
                </tr>
                <tr>
                    <td class="w-1/4 text-left py-3 px-4">Bio</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.bio }}</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="w-1/4 text-left py-3 px-4">Premium member</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.summit == 1 }}</td>
                </tr>
                <tr>
                    <td class="w-1/4 text-left py-3 px-4">Member since</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ date_joined }}</td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="w-1/4 text-left py-3 px-4">Weight</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ weight_as_string }}</td>
                </tr>
                <tr>
                    <td class="w-1/4 text-left py-3 px-4">Profile picture</td>
                    <td class="w-1/3 text-left py-3 px-4"><img :src="props.athlete.profile_medium" /></td>
                </tr>
                <tr class="bg-blue-100">
                    <td class="w-1/4 text-left py-3 px-4">Location</td>
                    <td class="w-1/3 text-left py-3 px-4">{{ props.athlete.city }} {{ props.athlete.state }} {{ props.athlete.country }}</td>
                </tr>
                </tbody>
                </table>
            </div>
            <div>
                <PrimaryButton @click="get_athlete_from_Strava()" class="bg-blue-800 mt-6 ml-3">Refresh</PrimaryButton>
                <span class="ml-6 italic">Last updated from Strava: {{ last_update_from_strava }}</span>
            </div>
        </div>
    </AuthenticatedLayout>
    <AuthenticatedLayoutNoAthlete v-else>
        <div class="grid h-screen place-items-center">
            <div class="p-8 border-solid border-2 border-indigo-600 text-center">
                <div class="font-bold capitalize">
                    {{ download_text.line1 }}
                </div>
                <div class="font-bold capitalize">
                    {{ download_text.line2 }}
                </div>
            <div>
                <PrimaryButton @click="get_athlete_from_Strava()" class="bg-blue-800 mt-6 ml-3" v-show="download_text.show_download">Download</PrimaryButton>
            </div>
            </div>
        </div>
    </AuthenticatedLayoutNoAthlete>
</template>
