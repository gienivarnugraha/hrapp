import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vuetify from 'vite-plugin-vuetify'
import vue from '@vitejs/plugin-vue';
import AutoImport from 'unplugin-auto-import/vite'
import Pages from 'vite-plugin-pages'
import Components from 'unplugin-vue-components/vite'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
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
        vuetify({
            // styles: { configFile: 'resources/sass/app.scss' }
        }),
        AutoImport({
            imports: [
                'vue',
                'vue-router',
                'pinia',
            ]
        }),
        Components({
            dirs: ['resources/js/components'],
            dts: 'resources/js/components.d.ts',
            // deep: true,
            //directoryAsNamespace: true
        }),
        Pages({
         dirs:'resources/js/pages',
        }),

    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            // '@': require('path').resolve(__dirname, 'resources/js'),
            // '~': require('path').resolve(__dirname, 'node_modules'),
        },
    },
});
