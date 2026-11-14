import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/shell/picker-entry.tsx',
                'resources/js/shell/onboarding-entry.tsx',
                'resources/js/shell/dashboard-entry.tsx',
                'resources/js/shell/marketplace-entry.tsx',
                'resources/js/modules/notes/admin.tsx',
                'resources/js/modules/tasks/admin.tsx',
                'resources/js/modules/files/admin.tsx',
                'resources/js/modules/personal-databases/admin.tsx',
                'resources/js/modules/website/admin.tsx',
                'resources/js/modules/crm/admin.tsx',
                'resources/js/modules/inventory/admin.tsx',
                'resources/js/modules/invoicing/admin.tsx',
                'resources/js/modules/forums/admin.tsx',
                'resources/js/modules/membership/admin.tsx',
                'resources/js/modules/events/admin.tsx',
                'resources/js/modules/directories/admin.tsx',
                'resources/js/modules/classrooms/admin.tsx',
                'resources/js/modules/assignments/admin.tsx',
                'resources/js/modules/knowledge-base/admin.tsx',
                'resources/js/modules/documents/admin.tsx',
                'resources/js/modules/workflows/admin.tsx',
                'resources/js/modules/projects/admin.tsx',
                'resources/js/modules/applications/admin.tsx',
                'resources/js/modules/public-information/admin.tsx',
                'resources/js/modules/forms/admin.tsx',
                'resources/js/modules/records/admin.tsx',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
        react(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
