import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    // Base para el subdirectorio en producción
    base: '/build/',
    
    build: {
        // Target para compatibilidad con navegadores
        target: 'es2015',
        
        // Configuración mejorada para producción
        minify: 'terser',
        
        // Asegurar que se genere el manifest.json
        manifest: true,
    },
    
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
            publicDirectory: 'public',
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
    
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
            'Modules': path.resolve(__dirname, 'Modules')
        },
    },
});