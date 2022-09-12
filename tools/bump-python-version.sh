#!/usr/bin/env bash

# Bump the version in the setup.py files.

set -u

error() {
  echo >&2 "error: $*"
  exit 1
}

command -v sed > /dev/null || error "sed command not found!"

version="$1"

for setup_path in */setup.py; do
  sed --in-place \
    "s/version=\".*\",/version=\"$version\",/" \
    "$setup_path"  ||
    error "could not bump version for $setup_path!"
done
