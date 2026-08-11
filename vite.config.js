// Vite RETIRADO (T9, 2026-08-10):
// La aplicación no usa @vite en ninguna vista: los layouts cargan Bootstrap, jQuery y
// demás vía CDN. No existe node_modules en producción y no se ejecuta `npm run build`.
// Si en el futuro se adopta Vite, restaurar este archivo.
/*
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
        outDir: 'public/build',
        manifest: true,
    }
});
*/