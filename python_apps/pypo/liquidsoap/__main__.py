""" Runs Airtime liquidsoap
"""
import argparse
import os
from . import generate_liquidsoap_cfg
import logging
import subprocess
from pypo import pure

PYPO_HOME = '/var/tmp/airtime/pypo/'

def run():
    '''Entry-point for this application'''
    print("Airtime Liquidsoap")
    parser = argparse.ArgumentParser()
    parser.add_argument("-d", "--debug", help="run in debug mode", action="store_true")
    args = parser.parse_args()

    os.environ["HOME"] = PYPO_HOME

    if args.debug:
        logging.basicConfig(level=getattr(logging, 'DEBUG', None))

    generate_liquidsoap_cfg.run()
    ''' check liquidsoap version if less than 1.3 use legacy liquidsoap script '''
    liquidsoap_version = subprocess.check_output("liquidsoap --version", shell=True, text=True)
    if pure.version_cmp(liquidsoap_version, "1.3") < 0:
        script_path = os.path.join(os.path.dirname(__file__), 'ls_script.liq')
    else:
        script_path = os.path.join(os.path.dirname(__file__), 'ls_script_legacy.liq')
    if args.debug:
        os.execl('/usr/bin/liquidsoap', 'airtime-liquidsoap', script_path, '--verbose', '-f', '--debug')
    else:
        os.execl('/usr/bin/liquidsoap', 'airtime-liquidsoap', script_path, '--verbose', '-f')

run()
