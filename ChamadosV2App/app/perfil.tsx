import React, { useEffect, useState } from 'react';
import Toast from 'react-native-toast-message';
import {
    View,
    Text,
    TextInput,
    Button,
    StyleSheet,
    Alert,
    ActivityIndicator,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { router } from 'expo-router';

export default function Perfil() {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [passwordConfirm, setPasswordConfirm] = useState('');
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const carregarDados = async () => {
            const token = await AsyncStorage.getItem('auth_token');
            try {
                const response = await fetch('http://127.0.0.1:8000/api/v1/me', {
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                    },
                });

                const data = await response.json();
                console.log('📦 Dados recebidos do /me:', data);

                if (data.user) {
                    setName(data.user.name || '');
                    setEmail(data.user.email || '');
                } else {
                    Alert.alert('Erro', 'Usuário não encontrado.');
                }
            } catch (err) {
                console.error('Erro ao buscar dados do perfil:', err);
                Alert.alert('Erro', 'Não foi possível carregar os dados.');

            }
        };

        carregarDados();
    }, []);

    const alterarSenha = async () => {
        if (!password || password !== passwordConfirm) {
            Alert.alert('Erro', 'As senhas não conferem.');
            return;
        }

        setLoading(true);

        try {
            const token = await AsyncStorage.getItem('auth_token');
            const response = await fetch('http://127.0.0.1:8000/api/v1/profile/password', {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                },
                body: JSON.stringify({ password }),
            });

            if (!response.ok) {
                throw new Error('Falha ao atualizar senha.');
            }
            Toast.show({
                type: 'success',
                text1: 'Sucesso!',
                text2: 'Senha atualizada com sucesso.',
                position: 'top',
            });
            router.replace('/tabs/home');
            //router.back();
        } catch (error: any) {
            Alert.alert('Erro', error.message || 'Erro inesperado.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <View style={styles.container}>
            <Text style={styles.title}>Perfil do Usuário</Text>
            <Text style={styles.label}>Nome:</Text>
            <Text style={styles.value}>{name}</Text>
            <Text style={styles.label}>Email:</Text>
            <Text style={styles.value}>{email}</Text>

            <Text style={styles.label}>Nova Senha</Text>
            <TextInput
                style={styles.input}
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                placeholder="Nova senha"
            />
            <Text style={styles.label}>Confirmar Senha</Text>
            <TextInput
                style={styles.input}
                value={passwordConfirm}
                onChangeText={setPasswordConfirm}
                secureTextEntry
                placeholder="Confirmar senha"
            />

            {loading ? (
                <ActivityIndicator size="large" />
            ) : (
                <Button title="Salvar Senha" onPress={alterarSenha} />
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        padding: 24,
        backgroundColor: '#fff',
    },
    title: {
        fontSize: 22,
        fontWeight: 'bold',
        marginBottom: 20,
        textAlign: 'center',
    },
    label: {
        marginTop: 12,
        fontWeight: '600',
    },
    value: {
        marginBottom: 8,
        fontSize: 16,
        color: '#333',
    },
    input: {
        height: 48,
        borderColor: '#ccc',
        borderWidth: 1,
        borderRadius: 8,
        paddingHorizontal: 12,
        marginBottom: 8,
    },
});
