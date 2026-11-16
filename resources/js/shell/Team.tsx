import { useEffect, useState } from 'react';
import { LogoLockup } from './Logo';

type Member = { id: number; name: string; email: string; roles: string[] };
type RoleOption = { role_slug: string; label: string };

export default function Team() {
    const [members, setMembers] = useState<Member[]>([]);
    const [roles, setRoles] = useState<RoleOption[]>([]);
    const [forbidden, setForbidden] = useState(false);
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [roleSlug, setRoleSlug] = useState('');
    const [error, setError] = useState<string | null>(null);
    const [busy, setBusy] = useState(false);

    const load = () =>
        fetch('/api/team').then((r) => {
            if (r.status === 403) {
                setForbidden(true);
                return null;
            }
            return r.json();
        }).then((data) => {
            if (!data) return;
            setMembers(data.members);
            setRoles(data.available_roles);
            setRoleSlug((current) => current || data.available_roles[0]?.role_slug || '');
        });

    useEffect(load, []);

    const create = (e: React.FormEvent) => {
        e.preventDefault();
        setBusy(true);
        setError(null);
        fetch('/api/team', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password, role_slug: roleSlug }),
        })
            .then((r) => {
                if (!r.ok) return r.json().then((body) => Promise.reject(new Error(body.message ?? 'Could not create that account.')));
                return r.json();
            })
            .then(() => {
                setName('');
                setEmail('');
                setPassword('');
                load();
            })
            .catch((err: Error) => setError(err.message))
            .finally(() => setBusy(false));
    };

    if (forbidden) {
        return (
            <div style={{ maxWidth: 480, margin: '80px auto', fontFamily: 'var(--brand-font-body)', textAlign: 'center' }}>
                <LogoLockup size={22} variant="light" textColor="var(--brand-charcoal)" />
                <p style={{ color: 'var(--brand-muted)', marginTop: 24 }}>
                    Only the instance owner can manage team accounts.
                </p>
            </div>
        );
    }

    return (
        <div style={{ maxWidth: 640, margin: '40px auto', fontFamily: 'var(--brand-font-body)' }}>
            <div style={{ marginBottom: 16 }}>
                <LogoLockup size={20} variant="light" textColor="var(--brand-charcoal)" />
            </div>
            <h1 style={{ fontFamily: 'var(--brand-font-display)', fontWeight: 600, fontSize: 22, marginBottom: 4 }}>Team</h1>
            <p style={{ color: 'var(--brand-muted)', fontSize: 14, marginTop: 0, marginBottom: 20 }}>
                Accounts that can sign in to this instance, and the role each one holds.
            </p>

            <div style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 28 }}>
                {members.map((m) => (
                    <div key={m.id} style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: '10px 14px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div>
                            <strong style={{ fontSize: 14 }}>{m.name}</strong>
                            <p style={{ margin: '2px 0 0', color: 'var(--brand-muted)', fontSize: 13 }}>{m.email}</p>
                        </div>
                        <span style={{ fontSize: 12, color: 'var(--brand-muted)', textTransform: 'capitalize' }}>{m.roles.join(', ') || 'no role'}</span>
                    </div>
                ))}
            </div>

            <div style={{ border: '1px solid var(--brand-border)', borderRadius: 10, padding: 20 }}>
                <strong style={{ fontSize: 13, textTransform: 'uppercase', color: 'var(--brand-muted)' }}>Add an account</strong>

                {roles.length === 0 ? (
                    <p style={{ fontSize: 13, color: 'var(--brand-muted)', marginTop: 10 }}>
                        No roles exist on this instance yet — install a profile first, which creates the roles it needs.
                    </p>
                ) : (
                    <form onSubmit={create} style={{ display: 'flex', flexDirection: 'column', gap: 10, marginTop: 12 }}>
                        {error && <p style={{ color: 'var(--brand-danger)', fontSize: 13, margin: 0 }}>{error}</p>}
                        <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Name" required
                            style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }} />
                        <input value={email} onChange={(e) => setEmail(e.target.value)} type="email" placeholder="Email" required
                            style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }} />
                        <input value={password} onChange={(e) => setPassword(e.target.value)} type="password" placeholder="Password" minLength={8} required
                            style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }} />
                        <select value={roleSlug} onChange={(e) => setRoleSlug(e.target.value)}
                            style={{ padding: 8, border: '1px solid var(--brand-border)', borderRadius: 6, fontSize: 14 }}>
                            {roles.map((r) => (
                                <option key={r.role_slug} value={r.role_slug}>{r.label}</option>
                            ))}
                        </select>
                        <button type="submit" disabled={busy}
                            style={{ padding: 10, border: 'none', borderRadius: 6, background: 'var(--brand-indigo)', color: '#fff', fontSize: 14, cursor: 'pointer' }}>
                            {busy ? 'Creating…' : 'Create account'}
                        </button>
                        <p style={{ fontSize: 12, color: 'var(--brand-muted-soft)', margin: 0 }}>
                            Share this password with them directly — there's no email invite yet.
                        </p>
                    </form>
                )}
            </div>
        </div>
    );
}
