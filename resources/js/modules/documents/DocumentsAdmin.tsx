import { useEffect, useState, FormEvent } from 'react';

type Doc = { id: number; title: string; url: string };
const API_BASE = '/api/modules/documents/documents';

export default function DocumentsAdmin() {
    const [docs, setDocs] = useState<Doc[]>([]);
    const [selected, setSelected] = useState<File | null>(null);

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setDocs);

    useEffect(load, []);

    const upload = (e: FormEvent) => {
        e.preventDefault();
        if (!selected) return;
        const form = new FormData();
        form.append('file', selected);
        fetch(API_BASE, { method: 'POST', body: form }).then(() => {
            setSelected(null);
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Documents</h1>
            <form onSubmit={upload} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input type="file" onChange={(e) => setSelected(e.target.files?.[0] ?? null)} style={{ flex: 1 }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Upload</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {docs.map((d) => (
                    <li key={d.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <a href={d.url} target="_blank" rel="noreferrer">{d.title}</a>
                        <button onClick={() => remove(d.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
