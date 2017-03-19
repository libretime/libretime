#!/bin/bash

set -xe

pushd dev_tools/release
bash -e release.sh ${TRAVIS_TAG}
popd
