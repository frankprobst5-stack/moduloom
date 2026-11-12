import { useEffect, useState, FormEvent } from 'react';

type Task = { id: number; title: string; done: boolean };
const API_BASE = '/api/modules/tasks/tasks';

export default function TasksAdmin() {
    const [tasks, setTasks] = useState<Task[]>([]);
    const [title, setTitle] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setTasks);

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

    const toggle = (task: Task) => {
        fetch(`${API_BASE}/${task.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ done: !task.done }),
        }).then(load);
    };

    const remove = (id: number) => {
        fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);
    };

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Tasks</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="New task" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {tasks.map((task) => (
                    <li key={task.id} style={{ display: 'flex', alignItems: 'center', gap: 8, padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <input type="checkbox" checked={task.done} onChange={() => toggle(task)} />
                        <span style={{ flex: 1, textDecoration: task.done ? 'line-through' : 'none', color: task.done ? '#9ca3af' : '#111827' }}>{task.title}</span>
                        <button onClick={() => remove(task.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
