import { useEffect, useState } from 'react';
import { LogoLockup } from './Logo';

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
        <div style={{ maxWidth: 560, margin: '80px auto', fontFamily: 'var(--brand-font-body)', textAlign: 'center' }}>
            <div style={{ display: 'flex', justifyContent: 'center', marginBottom: 20 }}>
                <LogoLockup size={22} variant="light" textColor="var(--brand-charcoal)" />
            </div>
            <h1 style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: 26 }}>You're all set</h1>
            {latest && (
                <p style={{ color: 'var(--brand-muted)' }}>
                    {latest.profile_slug} v{latest.version} installed.
                </p>
            )}

            <div style={{ textAlign: 'left', border: '1px solid var(--brand-border)', borderRadius: 10, padding: 20, margin: '24px 0' }}>
                <strong style={{ fontSize: 13, color: 'var(--brand-muted)', textTransform: 'uppercase' }}>Installed modules</strong>
                <ul style={{ paddingLeft: 18, marginBottom: 0 }}>
                    {nav.map((entry) => (
                        <li key={entry.slug}>{entry.label}</li>
                    ))}
                </ul>
            </div>

            <a href="/dashboard" style={{ display: 'inline-block', padding: '10px 20px', borderRadius: 6, background: 'var(--brand-indigo)', color: '#fff', textDecoration: 'none' }}>
                Go to dashboard
            </a>
        </div>
    );
}
