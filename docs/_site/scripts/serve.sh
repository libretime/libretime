#! /bin/sh

cd /vagrant
echo "Stopping any running Mkdocs servers."
pkill mkdocs
echo "Building Mkdocs documentation."
mkdocs build --clean -q > /dev/null
echo "Launching Mkdocs server."
mkdocs serve > /dev/null 2>&1 &
echo "Visit http://localhost:8888 to see the LibreTime documentation."
