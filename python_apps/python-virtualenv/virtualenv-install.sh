#!/bin/bash
# Absolute path to this script
SCRIPT=`readlink -f $0`
# Absolute directory this script is in
SCRIPTPATH=`dirname $SCRIPT`

VIRTUAL_ENV_DIR="/usr/lib/airtime/airtime_virtualenv"
VIRTUAL_ENV_SHARE="/usr/share/python-virtualenv/"
CHECK=$(${SCRIPTPATH}/virtualenv-check)
if [ "$CHECK" == "1" ]; then
    echo -e "\n*** Creating Virtualenv for Airtime ***"
    EXTRAOPTION=$(virtualenv --help | grep extra-search-dir)

    if [ "$?" -eq "0" ]; then
        virtualenv --extra-search-dir=${SCRIPTPATH}/3rd_party --no-site-package -p /usr/bin/python2.6 /usr/lib/airtime/airtime_virtualenv || exit 1
    else
        # copy distribute-0.6.10.tar.gz to /usr/share/python-virtualenv/
        # this is due to the bug in virtualenv 1.4.9
        if [ -d "$VIRTUAL_ENV_SHARE" ]; then
            cp ${SCRIPTPATH}/3rd_party/distribute-0.6.10.tar.gz /usr/share/python-virtualenv/
        fi
        virtualenv --no-site-package -p /usr/bin/python2.6 /usr/lib/airtime/airtime_virtualenv || exit 1
    fi
    
    echo -e "\n*** Installing Python Libraries ***"
    /usr/lib/airtime/airtime_virtualenv/bin/pip install ${SCRIPTPATH}/airtime_virtual_env.pybundle -E /usr/lib/airtime/airtime_virtualenv || exit 1
    
    echo -e "\n*** Patching Python Libraries ***"
    PATCHES=${SCRIPTPATH}/patches/*
    for file in $(find $PATCHES -print); do
        if [ -d $file ]; then
            DIRNAME=$(basename $file)
            echo -e "\n   ---Applying Patches for $DIRNAME---"
        else
            patch -N -p0 -i $file
        fi
    done
else
    echo -e "\n*** Existing Airtime Virtualenv Found ***"
fi 
exit 0
