#!/usr/bin/env bash

echo "::group::Install Python apps"
for app in $(ls python_apps); do
  if [[ -f "python_apps/$app/requirements-dev.txt" ]]; then
    pip3 install -r "python_apps/$app/requirements-dev.txt"
  fi
  pip3 install -e "python_apps/$app"
done
echo "::endgroup::"
