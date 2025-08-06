<script setup lang="ts">
import { computed, ref, watch } from 'vue';

import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

import { CircleX, Save } from 'lucide-vue-next';

import { useStrearchData } from '@/stores/StrearchStore';
import { storeToRefs } from 'pinia';

const strearchData = useStrearchData();
const { filters, loading: loading, activeFilter, activeFilterIndex } = storeToRefs(strearchData);
strearchData.initFilters();

const askForFilterName = ref(false);
const filterName = ref<string>('a new filter');
const filterNameNotEmpty = computed(() => {
    return filterName.value != '';
});

watch(activeFilter, (newValue) => {
    filterName.value = newValue?.name ?? 'a new filter name';
});

function onChangeOfFilter() {
    strearchData.changeActiveFilter();
}

const deleteFilter = () => {
    if (window.confirm("Are you sure you wish to delete the filter '" + strearchData.filters[strearchData.activeFilterIndex].name + "'?")) {
        strearchData.deleteFilter();
    }
};

const save_filters = () => {
    askForFilterName.value = false;
    strearchData.saveFilters(filterName.value ?? 'a filter name');
};
</script>

<template>
    <div class="ml-8">
        <Select
            @change="onChangeOfFilter"
            v-model="activeFilter"
            :loading="loading"
            :options="filters"
            showClear
            optionLabel="name"
            data-key="id"
            empty-message="No filters available"
            placeholder="Select a filter"
            style="background-color: #e0ffff"
            v-tooltip.top="{
                value: 'Select or clear the filter',
                pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
            }"
        />
    </div>
    <div class="pl-4">
        <a href="" @click.prevent="askForFilterName = true">
            <Button
                unstyled
                style="background-color: transparent"
                v-tooltip.top="{
                    value: 'Save Filter',
                    pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                }"
            >
                <Save color="blue"></Save> </Button
        ></a>
    </div>
    <div class="pl-1" v-show="activeFilterIndex >= 0">
        <a href="" @click.prevent="deleteFilter()">
            <Button
                unstyled
                style="background-color: transparent"
                v-tooltip.top="{
                    value: 'Delete Filter',
                    pt: { text: '!bg-secondary !text-primary !font-medium !text-sm' },
                }"
            >
                <CircleX color="red"></CircleX>
            </Button>
        </a>
    </div>

    <Dialog v-model:visible="askForFilterName" modal header="Filter name" :closable="false">
        <span class="mb-2 block text-surface-500 dark:text-surface-400">Enter a name for the filter</span>
        <div class="mb-4 flex items-center gap-4">
            <label for="filtername" class="w-24 font-semibold">Filter name</label>
            <InputText v-model="filterName" id="filtername" class="flex-auto" autofocus autocomplete="off" type="search" />
        </div>
        <div class="mb-4 flex items-center gap-4">
            <span class="text-sm text-red-500" v-if="strearchData.indexOfFilterWithName(filterName) >= 0"
                >Warning: this filter already exists and will be overwritten</span
            >
        </div>
        <div class="flex justify-end gap-2">
            <a href="" @click.prevent="askForFilterName = false">
                <Button severity="info" class=""> Cancel </Button>
            </a>
            <a href="" @click.prevent="save_filters" v-if="filterNameNotEmpty">
                <Button severity="info" class=""> OK </Button>
            </a>
        </div>
    </Dialog>
</template>
