import { useEffect } from 'react';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router } from 'expo-router';

export default function useAuthGuard() {
    useEffect(() => {
        const verificarToken = async () => {
            const token = await AsyncStorage.getItem('auth_token');
            if (!token) {
                router.replace('/tabs/home');
            }
        };

        verificarToken();
    }, []);
}
