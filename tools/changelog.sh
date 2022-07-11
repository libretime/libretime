#!/usr/bin/env bash

set -u

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v git > /dev/null || error "git command not found!"
command -v git-chglog > /dev/null || error "git-chglog command not found!"

changelog="CHANGELOG.md"
tag="${tag:-$(git describe --abbrev=0 --tags || error "could not extract latest tag")}"

if grep --quiet "<a name=\"$tag\"></a>" "$changelog"; then
  error "changelog has already been generated for tag $tag!"
fi

cat <(git-chglog "$tag") "$changelog" > "$changelog.tmp"
mv  "$changelog.tmp" "$changelog"

if command -v npx > /dev/null; then
  npx prettier --write "$changelog"
fi
