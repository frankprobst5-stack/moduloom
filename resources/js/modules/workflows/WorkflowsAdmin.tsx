import { useEffect, useState, FormEvent } from 'react';

type WorkflowRequest = { id: number; title: string; status: string };
const API_BASE = '/api/modules/workflows/requests';

export default function WorkflowsAdmin() {
    const [requests, setRequests] = useState<WorkflowRequest[]>([]);
    const [title, setTitle] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setRequests);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title }),
        }).then(() => {
            setTitle('');
            load();
        });
    };

    const setStatus = (r: WorkflowRequest, status: string) => {
        fetch(`${API_BASE}/${r.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status }),
        }).then(load);
    };

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Workflows</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Request title" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Submit</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {requests.map((r) => (
                    <li key={r.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{r.title} ({r.status})</span>
                        {r.status === 'pending' && (
                            <span>
                                <button onClick={() => setStatus(r, 'approved')} style={{ background: 'none', border: 'none', color: '#111827', cursor: 'pointer', marginRight: 8 }}>Approve</button>
                                <button onClick={() => setStatus(r, 'rejected')} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Reject</button>
                            </span>
                        )}
                    </li>
                ))}
            </ul>
        </div>
    );
}
