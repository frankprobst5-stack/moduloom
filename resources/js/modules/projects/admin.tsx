import { createRoot } from 'react-dom/client';
import ProjectsAdmin from './ProjectsAdmin';

const container = document.getElementById('projects-admin-root');
if (container) createRoot(container).render(<ProjectsAdmin />);
