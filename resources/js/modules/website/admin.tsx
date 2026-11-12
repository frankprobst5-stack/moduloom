import { createRoot } from 'react-dom/client';
import WebsiteAdmin from './WebsiteAdmin';

const container = document.getElementById('website-admin-root');
if (container) createRoot(container).render(<WebsiteAdmin />);
