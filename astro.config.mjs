import { defineConfig } from "astro/config";
import tailwind from "@astrojs/tailwind";
import mdx from "@astrojs/mdx";
import remarkGfm from "remark-gfm";
import swup from "@swup/astro";
import emoji from "remark-emoji";
import remarkUnwrapImages from "remark-unwrap-images";
import alpinejs from "@astrojs/alpinejs";

import react from "@astrojs/react";

// https://astro.build/config
export default defineConfig({
  // swup({ globalInstance: true, reloadScripts: true, updateHead:true, forms:true, smoothScrolling:true, progress:true, debug:true })
  integrations: [tailwind(), mdx(), alpinejs(), swup({
    // theme: ["overlay", { direction: "to-bottom" }],
    theme: "fade",
    progress: true,
    reloadScripts: true,
    updateHead: true,
    preload: true,
    globalInstance: true
  }), react()],
  experimental: {
    assets: true
  },
  markdown: {
    remarkPlugins: [remarkGfm, emoji, remarkUnwrapImages]
  }
});