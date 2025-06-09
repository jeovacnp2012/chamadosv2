import useAuthGuard from '../hooks/useAuthGuard';
import {View, Text} from 'react-native';
export default function Index() {
    useAuthGuard();

    return (
        <View>
            <Text>Bem-vindo à tela inicial</Text>
        </View>
    );
}
