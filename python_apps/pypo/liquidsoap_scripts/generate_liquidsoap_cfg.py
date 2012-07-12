import logging
import sys
from api_clients import api_client

def generate_liquidsoap_config(ss):
    data = ss['msg']
    fh = open('/etc/airtime/liquidsoap.cfg', 'w')
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")
    for d in data:
        str_buffer = d[u'keyname'] + " = "
        if(d[u'type'] == 'string'):
            temp = d[u'value']
            str_buffer += '"%s"' % temp
        else:
            temp = d[u'value']
            if(temp == ""):
                temp = "0"
            str_buffer += temp
        str_buffer += "\n"
        fh.write(api_client.encode_to(str_buffer))
    fh.write('log_file = "/var/log/airtime/pypo-liquidsoap/<script>.log"\n')
    fh.close()

logging.basicConfig(format='%(message)s')
ac = api_client(logging.getLogger())
ss = ac.get_stream_setting()

if ss is not None:
    try:
        generate_liquidsoap_config(ss)
    except Exception, e:
        logging.error(e)
else:
    print "Unable to connect to the Airtime server."
    sys.exit(1)
