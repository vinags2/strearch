<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import Button from 'primevue/button'
import Layout from '@/layouts/my/Layout.vue';
import SettingsLayout from '@/layouts/settings/SettingsLayout.vue';

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
    <Layout>
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
                        <Button severity="info" type="submit" :disabled="form.processing">Save</Button>

                        <span v-show="form.recentlySuccessful">Saved</span>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </Layout>
</template>
