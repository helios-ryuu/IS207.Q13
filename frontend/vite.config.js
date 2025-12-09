import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
    server: {
        host: '0.0.0.0',       // cho phép truy cập từ ngoài container
        port: 5173,            // cổng Vite mặc định
        watch: {
            usePolling: true,  // theo dõi file thay đổi qua polling
        },
        strictPort: true,      // tránh random port khác
    },
})
