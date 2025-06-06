
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

    return (
        <View style={styles.container}>
            <Text style={styles.subtitle}>
                {name && role ? `Olá, ${name} (${role})` : 'Visitante'}
            </Text>
            {name && (
                <View style={{ marginTop: 20 }}>
                    <Button title="Logout" onPress={handleLogout} color="#007bff" />
                </View>
            )}
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
