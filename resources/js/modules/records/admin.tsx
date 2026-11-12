import { createRoot } from 'react-dom/client';
import RecordsAdmin from './RecordsAdmin';

const container = document.getElementById('records-admin-root');
if (container) createRoot(container).render(<RecordsAdmin />);
