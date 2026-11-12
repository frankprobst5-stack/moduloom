import { useEffect, useState, FormEvent } from 'react';

type Rec = { id: number; title: string; record_type: string | null; is_redacted: boolean };
const API_BASE = '/api/modules/records/records';

export default function RecordsAdmin() {
    const [records, setRecords] = useState<Rec[]>([]);
    const [title, setTitle] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setRecords);

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

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Public Records</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Record title" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>File</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {records.map((r) => (
                    <li key={r.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{r.title}{r.is_redacted && <span style={{ color: '#b91c1c' }}> (redacted)</span>}</span>
                        <button onClick={() => remove(r.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
