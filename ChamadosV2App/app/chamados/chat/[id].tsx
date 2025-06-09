import React, { useEffect, useState } from 'react';
import {
    View,
    Text,
    TextInput,
    TouchableOpacity,
    FlatList,
    StyleSheet,
    ActivityIndicator,
    Platform,
    Linking,
} from 'react-native';
import * as DocumentPicker from 'expo-document-picker';
import { useLocalSearchParams } from 'expo-router';
import AsyncStorage from '@react-native-async-storage/async-storage';

interface Interaction {
    id: number;
    message: string;
    created_at: string;
    user: { id: number; name: string };
    attachment_url?: string;
}

export default function ChatChamado() {
    const { id } = useLocalSearchParams();
    const [mensagem, setMensagem] = useState('');
    const [mensagens, setMensagens] = useState<Interaction[]>([]);
    const [chamado, setChamado] = useState<any>(null);
    const [userId, setUserId] = useState<number | null>(null);
    const [carregando, setCarregando] = useState(true);
    const [anexo, setAnexo] = useState<DocumentPicker.DocumentPickerAsset | null>(null);

    const buscarMensagens = async () => {
        const token = await AsyncStorage.getItem('auth_token');
        const resp = await fetch(`http://127.0.0.1:8000/api/v1/calleds/${id}/interactions`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });
        const json = await resp.json();
        setMensagens(json.data || []);
        setCarregando(false);
    };

    const buscarChamado = async () => {
        const token = await AsyncStorage.getItem('auth_token');
        const resp = await fetch(`http://127.0.0.1:8000/api/v1/calleds/${id}`, {
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });
        const json = await resp.json();
        setChamado(json.data);
    };

    const buscarUsuario = async () => {
        const uid = await AsyncStorage.getItem('user_id');
        setUserId(Number(uid));
    };

    const enviarMensagem = async () => {
        const token = await AsyncStorage.getItem('auth_token');
        const formData = new FormData();
        formData.append('message', mensagem);
        if (anexo) {
            formData.append('file', {
                uri: anexo.uri,
                name: anexo.name,
                type: anexo.mimeType || 'application/octet-stream',
            } as any);
        }

        await fetch(`http://127.0.0.1:8000/api/v1/calleds/${id}/interactions`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
                'Content-Type': 'multipart/form-data',
            },
            body: formData,
        });

        setMensagem('');
        setAnexo(null);
        buscarMensagens();
    };

    const escolherArquivo = async () => {
        const result = await DocumentPicker.getDocumentAsync({
            type: ['image/*', 'video/*', 'application/pdf'],
            multiple: false,
            copyToCacheDirectory: true,
        });

        if (result.assets && result.assets.length > 0 && result.assets[0].uri) {
            setAnexo(result.assets[0]);
        }
    };

    useEffect(() => {
        buscarChamado();
        buscarUsuario();
        buscarMensagens();
    }, []);

    const excluirMensagem = async (msgId: number) => {
        const token = await AsyncStorage.getItem('auth_token');
        await fetch(`http://127.0.0.1:8000/api/v1/interactions/${msgId}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                Authorization: `Bearer ${token}`,
            },
        });
        buscarMensagens();
    };

    return (
        <View style={styles.container}>
            {carregando ? (
                <ActivityIndicator size="large" />
            ) : (
                <>
                    {chamado && (
                        <View style={{ padding: 10, backgroundColor: '#eef', marginBottom: 10 }}>
                            <Text style={{ fontWeight: 'bold', color: '#007bff'}}>DADOS DO CHAMADO</Text>
                            <Text style={{ fontWeight: 'bold' }}>Protocolo: {chamado.protocol}</Text>
                            <Text style={{ fontWeight: 'bold' }}>Plaqueta: {chamado.patrimony?.tag || 'N/A'}</Text>
                        </View>
                    )}
                    <View style={{ padding: 10, backgroundColor: '#eef', marginBottom: 10 }}>
                        <Text style={{ fontWeight: 'bold', color: '#007bff'}}>INTERAÇÕES</Text>
                    </View>
                    <FlatList
                        data={mensagens}
                        keyExtractor={(item) => String(item.id)}
                        renderItem={({ item }) => (
                            <View style={styles.msgBox}>
                                <Text style={styles.msgUser}>{item.user?.name}</Text>
                                <Text style={styles.msgText}>{item.message}</Text>

                                {item.attachment_url && (
                                    <TouchableOpacity onPress={() => Linking.openURL(item.attachment_url!)}>
                                        <Text style={styles.msgFile}>📎 Anexo: {item.attachment_url.split('/').pop()}</Text>
                                    </TouchableOpacity>
                                )}

                                {item.user?.id === userId && (
                                    <TouchableOpacity onPress={() => excluirMensagem(item.id)}>
                                        <Text style={{ color: 'red', fontWeight: 'bold' }}>🗑️</Text>
                                    </TouchableOpacity>
                                )}

                                <Text style={styles.msgDate}>{new Date(item.created_at).toLocaleString('pt-BR')}</Text>
                            </View>
                        )}
                    />

                    {anexo && <Text style={styles.anexoText}>📎 {anexo.name}</Text>}

                    <View style={styles.footer}>
                        <TouchableOpacity onPress={escolherArquivo} style={styles.anexoBtn}>
                            <Text style={{ color: '#fff' }}>📎</Text>
                        </TouchableOpacity>
                        <TextInput
                            style={styles.input}
                            value={mensagem}
                            onChangeText={setMensagem}
                            placeholder="Digite sua mensagem..."
                        />
                        <TouchableOpacity onPress={enviarMensagem} style={styles.sendBtn}>
                            <Text style={{ color: '#fff' }}>Enviar</Text>
                        </TouchableOpacity>
                    </View>
                </>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, padding: 10 },
    msgBox: { backgroundColor: '#f0f0f0', padding: 10, marginBottom: 8, borderRadius: 8 },
    msgUser: { fontWeight: 'bold', marginBottom: 2 },
    msgText: { fontSize: 14 },
    msgFile: { marginTop: 5, fontStyle: 'italic', color: '#007bff' },
    msgDate: { fontSize: 12, color: '#666', marginTop: 5 },
    anexoText: { textAlign: 'center', fontStyle: 'italic', marginBottom: 5 },
    footer: { flexDirection: 'row', alignItems: 'center', gap: 6, marginTop: 10 },
    input: { flex: 1, borderWidth: 1, borderColor: '#ccc', borderRadius: 8, paddingHorizontal: 10 },
    sendBtn: { backgroundColor: '#007bff', paddingVertical: 10, paddingHorizontal: 16, borderRadius: 8 },
    anexoBtn: { backgroundColor: '#555', padding: 10, borderRadius: 8 },
});
