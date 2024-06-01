import { defineConfig } from "astro/config";
import tailwind from "@astrojs/tailwind";
import mdx from "@astrojs/mdx";
import remarkGfm from "remark-gfm";
import emoji from "remark-emoji";
import remarkUnwrapImages from "remark-unwrap-images";
import alpinejs from "@astrojs/alpinejs";

// https://astro.build/config
export default defineConfig({
  integrations: [tailwind(), mdx(), alpinejs()],
  experimental: {
    assets: true
  },
  markdown: {
    remarkPlugins: [remarkGfm, emoji, remarkUnwrapImages]
  }
});
