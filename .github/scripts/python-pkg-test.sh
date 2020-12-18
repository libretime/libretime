#/bin/bash

# Starting at repo root

cd python_apps/airtime_analyzer
nosetests . -x

cd ../api_clients
nosetests . -x

# Reset to repo root
cd ../..