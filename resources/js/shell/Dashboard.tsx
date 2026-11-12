import { useEffect, useState } from 'react';

type NavEntry = { slug: string; label: string; sort_weight: number };

export default function Dashboard() {
    const [nav, setNav] = useState<NavEntry[]>([]);

    useEffect(() => {
        fetch('/api/dashboard/nav').then((r) => r.json()).then(setNav);
    }, []);

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
            </div>
            <div style={{ flex: 1, padding: 40 }}>
                <h1 style={{ fontSize: 22 }}>Overview</h1>
                <p style={{ color: '#6b7280' }}>{nav.length} module{nav.length === 1 ? '' : 's'} installed.</p>
            </div>
        </div>
    );
}
