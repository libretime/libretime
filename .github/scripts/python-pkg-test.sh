#!/usr/bin/env bash

failed='f'
# Starting at repo root

echo "::group::Airtime Analyzer"
pushd python_apps/airtime_analyzer
if ! nosetests . -x; then
    failed='t'
fi
popd
echo "::endgroup::"

echo "::group::API Client"
if ! make -C python_apps/api_clients test; then
    failed='t'
fi
echo "::endgroup::"

if [[ "$failed" = "t" ]]; then
    echo "Python tests failed"
    exit 1
fi
echo "Python tests passed!"
