import { useEffect, useState, FormEvent } from 'react';

type Collection = { id: number; name: string; records_count: number };
type Record_ = { id: number; data: Record<string, unknown> };

const API_BASE = '/api/modules/personal-databases';

export default function PersonalDatabasesAdmin() {
    const [collections, setCollections] = useState<Collection[]>([]);
    const [name, setName] = useState('');
    const [active, setActive] = useState<Collection | null>(null);
    const [records, setRecords] = useState<Record_[]>([]);
    const [rawJson, setRawJson] = useState('{"field": "value"}');
    const [error, setError] = useState<string | null>(null);

    const loadCollections = () => fetch(`${API_BASE}/collections`).then((r) => r.json()).then(setCollections);

    useEffect(loadCollections, []);

    const createCollection = (e: FormEvent) => {
        e.preventDefault();
        fetch(`${API_BASE}/collections`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name }),
        }).then(() => {
            setName('');
            loadCollections();
        });
    };

    const openCollection = (collection: Collection) => {
        setActive(collection);
        fetch(`${API_BASE}/collections/${collection.id}/records`).then((r) => r.json()).then(setRecords);
    };

    const addRecord = (e: FormEvent) => {
        e.preventDefault();
        if (!active) return;

        let parsed: Record<string, unknown>;
        try {
            parsed = JSON.parse(rawJson);
        } catch {
            setError('That is not valid JSON.');
            return;
        }

        fetch(`${API_BASE}/collections/${active.id}/records`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ data: parsed }),
        }).then(() => {
            setError(null);
            openCollection(active);
            loadCollections();
        });
    };

    return (
        <div style={{ maxWidth: 640, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Personal databases</h1>
            <p style={{ color: '#6b7280', fontSize: 13 }}>
                Named collections of free-form JSON records — a deliberately minimal first version.
            </p>

            <form onSubmit={createCollection} style={{ display: 'flex', gap: 8, margin: '16px 0' }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Collection name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Create</button>
            </form>

            <div style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                {collections.map((c) => (
                    <button key={c.id} onClick={() => openCollection(c)} style={{ padding: '6px 12px', border: active?.id === c.id ? '1px solid #111827' : '1px solid #d1d5db', background: '#fff', cursor: 'pointer' }}>
                        {c.name} ({c.records_count})
                    </button>
                ))}
            </div>

            {active && (
                <div>
                    <h2 style={{ fontSize: 16 }}>{active.name}</h2>
                    {error && <p style={{ color: '#b91c1c' }}>{error}</p>}
                    <form onSubmit={addRecord} style={{ display: 'flex', gap: 8, marginBottom: 12 }}>
                        <textarea value={rawJson} onChange={(e) => setRawJson(e.target.value)} rows={2} style={{ flex: 1, padding: 8, border: '1px solid #d1d5db', fontFamily: 'monospace' }} />
                        <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add record</button>
                    </form>
                    <pre style={{ background: '#f9fafb', padding: 12, fontSize: 12 }}>{JSON.stringify(records, null, 2)}</pre>
                </div>
            )}
        </div>
    );
}
