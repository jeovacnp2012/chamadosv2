import { Link, useLocation } from 'react-router-dom';

export default function BottomNav() {
    const location = useLocation();
    const current = location.pathname;

    return (
        <div className="fixed bottom-0 left-0 right-0 bg-gray-100 border-t flex justify-around py-2">
            <Link to="/dashboard" className={current === '/dashboard' ? 'font-bold text-blue-600' : ''}>Home</Link>
            <Link to="/chamados" className={current === '/chamados' ? 'font-bold text-blue-600' : ''}>Chamados</Link>
            <Link to="/relatorios" className={current === '/relatorios' ? 'font-bold text-blue-600' : ''}>Relatórios</Link>
        </div>
    );
}
