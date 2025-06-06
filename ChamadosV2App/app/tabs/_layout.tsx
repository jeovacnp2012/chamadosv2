import { Tabs } from 'expo-router';
import { View } from 'react-native';
import AvatarUsuario from '../components/AvatarUsuario';

export default function TabsLayout() {
    return (
        <Tabs>
            <Tabs.Screen
                name="home"
                options={{
                    title: 'HOME',
                    headerRight: () => (
                        <View style={{ paddingRight: 3 }}>
                            <AvatarUsuario />
                        </View>
                    ),
                }}
            />
            <Tabs.Screen
                name="chamados"
                options={{
                    title: 'CHAMADOS',
                    headerRight: () => (
                        <View style={{ paddingRight: 3 }}>
                            <AvatarUsuario />
                        </View>
                    ),
                }}
            />
            <Tabs.Screen
                name="relatorios"
                options={{
                    title: 'RELATÓRIOS',
                    headerRight: () => (
                        <View style={{ paddingRight: 3 }}>
                            <AvatarUsuario />
                        </View>
                    ),
                }}
            />
        </Tabs>
    );
}
