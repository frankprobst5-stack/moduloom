import { createRoot } from 'react-dom/client';
import Picker from './Picker';

const container = document.getElementById('shell-root');
if (container) createRoot(container).render(<Picker />);
