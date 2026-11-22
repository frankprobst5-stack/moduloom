import { useEffect, useState } from 'react';
import { LogoLockup } from './Logo';

type Peer = {
    id: number;
    base_url: string;
    label: string;
    subscribed_content_types: string[];
    last_fetched_at: string | null;
    content_count: number;
};

const CONTENT_TYPES = [
    { slug: 'website', label: 'Website pages' },
    { slug: 'knowledge-base', label: 'Knowledge base articles' },
    { slug: 'public-information', label: 'Public notices' },
];

export default function Federation() {
    const [peers, setPeers] = useState<Peer[]>([]);
    const [forbidden, setForbidden] = useState(false);
    const [baseUrl, setBaseUrl] = useState('');
    const [label, setLabel] = useState('');
    const [types, setTypes] = useState<string[]>([]);
    const [error, setError] = useState<string | null>(null);
    const [busy, setBusy] = useState<string | null>(null);

    const load = () =>
        fetch('/api/federation/peers').then((r) => {
            if (r.status === 403) {
                setForbidden(true);
                return null;
            }
            return r.json();
        }).then((data) => {
            if (data) setPeers(data);
        });

    useEffect(load, []);

    const toggleType = (slug: string) => {
        setTypes((current) => current.includes(slug) ? current.filter((t) => t !== slug) : [...current, slug]);
    };

    const addPeer = (e: React.FormEvent) => {
        e.preventDefault();
        setBusy('add');
        setError(null);
        fetch('/api/federation/peers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ base_url: baseUrl, label, subscribed_content_types: types }),
        })
            .then((r) => {
                if (!r.ok) return r.json().then((body) => Promise.reject(new Error(body.message ?? 'Could not add that peer.')));
                return r.json();
            })
            .then(() => {
                setBaseUrl('');
                setLabel('');
                setTypes([]);
                load();
            })
            .catch((err: Error) => setError(err.message))
            .finally(() => setBusy(null));
    };

    const removePeer = (id: number) => {
        setBusy(`remove-${id}`);
        fetch(`/api/federation/peers/${id}`, { method: 'DELETE' }).then(load).finally(() => setBusy(null));
    };

    const fetchNow = (id: number) => {
        setBusy(`fetch-${id}`);
        fetch(`/api/federation/peers/${id}/fetch`, { method: 'POST' }).then(load).finally(() => setBusy(null));
    };

    if (forbidden) {
        return (
            <div style={{ maxWidth: 480, margin: '80px auto', fontFamily: 'var(--brand-font-body)', textAlign: 'center' }}>
                <LogoLockup size={22} variant="light" textColor="var(--brand-charcoal)" />
                <p style={{ color: 'var(--brand-muted)', marginTop: 24 }}>
                    Only the instance owner can manage federation peers.
                </p>
            </div>
        );
    }

    return (
        <div style={{ maxWidth: 640, margin: '40px auto', fontFamily: 'var(--brand-font-body)' }}>
            <div style={{ marginBottom: 16 }}>
                <LogoLockup size={20} variant="light" textColor="var(--brand-charcoal)" />
            </div>
            <h1 style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: 22, marginBottom: 4 }}>Federation</h1>
            <p style={{ color: 'var(--brand-muted)', fontSize: 14, marginTop: 0, marginBottom: 20 }}>
                Other Moduloom instances this one pulls public content from, by content type. Fetched hourly, or on demand below.
                Shows up for visitors at <a href="/federation/browse">/federation/browse</a> and alongside local notices on <a href="/notices">/notices</a>.
            </p>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 28 }}>
                {peers.map((p) => (
                    <div key={p.id} style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: '10px 14px' }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                            <div>
                                <strong style={{ fontSize: 14 }}>{p.label}</strong>
                                <p style={{ margin: '2px 0 0', color: 'var(--brand-muted)', fontSize: 13 }}>{p.base_url}</p>
                                <p style={{ margin: '4px 0 0', color: 'var(--brand-muted-soft)', fontSize: 12 }}>
                                    {p.subscribed_content_types.join(', ')} &middot; {p.content_count} cached item{p.content_count === 1 ? '' : 's'}
                                    {p.last_fetched_at ? ` · last fetched ${new Date(p.last_fetched_at).toLocaleString()}` : ' · never fetched yet'}
                                </p>
                            </div>
                            <div style={{ display: 'flex', gap: 6 }}>
                                <button onClick={() => fetchNow(p.id)} disabled={busy === `fetch-${p.id}`}
                                    style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--brand-border)', background: '#fff', fontSize: 12, cursor: 'pointer' }}>
                                    {busy === `fetch-${p.id}` ? 'Fetching…' : 'Fetch now'}
                                </button>
                                <button onClick={() => removePeer(p.id)} disabled={busy === `remove-${p.id}`}
                                    style={{ padding: '5px 10px', borderRadius: 6, border: '1px solid var(--brand-danger)', background: '#fff', color: 'var(--brand-danger)', fontSize: 12, cursor: 'pointer' }}>
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
                {peers.length === 0 && <p style={{ color: 'var(--brand-muted)', fontSize: 13 }}>No peers added yet.</p>}
            </div>

            <div style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: 20 }}>
                <strong style={{ fontSize: 13, textTransform: 'uppercase', color: 'var(--brand-muted)' }}>Add a peer</strong>
                <form onSubmit={addPeer} style={{ display: 'flex', flexDirection: 'column', gap: 10, marginTop: 12 }}>
                    {error && <p style={{ color: 'var(--brand-danger)', fontSize: 13, margin: 0 }}>{error}</p>}
                    <input value={baseUrl} onChange={(e) => setBaseUrl(e.target.value)} type="url" placeholder="https://their-instance.example.com" required
                        style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }} />
                    <input value={label} onChange={(e) => setLabel(e.target.value)} placeholder="Label (e.g. their org's name)" required
                        style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }} />
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                        {CONTENT_TYPES.map((t) => (
                            <label key={t.slug} style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 13 }}>
                                <input type="checkbox" checked={types.includes(t.slug)} onChange={() => toggleType(t.slug)} />
                                {t.label}
                            </label>
                        ))}
                    </div>
                    <button type="submit" disabled={busy === 'add' || types.length === 0}
                        style={{ padding: 10, border: 'none', borderRadius: 6, background: 'var(--brand-indigo)', color: '#fff', fontSize: 14, cursor: 'pointer' }}>
                        {busy === 'add' ? 'Adding…' : 'Add peer'}
                    </button>
                </form>
            </div>
        </div>
    );
}
