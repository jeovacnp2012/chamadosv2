
import React, { useEffect } from 'react';
import { View, Text, StyleSheet, ActivityIndicator } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router } from 'expo-router';

export default function Clear() {
    useEffect(() => {
        const clearStorage = async () => {
            try {
                await AsyncStorage.clear();
                console.log('🔐 Tokens limpos com sucesso!');
            } catch (error) {
                console.error('Erro ao limpar AsyncStorage:', error);
            } finally {
                router.replace('/auth/login');
            }
        };

        clearStorage();
    }, []);

    return (
        <View style={styles.container}>
            <Text style={styles.text}>Saindo...</Text>
            <ActivityIndicator size="large" />
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        justifyContent: 'center',
        alignItems: 'center',
        backgroundColor: '#fff',
    },
    text: {
        fontSize: 18,
        marginBottom: 12,
    },
});
