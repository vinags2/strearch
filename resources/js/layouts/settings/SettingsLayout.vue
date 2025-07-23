<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: route('profile.edit'),
    },
    {
        title: 'Password',
        href: route('password.edit'),
    },
    {
        title: 'Preferences',
        href: route('preferences.edit'),
    },
    {
        title: 'Logout',
        href: route('logout.also'),
    },
];
</script>

<template>
    <div class="px-4">
        <Heading title="Settings" description="Manage your profile and account settings" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-x-0 space-y-8">
                    <Button v-for="item in sidebarNavItems" :key="item.href" variant="ghost" :class="['w-full justify-start']" as-child>
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
