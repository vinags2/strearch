<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 bg-gradient-to-r from-cyan-200 to-blue-300">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <svg 
                            class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]"
                            viewBox="0 0 62 65"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path fill="blue" 
                                d="m88.891 11.109h-77.781c-3.832 0-6.9453 3.1094-6.9453 6.9453v63.891c0 3.832 3.1094 6.9453 6.9453 6.9453h77.777c3.832 0 6.9453-3.1094 6.9453-6.9453v-63.891c0-3.832-3.1094-6.9453-6.9453-6.9453zm-81.945 6.9453c0-2.3047 1.8594-4.168 4.168-4.168h4.168v6.9453c0 0.77734 0.60938 1.3906 1.3906 1.3906 0.77734 0 1.3906-0.60938 1.3906-1.3906v-6.9453h8.332v6.9453c0 0.77734 0.60938 1.3906 1.3906 1.3906 0.77734 0 1.3906-0.60938 1.3906-1.3906v-6.9453h8.332v6.9453c0 0.77734 0.60938 1.3906 1.3906 1.3906 0.77734 0 1.3906-0.60938 1.3906-1.3906v-6.9453h48.609c2.3047 0 4.168 1.8594 4.168 4.168v12.5h-86.121zm86.109 63.891c0 2.3047-1.8594 4.168-4.168 4.168l-77.777-0.003906c-2.3047 0-4.168-1.8594-4.168-4.168l0.003906-48.609h86.109zm-62.5-10.805c3.4727 0 6.918-1.25 9.7227-3.582l10.891 10.891c0.27734 0.27734 0.64062 0.41797 0.97266 0.41797s0.72266-0.14063 0.97266-0.41797c0.55469-0.55469 0.55469-1.418 0-1.9727l-10.891-10.891c4.9727-6 4.75-14.918-0.89062-20.527-5.9453-5.9453-15.641-5.9453-21.609 0-5.9453 5.9453-5.9453 15.641 0 21.609 2.9727 2.9727 6.8906 4.4727 10.805 4.4727zm-8.832-24.113c2.4453-2.4453 5.6406-3.668 8.832-3.668 3.1953 0 6.3906 1.2227 8.832 3.668 4.8594 4.8594 4.8594 12.805 0 17.668-4.8594 4.8594-12.805 4.8594-17.668 0-4.8594-4.8594-4.8594-12.805 0-17.668zm40.777 8.5273c0-0.77734 0.60938-1.3906 1.3906-1.3906h19.445c0.77734 0 1.3906 0.60938 1.3906 1.3906 0 0.77734-0.60937 1.3906-1.3906 1.3906h-19.445c-0.77734 0-1.3906-0.60937-1.3906-1.3906zm0-11.109c0-0.77734 0.60938-1.3906 1.3906-1.3906h19.445c0.77734 0 1.3906 0.60937 1.3906 1.3906 0 0.77734-0.60937 1.3906-1.3906 1.3906h-19.445c-0.77734 0-1.3906-0.60938-1.3906-1.3906zm0 22.223c0-0.77734 0.60938-1.3906 1.3906-1.3906h19.445c0.77734 0 1.3906 0.60938 1.3906 1.3906 0 0.77734-0.60937 1.3906-1.3906 1.3906h-19.445c-0.77734 0-1.3906-0.60938-1.3906-1.3906zm0 11.109c0-0.77734 0.60938-1.3906 1.3906-1.3906h19.445c0.77734 0 1.3906 0.60938 1.3906 1.3906 0 0.77734-0.60937 1.3906-1.3906 1.3906h-19.445c-0.77734 0-1.3906-0.60938-1.3906-1.3906z"
                                transform="scale(0.6 0.6)"
                            />
                        </svg>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-8">
                       <div
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800"
                        >
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16"
                            >
                                <svg
                                    class="size-5 sm:size-6"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <g fill="blue">
                                        <path
                                            d="M16.597 12.635a.247.247 0 0 0-.08-.237 2.234 2.234 0 0 1-.769-1.68c.001-.195.03-.39.084-.578a.25.25 0 0 0-.09-.267 8.8 8.8 0 0 0-4.826-1.66.25.25 0 0 0-.268.181 2.5 2.5 0 0 1-2.4 1.824.045.045 0 0 0-.045.037 12.255 12.255 0 0 0-.093 3.86.251.251 0 0 0 .208.214c2.22.366 4.367 1.08 6.362 2.118a.252.252 0 0 0 .32-.079 10.09 10.09 0 0 0 1.597-3.733ZM13.616 17.968a.25.25 0 0 0-.063-.407A19.697 19.697 0 0 0 8.91 15.98a.25.25 0 0 0-.287.325c.151.455.334.898.548 1.328.437.827.981 1.594 1.619 2.28a.249.249 0 0 0 .32.044 29.13 29.13 0 0 0 2.506-1.99ZM6.303 14.105a.25.25 0 0 0 .265-.274 13.048 13.048 0 0 1 .205-4.045.062.062 0 0 0-.022-.07 2.5 2.5 0 0 1-.777-.982.25.25 0 0 0-.271-.149 11 11 0 0 0-5.6 2.815.255.255 0 0 0-.075.163c-.008.135-.02.27-.02.406.002.8.084 1.598.246 2.381a.25.25 0 0 0 .303.193 19.924 19.924 0 0 1 5.746-.438ZM9.228 20.914a.25.25 0 0 0 .1-.393 11.53 11.53 0 0 1-1.5-2.22 12.238 12.238 0 0 1-.91-2.465.248.248 0 0 0-.22-.187 18.876 18.876 0 0 0-5.69.33.249.249 0 0 0-.179.336c.838 2.142 2.272 4 4.132 5.353a.254.254 0 0 0 .15.048c1.41-.01 2.807-.282 4.117-.802ZM18.93 12.957l-.005-.008a.25.25 0 0 0-.268-.082 2.21 2.21 0 0 1-.41.081.25.25 0 0 0-.217.2c-.582 2.66-2.127 5.35-5.75 7.843a.248.248 0 0 0-.09.299.25.25 0 0 0 .065.091 28.703 28.703 0 0 0 2.662 2.12.246.246 0 0 0 .209.037c2.579-.701 4.85-2.242 6.456-4.378a.25.25 0 0 0 .048-.189 13.51 13.51 0 0 0-2.7-6.014ZM5.702 7.058a.254.254 0 0 0 .2-.165A2.488 2.488 0 0 1 7.98 5.245a.093.093 0 0 0 .078-.062 19.734 19.734 0 0 1 3.055-4.74.25.25 0 0 0-.21-.41 12.009 12.009 0 0 0-10.4 8.558.25.25 0 0 0 .373.281 12.912 12.912 0 0 1 4.826-1.814ZM10.773 22.052a.25.25 0 0 0-.28-.046c-.758.356-1.55.635-2.365.833a.25.25 0 0 0-.022.48c1.252.43 2.568.65 3.893.65.1 0 .2 0 .3-.008a.25.25 0 0 0 .147-.444c-.526-.424-1.1-.917-1.673-1.465ZM18.744 8.436a.249.249 0 0 0 .15.228 2.246 2.246 0 0 1 1.352 2.054c0 .337-.08.67-.23.972a.25.25 0 0 0 .042.28l.007.009a15.016 15.016 0 0 1 2.52 4.6.25.25 0 0 0 .37.132.25.25 0 0 0 .096-.114c.623-1.464.944-3.039.945-4.63a12.005 12.005 0 0 0-5.78-10.258.25.25 0 0 0-.373.274c.547 2.109.85 4.274.901 6.453ZM9.61 5.38a.25.25 0 0 0 .08.31c.34.24.616.561.8.935a.25.25 0 0 0 .3.127.631.631 0 0 1 .206-.034c2.054.078 4.036.772 5.69 1.991a.251.251 0 0 0 .267.024c.046-.024.093-.047.141-.067a.25.25 0 0 0 .151-.23A29.98 29.98 0 0 0 15.957.764a.25.25 0 0 0-.16-.164 11.924 11.924 0 0 0-2.21-.518.252.252 0 0 0-.215.076A22.456 22.456 0 0 0 9.61 5.38Z"
                                        />
                                    </g>
                                </svg>
                            </div>

                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">Strearch</h2>

                                <p class="mt-4 ">
                                    Strearch is a tool for analysing your Strava Rides and other activities. After downloading your rides, you may view your rides
                                    in a table which may be sorted in any order, and filtered as you see fit.
                               </p>
                                <p class="mt-4 ">
                                    For example, you may view your virtual rides that have an average heart rate below 140, where the climbing is more than 1000m,
                                    sorted in order of average wattage.
                                </p>
                                <p class="mt-4 ">
                                A summary of your rides over time may be analysed both textually and graphically.
                                </p>
                                <p class="mt-4 ">
                                To use the programme
                                    <ul class="list-disc ml-8">
                                        <li>
                                            register and verify your email
                                        </li>
                                        <li>
                                            login, and
                                        </li>
                                        <li>
                                            refresh your 'Athlete' data 
                                        </li>
                                    </ul>
                                You will be asked to give permission to Strava to download your data,
                                where upon information about you will be downloaded (such as your weight, gender, bio, profile picture, etc.)
                                </p>
                                <p class="mt-4 ">
                                The Activities page will display your rides and other activities. If the list is not up to date, click on one of the 'Refresh' buttons.
                                </p>
                                <p class="mt-4 ">
                                'Filters' will allow you to create filters for viewing your activities, and 'Analysis' will analyse your activities in a summary/trend.
                                </p>

                               
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>
    </div>
</template>
