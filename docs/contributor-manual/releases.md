---
title: Releases
---

## Releasing a new version

This guide walks you through the steps required to release a new version of LibreTime.

### 1. Inspect the release pull request

A release pull request is maintained by [`release-please`](https://github.com/googleapis/release-please). `release-please` guesses the next version to release based on the commit history, and will generate a changelog for that release.

Once a release is desired, checkout the release branch:

```bash
# For a release on the main branch
git checkout release-please--branches--main--components--libretime
# For a release on the stable branch
git checkout release-please--branches--stable--components--libretime
```

### 2. Release note

Prepare a new release note based on the `docs/releases/unreleased.md` file. Be sure that
the filename match the releases notes naming conventions:

```bash
ls -l docs/releases/
cp docs/releases/unreleased.md docs/releases/$VERSION.md
```

The release note file must be updated with:

- the version and date of this release,
- an auto generated features and bug fixes changelog,
- instructions for upgrading,
- deprecation notices,
- remove empty sections.

Reset and clean the `docs/releases/unreleased.md` file for a future version.

Commit the release note changes:

```bash
git add docs/releases
git commit -m "docs: add release note"
```

### 4. Merge the release pull request

Push any changes that we previously made to the release branch:

```bash
git push
```

Once the pull request CI succeeded and everything is ready, merge the release pull request. `release-please` will create a tag and a release, which will trigger the final release pipeline that will upload the tarball as release assets.
