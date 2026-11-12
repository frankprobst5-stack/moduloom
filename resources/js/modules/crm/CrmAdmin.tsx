import { useEffect, useState, FormEvent } from 'react';

type Contact = { id: number; name: string; email: string | null; phone: string | null };
const API_BASE = '/api/modules/crm/contacts';

export default function CrmAdmin() {
    const [contacts, setContacts] = useState<Contact[]>([]);
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setContacts);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email }),
        }).then(() => {
            setName('');
            setEmail('');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>CRM Contacts</h1>
            <form onSubmit={create} style={{ display: 'flex', flexDirection: 'column', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Name" required style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <input value={email} onChange={(e) => setEmail(e.target.value)} placeholder="Email" style={{ padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none', width: 'fit-content' }}>Add contact</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {contacts.map((c) => (
                    <li key={c.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{c.name} {c.email && <span style={{ color: '#6b7280' }}>({c.email})</span>}</span>
                        <button onClick={() => remove(c.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
