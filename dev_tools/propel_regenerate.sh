#!/usr/bin/env bash

set -e

# Absolute path to this script
SCRIPT=$(readlink -f "$0")
# Absolute directory this script is in
SCRIPTPATH=$(dirname "$SCRIPT")

cd "$SCRIPTPATH/../airtime_mvc/" || (echo "could not cd in $SCRIPTPATH/../airtime_mvc/" && exit 1)
path=$(pwd)
cd build
sed -i "s|\"project\.home =.*$\"|\"project.home = $path\"|g" build.properties
../../vendor/propel/propel1/generator/bin/propel-gen
