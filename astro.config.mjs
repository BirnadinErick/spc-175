import { defineConfig } from "astro/config";
import tailwind from "@astrojs/tailwind";
import mdx from "@astrojs/mdx";
import remarkGfm from "remark-gfm";
import emoji from "remark-emoji";
import remarkUnwrapImages from "remark-unwrap-images";

export default defineConfig({
    integrations: [tailwind(), mdx()],
    experimental: {
        assets: true,
    },
    markdown: {
        remarkPlugins: [remarkGfm, emoji, remarkUnwrapImages],
    },
});
