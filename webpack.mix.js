const mix = require("laravel-mix");
const path = require("path");

// ======================================
// CONFIG SCSS ENTRY POINTS
// ======================================
let styles = {
  theme: {
    input: "assets/src/scss/theme.scss",
    dest: "css/",
  },
  "wc-toasts": {
    input: "assets/src/scss/wc-toasts.scss",
    dest: "css/",
  },
};

// ======================================
// CONFIG JS ENTRY POINTS
// ======================================
let scripts = {
  theme: {
    input: "assets/src/scripts/theme.js",
    dest: "js/",
  },
  "wc-toasts": {
    input: "assets/src/scripts/wc-toasts.js",
    dest: "js/",
  },
  // =========================
  // Woo - Checkout only
  // =========================
  "woo/checkout-protection": {
    input: "assets/src/scripts/woo/tcp-checkout-protection.js",
    dest: "js/",
  },
  "woo/checkout-elearning": {
    input: "assets/src/scripts/woo/tcp-checkout-elearning.js",
    dest: "js/",
  },
  "woo/cart": {
    input: "assets/src/scripts/woo/cart.js",
    dest: "js/",
  },
};

// ======================================
// BUILD STYLES
// ======================================
Object.keys(styles).forEach(function (key) {
  mix
    .setPublicPath("dist")
    .options({ processCssUrls: false })
    .sass(styles[key].input, styles[key].dest + key + ".css");

  if (!mix.inProduction()) {
    mix.sourceMaps();
  }
});

// ======================================
// BUILD JAVASCRIPT
// ======================================
Object.keys(scripts).forEach(function (key) {
  mix
    .setPublicPath("dist")
    .js(scripts[key].input, scripts[key].dest + key + ".js");

  if (!mix.inProduction()) {
    mix.sourceMaps();
  }
});

// ======================================
// VERSIONING WHEN PRODUCTION
// ======================================
if (mix.inProduction()) {
  mix.version();
}

// ======================================
// DISABLE SASS WARNINGS
// ======================================
mix.webpackConfig({
  externals: {
    jquery: "jQuery",
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "assets/src/scripts"),
    },
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        enforce: "pre",
        loader: "sass-loader",
        options: {
          sassOptions: {
            quietDeps: true,
            silenceDeprecations: ["import"],
          },
        },
      },
      {
        test: /\.svg$/i,
        type: "asset/resource",
        resourceQuery: { not: [/raw/] },
      },
    ],
  },
});
