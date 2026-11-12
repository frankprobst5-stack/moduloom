import { createRoot } from 'react-dom/client';
import AssignmentsAdmin from './AssignmentsAdmin';

const container = document.getElementById('assignments-admin-root');
if (container) createRoot(container).render(<AssignmentsAdmin />);
