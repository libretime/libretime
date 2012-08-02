#/bin/sh
# Script for generating nightly Airtime snapshot packages
# Run from the directory containg the files checked out from git

VERSION=2.2.0~$(date "+%Y%m%d")
BUILDDEST=/tmp/airtime-${VERSION}/
DEBDIR=`pwd`/debian

git checkout devel
git pull

echo "cleaning up previous build..."

rm -rf /tmp/airtime-*
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

# Disable install script check for Debian package, it breaks the .deb install
sed -i '11s:DEB=$(dpkg:# DEB=$(dpkg:g' airtime/install_minimal/airtime-install
sed -i '13s\"$DEB" = "Status: install ok installed"\-f /var/lib/dpkg/info/airtime.config\g' airtime/install_minimal/airtime-install
sed -i '14s: Please use the debian package to upgrade.:..:g' airtime/install_minimal/airtime-install
sed -i '15s:exit 1:# We do not exit here:g' airtime/install_minimal/airtime-install

# Remove Liquidsoap binaries
rm -r airtime/python_apps/pypo/liquidsoap_bin/

# Disable installation of Liquidsoap binaries
sed -i '84s:print:#print:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '86s:binary_path:#binary_path:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '88s:try:#try:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '89s:open:#open:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '91s:try:#try:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '92s:os.remove:#os.remove:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '93s:except:#except:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '95s:pass:#pass:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '97s:os.symlink:#os.symlink:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '98s:except:#except:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '99s:    """:""":g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '107s:    """:""":g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '108s:print:#print:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '109s:print:#print:g' airtime/python_apps/pypo/install/pypo-initialize.py
sed -i '110s:sys.exit(1):#sys.exit(1):g' airtime/python_apps/pypo/install/pypo-initialize.py

#Remove phing library
rm -r airtime/airtime_mvc/library/phing/

#Remove ZFDebug
rm -r airtime/airtime_mvc/library/ZFDebug/

#Strip un-needed install scripts
rm -r airtime/install_full/

#############################

echo "running the build..."

debuild --no-lintian -b -uc -us $@ || exit

exit

# optionally, copy the new package to the public server
scp /tmp/airtime_${VERSION}_all.deb apt.sourcefabric.org:/var/www/apt/misc/
