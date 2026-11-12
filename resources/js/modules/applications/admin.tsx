import { createRoot } from 'react-dom/client';
import ApplicationsAdmin from './ApplicationsAdmin';

const container = document.getElementById('applications-admin-root');
if (container) createRoot(container).render(<ApplicationsAdmin />);
