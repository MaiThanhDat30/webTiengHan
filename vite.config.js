import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        manifest: true,          // 🔥 BẮT BUỘC
        outDir: 'public/build',  // 🔥 Laravel đọc ở đây
        emptyOutDir: true,
    },
})
