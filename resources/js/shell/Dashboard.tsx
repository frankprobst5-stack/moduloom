import { useEffect, useState } from 'react';

type NavEntry = { slug: string; label: string; sort_weight: number };
type ThemeState = { active: string; available: string[] };

export default function Dashboard() {
    const [nav, setNav] = useState<NavEntry[]>([]);
    const [theme, setTheme] = useState<ThemeState | null>(null);
    const [selected, setSelected] = useState('');
    const [error, setError] = useState<string | null>(null);

    const loadTheme = () =>
        fetch('/api/theme')
            .then((r) => r.json())
            .then((data: ThemeState) => {
                setTheme(data);
                setSelected(data.active);
            });

    useEffect(() => {
        fetch('/api/dashboard/nav').then((r) => r.json()).then(setNav);
        loadTheme();
    }, []);

    const activate = () => {
        fetch('/api/theme/activate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ theme_slug: selected }),
        })
            .then((r) => {
                if (!r.ok) return r.json().then((body) => Promise.reject(new Error(body.message)));
                return r.json();
            })
            .then((data: ThemeState) => {
                setTheme(data);
                setError(null);
            })
            .catch((err: Error) => setError(err.message));
    };

    return (
        <div style={{ display: 'flex', minHeight: '100vh', fontFamily: 'system-ui, sans-serif' }}>
            <div style={{ width: 220, background: '#111827', color: '#fff', padding: 20 }}>
                <strong style={{ display: 'block', marginBottom: 16 }}>Moduloom</strong>
                {nav.map((entry) => (
                    <a
                        key={entry.slug}
                        href={`/admin/${entry.slug}`}
                        style={{ display: 'block', color: '#d1d5db', textDecoration: 'none', padding: '6px 0', fontSize: 14 }}
                    >
                        {entry.label}
                    </a>
                ))}
                <a
                    href="/marketplace"
                    style={{ display: 'block', color: '#9ca3af', textDecoration: 'none', padding: '10px 0 0', marginTop: 10, borderTop: '1px solid #262c38', fontSize: 13 }}
                >
                    + Browse modules
                </a>
            </div>
            <div style={{ flex: 1, padding: 40 }}>
                <h1 style={{ fontSize: 22 }}>Overview</h1>
                <p style={{ color: '#6b7280' }}>{nav.length} module{nav.length === 1 ? '' : 's'} installed.</p>

                {theme && (
                    <div style={{ marginTop: 24, border: '1px solid #e5e7eb', padding: 16, maxWidth: 360 }}>
                        <strong style={{ fontSize: 13, textTransform: 'uppercase', color: '#6b7280' }}>Public site theme</strong>
                        <p style={{ fontSize: 13, color: '#6b7280', margin: '4px 0 12px' }}>
                            Governs only public pages (Website, Knowledge base, Public information) — never this dashboard.
                        </p>
                        {error && <p style={{ color: '#b91c1c', fontSize: 13 }}>{error}</p>}
                        <div style={{ display: 'flex', gap: 8 }}>
                            <select value={selected} onChange={(e) => setSelected(e.target.value)} style={{ flex: 1, padding: 6 }}>
                                {!theme.available.includes(theme.active) && (
                                    <option value={theme.active}>{theme.active} (not yet designed)</option>
                                )}
                                {theme.available.map((slug) => (
                                    <option key={slug} value={slug}>{slug}</option>
                                ))}
                            </select>
                            <button onClick={activate} disabled={selected === theme.active} style={{ padding: '6px 12px', background: '#111827', color: '#fff', border: 'none', cursor: 'pointer' }}>
                                {selected === theme.active ? 'Active' : 'Switch'}
                            </button>
                        </div>
                        <p style={{ fontSize: 12, color: '#9ca3af', marginTop: 8 }}>Currently active: {theme.active}</p>
                    </div>
                )}
            </div>
        </div>
    );
}
