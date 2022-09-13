module.exports = {
  title: "LibreTime",
  description: "Radio Broadcast & Automation Platform",
  version: "3.0.0-beta.0",
  website: "https://libretime.org",

  repository: {
    href: "https://github.com/libretime/libretime",
    label: "Github",
  },

  forum: {
    href: "https://discourse.libretime.org",
    label: "Discourse",
  },

  channel: {
    href: "https://chat.libretime.org",
    label: "Mattermost",
  },

  home: {
    links: [
      { label: "Get started 🚀", to: "/docs/admin-manual" },
      { label: "Release note", to: "/docs/releases/3.0.0-beta.0" },
    ],
  },

  doc: {
    sections: [
      { label: "Admin manual", to: "/docs/admin-manual" },
      { label: "User manual", to: "/docs/user-manual" },
      { label: "Developer manual", to: "/docs/developer-manual" },
    ],
  },
};
