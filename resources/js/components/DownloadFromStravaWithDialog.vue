<script setup lang="ts">
import { ref, computed, watch, defineEmits } from 'vue';
import { getStravaData } from '@/functions/StravaAPI.js'
import { strava_data } from '@/functions/Flags.js'
import {Button} from '@/components/ui/button'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip"
import { AlertDialog, AlertDialogAction, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle,
  AlertDialogTrigger } from '@/components/ui/alert-dialog'

const props = defineProps({
    downloadWhat: {
        type: String,
        default: 'activities',
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
    showAlert.value = true
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

</script>
<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger asChild>
                <Button @click="showDialog" class="-m-2 bg-transparent hover:bg-blue-400 text-inherit ml-2 rounded inline-flex items-center">
                    <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                    </svg>
                </Button>
            </TooltipTrigger>
            <TooltipContent>
            <p>Update {{ whatToDownload }} data</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
    <AlertDialog :open=showAlert>
        <AlertDialogTrigger as-child>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>
                    {{ textForAlertHeader }}
                </AlertDialogTitle>
                <AlertDialogDescription>
                    {{ textForAlertDescription }}
                    <div v-if="!showClose">
                        <div class="px-3 py-1 text-xs font-medium leading-none text-center text-blue-800 bg-blue-200 rounded-full
                            animate-pulse dark:bg-blue-900 dark:text-blue-200">downloading...</div>
                        <div class="py-3 font-small">There is a limit of 4000 activities per download</div>
                    </div>
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogAction v-if="showClose" @click="showAlert=false">Continue</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>