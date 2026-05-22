const glob = require("glob");
const fs = require("fs");
const path = require("path");

const BASE_SCSS = "assets/src/scss";

/**
 * Chỉ khai báo folder chứa scss
 */
const modules = [
    "pages",
    "pages/home/",
    "pages/checkout/",
    "pages/cart/",
    "pages/order-received/",
    "pages/about/",
    "singles",
    "taxonomies",
    "components",
    "components/woocommerce",
    "components/woocommerce/course",
    "layout",
];

function generate() {
    modules.forEach((src) => {
        const srcDir = path.join(BASE_SCSS, src);
        const indexFile = path.join(srcDir, "index.scss");

        if (!fs.existsSync(srcDir)) {
            console.warn(`⚠ Skip: ${srcDir} not found`);
            return;
        }

        const files = glob
            .sync(path.join(srcDir, "*.scss"))
            .filter((f) => !f.endsWith("index.scss"))
            .sort();

        const content = files
            .map((file) => {
                const name = path.basename(file, ".scss").replace(/^_/, "");
                return `@import "${name}";`;
            })
            .join("\n");

        fs.writeFileSync(indexFile, content);
        console.log(`✔ Generated: ${src}/${"index.scss"}`);
    });
}

generate();
