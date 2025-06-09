import React, { useState } from 'react';
import {
    View,
    Image,
    TouchableOpacity,
    StyleSheet,
    Text,
} from 'react-native';
import { router } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function AvatarUsuario() {
    const [menuAberto, setMenuAberto] = useState(false);

    const toggleMenu = () => {
        setMenuAberto((prev) => !prev);
    };

    const handleLogout = async () => {
        await AsyncStorage.clear();
        router.replace('/auth/login');
    };

    const handlePerfil = () => {
        router.push('/perfil'); // ou qualquer rota de perfil
    };

    return (
        <View style={styles.container}>
            <TouchableOpacity onPress={toggleMenu}>
                <Image
                    source={{
                        uri: 'https://ui-avatars.com/api/?name=Usuário&background=007bff&color=fff',
                    }}
                    style={styles.avatar}
                />
            </TouchableOpacity>

            {menuAberto && (
                <View style={styles.menu}>
                    <TouchableOpacity onPress={handlePerfil}>
                        <Text style={styles.menuItem}>👤 Perfil</Text>
                    </TouchableOpacity>
                    <TouchableOpacity onPress={handleLogout}>
                        <Text style={[styles.menuItem, { color: 'red' }]}>🚪 Logout</Text>
                    </TouchableOpacity>
                </View>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        position: 'relative',
        alignItems: 'flex-end',
    },
    avatar: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: '#ccc',
    },
    menu: {
        position: 'absolute',
        top: 40,
        right: 0,
        backgroundColor: '#fff',
        padding: 8,
        borderRadius: 8,
        elevation: 6,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.2,
        shadowRadius: 6,
        zIndex: 999,
    },
    menuItem: {
        paddingVertical: 6,
        paddingHorizontal: 12,
        fontSize: 14,
    },
});
