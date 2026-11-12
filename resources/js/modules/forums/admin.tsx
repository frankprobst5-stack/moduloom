import { createRoot } from 'react-dom/client';
import ForumsAdmin from './ForumsAdmin';

const container = document.getElementById('forums-admin-root');
if (container) createRoot(container).render(<ForumsAdmin />);
