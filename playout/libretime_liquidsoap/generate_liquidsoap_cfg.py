import os
import sys
import time
import traceback
from pathlib import Path
from typing import Optional

from libretime_api_client.version1 import AirtimeApiClient
from loguru import logger


def generate_liquidsoap_config(ss, log_filepath: Optional[Path]):
    data = ss["msg"]
    fh = open("/etc/libretime/liquidsoap.cfg", "w")
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")
    fh.write("# The ignore() lines are to squash unused variable warnings\n")

    for key, value in data.items():
        try:
            if not "port" in key and not "bitrate" in key:  # Stupid hack
                raise ValueError()
            str_buffer = f"{key} = {int(value)}\n"
        except ValueError:
            try:  # Is it a boolean?
                if value == "true" or value == "false":
                    str_buffer = f"{key} = {value.lower()}\n"
                else:
                    raise ValueError()  # Just drop into the except below
            except:  # Everything else is a string
                str_buffer = f'{key} = "{value}"\n'

        fh.write(str_buffer)
        # ignore squashes unused variable errors from Liquidsoap
        fh.write("ignore(%s)\n" % key)

    auth_path = os.path.dirname(os.path.realpath(__file__))
    log_file = log_filepath.resolve() if log_filepath is not None else ""

    fh.write(f'log_file = "{log_file}"\n')
    fh.write('auth_path = "%s/liquidsoap_auth.py"\n' % auth_path)
    fh.close()


def run(log_filepath: Optional[Path]):
    attempts = 0
    max_attempts = 10
    successful = False

    while not successful:
        try:
            ac = AirtimeApiClient(logger)
            ss = ac.get_stream_setting()
            generate_liquidsoap_config(ss, log_filepath)
            successful = True
        except Exception as e:
            print("Unable to connect to the Airtime server.")
            logger.error(str(e))
            logger.error("traceback: %s", traceback.format_exc())
            if attempts == max_attempts:
                logger.error("giving up and exiting...")
                sys.exit(1)
            else:
                logger.info("Retrying in 3 seconds...")
                time.sleep(3)
        attempts += 1
