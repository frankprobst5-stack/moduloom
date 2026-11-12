import { createRoot } from 'react-dom/client';
import CrmAdmin from './CrmAdmin';

const container = document.getElementById('crm-admin-root');
if (container) createRoot(container).render(<CrmAdmin />);
