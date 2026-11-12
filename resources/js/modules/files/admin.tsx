import { createRoot } from 'react-dom/client';
import FilesAdmin from './FilesAdmin';

const container = document.getElementById('files-admin-root');
if (container) createRoot(container).render(<FilesAdmin />);
