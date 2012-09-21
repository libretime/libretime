#/bin/sh
# Script for generating nightly Airtime snapshot packages
# Run from the directory containg the files checked out from git

#VERSION=2.2.0~$(date "+%Y%m%d")
VERSION=2.2.0-rc1
BUILDDEST=/tmp/airtime-${VERSION}/
DEBDIR=`pwd`/debian

git checkout devel
git pull

echo "cleaning up previous build..."

rm -rf /tmp/airtime*
mkdir -p ${BUILDDEST}airtime

echo "copying files to temporary directory..."

cp -a * ${BUILDDEST}airtime || exit
cp -a $DEBDIR ${BUILDDEST}debian || exit

cd ${BUILDDEST} || exit

# Set the version of the snapshot package

sed -i "1s:(2.2.0-1):(${VERSION}):g" debian/changelog

# FIXES for 2.2.0 #############

# these are all moved to debian/copyright
rm airtime/python_apps/pypo/LICENSE
rm airtime/airtime_mvc/library/php-amqplib/LICENSE
rm airtime/airtime_mvc/library/phing/LICENSE
rm airtime/airtime_mvc/library/propel/LICENSE
rm airtime/airtime_mvc/library/soundcloud-api/README.md

# Remove Liquidsoap binaries
rm -r airtime/python_apps/pypo/liquidsoap_bin/

#Remove phing library
rm -r airtime/airtime_mvc/library/phing/

#Remove ZFDebug
rm -r airtime/airtime_mvc/library/ZFDebug/

#Strip un-needed install scripts
rm -r airtime/install_full/

#############################

echo "running the build..."

debuild -b -uc -us $@ || exit

cp /tmp/airtime_${VERSION}* /var/www/apt/snapshots/
