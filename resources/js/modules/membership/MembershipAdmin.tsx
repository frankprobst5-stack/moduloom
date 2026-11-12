import { useEffect, useState, FormEvent } from 'react';

type Member = { id: number; member_name: string; tier: string; status: string };
const API_BASE = '/api/modules/membership/members';

export default function MembershipAdmin() {
    const [members, setMembers] = useState<Member[]>([]);
    const [name, setName] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setMembers);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ member_name: name }),
        }).then(() => {
            setName('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Membership</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Member name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {members.map((m) => (
                    <li key={m.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{m.member_name} — {m.tier} ({m.status})</span>
                        <button onClick={() => remove(m.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
