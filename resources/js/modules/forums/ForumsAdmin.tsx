import { useEffect, useState, FormEvent } from 'react';

type Thread = { id: number; title: string; author_name: string; body: string; replies_count: number };
const API_BASE = '/api/modules/forums/threads';

export default function ForumsAdmin() {
    const [threads, setThreads] = useState<Thread[]>([]);
    const [title, setTitle] = useState('');
    const [author, setAuthor] = useState('');
    const [body, setBody] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setThreads);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, author_name: author, body }),
        }).then(() => {
            setTitle('');
            setBody('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Forums</h1>
            <form onSubmit={create} style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Thread title" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <input value={author} onChange={(e) => setAuthor(e.target.value)} placeholder="Your name" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <textarea value={body} onChange={(e) => setBody(e.target.value)} placeholder="Message" rows={3} required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none', width: 'fit-content' }}>Post thread</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {threads.map((t) => (
                    <li key={t.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '8px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{t.title} — {t.author_name} ({t.replies_count} replies)</span>
                        <button onClick={() => remove(t.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
