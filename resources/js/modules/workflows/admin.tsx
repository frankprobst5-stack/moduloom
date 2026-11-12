import { createRoot } from 'react-dom/client';
import WorkflowsAdmin from './WorkflowsAdmin';

const container = document.getElementById('workflows-admin-root');
if (container) createRoot(container).render(<WorkflowsAdmin />);
