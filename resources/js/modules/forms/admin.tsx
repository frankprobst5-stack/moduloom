import { createRoot } from 'react-dom/client';
import FormsAdmin from './FormsAdmin';

const container = document.getElementById('forms-admin-root');
if (container) createRoot(container).render(<FormsAdmin />);
