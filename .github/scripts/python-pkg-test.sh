#/bin/bash

failed='f'
# Starting at repo root

echo "::group::Airtime Analyzer"
cd python_apps/airtime_analyzer
if ! nosetests . -x; then
    failed='t'
fi
echo "::endgroup::"

echo "::group::API Client"
cd ../api_clients
if ! nosetests . -x; then
    failed='t'
fi
echo "::endgroup::"

# Reset to repo root
cd ../..
if [[ "$failed" = "t" ]]; then
    echo "Python tests failed"
    exit 1
fi
echo "Python tests passed!"
