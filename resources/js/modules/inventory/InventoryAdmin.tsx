import { useEffect, useState, FormEvent } from 'react';

type Item = { id: number; name: string; quantity: number; reorder_threshold: number };
const API_BASE = '/api/modules/inventory/items';

export default function InventoryAdmin() {
    const [items, setItems] = useState<Item[]>([]);
    const [name, setName] = useState('');
    const [quantity, setQuantity] = useState('0');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setItems);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, quantity: Number(quantity) }),
        }).then(() => {
            setName('');
            setQuantity('0');
            load();
        });
    };

    const remove = (id: number) => fetch(`${API_BASE}/${id}`, { method: 'DELETE' }).then(load);

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Inventory</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={name} onChange={(e) => setName(e.target.value)} placeholder="Item name" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <input value={quantity} onChange={(e) => setQuantity(e.target.value)} type="number" style={{ width: 80, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {items.map((item) => (
                    <li key={item.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{item.name} — {item.quantity}{item.quantity <= item.reorder_threshold && <span style={{ color: '#b91c1c' }}> (low)</span>}</span>
                        <button onClick={() => remove(item.id)} style={{ background: 'none', border: 'none', color: '#b91c1c', cursor: 'pointer' }}>Delete</button>
                    </li>
                ))}
            </ul>
        </div>
    );
}
