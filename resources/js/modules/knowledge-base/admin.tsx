import { createRoot } from 'react-dom/client';
import KnowledgeBaseAdmin from './KnowledgeBaseAdmin';

const container = document.getElementById('kb-admin-root');
if (container) createRoot(container).render(<KnowledgeBaseAdmin />);
