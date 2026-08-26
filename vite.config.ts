import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue2 from '@vitejs/plugin-vue2';
import i18n from 'laravel-vue-i18n/vite';

const fontExtensions = ['woff', 'woff2', 'ttf', 'eot', 'otf'];
const imageExtensions = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 'webp', 'avif'];

export default defineConfig({
    define: {
        'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm.js',
        },
    },
    build: {
        emptyOutDir: false,
        rollupOptions: {
            output: {
                chunkFileNames: 'js/[name]-[hash].js',
                entryFileNames: 'js/[name]-[hash].js',
                assetFileNames: (assetInfo) => {
                    const name = assetInfo.name ?? '';
                    const ext = name.split('.').pop()?.toLowerCase() ?? '';

                    if (ext === 'css') return 'css/[name]-[hash].[ext]';
                    if (fontExtensions.includes(ext)) return 'font/[name]-[hash].[ext]';
                    if (imageExtensions.includes(ext)) return 'img/[name]-[hash].[ext]';
                    return 'assets/[name]-[hash].[ext]';
                },
            },
        },
    },
    optimizeDeps: {
        include: ['bootstrap', '@popperjs/core'],
    },
    plugins: [
        laravel({
            input: [
                'resources/vue/ts/app.ts',
                'resources/vue/sass/horizontcms-next.scss',
            ],
            refresh: true,
            publicDirectory: '.',
            buildDirectory: 'resources',
        }),
        vue2(),
        i18n('resources/lang'),
    ],
});
