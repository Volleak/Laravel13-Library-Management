import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },

    // Add this CSS block to silence Bootstrap's legacy Sass warnings
    css: {
        preprocessorOptions: {
            scss: {
                quietDeps: true,
                silenceDeprecations: [
                    'import', 
                    'global-builtin', 
                    'color-functions', 
                    'if-function'
                ],
            },
        },
    },
});
