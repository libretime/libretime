#!/bin/bash

SELENIUM_BIN=selenium-server-standalone-2.42.2.jar
SELENIUM_URL=http://selenium-release.storage.googleapis.com/2.42/selenium-server-standalone-2.42.2.jar

printUsage()
{
    echo "Usage: ${0} airtime_url"
    echo " Example: ${0} http://bananas.airtime.pro"
}
if [ -z "$1" ]
then
    printUsage
    exit 1
fi

AIRTIME_URL="${1}"

# Check if java is installed
which java >& /dev/null
if [ $? -gt 0 ]
then
    echo "java not found. Please install it."
fi

# Check for selenium-server 
if [ ! -f ${SELENIUM_BIN} ]
then
    echo "Selenium not found, downloading it..."
    wget ${SELENIUM_URL}
fi

# Check for xvfb-run, which lets us run Firefox in a headless X server
which xvfb-run >& /dev/null
if [ $? -gt 0 ]
then
    echo "xvfb-run not found, apt-getting it now..."
    sudo apt-get install xvfb
fi

# You must pass the full path to the HTML suite and the results file to Selenium:
xvfb-run java -jar ${SELENIUM_BIN} -htmlSuite "*firefox" "${AIRTIME_URL}" "${PWD}"/selenium/Airtime.html "${PWD}"/results.html

