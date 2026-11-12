import { createRoot } from 'react-dom/client';
import Onboarding from './Onboarding';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Onboarding />);
