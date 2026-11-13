import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    build: {
        rollupOptions: {
            output: {
                // pdfjs-dist's worker ships as .mjs. Servers without an .mjs MIME mapping
                // (common on nginx/Apache) send it as application/octet-stream, and the
                // browser refuses to start it as a module worker. Emit it as .js instead.
                assetFileNames: ({ names }) => names?.some(n => n.endsWith('.mjs'))
                    ? 'assets/[name]-[hash].js'
                    : 'assets/[name]-[hash][extname]',
            },
        },
    },
});
