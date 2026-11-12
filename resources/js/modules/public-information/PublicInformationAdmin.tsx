import { useEffect, useState, FormEvent } from 'react';

type Notice = { id: number; title: string; body: string | null };
const API_BASE = '/api/modules/public-information/notices';

export default function PublicInformationAdmin() {
    const [notices, setNotices] = useState<Notice[]>([]);
    const [title, setTitle] = useState('');
    const [body, setBody] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setNotices);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, body }),
        }).then(() => {
            setTitle('');
            setBody('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Public information</h1>
            <form onSubmit={create} style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Notice title" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <textarea value={body} onChange={(e) => setBody(e.target.value)} placeholder="Body" rows={3} style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none', width: 'fit-content' }}>Publish</button>
            </form>
            <a href="/notices" target="_blank" rel="noreferrer" style={{ display: 'block', marginBottom: 12, fontSize: 13 }}>View public notices page →</a>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {notices.map((n) => (
                    <li key={n.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{n.title}</span>
                        <button onClick={() => remove(n.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
