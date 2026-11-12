import { useEffect, useState, FormEvent } from 'react';

type Page = { id: number; title: string; slug: string; content: string | null };
const API_BASE = '/api/modules/website/pages';

export default function WebsiteAdmin() {
    const [pages, setPages] = useState<Page[]>([]);
    const [title, setTitle] = useState('');
    const [slug, setSlug] = useState('');
    const [content, setContent] = useState('');
    const [error, setError] = useState<string | null>(null);

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setPages);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, slug, content }),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Could not create page — slug may already be taken.');
                return r.json();
            })
            .then(() => {
                setTitle('');
                setSlug('');
                setContent('');
                setError(null);
                load();
            })
            .catch((err: Error) => setError(err.message));
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 560, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Website pages</h1>
            {error && <p style={{ color: '#b91c1c' }}>{error}</p>}
            <form onSubmit={create} style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Title" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <input value={slug} onChange={(e) => setSlug(e.target.value)} placeholder="slug-like-this" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <textarea value={content} onChange={(e) => setContent(e.target.value)} placeholder="Content" rows={3} style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none', width: 'fit-content' }}>Add page</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {pages.map((page) => (
                    <li key={page.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '8px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <a href={`/site/${page.slug}`} target="_blank" rel="noreferrer">{page.title}</a>
                        <button onClick={() => remove(page.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
