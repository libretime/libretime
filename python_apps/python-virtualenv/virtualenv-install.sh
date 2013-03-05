#!/bin/bash
# Absolute path to this script
SCRIPT=`readlink -f $0`
# Absolute directory this script is in
SCRIPTPATH=`dirname $SCRIPT`

which virtualenv > /dev/null
if [ "$?" -ne "0" ]; then
    echo "virtualenv not found!"
    echo -e "Please install virtualenv and retry Airtime installation.\n"
    exit 1
fi

#Check whether version of virtualenv is <= 1.4.8. If so exit install. 
BAD_VERSION="1.4.8"
VERSION=$(virtualenv --version)
NEWEST_VERSION=$(echo -e "$BAD_VERSION\n$VERSION\n'" | sort -t '.' -g | tail -n 1)
echo -n "Ensuring python-virtualenv version > $BAD_VERSION..."
if [[ "$NEWEST_VERSION" = "$BAD_VERSION" ]]; then
    URL="http://apt.sourcefabric.org/pool/main/p/python-virtualenv/python-virtualenv_1.4.9-3_all.deb"
    echo "Failed!"
    echo "You have version $BAD_VERSION or older installed. Please install package at $URL first and then try installing Airtime again."
    exit 1
else
    echo "Success!"
fi

VIRTUAL_ENV_DIR="/usr/lib/airtime/airtime_virtualenv"
VIRTUAL_ENV_SHARE="/usr/share/python-virtualenv/"

if [ -d $VIRTUAL_ENV_DIR ]; then
    echo -e "\n*** Existing Airtime Virtualenv Found ***"
    rm -rf ${VIRTUAL_ENV_DIR}
    echo -e "\n*** Reinstalling Airtime Virtualenv ***"
fi

echo -e "\n*** Creating Virtualenv for Airtime ***"
EXTRAOPTION=$(virtualenv --help | grep extra-search-dir)

if [ "$?" -eq "0" ]; then
    virtualenv --extra-search-dir=${SCRIPTPATH}/3rd_party --no-site-package -p /usr/bin/python /usr/lib/airtime/airtime_virtualenv 2>/dev/null || exit 1
else
    # copy distribute-0.6.10.tar.gz to /usr/share/python-virtualenv/
    # this is due to the bug in virtualenv 1.4.9
    if [ -d "$VIRTUAL_ENV_SHARE" ]; then
        cp ${SCRIPTPATH}/3rd_party/distribute-0.6.10.tar.gz /usr/share/python-virtualenv/
    fi
    virtualenv --no-site-package -p /usr/bin/python /usr/lib/airtime/airtime_virtualenv 2>/dev/null || exit 1
fi

echo -e "\n*** Installing Python Libraries ***"
/usr/lib/airtime/airtime_virtualenv/bin/pip install ${SCRIPTPATH}/airtime_virtual_env.pybundle || exit 1
