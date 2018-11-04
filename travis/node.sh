#!/bin/bash

set -xe

[[ "$NODE" != true ]] && exit 0

pushd site
npm test
popd
