<script setup lang="ts">

import { ref, watch, computed } from 'vue';

import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from "@/components/ui/tooltip"
import { Button } from '@/components/ui/button';

import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';

import { CircleX, Save, Check } from 'lucide-vue-next';

import { useStrearchData } from '@/stores/StrearchStore';
import { storeToRefs } from 'pinia'

const strearchData = useStrearchData()
const { filters, loading: loading, activeFilter, activeFilterIndex} = storeToRefs(strearchData)

const askForFilterName = ref(false);
const filterName = ref<string>('a new filter')
const filterNameNotEmpty = computed(() => { return filterName.value != '' }) 

watch(activeFilter, (newValue) => { filterName.value = newValue?.name ?? 'a new filter name' })

function onChangeOfFilter() { strearchData.changeActiveFilter() }

const deleteFilter = () => {
    if (window.confirm("Are you sure you wish to delete the filter '"+strearchData.filters[strearchData.activeFilterIndex].name+"'?")) {
        strearchData.deleteFilter()
    }
};

const save_filters = () => {
    askForFilterName.value = false
    strearchData.saveFilters(filterName.value ?? 'a filter name')
};

</script>

<template>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger asChild>
                <div class="">
                    <Select
                        @change="onChangeOfFilter"
                        v-model="activeFilter"
                        :loading="loading"
                        :options="filters"
                        showClear
                        optionLabel="name" data-key="id"
                        empty-message="No filters available" placeholder="Select a filter"
                        style="background-color: #E0FFFF"
                    />
                </div>
            </TooltipTrigger>
            <TooltipContent>
                <p>Select a filter</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger asChild>
                <div class="pl-8">
                    <a href="" @click.prevent="askForFilterName = true"><Button unstyled style="background-color: #E0FFFF"><Save color="black"></Save></Button></a>
                </div>
            </TooltipTrigger>
            <TooltipContent>
                <p>Save Filter</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
    <TooltipProvider>
        <Tooltip>
            <TooltipTrigger asChild>
                <div class="pl-1" v-show="activeFilterIndex >= 0">
                    <a href="" @click.prevent="deleteFilter()"><Button unstyled style="background-color: #E0FFFF"><CircleX color="red"></CircleX></Button></a>
                </div>
            </TooltipTrigger>
            <TooltipContent>
                <p>Delete Filter</p>
            </TooltipContent>
        </Tooltip>
    </TooltipProvider>
    
    <Dialog v-model:visible="askForFilterName" modal header="Filter name" :closable="false">
        <span class="text-surface-500 dark:text-surface-400 block mb-2">Enter a name for the filter</span>
        <div class="flex items-center gap-4 mb-4">
            <label for="filtername" class="font-semibold w-24">Filter name</label>
            <InputText v-model="filterName" id="filtername" class="flex-auto" autofocus autocomplete="off" type="search" />
        </div>
        <div class="flex items-center gap-4 mb-4">
            <span class="text-red-500 text-sm" v-if="strearchData.indexOfFilterWithName(filterName) >= 0">Warning: this filter already exists and will be overwritten</span>
        </div>
        <div class="flex justify-end gap-2">
            <a href="" @click.prevent="askForFilterName = false"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><CircleX color="red"></CircleX>Cancel</Button></a>
            <a href="" @click.prevent="save_filters" v-if="filterNameNotEmpty"><Button class="bg-blue-400 hover:bg-blue-700 text-white font-bold rounded" unstyled><Check color="green"></Check>OK</Button></a>
        </div>
    </Dialog>
</template>