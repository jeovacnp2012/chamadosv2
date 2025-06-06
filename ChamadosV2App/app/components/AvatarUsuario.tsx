import React from 'react';
import { Image, StyleSheet } from 'react-native';

export default function AvatarUsuario() {
    return (
        <Image
            source={{ uri: 'https://ui-avatars.com/api/?name=Usuário&background=007bff&color=fff' }}
            style={styles.avatar}
        />
    );
}

const styles = StyleSheet.create({
    avatar: {
        width: 36,
        height: 36,
        borderRadius: 18,
        backgroundColor: '#ccc',
    },
});
