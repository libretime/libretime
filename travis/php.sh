#!/bin/bash

set -xe

[[ "$PYTHON" == true ]] && exit 0

pushd airtime_mvc/tests
../../vendor/bin/phpunit
popd
