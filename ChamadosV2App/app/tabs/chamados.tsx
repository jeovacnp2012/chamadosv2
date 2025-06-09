
import React, { useState, useEffect } from 'react';
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
import { router } from 'expo-router';
import useAuthGuard from '../../hooks/useAuthGuard';

interface Chamado {
    id: number;
    protocol: string;
    problem: string;
    status: string;
    created_at: string;
    closing_date?: string | null;
    user?: { id: number; name: string };
    sector?: { id: number; name: string };
    supplier?: { id: number; trade_name: string };
    patrimony?: { id: number; tag: string };
}

export default function Chamados() {
    useAuthGuard();

    const [status, setStatus] = useState<'A' | 'F' | null>(null);
    const [page, setPage] = useState(1);
    const [lastPage, setLastPage] = useState(1);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(false);
    const [chamados, setChamados] = useState<Chamado[]>([]);
    const [menuAberto, setMenuAberto] = useState<number | null>(null);
    const [role, setRole] = useState<string | null>(null);

    useEffect(() => {
        const obterPerfil = async () => {
            const token = await AsyncStorage.getItem('auth_token');
            const response = await fetch('http://127.0.0.1:8000/api/v1/me', {
                headers: {
                    Accept: 'application/json',
                    Authorization: `Bearer ${token}`,
                },
            });
            const data = await response.json();
            setRole(data.user?.role || null);
        };
        obterPerfil();
    }, []);


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
                `http://127.0.0.1:8000/api/v1/calleds?status=${tipo}&page=${pageParam}&per_page=5`,
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
                    onPress={() => buscarChamados('A', 1)}
                    style={[styles.button, status === 'A' && styles.buttonSelected]}
                >
                    <Text style={styles.buttonText}>Abertos</Text>
                </TouchableOpacity>
                <TouchableOpacity
                    onPress={() => buscarChamados('F', 1)}
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
                                            {role === 'Executor' ? (
                                                <>
                                                    <TouchableOpacity onPress={() => router.push(`/chamados/chat/${item.id}`)}>
                                                        <Text style={[styles.dropdownItem, { color: '#007bff' }]}>💬 Chat</Text>
                                                    </TouchableOpacity>
                                                    <TouchableOpacity onPress={() => router.push(`/chamados/baixa/${item.id}`)}>
                                                        <Text style={[styles.dropdownItem, { color: '#28a745' }]}>✅ Dar baixa</Text>
                                                    </TouchableOpacity>
                                                </>
                                            ) : (
                                                <>
                                                    <TouchableOpacity onPress={() => router.push(`/chamados/view/${item.id}`)}>
                                                        <Text style={styles.dropdownItem}>👁️ Visualizar</Text>
                                                    </TouchableOpacity>
                                                    <TouchableOpacity onPress={() => router.push(`/chamados/edit/${item.id}`)}>
                                                        <Text style={styles.dropdownItem}>✏️ Editar</Text>
                                                    </TouchableOpacity>
                                                    <TouchableOpacity onPress={() => handleDelete(item.id)}>
                                                        <Text style={[styles.dropdownItem, { color: 'red' }]}>🗑️ Excluir</Text>
                                                    </TouchableOpacity>
                                                    <TouchableOpacity onPress={() => router.push(`/chamados/chat/${item.id}`)}>
                                                        <Text style={[styles.dropdownItem, { color: '#007bff' }]}>💬 Chat</Text>
                                                    </TouchableOpacity>
                                                </>
                                            )}
                                        </View>
                                    )}

                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Protocolo:</Text>
                                    <Text style={styles.protocol}>{item.protocol}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Setor:</Text>
                                    <Text style={styles.value}>{item.sector?.name}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Responsável:</Text>
                                    <Text style={styles.value}>{item.user?.name}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Plaqueta:</Text>
                                    <Text style={styles.protocol}>{item.patrimony?.tag || 'Sem patrimônio'}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Data:</Text>
                                    <Text style={styles.value}>{formatarData(item.created_at)}</Text>
                                </View>
                                <View style={styles.infoRow}>
                                    <Text style={styles.label}>Problema:</Text>
                                    <Text style={styles.problem}>{item.problem}</Text>
                                </View>
                            </View>
                        )}
                    />
                    <View style={styles.pagination}>
                        <TouchableOpacity
                            onPress={() => page > 1 && buscarChamados(status!, page - 1)}
                            style={[styles.pageButton, page === 1 && { opacity: 0.5 }]}
                            disabled={page === 1}
                        >
                            <Text style={styles.pageButtonText}>◀ Anterior</Text>
                        </TouchableOpacity>
                        <Text style={styles.pageText}>
                            Página {page} de {lastPage}
                        </Text>
                        <TouchableOpacity
                            onPress={() => page < lastPage && buscarChamados(status!, page + 1)}
                            style={[styles.pageButton, page === lastPage && { opacity: 0.5 }]}
                            disabled={page === lastPage}
                        >
                            <Text style={styles.pageButtonText}>Próxima ▶</Text>
                        </TouchableOpacity>
                    </View>
                    <Text style={styles.totalText}>{total} Registros</Text>
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
    protocol: { fontWeight: 'bold', fontSize: 16, marginBottom: 4, color: '#222' },
    problem: { fontSize: 14, color: '#444', marginBottom: 8 },
    infoRow: { flexDirection: 'row', marginTop: 2 },
    label: { fontWeight: '600', marginRight: 4 },
    value: { color: '#555' },
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
    pagination: {
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        marginTop: 12,
        paddingHorizontal: 20,
    },
    pageButton: {
        backgroundColor: '#007bff',
        paddingVertical: 8,
        paddingHorizontal: 12,
        borderRadius: 6,
    },
    pageButtonText: {
        color: '#fff',
        fontWeight: 'bold',
    },
    pageText: {
        fontWeight: '600',
    },
});
