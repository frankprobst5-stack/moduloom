import { createRoot } from 'react-dom/client';
import ClassroomsAdmin from './ClassroomsAdmin';

const container = document.getElementById('classrooms-admin-root');
if (container) createRoot(container).render(<ClassroomsAdmin />);
