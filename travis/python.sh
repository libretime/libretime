#!/bin/bash

set -xe

[[ "$PYTHON" == false ]] && exit 0

pushd python_apps/airtime_analyzer
pyenv local 3.7
pip3 install -e .
nosetests
popd

echo "Building docs..."
mkdocs build --clean -q > /dev/null
echo -n "done"
