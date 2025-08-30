<script setup lang="ts">
import { error_message, whatWeAreDownloading } from '@/functions/Flags.js';
import { getStravaData } from '@/functions/StravaAPI.js';
import { Download } from 'lucide-vue-next';
import Button from 'primevue/button';
import { defineEmits, onMounted, ref } from 'vue';

import Dialog from 'primevue/dialog';

const props = defineProps({
    flag: {
        type: Number,
        default: 10, // See flags.ts for values
    },
    withDialog: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits<{
    newdownload: any;
}>();

const showClose = ref(false);
const showAlert = ref(false);
const showError = ref(false);
const textForAlertDescription = ref('Please wait while the informaton from Strava is being downloaded');
const textForAlertHeader = ref('Downloading ' + whatWeAreDownloading(props.flag) + ' information');
const tip = ref('Update ' + whatWeAreDownloading(props.flag) + ' data');

function showDialog() {
    textForAlertDescription.value = 'Please wait while the informaton from Strava is being downloaded';
    textForAlertHeader.value = 'Downloading ' + whatWeAreDownloading(props.flag) + ' information';
    showClose.value = false;
    showAlert.value = props.withDialog;
    getData();
}

async function getData() {
    const flag = await getStravaData(props.flag);
    if (!flag) {
        showSuccessBeforeClosing(0, false);
        return;
    }

    showSuccessBeforeClosing(flag, true);
}

function showSuccessBeforeClosing(flag: number, success: boolean = true) {
    if (success) {
        textForAlertHeader.value = 'Information downloaded successfully';
        textForAlertDescription.value = 'The informaton from Strava has being downloaded successfully';
        emit('newdownload', 1);
    } else {
        textForAlertHeader.value = 'Download FAILED';
        textForAlertDescription.value = error_message(flag, true);
        showAlert.value = true;
        showError.value = true;
        emit('newdownload', 0);
    }
    showClose.value = true;
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
