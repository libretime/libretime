---
title: Releases
---

## Distributions releases support

New releases target the current stable distributions release, and development should prepare for future stable distributions releases.

- We recommend installing LibreTime on the current stable distributions.
- We do not provide maintenance releases for old stable distributions.

|              | Ubuntu 18.04 | Debian 10  | Ubuntu 20.04 |  Debian 11  |
| ------------ | :----------: | :--------: | :----------: | :---------: |
| Release date |  2018-04-26  | 2019-07-06 |  2020-04-23  | 2021-08-14  |
| End of life  |   2023-04    |  2024-06   |   2025-04    |   2026-06   |
| Versions     |              |            |              |             |
| 3.0.x        |  deprecated  | deprecated | recommended  | recommended |

## Versioning schema

We follow the [Semantic Versioning](https://semver.org/spec/v2.0.0.html) standards.

In a nutshell, given a version number `MAJOR.MINOR.PATCH` we increment the:

1. `MAJOR` version when we make incompatible API changes,
2. `MINOR` version when we add functionality in a backwards-compatible manner, and
3. `PATCH` version when we make backwards-compatible bug fixes.

## Releasing a new version

This guide walks you through the steps required to release a new version of LibreTime.

:::caution

This guide is still a work in progress, and doesn't cover every use cases. Depending on
the version bump, some steps might be wrong. For example, in case of a patch release,
the documentation requires different changes.

:::

Before releasing a new version, make sure linter don't fail and tests are passing.

Start by cleaning the repository and make sure you don't have uncommitted changes:

```
git checkout main
make clean
git status
```

Choose the next version based the our [versioning schema](#versioning-schema):

```bash
export VERSION=3.0.0-beta.0
```

Create a new `release-$VERSION` branch and release commit to prepare a release pull request:

```bash
git checkout -b "release-$VERSION"
export COMMIT_MESSAGE="chore: release $VERSION"
git commit --allow-empty --message="$COMMIT_MESSAGE"
```

### 1. Version bump

Write the new `$VERSION` to the VERSION file, and bump the python packages version:

```bash
bash tools/bump-python-version.sh "$VERSION"

git add .
git commit --fixup ":/$COMMIT_MESSAGE"
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
git add .
git commit --fixup ":/$COMMIT_MESSAGE"
```

### 3. Create a new pull request

Squash the changes and open a pull request for others to review:

```bash
git rebase --autosquash --interactive main
```

Merge the pull request when it's reviewed and ready.

### 4. Create and push a tag

Pull the merged release commit:

```bash
git checkout main
git pull upstream main
```

Make sure `HEAD` is the previously merged release commit and tag it with the new version:

```bash
git show --quiet

git tag -a -m "$VERSION" "$VERSION"
```

Generate the changelog for the newly tagged version:

```bash
make changelog

git add .
git commit -m "chore: generate changelog for $VERSION"
```

Push the tag upstream to finalize the release process:

```bash
git push upstream main --follow-tags
```
