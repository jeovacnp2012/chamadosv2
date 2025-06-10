/* eslint-env node */
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

const isNative = process.env.BUILD_NATIVE === 'true';

export default defineConfig({
    plugins: [react()],
    resolve: {
        alias: {
            './utils/storage': isNative
                ? path.resolve(__dirname, 'src/utils/storage.native.js')
                : path.resolve(__dirname, 'src/utils/storage.web.js'),
        },
    },
});
