#!/bin/bash

set -xe

[[ "$PYTHON" == false ]] && exit 0

pushd python_apps/airtime_analyzer 
nosetests -a '!rgain'
echo "replaygain tests where skipped due to not having a reliable replaygain install on travis."
popd
