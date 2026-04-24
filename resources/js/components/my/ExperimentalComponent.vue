<script setup lang="ts">
import { getStravaData } from '@/functions/StravaAPI';
import Button from 'primevue/button';
import { ref } from 'vue';
const props = defineProps({
    aProp: {
        type: Object,
        default: { key: 'value from the componenent itself' },
    },
});

const fullActivity = ref<any>();

async function getFullActivity() {
    const ret = await getStravaData(7, 15654812865);
    fullActivity.value = ret;
}
</script>
<template>
    <div>
        <Button severity="info" class="mt-4" @click="getFullActivity()">Get the activity </Button>
    </div>
    <div class="mt-4 font-bold" v-if="fullActivity">{{ fullActivity }}</div>
    <div class="mt-4 font-bold" v-if="fullActivity">Segment Names</div>
    <div class="mt-4">
        <li class="ml-4" v-if="fullActivity" v-for="segment in fullActivity.segment_efforts">{{ segment.name }}</li>
    </div>
</template>
