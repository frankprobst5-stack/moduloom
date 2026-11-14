import { useEffect, useMemo, useState } from 'react';

type ModuleEntry = {
    slug: string;
    label: string;
    description: string;
    category: string;
    requires: string[];
    installed: boolean;
    reference_count: number;
};

export default function Marketplace() {
    const [modules, setModules] = useState<ModuleEntry[]>([]);
    const [category, setCategory] = useState('All');
    const [busy, setBusy] = useState<string | null>(null);

    const load = () => fetch('/api/marketplace/modules').then((r) => r.json()).then(setModules);

    useEffect(load, []);

    const categories = useMemo(() => ['All', ...Array.from(new Set(modules.map((m) => m.category)))], [modules]);
    const visible = category === 'All' ? modules : modules.filter((m) => m.category === category);

    const install = (slug: string) => {
        setBusy(slug);
        fetch(`/api/marketplace/install/${slug}`, { method: 'POST' }).then(load).finally(() => setBusy(null));
    };

    const uninstall = (slug: string) => {
        setBusy(slug);
        fetch(`/api/marketplace/uninstall/${slug}`, { method: 'POST' }).then(load).finally(() => setBusy(null));
    };

    return (
        <div style={{ maxWidth: 880, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22, marginBottom: 4 }}>Module marketplace</h1>
            <p style={{ color: '#6b7280', fontSize: 14, marginTop: 0, marginBottom: 20 }}>
                Every module works standalone or inside any ecosystem. Installing one here never touches anything else already running.
            </p>

            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginBottom: 20 }}>
                {categories.map((c) => (
                    <button
                        key={c}
                        onClick={() => setCategory(c)}
                        style={{
                            padding: '6px 12px',
                            border: '1px solid #d1d5db',
                            background: category === c ? '#111827' : '#fff',
                            color: category === c ? '#fff' : '#111827',
                            cursor: 'pointer',
                            fontSize: 13,
                        }}
                    >
                        {c}
                    </button>
                ))}
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: 12 }}>
                {visible.map((m) => (
                    <div key={m.slug} style={{ border: '1px solid #e5e7eb', padding: 16, display: 'flex', flexDirection: 'column', gap: 8 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                            <strong style={{ fontSize: 14 }}>{m.label}</strong>
                            <span style={{ fontSize: 11, color: '#9ca3af', textTransform: 'uppercase' }}>{m.category}</span>
                        </div>
                        <p style={{ fontSize: 13, color: '#6b7280', margin: 0, flex: 1 }}>{m.description}</p>
                        {m.requires.length > 0 && (
                            <p style={{ fontSize: 11, color: '#9ca3af', margin: 0 }}>Requires: {m.requires.join(', ')}</p>
                        )}
                        {m.installed ? (
                            <button
                                onClick={() => uninstall(m.slug)}
                                disabled={busy === m.slug}
                                style={{ padding: '6px 0', border: '1px solid #d1d5db', background: '#fff', cursor: 'pointer', fontSize: 13 }}
                            >
                                {busy === m.slug ? 'Working…' : 'Uninstall'}
                            </button>
                        ) : (
                            <button
                                onClick={() => install(m.slug)}
                                disabled={busy === m.slug}
                                style={{ padding: '6px 0', border: 'none', background: '#111827', color: '#fff', cursor: 'pointer', fontSize: 13 }}
                            >
                                {busy === m.slug ? 'Working…' : 'Install'}
                            </button>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}
