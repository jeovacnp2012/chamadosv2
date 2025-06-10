import { Navigate } from 'react-router-dom';
import { useAuth } from './AuthContext';

export default function PrivateRoute({ children }) {
    const { user, loading } = useAuth();

    if (loading) {
        return <div className="text-center p-6">Carregando...</div>;
    }

    if (!user) {
        return <Navigate to="/" />;
    }

    return children;
}
