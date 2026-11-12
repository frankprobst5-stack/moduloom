import { createRoot } from 'react-dom/client';
import DocumentsAdmin from './DocumentsAdmin';

const container = document.getElementById('documents-admin-root');
if (container) createRoot(container).render(<DocumentsAdmin />);
