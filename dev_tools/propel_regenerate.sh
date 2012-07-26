#!/bin/bash -e
# Absolute path to this script
SCRIPT=`readlink -f $0`
# Absolute directory this script is in
SCRIPTPATH=`dirname $SCRIPT`

cd $SCRIPTPATH/../airtime_mvc/
path=`pwd`
cd build
sed -i s#"project\.home =.*$"#"project.home = $path"#g build.properties
../library/propel/generator/bin/propel-gen
