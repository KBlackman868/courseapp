import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    build: {
        // Enable CSS code splitting
        cssCodeSplit: true,
        // Optimize chunk splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Separate vendor chunks for better caching
                    'vendor-react': ['react', 'react-dom'],
                },
                // Optimize asset naming for caching
                assetFileNames: 'assets/[name]-[hash][extname]',
                chunkFileNames: 'assets/[name]-[hash].js',
            },
        },
        // Target modern browsers for smaller bundles
        target: 'es2020',
        // Report chunk sizes
        chunkSizeWarningLimit: 500,
    },
});
