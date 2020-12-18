#/bin/bash

# Starting at repo root

cd python_apps/airtime_analyzer
nosetests . -x -e

cd ../api_clients
nosetests . -x -e

# Reset to repo root
cd ../..