import logging
import sys
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

logging.basicConfig(format='%(message)s')
ac = AirtimeApiClient(logging.getLogger())
try:
    ss = ac.get_stream_setting()
    generate_liquidsoap_config(ss)
except Exception, e:
    logging.error(str(e))
    print "Unable to connect to the Airtime server."
    sys.exit(1)
