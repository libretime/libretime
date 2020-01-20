#!/bin/bash

set -xe

[[ "$PYTHON" == false ]] && exit 0

python3 --version

pushd python_apps/airtime_analyzer
pyenv local 3.6
pip3 install -e .
nosetests
popd

echo "Building docs..."
mkdocs build --clean -q > /dev/null
echo -n "done"
