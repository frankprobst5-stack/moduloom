import { useEffect, useState } from 'react';
import { LogoLockup } from './Logo';

type Profile = {
    slug: string;
    label: string;
    modules: string[];
    theme_slug: string;
    already_installed: boolean;
};

export default function Picker() {
    const [profiles, setProfiles] = useState<Profile[]>([]);
    const [installing, setInstalling] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        fetch('/api/setup/profiles')
            .then((r) => r.json())
            .then(setProfiles);
    }, []);

    const install = (slug: string) => {
        setInstalling(slug);
        setError(null);
        fetch(`/api/setup/install/${slug}`, { method: 'POST' })
            .then((r) => {
                if (!r.ok) throw new Error('Install failed.');
                return r.json();
            })
            .then(() => {
                window.location.href = `/setup/onboarding?profile=${slug}`;
            })
            .catch((err: Error) => {
                setError(err.message);
                setInstalling(null);
            });
    };

    return (
        <div style={{ maxWidth: 640, margin: '60px auto', fontFamily: 'var(--brand-font-body)' }}>
            <div style={{ marginBottom: 20 }}>
                <LogoLockup size={22} variant="light" textColor="var(--brand-charcoal)" />
            </div>
            <h1 style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: 24, marginBottom: 4 }}>Choose your ecosystem</h1>
            <p style={{ color: 'var(--brand-muted)', marginTop: 0, marginBottom: 24 }}>
                Every ecosystem is just a starting set of modules. Add or remove any module later.
            </p>

            {error && <p style={{ color: 'var(--brand-danger)' }}>{error}</p>}

            <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                {profiles.map((profile) => (
                    <div key={profile.slug} style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: 16, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div>
                            <strong>{profile.label}</strong>
                            <p style={{ margin: '4px 0 0', color: 'var(--brand-muted)', fontSize: 13 }}>
                                {profile.modules.join(', ')}
                            </p>
                        </div>
                        <button
                            onClick={() => install(profile.slug)}
                            disabled={installing === profile.slug}
                            style={{ padding: '8px 16px', borderRadius: 6, background: 'var(--brand-indigo)', color: '#fff', border: 'none', cursor: 'pointer' }}
                        >
                            {profile.already_installed
                                ? 'Reinstall'
                                : installing === profile.slug
                                  ? 'Installing…'
                                  : 'Install'}
                        </button>
                    </div>
                ))}
                {profiles.length === 0 && <p>No ecosystems available yet.</p>}
            </div>
        </div>
    );
}
