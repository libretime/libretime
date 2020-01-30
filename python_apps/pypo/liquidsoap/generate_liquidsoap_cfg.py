
import logging
import os
import sys
import time
import traceback
from api_clients.version1 import AirtimeApiClient

def generate_liquidsoap_config(ss):
    data = ss['msg']
    fh = open('/etc/airtime/liquidsoap.cfg', 'w')
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")
    fh.write("# The ignore() lines are to squash unused variable warnings\n")

    for key, value in data.items():
        try:
            if not "port" in key and not "bitrate" in key: # Stupid hack
                raise ValueError()
            str_buffer = "%s = %s\n" % (key, int(value))
        except ValueError:
            try: # Is it a boolean?
                if value=="true" or value=="false":
                    str_buffer = "%s = %s\n" % (key, value.lower())
                else:
                    raise ValueError() # Just drop into the except below
            except: #Everything else is a string
                str_buffer = "%s = \"%s\"\n" % (key, value)

        fh.write(str_buffer)
        # ignore squashes unused variable errors from Liquidsoap
        fh.write("ignore(%s)\n" % key)

    auth_path = os.path.dirname(os.path.realpath(__file__))
    fh.write('log_file = "/var/log/airtime/pypo-liquidsoap/<script>.log"\n')
    fh.write('auth_path = "%s/liquidsoap_auth.py"\n' % auth_path)
    fh.close()

def run():
    logging.basicConfig(format='%(message)s')
    attempts = 0
    max_attempts = 10
    successful = False

    while not successful:
        try:
            ac = AirtimeApiClient(logging.getLogger())
            ss = ac.get_stream_setting()
            generate_liquidsoap_config(ss)
            successful = True
        except Exception as e:
            print("Unable to connect to the Airtime server.")
            logging.error(str(e))
            logging.error("traceback: %s", traceback.format_exc())
            if attempts == max_attempts:
                logging.error("giving up and exiting...")
                sys.exit(1)
            else:
                logging.info("Retrying in 3 seconds...")
                time.sleep(3)
        attempts += 1
