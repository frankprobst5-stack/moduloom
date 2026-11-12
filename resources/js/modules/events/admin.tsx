import { createRoot } from 'react-dom/client';
import EventsAdmin from './EventsAdmin';

const container = document.getElementById('events-admin-root');
if (container) createRoot(container).render(<EventsAdmin />);
