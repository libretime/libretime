import logging
import sys
import time
import traceback
from api_clients.api_client import AirtimeApiClient

def generate_liquidsoap_config(ss):
    data = ss['msg']
    fh = open('/etc/airtime/liquidsoap.cfg', 'w')
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")

    for d in data:
        key = d['keyname']

        str_buffer = d[u'keyname'] + " = "
        if d[u'type'] == 'string':
            val = '"%s"' % d['value']
        else:
            val = d[u'value']
            val = val if len(val) > 0 else "0"
        str_buffer = "%s = %s\n" % (key, val)
        fh.write(str_buffer.encode('utf-8'))

    fh.write('log_file = "/var/log/airtime/pypo-liquidsoap/<script>.log"\n')
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
        except Exception, e:
            print "Unable to connect to the Airtime server."
            logging.error(str(e))
            logging.error("traceback: %s", traceback.format_exc())
            if attempts == max_attempts:
                logging.error("giving up and exiting...")
                sys.exit(1)
            else:
                time.sleep(3)
        attempts += 1
