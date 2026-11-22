import { useEffect, useMemo, useState } from 'react';
import { LogoLockup } from './Logo';
import { csrfToken } from './csrf';

type ModuleEntry = {
    slug: string;
    label: string;
    description: string;
    category: string;
    requires: string[];
    installed: boolean;
    orphaned: boolean;
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

    const purge = (slug: string, label: string) => {
        if (!window.confirm(`Permanently delete all of ${label}'s data? This cannot be undone.`)) return;
        setBusy(slug);
        fetch(`/api/marketplace/purge/${slug}`, { method: 'DELETE' }).then(load).finally(() => setBusy(null));
    };

    return (
        <div style={{ maxWidth: 880, margin: '40px auto', fontFamily: 'var(--brand-font-body)' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
                <LogoLockup size={20} variant="light" textColor="var(--brand-charcoal)" />
                <form method="POST" action="/logout">
                    <input type="hidden" name="_token" value={csrfToken()} />
                    <button type="submit" style={{ background: 'transparent', border: '1px solid var(--brand-border)', borderRadius: 6, padding: '5px 10px', color: 'var(--brand-muted)', fontSize: 12, cursor: 'pointer' }}>
                        Log out
                    </button>
                </form>
            </div>
            <h1 style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: 22, marginBottom: 4 }}>Module marketplace</h1>
            <p style={{ color: 'var(--brand-muted)', fontSize: 14, marginTop: 0, marginBottom: 20 }}>
                Every module works standalone or inside any ecosystem. Installing one here never touches anything else already running.
            </p>

            <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap', marginBottom: 20 }}>
                {categories.map((c) => (
                    <button
                        key={c}
                        onClick={() => setCategory(c)}
                        style={{
                            padding: '6px 12px',
                            borderRadius: 999,
                            border: '1px solid var(--brand-border)',
                            background: category === c ? 'var(--brand-indigo)' : '#fff',
                            color: category === c ? '#fff' : 'var(--brand-charcoal)',
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
                    <div key={m.slug} style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: 16, display: 'flex', flexDirection: 'column', gap: 8 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                            <strong style={{ fontSize: 14 }}>{m.label}</strong>
                            <span style={{ fontSize: 11, color: 'var(--brand-muted-soft)', textTransform: 'uppercase' }}>{m.category}</span>
                        </div>
                        <p style={{ fontSize: 13, color: 'var(--brand-muted)', margin: 0, flex: 1 }}>{m.description}</p>
                        {m.requires.length > 0 && (
                            <p style={{ fontSize: 11, color: 'var(--brand-muted-soft)', margin: 0 }}>Requires: {m.requires.join(', ')}</p>
                        )}
                        {m.orphaned && (
                            <p style={{ fontSize: 11, color: 'var(--brand-brass)', margin: 0 }}>
                                Not in use — its data is still here. Reinstall to pick back up, or purge to delete it for good.
                            </p>
                        )}
                        {m.installed ? (
                            <button
                                onClick={() => uninstall(m.slug)}
                                disabled={busy === m.slug}
                                style={{ padding: '6px 0', borderRadius: 6, border: '1px solid var(--brand-border)', background: '#fff', cursor: 'pointer', fontSize: 13 }}
                            >
                                {busy === m.slug ? 'Working…' : 'Uninstall'}
                            </button>
                        ) : (
                            <button
                                onClick={() => install(m.slug)}
                                disabled={busy === m.slug}
                                style={{ padding: '6px 0', borderRadius: 6, border: 'none', background: 'var(--brand-indigo)', color: '#fff', cursor: 'pointer', fontSize: 13 }}
                            >
                                {busy === m.slug ? 'Working…' : m.orphaned ? 'Reinstall' : 'Install'}
                            </button>
                        )}
                        {m.orphaned && (
                            <button
                                onClick={() => purge(m.slug, m.label)}
                                disabled={busy === m.slug}
                                style={{ padding: '6px 0', borderRadius: 6, border: '1px solid var(--brand-danger)', background: '#fff', color: 'var(--brand-danger)', cursor: 'pointer', fontSize: 13 }}
                            >
                                {busy === m.slug ? 'Working…' : 'Purge data permanently'}
                            </button>
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}
