import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../auth/AuthContext';

export default function Login() {
    const [email, setEmail] = useState('');
    const [senha, setSenha] = useState('');
    const [mostrarSenha, setMostrarSenha] = useState(false);
    const [loading, setLoading] = useState(false);
    const navigate = useNavigate();
    const { login } = useAuth();

    const handleLogin = async () => {
        setLoading(true);
        const sucesso = await login(email, senha);
        setLoading(false);

        if (sucesso) {
            navigate('/dashboard');
        } else {
            alert('E-mail ou senha inválidos.');
        }
    };

    return (
        <div className="min-h-screen flex items-center justify-center bg-gray-100 px-4">
            <div className="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
                <h1 className="text-center text-xl font-semibold text-gray-800">Laravel</h1>
                <h2 className="text-center text-2xl font-bold mb-6 text-gray-900">Faça login</h2>

                <div className="mb-4">
                    <label className="block text-sm font-medium mb-1 text-gray-700">
                        E-mail<span className="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        className="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="email@exemplo.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                    />
                </div>

                <div className="mb-4 relative">
                    <label className="block text-sm font-medium mb-1 text-gray-700">
                        Senha<span className="text-red-500">*</span>
                    </label>
                    <input
                        type={mostrarSenha ? 'text' : 'password'}
                        className="w-full px-4 py-2 border border-gray-300 rounded-md pr-10 focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="••••••••"
                        value={senha}
                        onChange={(e) => setSenha(e.target.value)}
                    />
                    <button
                        type="button"
                        onClick={() => setMostrarSenha(!mostrarSenha)}
                        className="absolute right-3 top-9 text-gray-500"
                    >
                        👁
                    </button>
                </div>

                <div className="mb-6 flex items-center space-x-2">
                    <input type="checkbox" id="remember" className="w-4 h-4" />
                    <label htmlFor="remember" className="text-sm text-gray-700">Lembre de mim</label>
                </div>

                <button
                    onClick={handleLogin}
                    disabled={loading}
                    className={`w-full text-white font-semibold py-2 rounded-lg transition ${
                        loading ? 'bg-orange-300' : 'bg-orange-600 hover:bg-orange-700'
                    }`}
                >
                    {loading ? 'Entrando...' : 'Login'}
                </button>
            </div>
        </div>
    );
}
