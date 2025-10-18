import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        // Enable code splitting
        rollupOptions: {
            output: {
                manualChunks: {
                    // Split vendor code into separate chunk
                    vendor: ['alpinejs'],
                },
                // Asset file naming with hash for cache busting
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
                        return `images/[name]-[hash][extname]`;
                    } else if (/woff2?|ttf|otf|eot/i.test(ext)) {
                        return `fonts/[name]-[hash][extname]`;
                    }
                    return `assets/[name]-[hash][extname]`;
                },
                // Chunk file naming with hash
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
            },
        },
        // Optimize chunk size
        chunkSizeWarningLimit: 1000,
        // Enable minification
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true, // Remove console.log in production
                drop_debugger: true, // Remove debugger statements
                pure_funcs: ['console.log', 'console.info'], // Remove specific console methods
            },
            format: {
                comments: false, // Remove comments
            },
        },
        // Source maps for production debugging (optional)
        sourcemap: false,
        // CSS code splitting
        cssCodeSplit: true,
        // Asset inlining threshold (4kb)
        assetsInlineLimit: 4096,
    },
    // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs'],
    },
    // CSS minification
    css: {
        devSourcemap: true,
    },
});
