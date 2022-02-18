// @ts-check
// Note: type annotations allow type checking and IDEs autocompletion

const vars = require("./vars");

const lightCodeTheme = require("prism-react-renderer/themes/github");
const darkCodeTheme = require("prism-react-renderer/themes/dracula");

/** @type {import('@docusaurus/types').Config} */
const config = {
  title: vars.title,
  tagline: vars.description,
  url: vars.website,
  baseUrl: "/libretime/",
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
        },
        blog: false,
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
            items: [
              { label: "User manual", to: "/docs/user-manual" },
              { label: "Admin manual", to: "/docs/admin-manual" },
              { label: "Development", to: "/docs/development" },
            ],
          },
          { label: "Contribute", to: "/contribute", position: "left" },

          { ...vars.repository, position: "right" },
          { ...vars.forum, position: "right" },
          { ...vars.channel, position: "right" },
          // { type: "localeDropdown", position: "right" },
        ],
      },
      footer: {
        style: "dark",
        links: [
          {
            title: "Docs",
            items: [
              { label: "User manual", to: "/docs/user-manual" },
              { label: "Admin manual", to: "/docs/admin-manual" },
              { label: "Development", to: "/docs/development" },
            ],
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
        theme: lightCodeTheme,
        darkTheme: darkCodeTheme,
        additionalLanguages: ["apacheconf", "ini"],
      },
    }),
};

module.exports = config;
