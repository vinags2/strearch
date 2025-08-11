<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import PoweredByStravaIconSmall from '@/components/PoweredByStravaIconSmall.vue';
import { router } from '@inertiajs/vue3';
import { User } from 'lucide-vue-next';
import Menubar from 'primevue/menubar';
import ProgressSpinner from 'primevue/progressspinner';
import { ref } from 'vue';

const items = ref([
    {
        label: 'Athlete',
        command: () => {
            showLoading.value = true;
            router.visit(route('athlete'));
        },
    },
    {
        label: 'Summary',
        command: () => {
            showLoading.value = true;
            router.visit(route('statistics'));
        },
    },
    {
        label: 'Activities',
        command: () => {
            showLoading.value = true;
            router.visit(route('activities'));
        },
    },
]);

const showLoading = ref(false);

const goProfile = () => {
    window.location.href = route('profile.edit');
};
</script>

<template>
    <div class="absolute bottom-2 right-20">
        <PoweredByStravaIconSmall></PoweredByStravaIconSmall>
    </div>
    <div
        class="flex h-full min-h-screen flex-1 flex-col gap-4 rounded-xl border-b border-sidebar-border/80 bg-gradient-to-r from-cyan-200 to-blue-300 p-4"
    >
        <Menubar style="background-image: linear-gradient(to right, #a2f4fd, #8ec5ff); border: none" :model="items">
            <template #start>
                <div class="flex items-center gap-2">
                    <AppLogo></AppLogo>
                </div>
            </template>
            <template #end>
                <div class="items-center">
                    <div
                        class="flex items-center gap-2"
                        v-tooltip.left="{
                            value: 'Logout, or change password or profile',
                            pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                        }"
                    >
                        <User class="cursor-pointer" @click="goProfile"></User>
                    </div>
                </div>
            </template>
        </Menubar>
        <div class="grid h-screen place-items-center" v-if="showLoading">
            <div>
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="transparent" animationDuration=".5s"></ProgressSpinner>
                <div class="animate-pulse">Loading...</div>
            </div>
        </div>
        <div v-else>
            <slot />
        </div>
    </div>
</template>
