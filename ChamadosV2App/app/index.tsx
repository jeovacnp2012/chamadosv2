// import useAuthGuard from '../hooks/useAuthGuard';
// import {View, Text} from 'react-native';
// export default function Index() {
//     useAuthGuard();
//
//     return (
//         <View>
//             <Text>Bem-vindo à tela inicial</Text>
//         </View>
//     );
// }
import React, { useEffect, useState } from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router } from 'expo-router';

export default function Home() {
    const [name, setName] = useState<string | null>(null);
    const [role, setRole] = useState<string | null>(null);

    useEffect(() => {
        const loadUserData = async () => {
            const storedName = await AsyncStorage.getItem('user_name');
            const storedRole = await AsyncStorage.getItem('user_role');
            setName(storedName);
            setRole(storedRole);
        };
        loadUserData();
    }, []);

    const handleLogout = async () => {
        await AsyncStorage.multiRemove(['auth_token', 'user_name', 'user_role']);
        router.replace('/auth/login');
    };

    const handleLogin = () => {
        router.replace('/auth/login');
    };

    const isLoggedIn = name && role;

    return (
        <View style={styles.container}>
            <Text style={styles.subtitle}>
                {isLoggedIn ? `Olá, ${name} (${role})` : 'Visitante'}
            </Text>

            <View style={{ marginTop: 20 }}>
                {isLoggedIn ? (
                    <Button title="Logout" onPress={handleLogout} color="#c62828" />
                ) : (
                    <Button title="Entrar" onPress={handleLogin} color="#007bff" />
                )}
            </View>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#fff',
        padding: 20,
    },
    subtitle: {
        fontSize: 18,
        fontWeight: '600',
    },
});

