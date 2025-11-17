// File: vite.config.js

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // === INI PERBAIKANNYA ===
            // Pastikan kedua file ini (app.css dan app.js)
            // terdaftar di dalam array 'input'
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            // ========================
            
            refresh: true,
        }),
    ],
});