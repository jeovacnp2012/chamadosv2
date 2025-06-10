import { useAuth } from '../auth/AuthContext';
import { useState } from 'react';

export default function TopBar() {
    const { user, logout } = useAuth();
    const [open, setOpen] = useState(false);

    const initials = user?.name?.split(' ').map(p => p[0]).join('').toUpperCase() || '?';

    return (
        <div className="flex justify-end items-center p-4 bg-white shadow-md relative">
            <div
                onClick={() => setOpen(!open)}
                className="bg-blue-600 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold cursor-pointer"
            >
                {initials}
            </div>
            {open && (
                <div className="absolute right-4 top-16 bg-white border rounded shadow-md w-32">
                    <button onClick={() => alert('Perfil')} className="w-full px-4 py-2 hover:bg-gray-100 text-left">Perfil</button>
                    <button onClick={logout} className="w-full px-4 py-2 hover:bg-gray-100 text-left text-red-600">Sair</button>
                </div>
            )}
        </div>
    );
}
