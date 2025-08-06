<script setup lang="ts">
import { API_data, error_message } from '@/functions/Flags.js';
import { getStravaData } from '@/functions/StravaAPI.js';
import { Download } from 'lucide-vue-next';
import Button from 'primevue/button';
import { computed, defineEmits, onMounted, ref, watch } from 'vue';

import Dialog from 'primevue/dialog';

const props = defineProps({
    downloadWhat: {
        type: String,
        default: 'activities',
    },
    withDialog: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits<{
    newdownload: any;
}>();

const whatToDownload = computed(() => {
    if (props.downloadWhat == 'activities') {
        return 'Activities';
    } else if (props.downloadWhat == 'athlete') {
        return 'Athlete';
    } else {
        return 'All Activities';
    }
});

const showClose = ref(false);
const showAlert = ref(false);
const showError = ref(false);
const textForAlertDescription = ref('Please wait while the informaton from Strava is being downloaded');
const textForAlertHeader = ref('Downloading ' + whatToDownload.value + ' information');
const tip = ref('Update ' + whatToDownload.value + ' data');
// let errorShown: boolean = false;

watch(API_data, (newValue) => {
    if ([1, 3, 5, 10].includes(newValue.code)) {
        API_data.value = { code: 0, data: ['Resetting API_data values.'], error: false };
        showSuccessBeforeClosing(newValue.code, newValue.error == false);
    }
});

function showDialog() {
    textForAlertDescription.value = 'Please wait while the informaton from Strava is being downloaded';
    textForAlertHeader.value = 'Downloading ' + whatToDownload.value + ' information';
    showClose.value = false;
    // errorShown = false;
    showAlert.value = props.withDialog;
    getStravaData(props.downloadWhat);
}

function showSuccessBeforeClosing(flag: number, success: boolean = true) {
    // if (errorShown) {
    //     return;
    // } else {
    //     errorShown = true;
    // }
    // if (!useStrearchData().showAnotherErrorMessage) {
    //     return;
    // }
    if (success) {
        textForAlertHeader.value = 'Information downloaded successfully';
        textForAlertDescription.value = 'The informaton from Strava has being downloaded successfully';
        emit('newdownload', 1);
    } else {
        textForAlertHeader.value = 'Download FAILED';
        textForAlertDescription.value = error_message(flag, true);
        showAlert.value = true;
        showError.value = true;
        // errorShown = true;
        emit('newdownload', 0);
    }
    showClose.value = true;
    // setTimeout(function () {
    //     showAlert.value = false;
    // }, 3000);
}

onMounted(() => {
    if (!props.withDialog) showDialog();
});
</script>
<template>
    <Button
        @click="showDialog"
        v-if="withDialog"
        style="background-color: transparent; border: none"
        v-tooltip.top="{
            value: tip,
            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
        }"
    >
        <Download color="blue" :size="16" />
    </Button>
    <Dialog v-model:visible="showAlert" modal :closable="false" :header="textForAlertHeader">
        {{ textForAlertDescription }}
        <div v-if="showError">
            <div class="mt-2">The programme may not function as intended.</div>
            <div class="mt-2">The issue has been reported to the developer and will be fixed ASAP.</div>
        </div>
        <div v-if="!showClose">
            <div
                class="animate-pulse rounded-full bg-blue-200 px-3 py-1 text-center text-xs font-medium leading-none text-blue-800 dark:bg-blue-900 dark:text-blue-200"
            >
                downloading...
            </div>
            <div class="font-small py-3">There is a limit of 4000 activities per download</div>
        </div>
        <div v-if="showClose" class="mt-2">
            <Button @click="showAlert = false" size="small" severity="info">Continue</Button>
        </div>
    </Dialog>
</template>
