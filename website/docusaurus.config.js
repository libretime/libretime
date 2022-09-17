// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

const vars = require("./vars");

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: vars.title,
  tagline: vars.description,
  url: process.env.URL || vars.website,
  baseUrl: process.env.BASE_URL || "/",
  onBrokenLinks: "throw",
  onBrokenMarkdownLinks: "throw",
  favicon: "img/icon.svg",
  organizationName: "libretime",
  projectName: "libretime",
  trailingSlash: true,

  plugins: [
    [
      "@cmfcmf/docusaurus-search-local",
      {
        indexBlog: false,
        indexPages: false,
      },
    ],
    [
      "@docusaurus/plugin-client-redirects",
      {
        redirects: require("./redirects"),
      },
    ],
  ],

  i18n: {
    defaultLocale: "en",
    locales: ["en"],
  },

  presets: [
    [
      "classic",
      /** @type {import('@docusaurus/preset-classic').Options} */
      ({
        docs: {
          path: "../docs",
          sidebarPath: require.resolve("./sidebars.js"),
          editUrl: `${vars.repository.href}/blob/main/docs`,

          async sidebarItemsGenerator({
            defaultSidebarItemsGenerator,
            ...args
          }) {
            const items = await defaultSidebarItemsGenerator(args);

            return items.map((item) => {
              // Reverse releases pages ordering
              if (item.type === "category" && item.label === "Releases") {
                return { ...item, items: item.items.reverse() };
              }

              return item;
            });
          },
          remarkPlugins: [require("mdx-mermaid")],
        },
        theme: {
          customCss: require.resolve("./src/css/custom.css"),
        },
      }),
    ],
  ],

  themeConfig:
    /** @type {import('@docusaurus/preset-classic').ThemeConfig} */
    ({
      navbar: {
        title: vars.title,
        logo: {
          alt: "LibreTime tower",
          src: "img/icon.svg",
        },
        items: [
          {
            label: "Docs",
            to: "/docs",
            position: "left",
            type: "dropdown",
            items: vars.doc.sections,
          },
          { label: "Contribute", to: "/contribute", position: "left" },
          { label: "Blog", to: "blog", position: "left" },

          { ...vars.repository, position: "right" },
          { ...vars.forum, position: "right" },
          { ...vars.channel, position: "right" },
          { type: "docsVersionDropdown", position: "right" },
          // { type: "localeDropdown", position: "right" },
        ],
      },
      footer: {
        style: "dark",
        links: [
          {
            title: "Docs",
            items: vars.doc.sections,
          },
          {
            title: "Community",
            items: [vars.forum, vars.channel],
          },
          {
            title: "More",
            items: [vars.repository],
          },
        ],
        copyright: `Code licensed under AGPLv3; docs licensed under GPLv2.`,
      },
      prism: {
        additionalLanguages: ["apacheconf", "ini", "nginx"],
      },
    }),
};

module.exports = config;
