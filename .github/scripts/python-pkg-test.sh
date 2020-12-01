#/bin/bash

# Starting at repo root

cd python_apps/airtime_analyzer
nosetests .

cd ../api_clients
nosetests .

# Reset to repo root
cd ../..