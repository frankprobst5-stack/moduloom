import { createRoot } from 'react-dom/client';
import DirectoriesAdmin from './DirectoriesAdmin';

const container = document.getElementById('directories-admin-root');
if (container) createRoot(container).render(<DirectoriesAdmin />);
