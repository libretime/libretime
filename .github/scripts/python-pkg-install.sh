#!/usr/bin/env bash

echo "::group::Install Python apps"
for app in python_apps/*; do
  if [[ -f "$app/requirements-dev.txt" ]]; then
    pip3 install -r "$app/requirements-dev.txt"
  fi
  pip3 install -e "$app"
done
echo "::endgroup::"
