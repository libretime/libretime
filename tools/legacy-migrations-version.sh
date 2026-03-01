#!/usr/bin/env bash

set -u

error() {
  echo >&2 "error: $*"
  exit 1
}

migrations="api/libretime_api/legacy/migrations"
version_file="$migrations/__init__.py"

latest_migration="$(find "$migrations" -name '[0-9][0-9][0-9][0-9]_*.py' | sort | tail -n 1)"

latest_migration_version="$(basename "$latest_migration" | cut -d '_' -f 1)"
latest_migration_version="$((10#$latest_migration_version))" # Strip leading zeros

sed \
  -i "s#^LEGACY_SCHEMA_VERSION =.*#LEGACY_SCHEMA_VERSION = \"$latest_migration_version\"#" \
  "$version_file"
