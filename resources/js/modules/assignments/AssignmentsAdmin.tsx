import { useEffect, useState, FormEvent } from 'react';

type Classroom = { id: number; name: string };
type Assignment = { id: number; title: string; classroom: Classroom; submissions_count: number };
const API_BASE = '/api/modules/assignments/assignments';
const CLASSROOMS_BASE = '/api/modules/classrooms/classrooms';

export default function AssignmentsAdmin() {
    const [assignments, setAssignments] = useState<Assignment[]>([]);
    const [classrooms, setClassrooms] = useState<Classroom[]>([]);
    const [title, setTitle] = useState('');
    const [classroomId, setClassroomId] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setAssignments);

    useEffect(() => {
        load();
        fetch(CLASSROOMS_BASE).then((r) => r.json()).then(setClassrooms);
    }, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, classroom_id: Number(classroomId) }),
        }).then(() => {
            setTitle('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Assignments</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Assignment title" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <select value={classroomId} onChange={(e) => setClassroomId(e.target.value)} required style={{ padding: 8, border: '1px solid #d1d5db' }}>
                    <option value="">Classroom…</option>
                    {classrooms.map((c) => <option key={c.id} value={c.id}>{c.name}</option>)}
                </select>
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {assignments.map((a) => (
                    <li key={a.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{a.title} — {a.classroom.name} ({a.submissions_count} submitted)</span>
                        <button onClick={() => remove(a.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
