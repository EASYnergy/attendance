import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

export default defineConfig({
  plugins: [sveltekit()],
  build: { // Add a comma here to separate 'plugins' from 'build'
    rollupOptions: {
      external: ['jsqr']
    }
  }
});
