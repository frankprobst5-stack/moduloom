import { createRoot } from 'react-dom/client';
import PublicInformationAdmin from './PublicInformationAdmin';

const container = document.getElementById('public-information-admin-root');
if (container) createRoot(container).render(<PublicInformationAdmin />);
