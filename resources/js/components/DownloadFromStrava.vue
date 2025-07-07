<script setup lang="ts">
import { ref, computed, watch, defineEmits, onMounted, defineAsyncComponent} from 'vue';
import { getStravaData } from '@/functions/StravaAPI.js'
import { strava_data } from '@/functions/Flags.js'
import Button from 'primevue/button'
import { Download } from 'lucide-vue-next';

// const Dialog = defineAsyncComponent(() =>
//   import('primevue/dialog')
// )
  import Dialog from 'primevue/dialog'

const props = defineProps({
    downloadWhat: {
        type: String,
        default: 'activities',
    },
    withDialog: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits<{
    newdownload: any
}>()

const whatToDownload = computed(() => {
    if (props.downloadWhat == 'activities') {
        return 'Activities'
    } else if (props.downloadWhat == 'athlete') {
        return 'Athlete'
    } else {
        return 'All Activities'
    }
})

const showClose = ref(false)
const showAlert = ref(false)
const textForAlertDescription = ref('Please wait while the informaton from Strava is being downloaded')
const textForAlertHeader = ref('Downloading ' + whatToDownload.value + ' information')
const tip = ref('Update ' + whatToDownload.value + ' data')


watch(strava_data, (newValue) => {
    if (newValue.code == 3 || newValue.code ==  4) {
        showSuccessBeforeClosing(false)
    } else if (newValue.code == 6 || newValue.code == 10) {
        showSuccessBeforeClosing(true)
    }
}) 

function showDialog() {
    textForAlertDescription.value = 'Please wait while the informaton from Strava is being downloaded'
    textForAlertHeader.value = 'Downloading ' + whatToDownload.value + ' information'
    showClose.value = false
    showAlert.value = props.withDialog
    getStravaData(props.downloadWhat)
}

function showSuccessBeforeClosing(success: boolean = true) {
    if (success) {
        textForAlertHeader.value = 'Information downloaded successfully'
        textForAlertDescription.value = 'The informaton from Strava has being downloaded successfully'
        emit('newdownload', 1);
    } else {
        textForAlertHeader.value = 'Download FAILED'
        textForAlertDescription.value = 'Report the issue to the developer'
    }
    showClose.value = true;
    // setTimeout(function () {
    //     showAlert.value = false;
    // }, 3000);
}

onMounted(() => {
    if (!props.withDialog) showDialog()
})

</script>
<template>
    <Button @click="showDialog" v-if="withDialog" style="background-color: transparent; border:none;"
        v-tooltip.top="{
            value: tip,
            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' }
        }"
    >
        <Download color="blue" :size=16 />
    </Button>
    <Dialog v-model:visible="showAlert" modal :closable="false" :header="textForAlertHeader">
        {{ textForAlertDescription }}
        <div v-if="!showClose">
            <div class="px-3 py-1 text-xs font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full
                animate-pulse dark:bg-blue-900 dark:text-blue-200">downloading...</div>
            <div class="py-3 font-small">There is a limit of 4000 activities per download</div>
        </div>
        <div v-if="showClose" class="mt-2">
            <Button @click="showAlert=false" size="small" severity="info">Continue</Button>
        </div>
    </Dialog>
</template>