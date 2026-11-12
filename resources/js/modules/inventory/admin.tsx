import { createRoot } from 'react-dom/client';
import InventoryAdmin from './InventoryAdmin';

const container = document.getElementById('inventory-admin-root');
if (container) createRoot(container).render(<InventoryAdmin />);
