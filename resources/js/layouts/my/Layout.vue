<script setup lang="ts">

import { ref } from "vue";
import Menubar from 'primevue/menubar';
import { router } from '@inertiajs/vue3'
import AppLogo from "@/components/AppLogo.vue";
import { User } from 'lucide-vue-next'
import ProgressSpinner from 'primevue/progressspinner';

const items = ref(
    [
        {
            label: 'Athlete',
            command: () => { showLoading.value = true;  router.visit(route('athlete')) }
        },
        {
            label: 'Summary',
            command: () => { showLoading.value = true;  router.visit(route('statistics')) }
        },
        {
            label: 'Activities',
            command: () => { showLoading.value = true;  router.visit(route('activities')) }
        },
    ]
)

const home = route('home')
const profile = route('profile.edit')
const showLoading = ref(false)

</script>

<template>
    <div class="min-h-screen border-b border-sidebar-border/80 flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-gradient-to-r from-cyan-200 to-blue-300 ">
        <Menubar style="background-image: linear-gradient(to right, #A2F4FD , #8ec5ff);border: none" :model="items">
            <template #start>
                <a :href=home><AppLogo></AppLogo></a>
            </template>
            <template #end>
                <div class="flex items-center gap-2">
                    <a :href=profile><User></User></a>
                </div>
            </template>
        </Menubar>
        <div class="grid h-screen place-items-center" v-if="showLoading">
            <div>
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="transparent" animationDuration=".5s"></ProgressSpinner>
                <div class="animate-pulse">
                    Loading...
                </div>
            </div>
        </div>
        <div v-else>
            <slot />
        </div>
    </div>
</template>
