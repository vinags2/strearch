<script setup lang="ts">
import { TransitionRoot } from '@headlessui/vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

interface Props {
    autoUpdateActivities: boolean
}

const props = defineProps<Props>();

const form = useForm({
    autoUpdateActivities: props.autoUpdateActivities,
});

const submit = () => {
    form.patch(route('preferences.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="">
                <HeadingSmall title="Preferences" description="Update your preferences" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="flex justify-start mt-6">
                        <div>
                            <input id="autoUpdateActivities" type="checkbox" class="mt-2" v-model="form.autoUpdateActivities"/>
                        </div>
                        <div>
                            <label class="ml-6" for="autoUpdateActivities">Auto-update your latest activities</label>
                        </div>
                        <div>
                            <InputError class="mt-2" :message="form.errors.autoUpdateActivities" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save</Button>

                        <TransitionRoot
                            :show="form.recentlySuccessful"
                            enter="transition ease-in-out"
                            enter-from="opacity-0"
                            leave="transition ease-in-out"
                            leave-to="opacity-0"
                        >
                            <p class="text-sm text-neutral-600">Saved.</p>
                        </TransitionRoot>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
