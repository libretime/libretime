#!/usr/bin/env bash

failed="false"

echo "::group::Airtime Analyzer"
if ! make -C python_apps/airtime_analyzer test; then
  failed="true"
fi
echo "::endgroup::"

echo "::group::API Client"
if ! make -C python_apps/api_clients test; then
  failed="true"
fi
echo "::endgroup::"

if [[ "$failed" = "true" ]]; then
  echo "Python tests failed"
  exit 1
fi
echo "Python tests passed!"
