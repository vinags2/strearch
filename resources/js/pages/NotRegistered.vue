<script setup lang="ts">
import ConnectWithStravaIcon from '@/components/ConnectWithStravaIcon.vue';
import { Head, router } from '@inertiajs/vue3';

function registerWithStrava() {
    window.alert('Not implemented yet.');
}
</script>

<template>
    <Head title="Not Registered with Strava">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gradient-to-r from-cyan-200 to-blue-300 p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:p-8"
    >
        <div class="w-250 max-w-lg justify-between">
            <div class="flex items-center justify-center space-x-2">
                <h1 class="mb-4 justify-center text-xl font-bold">Welcome to strearch</h1>
            </div>
            <div class="mb-4">
                'strearch' will allow you to search your Strava activities based on many factors, such as your heart rate, your power output, the
                activity's title, the time of the year, etc
            </div>
            <div class="mb-4">But to continue, you must first register with Strava to allow access to your data.</div>
            <div class="mb-4">After possibly being asked to login, you will be asked to grant access for 'strearch' to download your data.</div>
            <div class="text-bold mb-4 text-red-700">[If you change the accesses requested, 'strearch' may not work properly.]</div>
            <div class="mb-4">When ready, click on 'Connect with Strava'</div>
            <div class="mb-8">[Alternatively, logout to do this at another time]</div>
            <div class="flex items-center justify-between space-x-2">
                <!-- <button class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    proceed
                </button> -->
                <a class="cursor-pointer" @click="registerWithStrava"><ConnectWithStravaIcon></ConnectWithStravaIcon></a>
                <button
                    class="h-12 rounded bg-white px-4 py-3 text-sm font-extrabold uppercase text-black focus:outline-none focus:ring-2 focus:ring-blue-400"
                    @click="router.visit(route('logout'))"
                >
                    logout
                </button>
            </div>
        </div>
    </div>
</template>
