import { createRoot } from 'react-dom/client';
import Marketplace from './Marketplace';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Marketplace />);
