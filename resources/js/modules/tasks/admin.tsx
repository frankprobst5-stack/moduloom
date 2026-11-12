import { createRoot } from 'react-dom/client';
import TasksAdmin from './TasksAdmin';

const container = document.getElementById('tasks-admin-root');
if (container) createRoot(container).render(<TasksAdmin />);
