import { async_axios } from '@/functions/Flags.js';

export async function saveAccessToken(saved_access_token: string, saved_refresh_token: string) {
    const route_to_save_token = route('token.save', [saved_access_token, saved_refresh_token]);
    await async_axios({ url: route_to_save_token, flag: 2, method: 'post' });
}

export async function getStats() {
    await async_axios({ url: route('stats'), flag: 8 });
}

export async function getChartData() {
    await async_axios({ url: route('chartData.get'), flag: 22 });
}

export async function getAthlete() {
    await async_axios({ url: route('athlete.get'), flag: 11 });
}

export async function save_athlete(athlete: any) {
    await async_axios({ url: route('athlete.save', athlete.id), flag: 6, method: 'post', post_data: athlete });
}

export async function save_activities(activities: any, filtered_activities: boolean = false) {
    let save_route = filtered_activities ? route('activities.filtered.save') : route('activities.save');
    await async_axios({ url: save_route, flag: 9, method: 'post', post_data: activities });
}
