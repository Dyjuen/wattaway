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
        // Enable source maps for production debugging
        sourcemap: false,
        // Minify CSS and JS
        cssMinify: true,
        // Generate manifest for better caching
        // Optimize chunks
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['axios'],
                    tailwind: ['tailwindcss'],
                },
            },
        },
        // Increase chunk size warning limit
        chunkSizeWarningLimit: 1000,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['axios', 'tailwindcss'],
    },
    // Server configuration for development
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
});
