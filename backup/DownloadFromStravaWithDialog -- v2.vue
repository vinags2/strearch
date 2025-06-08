
<script setup lang="ts">
import { ref } from 'vue';
import {Button} from '@/components/ui/button'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

const props = defineProps({
    downloadWhat: {
        type: String,
        default: 'activities',
    },
})

const alertT = document.getElementById("alertTrigger")

const showOK = ref(false)

let stateChange = () => {
    console.log(alertT)
    // if (alertT) {
        alertT.click()
    // }
    showOK.value = false
    setTimeout(function () {
        showOK.value = true;
    }, 5000);
}

</script>
<template>
        <TooltipProvider>
           <Tooltip>
                <TooltipTrigger asChild>
                    <Button @click="stateChange()" class="-m-2 bg-transparent hover:bg-blue-400 text-inherit ml-2 rounded inline-flex items-center">
                        <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/>
                        </svg>
                    </Button>
                </TooltipTrigger>
                <TooltipContent>
                <p>Download athlete</p>
                </TooltipContent>
            </Tooltip>
        </TooltipProvider>
    
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button v-show=true id="alertTrigger" class="-m-2 bg-transparent hover:bg-blue-400 text-inherit ml-2 rounded inline-flex items-center">
                Alert Button
            </Button>
        </AlertDialogTrigger>
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Downloading Athlete Information</AlertDialogTitle>
                <AlertDialogDescription>
                    Please wait while the informaton from Strava is being downloaded
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <!-- <AlertDialogCancel v-if=false>Cancel</AlertDialogCancel> -->
                <AlertDialogAction v-if="showOK">Continue</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>