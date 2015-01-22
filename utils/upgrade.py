#!/usr/bin/python

import ConfigParser
import argparse
import requests
from urlparse import urlparse
import sys

CONFIG_PATH='/etc/airtime/airtime.conf'
GENERAL_CONFIG_SECTION = "general"

def read_config_file(config_path):
    """Parse the application's config file located at config_path."""
    config = ConfigParser.SafeConfigParser()
    try:
        config.readfp(open(config_path))
    except IOError as e:
        print "Failed to open config file at " + config_path + ": " + e.strerror 
        exit(-1)
    except Exception:
        print e.strerror 
        exit(-1)

    return config

if __name__ == '__main__':
    config = read_config_file(CONFIG_PATH)
    api_key = config.get(GENERAL_CONFIG_SECTION, 'api_key')
    base_url = config.get(GENERAL_CONFIG_SECTION, 'base_url')
    base_dir = config.get(GENERAL_CONFIG_SECTION, 'base_dir')
    action = "upgrade"
    airtime_url = ""

    parser = argparse.ArgumentParser()
    parser.add_argument('--downgrade', help='Downgrade the station', action="store_true")
    parser.add_argument('station_url', help='station URL', nargs='?', default='')
    args = parser.parse_args()
    
    if args.downgrade:
        action = "downgrade"

    if airtime_url == "":
        airtime_url = "http://%s%s" % (base_url, base_dir)

    # Add http:// if you were lazy and didn't pass a scheme to this script
    url = urlparse(airtime_url) 
    if not url.scheme:
        airtime_url = "http://%s" % airtime_url

    print "Requesting %s..." % action
    r = requests.get("%s/%s" % (airtime_url, action), auth=(api_key, ''))
    print r.text
    r.raise_for_status()

