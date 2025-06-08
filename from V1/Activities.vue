<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, watch, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { getDataFromStrava, strava_data} from '@/Functions/StravaAPI.js'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
    activities: {
        type: [Object, Boolean],
        default: [],
    },
    filters: {
        type: [Object, Boolean],
        default: [],
    },
    // access_token: {
    //     type: String,
    //     default: 'no access token saved'
    // },
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
    filter_parameter: {
        type: Boolean,
        default: false
    },
    date_of_last_strava_update: {
        type: [Number, Boolean],
        default: 0
    },
    sorted_by: {
        type: String,
        default: 'hghghghgh',
    },
    sorted_by_order: {
        type: String,
        default: 'asc',
    }
});

const form = useForm({
    selected: 16,
})

onMounted(() => {
    get_activities_from_database()
    // last_update_from_strava.value = props.date_of_last_strava_update
    display_readable_date()
    get_active_filter()
});

var activities = ref(props.activities)
const response = ref('')
const last_update_from_strava = ref('unknown')
const showWhat = ref('activities')
const showProgress = ref(true)
const pageNo = ref(0)
var params = ''
const access_token = ref(props.access_token)
var url_for_token_save = route('token.save', 1)
const readable_last_save = ref(null)
const download_text = ref({line1:'No activities have been downloaded from Strava.', line2:"Click 'Download' to download your activities", show_download:true})
const heading_classes = {
    'normal' : "w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm",
    'sorted_by_asc': "w-1/4 text-left py-3 px-4 uppercase bg-blue-400 font-semibold text-sm",
    'sorted_by_desc': "w-1/4 text-left py-3 px-4 uppercase bg-blue-600 font-semibold text-sm"
}

watch(strava_data, (newValue, oldValue) => {
    if (newValue.code == 1) {
        if ((newValue.data === null) || (newValue.data.length == 0)) {
            get_activities_from_database_using_API()
            showWhat.value = 'activities'
        } else {
            save_activities(newValue.data)
            strava_data.value = {'code': 2, 'data': null}
            getDataFromStrava(url_for_token_save, access_token, props.client_id, props.client_secret, props.refresh_token, props.url, params + pageNo.value++)
        }
    } else {
        console.log('retrieving data from Strava, page number = ', pageNo.value-1)
    }
})

function get_active_filter() {
    let filter = props.filters.filter((filter) => filter.active === 1)
    form.selected = filter[0]?.id ?? 0
}

function remove_access_token_from_url() {
    const pos_of_last_slash = url_for_token_save.lastIndexOf('/')
    url_for_token_save = url_for_token_save.substring(0,pos_of_last_slash+1)
}

function display_readable_date() {
    readable_last_save.value = new Date((last_update_from_strava.value + 11*60*60) * 1000)
    readable_last_save.value = readable_last_save.value.toLocaleString()
}

function get_activities_from_database_using_API() {
    window.location.reload();
}

function get_activities_from_database() {
    response.value = activities.value
    if (activities.value) {
        console.log('activities data retrieved from database successfully')
        return true
    } else {
        console.log('activities data retrieved from strava successfully')
        return false
    }
}

function get_activities_from_Strava(all = true) {
    showWhat.value = 'progress'
    pageNo.value = 1
    strava_data.value = {'code': 2, 'data': null}
    if (all) {
        params = 'per_page=200&page='
        getDataFromStrava(url_for_token_save, access_token, props.client_id, props.client_secret, props.refresh_token, props.url, params + pageNo.value++)
    } else {
        params = 'after='+last_update_from_strava.value+'&per_page=200&page='
        getDataFromStrava(url_for_token_save, access_token, props.client_id, props.client_secret, props.refresh_token, props.url, params + pageNo.value++)
    }
}

function convert_strava_distance(distance) {
    const new_distance = distance / 1000;
    return round_number(new_distance,2)
}

function convert_strava_speed(speed) {
    const new_speed = speed * 3.6;
    return round_number(new_speed,1)
}

function round_number(num, decimal_places) {
    if (num == null) return num
    if (typeof num !== 'undefined') {
        const factor = 10 ** (decimal_places)
        return Math.round((num + Number.EPSILON)*factor)/factor
    } else {
        return ''
    }
}

function convert_strava_time(time_in_seconds) {
    var d = Number(time_in_seconds);
    var h = Math.floor(d / 3600);
    var m = Math.floor(d % 3600 / 60);
    var s = Math.floor(d % 3600 % 60);

    var hDisplay = h;
    var mDisplay = m <= 9 ? "0" + m : m;
    var sDisplay = s <= 9 ? "0" + s : s;
    return hDisplay + ":" + mDisplay + ':' + sDisplay; 
}

function display_date(data) {
    const dj = new Date(data)
    return dj.toDateString()
}

function save_activities(data) {
    axios
        .post(route('activity.save'), data)
        .then ((response) => {
            console.log('activities data saved')
        })
        .catch((error) => {
            console.log('You are not logged on, and may not save the data')
        })
}

