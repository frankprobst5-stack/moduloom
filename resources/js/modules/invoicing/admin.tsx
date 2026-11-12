import { createRoot } from 'react-dom/client';
import InvoicingAdmin from './InvoicingAdmin';

const container = document.getElementById('invoicing-admin-root');
if (container) createRoot(container).render(<InvoicingAdmin />);
