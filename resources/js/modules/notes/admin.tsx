import { createRoot } from 'react-dom/client';
import NotesAdmin from './NotesAdmin';

const container = document.getElementById('notes-admin-root');

if (container) {
    createRoot(container).render(<NotesAdmin />);
}
