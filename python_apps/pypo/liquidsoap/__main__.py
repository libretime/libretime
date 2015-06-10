""" Runs Airtime liquidsoap
"""

import argparse
import os
import generate_liquidsoap_cfg

PYPO_HOME = '/var/tmp/airtime/pypo/'

def run():
    '''Entry-point for this application'''
    print "Airtime Liquidsoap"
    parser = argparse.ArgumentParser()
    parser.add_argument("-d", "--debug", help="run in debug mode", action="store_true")
    args = parser.parse_args()
    
    os.environ["HOME"] = PYPO_HOME
    
    generate_liquidsoap_cfg.run()
    script_path = os.path.join(os.path.dirname(__file__), 'ls_script.liq')
    
    if args.debug:
        os.execl('/usr/bin/liquidsoap', 'airtime-liquidsoap', script_path, '--verbose', '-f', '--debug')
    else:
        os.execl('/usr/bin/liquidsoap', 'airtime-liquidsoap', script_path, '--verbose', '-f')

run()