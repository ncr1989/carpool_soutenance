import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173, 
    proxy: {
      '/api': {
        target: 'http://localhost:8000', 
        changeOrigin: true,
      },
    },
  },
});
