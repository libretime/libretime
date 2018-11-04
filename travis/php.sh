#!/bin/bash

set -xe

[[ "$PYTHON" == true ]] && exit 0
[[ "$NODE" == true ]] && exit 0
[[ "$SITE" == true ]] && exit 0

pushd airtime_mvc/tests
../../vendor/bin/phpunit
popd
