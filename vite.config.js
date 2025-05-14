import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        // Genera manifest para que Laravel pueda mapear los assets
        manifest: true,
        
        // No usar contenthash en nombres de archivo
        rollupOptions: {
            output: {
                entryFileNames: `assets/[name].js`,
                chunkFileNames: `assets/[name].js`,
                assetFileNames: `assets/[name].[ext]`,
            },
        },
        
        // Ajustes para mejor compatibilidad
        minify: 'terser',
        terserOptions: {
            compress: {
                // Deshabilitar características avanzadas de JavaScript que podrían causar problemas
                ecma: 2015,
                drop_console: false,
                drop_debugger: true,
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
            'ziggy': '/vendor/tightenco/ziggy',
            'Modules': path.resolve(__dirname, 'Modules')
        },
    },
});