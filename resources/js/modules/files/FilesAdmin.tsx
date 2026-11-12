import { useEffect, useState, FormEvent } from 'react';

type UploadedFile = { id: number; original_name: string; size_bytes: number; url: string };
const API_BASE = '/api/modules/files/files';

export default function FilesAdmin() {
    const [files, setFiles] = useState<UploadedFile[]>([]);
    const [selected, setSelected] = useState<File | null>(null);
    const [error, setError] = useState<string | null>(null);

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setFiles);

    useEffect(load, []);

    const upload = (e: FormEvent) => {
        e.preventDefault();
        if (!selected) return;

        const form = new FormData();
        form.append('file', selected);

        fetch(API_BASE, { method: 'POST', body: form })
            .then((r) => {
                if (!r.ok) throw new Error('Upload failed.');
                return r.json();
            })
            .then(() => {
                setSelected(null);
                setError(null);
                load();
            })
            .catch((err: Error) => setError(err.message));
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Files</h1>
            {error && <p style={{ color: '#b91c1c' }}>{error}</p>}
            <form onSubmit={upload} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input type="file" onChange={(e) => setSelected(e.target.files?.[0] ?? null)} style={{ flex: 1 }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Upload</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {files.map((file) => (
                    <li key={file.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <a href={file.url} target="_blank" rel="noreferrer">{file.original_name}</a>
                        <button onClick={() => remove(file.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
