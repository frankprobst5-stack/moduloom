import { useEffect, useState, FormEvent } from 'react';

type Proj = { id: number; name: string; tasks_count: number };
const API_BASE = '/api/modules/projects/projects';

export default function ProjectsAdmin() {
    const [projects, setProjects] = useState<Proj[]>([]);
    const [name, setName] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setProjects);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name }),
        }).then(() => {
            setName('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Projects</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Project name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {projects.map((p) => (
                    <li key={p.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{p.name} — {p.tasks_count} tasks</span>
                        <button onClick={() => remove(p.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
