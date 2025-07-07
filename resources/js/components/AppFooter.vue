<script setup lang="ts">
const props = defineProps({
    laravelVersion: {
        type: String,
        required: true,
        default: '0',
    },
    phpVersion: {
        type: String,
        required: true,
        default: '0',
    },
    appVersion: {
        type: String,
        required: true,
        default: '0',
    },
    quote: {
        type: Object,
        default: {message: 'no quote found', author: 'no author'}
    }
})
</script>
<template>
    <footer>
        <div class="text-[#706f6c] dark:text-[#A1A09A] text-xs">
            <div v-if="props.quote.message != 'no quote found'" class="flex justify-center py-2">
                {{ quote?.message }} ({{ quote?.author }})
            </div>
            <div class="flex justify-evenly">
            <div v-if="props.appVersion != '0' || props.laravelVersion != '0' || props.phpVersion != '0'" class="font-semibold tracking-tight">Software versions:</div>
            <div v-if="props.appVersion != '0'">Strearch version: {{ appVersion }}</div>
            <div v-if="props.laravelVersion != '0'"><a target="_blank" href="https://laravel.com">Laravel</a> version: {{ laravelVersion }}</div>
            <div v-if="props.phpVersion != '0'"><a target="_blank" href="https://php.net">PHP</a> version: {{ phpVersion }}</div>
            <div>Created by <a target="_blank" href="https://gregvinall.com">Greg Vinall</a></div></div>
        </div>
    </footer>
</template>