
import { View, Text, StyleSheet } from 'react-native';

export default function Relatorios() {
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
