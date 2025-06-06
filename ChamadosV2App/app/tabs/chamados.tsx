import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    TouchableOpacity,
    FlatList,
    StyleSheet,
    ActivityIndicator,
    SafeAreaView,
} from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';

export default function Chamados() {
    const [status, setStatus] = useState<'A' | 'F' | null>(null);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(false);
    const [chamados, setChamados] = useState<any[]>([]);
    const [menuAberto, setMenuAberto] = useState<number | null>(null);

    const toggleMenu = (id: number) => {
        setMenuAberto(menuAberto === id ? null : id);
    };

    const buscarChamados = async (tipo: 'A' | 'F', pageParam = 1) => {
        setStatus(tipo);
        setLoading(true);
        setPage(pageParam);
        const token = await AsyncStorage.getItem('auth_token');
        try {
            const response = await fetch(
                `http://127.0.0.1:8000/api/v1/calleds?status=${tipo}&page=${pageParam}`,
                {
                    headers: {
                        Accept: 'application/json',
                        Authorization: `Bearer ${token}`,
                    },
                }
            );
            const json = await response.json();
            setChamados(json.data || []);
            setLastPage(json.last_page || 1);
            setTotal(json.total || json.data.length || 0);
        } catch (error) {
            console.error('Erro ao buscar chamados:', error);
        } finally {
            setLoading(false);
        }
    };

    const formatarData = (data: string) => {
        const d = new Date(data);
        return d.toLocaleDateString('pt-BR');
    };

    return (
        <SafeAreaView style={styles.container}>
            <Text style={styles.subtitle}>Meus Chamados</Text>
            <View style={styles.filters}>
                <TouchableOpacity
                    onPress={() => buscarChamados('A')}
                    style={[styles.button, status === 'A' && styles.buttonSelected]}
                >
                    <Text style={styles.buttonText}>Abertos</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    onPress={() => buscarChamados('F')}
                    style={[styles.button, status === 'F' && styles.buttonSelected]}
                >
                    <Text style={styles.buttonText}>Fechados</Text>
                </TouchableOpacity>
            </View>

            {loading ? (
                <ActivityIndicator size="large" />
            ) : (
                <>
                    <FlatList
                        data={chamados}
                        keyExtractor={(item) => String(item.id)}
                        renderItem={({ item }) => (
                            <View style={styles.card}>
                                <View style={styles.actionContainer}>
                                    <TouchableOpacity
                                        onPress={() => toggleMenu(item.id)}
                                        style={styles.actionButton}
                                    >
                                        <Text style={styles.actionButtonText}>⋮ Ações</Text>
                                    </TouchableOpacity>
                                    {menuAberto === item.id && (
                                        <View style={styles.dropdown}>
                                            <Text style={styles.dropdownItem}>👁️ Visualizar</Text>
                                            <Text style={styles.dropdownItem}>✏️ Editar</Text>
                                            <Text style={[styles.dropdownItem, { color: 'red' }]}>🗑️ Excluir</Text>
                                            <Text style={[styles.dropdownItem, { color: '#007bff' }]}>💬 Chat</Text>
                                        </View>
                                    )}
                                </View>
                                <Text style={styles.protocolo}>{item.protocolo}</Text>
                                <Text style={styles.problem}>{item.problem}</Text>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Setor:</Text>
                                    <Text style={styles.value}>{item.sector?.name}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Status:</Text>
                                    <Text style={[styles.value, item.closing_date ? styles.fechado : styles.aberto]}>
                                        {item.closing_date ? 'Fechado' : 'Aberto'}
                                    </Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Responsável:</Text>
                                    <Text style={styles.value}>{item.user?.name}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Data:</Text>
                                    <Text style={styles.value}>{formatarData(item.created_at)}</Text>
                                </View>
                            </View>
                        )}
                    />
                    {status && chamados.length > 0 && <Text style={styles.totalText}>{total} Registros</Text>}
                </>
            )}
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, padding: 16, backgroundColor: '#f4f4f4' },
    subtitle: { fontSize: 22, fontWeight: 'bold', marginBottom: 10, textAlign: 'center' },
    filters: { flexDirection: 'row', justifyContent: 'center', gap: 10, marginBottom: 20 },
    button: {
        backgroundColor: '#007bff',
        paddingVertical: 8,
        paddingHorizontal: 16,
        borderRadius: 8,
    },
    buttonSelected: { backgroundColor: '#0056b3' },
    buttonText: { color: '#fff', fontWeight: 'bold' },
    card: {
        backgroundColor: '#fff',
        padding: 12,
        borderRadius: 10,
        marginBottom: 12,
        elevation: 2,
        position: 'relative',
    },
    protocolo: { fontWeight: 'bold', fontSize: 16, marginBottom: 4, color: '#222' },
    problem: { fontSize: 14, color: '#444', marginBottom: 8 },
    infoRow: { flexDirection: 'row', marginTop: 2 },
    label: { fontWeight: '600', marginRight: 4 },
    value: { color: '#555' },
    aberto: { color: '#2e7d32', fontWeight: 'bold' },
    fechado: { color: '#c62828', fontWeight: 'bold' },
    totalText: { textAlign: 'center', fontSize: 14, marginVertical: 12, color: '#444' },
    actionContainer: {
        position: 'absolute',
        top: 8,
        right: 8,
        zIndex: 999,
    },
    actionButton: {
        backgroundColor: '#f39c12',
        paddingVertical: 4,
        paddingHorizontal: 10,
        borderRadius: 6,
    },
    actionButtonText: { color: '#fff', fontWeight: 'bold' },
    dropdown: {
        position: 'absolute',
        top: 36,
        right: 0,
        backgroundColor: '#ffffff',
        borderRadius: 12,
        paddingVertical: 8,
        paddingHorizontal: 12,
        width: 180,
        elevation: 10,
        zIndex: 999,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.2,
        shadowRadius: 6,
    },
    dropdownItem: { paddingVertical: 6, fontSize: 14 },
})
