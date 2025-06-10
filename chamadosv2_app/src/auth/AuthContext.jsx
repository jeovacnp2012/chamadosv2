import { createContext, useContext, useState, useEffect } from 'react';
import { storage } from './utils/storage';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [token, setToken] = useState(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const load = async () => {
            const storedToken = await storage.get('token');
            const storedUser = await storage.get('user');

            if (storedToken && storedUser) {
                setToken(storedToken);
                setUser(JSON.parse(storedUser));
            }

            setLoading(false);
        };

        load();
    }, []);

    const login = async (email, password) => {
        try {
            const response = await fetch('http://localhost:8000/api/v1/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password }),
            });

            const data = await response.json();
            if (!response.ok) return false;

            await storage.set('token', data.token);
            await storage.set('user', JSON.stringify(data.user));

            setToken(data.token);
            setUser(data.user);

            return true;
        } catch (err) {
            return false;
        }
    };

    const logout = async () => {
        await storage.remove('token');
        await storage.remove('user');
        setToken(null);
        setUser(null);
    };
    console.log('Ambiente de build:', import.meta.env);
    return (
        <AuthContext.Provider value={{ user, token, login, logout, loading }}>
            {children}
        </AuthContext.Provider>
    );
};

export const useAuth = () => useContext(AuthContext);
