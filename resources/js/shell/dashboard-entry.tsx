import { createRoot } from 'react-dom/client';
import Dashboard from './Dashboard';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Dashboard />);
