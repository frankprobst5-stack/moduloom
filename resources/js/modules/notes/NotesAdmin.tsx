import { useEffect, useState, FormEvent } from 'react';

type Note = {
    id: number;
    title: string;
    body: string | null;
};

const API_BASE = '/api/modules/notes/notes';

export default function NotesAdmin() {
    const [notes, setNotes] = useState<Note[]>([]);
    const [title, setTitle] = useState('');
    const [body, setBody] = useState('');
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    const loadNotes = () => {
        setLoading(true);
        fetch(API_BASE)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Notes module is not reachable (HTTP ${response.status}).`);
                }
                return response.json();
            })
            .then((data: Note[]) => {
                setNotes(data);
                setError(null);
            })
            .catch((err: Error) => setError(err.message))
            .finally(() => setLoading(false));
    };

    useEffect(loadNotes, []);

    const createNote = (event: FormEvent) => {
        event.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, body }),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Could not create note.');
                }
                return response.json();
            })
            .then(() => {
                setTitle('');
                setBody('');
                loadNotes();
            })
            .catch((err: Error) => setError(err.message));
    };

    const deleteNote = (id: number) => {
        fetch(`${API_BASE}/${id}`, { method: 'DELETE' })
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Could not delete note.');
                }
                loadNotes();
            })
            .catch((err: Error) => setError(err.message));
    };

    return (
        <div style={{ maxWidth: 560, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22, marginBottom: 4 }}>Notes</h1>
            <p style={{ color: '#6b7280', fontSize: 14, marginTop: 0, marginBottom: 24 }}>
                Phase 1's proof module — no dependencies on anything else in Moduloom.
            </p>

            {error && (
                <div style={{ background: '#fef2f2', color: '#b91c1c', padding: '8px 12px', marginBottom: 16, fontSize: 14 }}>
                    {error}
                </div>
            )}

            <form onSubmit={createNote} style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 24 }}>
                <input
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    placeholder="Title"
                    required
                    style={{ padding: 8, border: '1px solid #d1d5db' }}
                />
                <textarea
                    value={body}
                    onChange={(e) => setBody(e.target.value)}
                    placeholder="Body"
                    rows={3}
                    style={{ padding: 8, border: '1px solid #d1d5db' }}
                />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none', cursor: 'pointer', width: 'fit-content' }}>
                    Add note
                </button>
            </form>

            {loading ? (
                <p>Loading…</p>
            ) : (
                <ul style={{ listStyle: 'none', padding: 0, display: 'flex', flexDirection: 'column', gap: 8 }}>
                    {notes.map((note) => (
                        <li key={note.id} style={{ border: '1px solid #e5e7eb', padding: 12, display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', gap: 12 }}>
                            <div>
                                <strong>{note.title}</strong>
                                {note.body && <p style={{ margin: '4px 0 0', color: '#4b5563', fontSize: 14 }}>{note.body}</p>}
                            </div>
                            <button onClick={() => deleteNote(note.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer', fontSize: 13 }}>
                                Delete
                            </button>
                        </li>
                    ))}
                    {notes.length === 0 && <p style={{ color: '#6b7280' }}>No notes yet.</p>}
                </ul>
            )}
        </div>
    );
}
