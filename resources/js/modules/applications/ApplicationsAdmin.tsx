import { useEffect, useState, FormEvent } from 'react';

type App_ = { id: number; applicant_name: string; status: string };
const API_BASE = '/api/modules/applications/applications';

export default function ApplicationsAdmin() {
    const [apps, setApps] = useState<App_[]>([]);
    const [name, setName] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setApps);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ applicant_name: name }),
        }).then(() => {
            setName('');
            load();
        });
    };

    const approve = (a: App_) => {
        fetch(`${API_BASE}/${a.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'approved' }),
        }).then(load);
    };

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Applications</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Applicant name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Submit</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {apps.map((a) => (
                    <li key={a.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{a.applicant_name} ({a.status})</span>
                        {a.status !== 'approved' && <button onClick={() => approve(a)} style={{ background: 'none', border: 'none', color: '#111827', cursor: 'pointer' }}>Approve</button>}
                    </li>
                ))}
            </ul>
        </div>
    );
}
