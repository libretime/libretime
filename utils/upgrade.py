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
    base_port = config.get(GENERAL_CONFIG_SECTION, 'base_port', 80)
    action = "upgrade"
    station_url = ""

    default_url = "http://%s:%s%s" % (base_url, base_port, base_dir)

    parser = argparse.ArgumentParser()
    parser.add_argument('--downgrade', help='Downgrade the station', action="store_true")
    parser.add_argument('station_url', help='station URL', nargs='?', default=default_url)
    args = parser.parse_args()
    
    if args.downgrade:
        action = "downgrade"

    if args.station_url:
        station_url = args.station_url

    # Add http:// if you were lazy and didn't pass a scheme to this script
    url = urlparse(station_url) 
    if not url.scheme:
        station_url = "http://%s" % station_url

    print "Requesting %s..." % action
    r = requests.get("%s/%s" % (station_url, action), auth=(api_key, ''))
    print r.text
    r.raise_for_status()

