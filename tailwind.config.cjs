const defaultTheme = require("tailwindcss/defaultTheme");

/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{astro,html,js,jsx,md,mdx,svelte,ts,tsx,vue}"],
  theme: {
    fontFamily: {
      serif: ["Playfair Display", "serif", ...defaultTheme.fontFamily.serif],
      sans: ["Space Grotesk", "sans-serif", ...defaultTheme.fontFamily.sans],
      mono: ["Space Mono", "monospace", ...defaultTheme.fontFamily.mono],
    },
    extend: {
      colors: {
        "spc-gold": "#FFCD00",
        "spc-green": "#115740",
        "spc-sea": "#5ABF64",
        "spc-dark": "#202020",
        "spc-light": "#F9F9F2",
        "spc-dark-green": "#0B1808",
        "spc-bg-mid": "#333333",
      },
    },
  },
  plugins: [require("@tailwindcss/forms"), require("@tailwindcss/typography")],
};
