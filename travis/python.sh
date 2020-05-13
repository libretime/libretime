#!/bin/bash

set -xe

[[ "$PYTHON" == false ]] && exit 0

pyenv local 3.7
pushd python_apps/airtime_analyzer
pip3 install -e .
nosetests
popd

pushd python_apps/api_clients
pip3 install -e .
nosetests
popd
