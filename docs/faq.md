---
sidebar: main
---

# Frequently Asked Questions

**What is LibreTime?**

LibreTime is a community managed fork of the AirTime project.

It is managed by a friendly inclusive community of stations
from around the globe that use, document and improve LibreTime.

**Can I upgrade to LibreTime?**

In theory you can update any pre 3.0 version of AirTime to
LibreTime 3.0.0 and above. More information on [Upgrading](upgrading)
is in the docs.

LibreTime is complex software, as such it is close to impossible
to guarantee that every upgrade path works as intended. This
means you should trial the update on a parallel test
infrastructure to minimize possible downtime.

Please let the community know if you encounter issues with the
update process.

**Why are Cue-In/Out points wrong in some tracks? / What's with silan?**

The silan silence detection is currently outdated on almost all distributions. The older versions report clearly wrong information and may segfault at the worst. Versions starting with 0.3.3 (and some patched 0.3.2 builds) are much better but still need thorough testing. Please see the [release notes](https://github.com/LibreTime/libretime/releases) for up to date mitigation scenarios and details on the issues.

**Why did you fork AirTime?**

See this [open letter to the Airtime community](https://gist.github.com/hairmare/8c03b69c9accc90cfe31fd7e77c3b07d).
