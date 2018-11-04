#!/bin/bash

set -xe

[[ "$SITE" != true ]] && exit 0

echo "Building docs..."
mkdocs build --clean -q > /dev/null
echo -n "done"

pushd site
echo "Building site..."
gulp deploy
echo -n "done"
popd
