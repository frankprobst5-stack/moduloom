import { createRoot } from 'react-dom/client';
import Federation from './Federation';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Federation />);
