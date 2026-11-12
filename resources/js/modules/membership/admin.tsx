import { createRoot } from 'react-dom/client';
import MembershipAdmin from './MembershipAdmin';

const container = document.getElementById('membership-admin-root');
if (container) createRoot(container).render(<MembershipAdmin />);
