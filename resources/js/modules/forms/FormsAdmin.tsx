import { useEffect, useState, FormEvent } from 'react';

type Template = { id: number; title: string; submissions_count: number };
const API_BASE = '/api/modules/forms/templates';

export default function FormsAdmin() {
    const [templates, setTemplates] = useState<Template[]>([]);
    const [title, setTitle] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setTemplates);

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
            <h1 style={{ fontSize: 22 }}>Forms</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Form title" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Create</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {templates.map((t) => (
                    <li key={t.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{t.title} — {t.submissions_count} submissions</span>
                        <button onClick={() => remove(t.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
