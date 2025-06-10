import { Preferences } from '@capacitor/preferences';

export const storage = {
    async get(key) {
        const { value } = await Preferences.get({ key });
        return value;
    },
    async set(key, value) {
        await Preferences.set({ key, value });
    },
    async remove(key) {
        await Preferences.remove({ key });
    },
};
