#!/usr/bin/env bash

# Sync the docs folder with the libretime/website repository.

set -e

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v git > /dev/null || error "git command not found!"

usage() {
  cat >&2 <<- EOF
Usage : $0 <commit_range>

Positional arguments:
  commit_range  Commit range to scan for changes within the docs folder.

EOF
}

if [[ $# -lt 1 ]]; then
  usage
  exit 1
fi

commit_range="$1"

[[ -n "$GITHUB_REF_NAME" ]] || error "GITHUB_REF_NAME variable is not set!"
[[ -n "$GITHUB_REPOSITORY" ]] || error "GITHUB_REPOSITORY variable is not set!"

git config --global user.name "libretime-bot"
git config --global user.email "libretime-bot@users.noreply.github.com"

if [[ "$GITHUB_REF_NAME" == "main" ]]; then
  dest="docs"
else
  dest="versioned_docs/version-$GITHUB_REF_NAME"
fi

for commit in $(git rev-list --reverse --no-merges "$commit_range" -- docs); do
  rm -fR "website/$dest"
  cp -r "docs" "website/$dest"

  git show \
    --quiet \
    --format="%B%n${GITHUB_REPOSITORY}@%H" \
    "$commit" \
    > commit-message

  pushd website
  git add "$dest"
  git diff-index --quiet HEAD -- || git commit --file=../commit-message
  popd
  rm commit-message
done

pushd website
git push
popd
