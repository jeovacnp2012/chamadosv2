import TopBar from '../components/TopBar';
import BottomNav from '../components/BottomNav';


export default function Dashboard() {
    return (
        <div className="pb-20">
            <TopBar />
            <div className="p-4">
                <h1 className="text-xl font-bold">Bem-vindo ao ChamadosV2</h1>
            </div>
            <BottomNav />
        </div>
    );
}

