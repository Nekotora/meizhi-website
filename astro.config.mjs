import { defineConfig } from "astro/config";
import vue from '@astrojs/vue';

// https://astro.build/config
export default defineConfig({
  integrations: [vue()],
  build: {
    format: 'preserve',
    assets: '_meizhi'
  }
});