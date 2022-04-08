module.exports = [
  {
    to: "/docs",
    from: ["/documentation/", "/manual/", "/manual/about-this-manual/"],
  },
  {
    to: "/docs/admin-manual/backup",
    from: ["/manual/backing-up-the-server/", "/docs/backing-up-the-server"],
  },
  {
    to: "/docs/admin-manual/setup/install",
    from: ["/install"],
  },
  {
    to: "/docs/admin-manual/setup/upgrade",
    from: ["/manual/upgrading/", "/docs/upgrading"],
  },
  {
    to: "/docs/admin-manual/troubleshooting",
    from: ["/manual/troubleshooting/", "/docs/troubleshooting"],
  },
];
