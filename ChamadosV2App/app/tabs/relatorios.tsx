
import { View, Text, StyleSheet } from 'react-native';
import useAuthGuard from '../../hooks/useAuthGuard';

export default function Relatorios() {
    useAuthGuard();
    return (
        <View style={styles.container}>
            <Text style={styles.text}>Página de Relatórios</Text>
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
        fontWeight: '500',
    },
});
