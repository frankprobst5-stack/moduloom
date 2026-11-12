import { useEffect, useState, FormEvent } from 'react';

type Evt = { id: number; title: string; starts_at: string; registrations_count: number };
const API_BASE = '/api/modules/events/events';

export default function EventsAdmin() {
    const [events, setEvents] = useState<Evt[]>([]);
    const [title, setTitle] = useState('');
    const [startsAt, setStartsAt] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setEvents);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, starts_at: startsAt }),
        }).then(() => {
            setTitle('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Events</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Event title" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <input value={startsAt} onChange={(e) => setStartsAt(e.target.value)} type="datetime-local" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {events.map((e) => (
                    <li key={e.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{e.title} — {e.registrations_count} registered</span>
                        <button onClick={() => remove(e.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
