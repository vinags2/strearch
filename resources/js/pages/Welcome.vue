<script setup lang="ts">
import PoweredByStravaIcon from '@/components/PoweredByStravaIcon.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-gradient-to-r from-cyan-200 to-blue-300 p-6 text-[#1b1b18] dark:bg-[#0a0a0a] lg:justify-center lg:p-8"
    >
        <header class="not-has-[nav]:hidden mb-6 w-full max-w-[335px] text-sm lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('activities')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    Go
                </Link>
                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]"
                    >
                        Log in
                    </Link>
                    <Link
                        :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        <div class="duration-750 starting:opacity-0 flex w-full items-center justify-center opacity-100 transition-opacity lg:grow">
            <main class="flex w-full max-w-[335px] flex-col-reverse overflow-hidden rounded-lg lg:max-w-4xl lg:flex-row">
                <div
                    class="flex-1 rounded-bl-lg rounded-br-lg bg-white p-6 pb-12 text-[13px] leading-[20px] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:bg-[#161615] dark:text-[#EDEDEC] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:rounded-br-none lg:rounded-tl-lg lg:p-20"
                >
                    <h1 class="mb-1 font-medium">Welcome to Strearch</h1>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        Strearch is a tool for searching your Strava activities, and comparing your performances over time. You may apply filters to
                        view a subset of your activities and order your activities as you see fit.
                    </p>
                    <p class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">
                        For example, you may view your virtual rides that have an average heart rate below 140, where the climbing is more than 1000m,
                        sorted in order of average wattage.
                    </p>
                    <p v-if="$page.props.auth.user" class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Click 'Go' above to continue.</p>
                    <p v-else class="mb-2 text-[#706f6c] dark:text-[#A1A09A]">Click 'Login' or 'Register' above to continue.</p>
                </div>
                <div
                    class="relative -mb-px aspect-[335/376] w-full shrink-0 overflow-hidden rounded-t-lg bg-gradient-to-r from-cyan-200 to-blue-300 dark:bg-[#1D0002] lg:-ml-px lg:mb-0 lg:aspect-auto lg:w-[438px] lg:rounded-r-lg lg:rounded-t-none"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="438"
                        height="438"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#afffff"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle cx="18.5" cy="17.5" r="3.5"></circle>
                        <circle cx="5.5" cy="17.5" r="3.5"></circle>
                        <circle cx="15" cy="5" r="1"></circle>
                        <path d="M12 17.5V14l-3-3 4-3 2 3h2"></path>
                    </svg>
                    <div
                        class="absolute inset-0 rounded-t-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] lg:overflow-hidden lg:rounded-r-lg lg:rounded-t-none"
                    />
                    <div class="mb-2 ml-2">
                        <PoweredByStravaIcon></PoweredByStravaIcon>
                    </div>
                </div>
            </main>
        </div>
        <div class="h-14.5 hidden lg:block"></div>
    </div>
</template>
