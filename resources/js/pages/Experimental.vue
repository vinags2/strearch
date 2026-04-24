<script setup lang="ts">
import ExperimentalComponent from '@/components/my/ExperimentalComponent.vue';
import Layout from '@/layouts/my/Layout.vue';
import { Head } from '@inertiajs/vue3';
const props = defineProps({
    aProp: {
        type: Object,
        default: { key: 'value from experimental.vue' },
    },
});
</script>

<template>
    <Head title="Testing experimental features" />

    <Layout>
        <ExperimentalComponent :aProp="aProp" />
    </Layout>
</template>
