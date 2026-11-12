import { useEffect, useState, FormEvent } from 'react';

type Entry = { id: number; name: string; category: string | null };
const API_BASE = '/api/modules/directories/entries';

export default function DirectoriesAdmin() {
    const [entries, setEntries] = useState<Entry[]>([]);
    const [name, setName] = useState('');
    const [category, setCategory] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setEntries);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, category }),
        }).then(() => {
            setName('');
            setCategory('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Directories</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <input value={category} onChange={(e) => setCategory(e.target.value)} placeholder="Category" style={{ width: 140, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {entries.map((e) => (
                    <li key={e.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{e.name} {e.category && <span style={{ color: '#6b7280' }}>({e.category})</span>}</span>
                        <button onClick={() => remove(e.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
