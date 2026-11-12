import { createRoot } from 'react-dom/client';
import PersonalDatabasesAdmin from './PersonalDatabasesAdmin';

const container = document.getElementById('personal-databases-admin-root');
if (container) createRoot(container).render(<PersonalDatabasesAdmin />);