function activity_url(id) {
    return "https://www.strava.com/activities/" + id
}

function createSortUrl(sort) {
    return props.filter_parameter ? route('activities', [1]) + "?sort="+sort : route('activities') + "?sort="+sort
}

const submit = () => {
  form.get(route('activities.newfilter'), {
    selected: form.selected,
  });
};

</script>

<template>
    <AuthenticatedLayout>
        <div v-if="showWhat == 'activities'" class="md:px-32 py-8 w-full">
            <div class="shadow overflow-hidden rounded border-b border-gray-200">
                <form>
                    <label class="ml-2">Select the filter to activate:
                        <select  @change="submit" id="filternames" v-model="form.selected" class="h-10 mb-6 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-1/3 h-9 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="0" >No filter</option>
                            <option v-for="filter in props.filters" :value="filter.id" :selected="filter.id == form.selected">{{ filter.name }}</option>
                        </select>
                    </label>
                </form>
                <table class="bg-white">
                <thead class="bg-blue-800 text-white">
                    <tr>
                    <th :class="props.sorted_by == 'start_date_local' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('start_date_local')">Date</Link></button></th>
                    <th :class="props.sorted_by == 'name' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('name')">Title</Link></button></th>
                    <th :class="props.sorted_by == 'moving_time' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('moving_time')">Moving time</Link></button></th>
                    <th :class="props.sorted_by == 'average_speed' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('average_speed')">Avg Speed</Link></button></th>
                    <th :class="props.sorted_by == 'distance' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('distance')">Distance</Link></button></th>
                    <th :class="props.sorted_by == 'total_elevation_gain' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('total_elevation_gain')">Elevation</Link></button></th>
                    <th :class="props.sorted_by == 'average_cadence' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('average_cadence')">Avg Cadence</Link></button></th>
                    <th :class="props.sorted_by == 'average_watts' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('average_watts')">Avg Watts</Link></button></th>
                    <th :class="props.sorted_by == 'average_heartrate' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('average_heartrate')">Avg HR</Link></button></th>
                    <th :class="props.sorted_by == 'max_heartrate' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('max_heartrate')">Max HR</Link></button></th>
                    <th :class="props.sorted_by == 'suffer_score' ? (props.sorted_by_order == 'asc' ? heading_classes.sorted_by_asc : heading_classes.sorted_by_desc) : heading_classes.normal"><button><Link :href="createSortUrl('suffer_score')">Relative effort</Link></button></th>
                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm"></th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                <tr v-for="item of response.data" :key="item.id" class="odd:bg-white even:bg-blue-50 hover:bg-yellow-50">
                    <td class="w-1/4 text-left py-3 px-4">{{ display_date(item.start_date_local) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ item.name }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ convert_strava_time(item.moving_time) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ convert_strava_speed(item.average_speed) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ convert_strava_distance(item.distance) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.total_elevation_gain,1) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.average_cadence,0) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.average_watts,0) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.average_heartrate,0) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.max_heartrate,0) }}</td>
                    <td class="w-1/4 text-left py-3 px-4">{{ round_number(item.suffer_score,0) }}</td>
                    <td class="w-1/4 text-left py-3 px-4"><PrimaryButton class="bg-blue-800"><a target="_blank" :href="activity_url(item.id)">View</a></PrimaryButton></td>
                </tr>
                </tbody>
                </table>
                <pagination class="mt-6" :links="activities.links" />
            </div>
            <div>
                <PrimaryButton @click="get_activities_from_Strava(true)" class="bg-blue-800 mt-6 ml-3">Refresh All</PrimaryButton>
                <PrimaryButton @click="get_activities_from_Strava(false)" class="bg-blue-800 mt-6 ml-3">Refresh Latest</PrimaryButton>
                <span class="ml-6 mt-6 italic">Last updated from Strava: {{ readable_last_save }}</span>
            </div>
        </div>
        <div v-else-if="showWhat == 'progress'" class="grid h-screen place-items-center">
            <div class="p-8 border-solid border-2 border-indigo-600 text-center">
                <div class="font-bold capitalize">
                    retrieving records from Strava
                </div>
                <div class="mt-6">
                    Please Wait...depending on the number of activities, this could take several minutes
                </div>
                <div class="mt-6">
                    Retrieving page {{ pageNo - 1 }}
                </div>
            </div>
        </div>
        <div v-else class="grid h-screen place-items-center">
            <div class="p-8 border-solid border-2 border-indigo-600 text-center">
                <div class="font-bold capitalize">
                    {{ download_text.line1 }}
                </div>
                <div class="font-bold capitalize">
                    {{ download_text.line2 }}
                </div>
            <div>
                <PrimaryButton @click="get_activities_from_Strava()" class="bg-blue-800 mt-6 ml-3" v-show="download_text.show_download">Download</PrimaryButton>
            </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
