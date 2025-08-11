import { async_axios } from '@/functions/Flags.js';
import { FilterMatchMode, FilterOperator } from '@primevue/core/api';
import { defineStore } from 'pinia';

type Filter = {
    average_cadence: object;
    average_heartrate: object;
    average_speed: object;
    average_watts: object;
    distance: object;
    kilojoules: object;
    max_heartrate: object;
    max_speed: object;
    max_watts: object;
    moving_time: object;
    moving_time_as_string: object;
    name: object;
    sport_type: object;
    start_date_local: object;
    start_date_local_as_timestamp: object;
    suffer_score: object;
    total_elevation_gain: object;
};

type FiltersElement = {
    id: number;
    active: number;
    name: string;
    filter: Filter;
};

type Activity = {
    id: number;
    average_cadence: number;
    average_heartrate: number;
    average_speed: number;
    average_watts: number;
    distance: number;
    kilojoules: number;
    max_heartrate: number;
    max_speed: number;
    max_watts: number;
    moving_time: number;
    moving_time_as_string: string;
    name: string;
    sport_type: string;
    start_date_local: Date;
    start_date_local_as_timestamp: number;
    suffer_score: number;
    total_elevation_gain: number;
};

export const useStrearchData = defineStore('strearchData', {
    state: () => ({
        activities: <Activity[]>[],
        sportTypes: <string>{},
        lastUpdate: <string>'',
        filteredActivities: <Activity[]>[],
        filter: <Filter>{},
        filters: <FiltersElement[]>[],
        activeFilter: <FiltersElement>{},
        justTheFilter: <Filter>{},
        activeFilterIndex: -1,
        loading: false,
        maxFilterId: -1,
        filteredActivitiesFlag: false,
        timeToShowError: true,
    }),

    actions: {
        async initFilters(force = false) {
            if (this.filters.length == 0 || force) {
                const ret = await async_axios({ url: route('filters.get'), flag: 14 });
                if (ret.error) {
                    return;
                }
                this.filters = ret.data.filters;
                this.convertAPIFiltertoJStypes();
                this.setActiveFilter(false);
                this.findMaxFilterId();
            }
        },

        async initActivities(force = false) {
            if (this.activities.length == 0 || force) {
                const ret = await async_axios({ url: route('activities.get'), flag: 12 });
                this.activities = ret.data.activities;
                this.sportTypes = ret.data.sportTypes;
                this.stringToDate();
                this.lastUpdate = ret.data.date_of_last_activities_strava_update;
            }
        },

        init() {
            this.loading = true;
            this.initActivities();
            this.initFilters();
            this.loading = false;
        },

        // Convert dates as strings (from DB) to JS Date objects
        stringToDate() {
            for (const element of this.activities) {
                element.start_date_local = new Date(element.start_date_local_as_timestamp);
            }
        },

        // Convert JS Objects to JSON as strings for saving to DB
        convertJStypestoAPIFilter() {
            let convertedFilters = <any>[];

            this.filters.forEach((element: any) => {
                let combined = {
                    filterName: element.name,
                    filters: JSON.stringify(element.filter),
                    active: element.active,
                };
                convertedFilters.push(combined);
            });

            return convertedFilters;
        },

        // Convert JSON strings from DB to JS Objects
        convertAPIFiltertoJStypes() {
            this.filters.forEach((el: any) => {
                el.filter = JSON.parse(el.filter);
                this.convertConstraintStringsToDates(el.filter.start_date_local.constraints);
            });
        },

        // Convert Constraints in the DB from strings to JS Date objects
        convertConstraintStringsToDates(constraints: any) {
            constraints.forEach((element: any) => {
                if (element.value) {
                    element.value = new Date(element.value);
                }
            });
        },

        // find the maximum filter id (used when creating a new filter)
        findMaxFilterId() {
            this.filters.forEach((element: any) => {
                if (element.id > this.maxFilterId) {
                    this.maxFilterId = element.id;
                }
            });
        },

        // Update the Filtered Activities variable with the changed filtered activities from the Activities Table
        async updateFilteredActivities(data: Activity[]) {
            this.filteredActivities = data;
            await this.saveFilteredActivities();
        },

        // Save the Filters to the DB
        async saveFilteredActivities() {
            await async_axios({ url: route('activities.filtered.save'), flag: 13, method: 'post', post_data: this.filteredActivities });
            this.filteredActivitiesFlag = !this.filteredActivitiesFlag;
        },

        // Set the Active Filter and it's dependencies, searching for the filter where active = 1
        setActiveFilter(withSave = true) {
            var i = 0;
            this.activeFilterIndex = -1;
            this.activeFilter = this.newFiltersElement;
            this.filters.forEach((el: FiltersElement) => {
                if (el.active == 1) {
                    this.activeFilter = el;
                    this.activeFilterIndex = i;
                }
                i++;
            });
            this.justTheFilter = this.activeFilter.filter;
            if (withSave) this.saveFiltersActually();
        },

        // Change the Active Filter to what has been set in the variable ActiveFilter
        changeActiveFilter() {
            this.filters.forEach((el: any) => {
                if (el.id == this.activeFilter?.id) {
                    el.active = 1;
                } else {
                    el.active = 0;
                }
            });
            this.setActiveFilter();
        },

        // Save the Filters to the DB
        async saveFiltersActually() {
            await async_axios({ url: route('filters.save'), flag: 16, method: 'post', post_data: this.convertJStypestoAPIFilter() });
        },

        // Return the index in the filters array to the filter which is active (or -1)
        indexOfFilterWithName(filterName: string): number {
            let index = -1;
            let i = -1;
            this.filters.forEach((element: any) => {
                i++;
                if (element.name == filterName) {
                    index = i;
                    return;
                }
            });
            return index;
        },

        // Prepare to save a new filter
        saveNewFilter(filterName: string) {
            const newFilter = this.newFiltersElement;
            newFilter.filter = this.justTheFilter;
            newFilter.name = filterName;
            newFilter.active = 1;
            if (this.activeFilterIndex != -1) this.filters[this.activeFilterIndex].active = 0;
            this.filters.push(newFilter);
            this.activeFilterIndex = this.filters.length - 1;
            this.activeFilter = this.filters[this.activeFilterIndex];
        },

        // Prepare to save a changed existing filter
        saveExistingFilter(filterIndex: number) {
            this.activeFilterIndex = filterIndex;
            this.filters[this.activeFilterIndex].filter = this.justTheFilter;
            this.setAllFiltersActiveToZero();
            this.filters[this.activeFilterIndex].active = 1;
            this.activeFilter = this.filters[this.activeFilterIndex];
        },

        // Set all filters to inactive (filter.active = 0)
        setAllFiltersActiveToZero() {
            this.filters.forEach((element: any) => {
                element.active = 0;
            });
        },

        // Save the array of filters to the DB
        saveFilters(filterName: string) {
            const index = this.indexOfFilterWithName(filterName);
            if (index == -1) {
                this.saveNewFilter(filterName);
            } else {
                this.saveExistingFilter(index);
            }
            this.saveFiltersActually();
        },

        // Delete the filter with 'id' from the DB
        async deleteFilterActually(id: number) {
            const ret = await async_axios({ url: route('filter.delete', id), flag: 16 });
            if (!ret.error) {
                window.alert('The filter has been deleted permanently');
            }
        },

        // Prepare to delete the current filter from the DB
        deleteFilterFromFiltersArray() {
            let index = this.activeFilterIndex;
            this.filters.splice(index, 1);
            this.changeActiveFilter();
        },

        // Delete the filter from the filters array and the DB
        deleteFilter() {
            if (this.activeFilterIndex == -1) return;
            const id = this.filters[this.activeFilterIndex].id;
            this.deleteFilterFromFiltersArray();
            this.deleteFilterActually(id);
        },
    },

    getters: {
        // a blank new element for the filters array`
        newFiltersElement(): FiltersElement {
            return {
                active: 1,
                filter: this.newFilterElement,
                id: ++this.maxFilterId,
                name: 'a new filter',
            };
        },

        // a blank filter element
        newFilterElement(): Filter {
            return {
                name: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
                start_date_local: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                start_date_local_as_timestamp: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.DATE_IS }] },
                sport_type: { value: null, matchMode: FilterMatchMode.IN },
                distance: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                average_speed: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                average_cadence: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                average_watts: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                average_heartrate: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                max_heartrate: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                total_elevation_gain: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                suffer_score: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                kilojoules: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                max_speed: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                max_watts: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                moving_time: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.EQUALS }] },
                moving_time_as_string: { operator: FilterOperator.AND, constraints: [{ value: null, matchMode: FilterMatchMode.STARTS_WITH }] },
            };
        },

        waitBeforeShowingAnotherErrorMessage() {
            return new Promise((resolve) => {
                setTimeout(
                    () => {
                        this.timeToShowError = true;
                        console.log('useStrearchData: timeToShowError set to true');
                        resolve('resolved');
                    },
                    10 * 60 * 1000, // 10 minutes
                );
            });
        },
        showAnotherErrorMessage(state): boolean {
            // console.log('useStrearchData: showAnotherErrorMessage called, timeToShowError = ', state.timeToShowError);
            if (state.timeToShowError) {
                state.timeToShowError = false;
                // console.log('resolved = ', this.waitBeforeShowingAnotherErrorMessage);
                return true;
            }
            return false;
        },
    },
});
