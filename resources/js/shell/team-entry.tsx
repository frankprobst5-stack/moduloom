import { createRoot } from 'react-dom/client';
import Team from './Team';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Team />);
