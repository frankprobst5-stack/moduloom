import { useEffect, useState } from 'react';

type NavEntry = { slug: string; label: string; sort_weight: number };
type LatestInstall = { profile_slug: string; version: string; installed_at: string } | null;

export default function Onboarding() {
    const [nav, setNav] = useState<NavEntry[]>([]);
    const [latest, setLatest] = useState<LatestInstall>(null);

    useEffect(() => {
        fetch('/api/dashboard/nav').then((r) => r.json()).then(setNav);
        fetch('/api/dashboard/latest-install').then((r) => r.json()).then(setLatest);
    }, []);

    return (
        <div style={{ maxWidth: 560, margin: '80px auto', fontFamily: 'system-ui, sans-serif', textAlign: 'center' }}>
            <h1 style={{ fontSize: 26 }}>You're all set</h1>
            {latest && (
                <p style={{ color: '#6b7280' }}>
                    {latest.profile_slug} v{latest.version} installed.
                </p>
            )}

            <div style={{ textAlign: 'left', border: '1px solid #e5e7eb', padding: 20, margin: '24px 0' }}>
                <strong style={{ fontSize: 13, color: '#6b7280', textTransform: 'uppercase' }}>Installed modules</strong>
                <ul style={{ paddingLeft: 18, marginBottom: 0 }}>
                    {nav.map((entry) => (
                        <li key={entry.slug}>{entry.label}</li>
                    ))}
                </ul>
            </div>

            <a href="/dashboard" style={{ display: 'inline-block', padding: '10px 20px', background: '#111827', color: '#fff', textDecoration: 'none' }}>
                Go to dashboard
            </a>
        </div>
    );
}
