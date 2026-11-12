import { useEffect, useState, FormEvent } from 'react';

type Invoice = { id: number; customer_name: string; amount_cents: number; status: string };
const API_BASE = '/api/modules/invoicing/invoices';

export default function InvoicingAdmin() {
    const [invoices, setInvoices] = useState<Invoice[]>([]);
    const [customerName, setCustomerName] = useState('');
    const [amount, setAmount] = useState('0');

    const load = () => fetch(API_BASE).then((r) => r.json()).then(setInvoices);

    useEffect(load, []);

    const create = (e: FormEvent) => {
        e.preventDefault();
        fetch(API_BASE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ customer_name: customerName, amount_cents: Math.round(Number(amount) * 100) }),
        }).then(() => {
            setCustomerName('');
            setAmount('0');
            load();
        });
    };

    const markPaid = (invoice: Invoice) => {
        fetch(`${API_BASE}/${invoice.id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ status: 'paid' }),
        }).then(load);
    };

    return (
        <div style={{ maxWidth: 480, margin: '40px auto', fontFamily: 'system-ui, sans-serif' }}>
            <h1 style={{ fontSize: 22 }}>Invoicing</h1>
            <form onSubmit={create} style={{ display: 'flex', gap: 8, marginBottom: 20 }}>
                <input value={customerName} onChange={(e) => setCustomerName(e.target.value)} placeholder="Customer" required style={{ flex: 1, padding: 8, border: '1px solid #d1d5db' }} />
                <input value={amount} onChange={(e) => setAmount(e.target.value)} type="number" step="0.01" style={{ width: 100, padding: 8, border: '1px solid #d1d5db' }} />
                <button type="submit" style={{ padding: '8px 16px', background: '#111827', color: '#fff', border: 'none' }}>Add</button>
            </form>
            <ul style={{ listStyle: 'none', padding: 0 }}>
                {invoices.map((inv) => (
                    <li key={inv.id} style={{ display: 'flex', justifyContent: 'space-between', padding: '6px 0', borderBottom: '1px solid #f3f4f6' }}>
                        <span>{inv.customer_name} — ${(inv.amount_cents / 100).toFixed(2)} ({inv.status})</span>
                        {inv.status !== 'paid' && <button onClick={() => markPaid(inv)} style={{ background: 'none', border: 'none', color: '#111827', cursor: 'pointer' }}>Mark paid</button>}
                    </li>
                ))}
            </ul>
        </div>
    );
}
