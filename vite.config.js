import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

const viteServerHost = process.env.VITE_HOST ?? '0.0.0.0';
const viteServerPort = Number(process.env.VITE_PORT ?? 5173);

// When running Vite in Docker with a host port mapping (e.g. 5183:5173),
// the browser must connect to the *host* port while the server listens on the
// *container* port.
const viteClientHost = process.env.VITE_CLIENT_HOST ?? 'localhost';
const viteClientPort = Number(process.env.VITE_CLIENT_PORT ?? viteServerPort);
const viteOrigin = process.env.VITE_ORIGIN ?? `http://${viteClientHost}:${viteClientPort}`;

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            publicDirectory: 'laravel/public',
            input: ['laravel/resources/css/app.css', 'laravel/resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        cors: {
            origin: '*',
        },
        host: viteServerHost,
        port: viteServerPort,
        strictPort: true,
        origin: viteOrigin,
        hmr: {
            host: viteClientHost,
            clientPort: viteClientPort,
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
            usePolling: true,
            interval: 100,
        },
    },
});
