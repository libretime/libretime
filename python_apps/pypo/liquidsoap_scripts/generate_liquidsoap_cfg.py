import logging
import sys
from api_clients import api_client
from configobj import ConfigObj

def generate_liquidsoap_config(ss):
    data = ss['msg']
    fh = open('/etc/airtime/liquidsoap.cfg', 'w')
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")
    for d in data:
        buffer = d[u'keyname'] + " = "
        if(d[u'type'] == 'string'):
            temp = d[u'value']
            buffer += '"%s"' % temp
        else:
            temp = d[u'value']
            if(temp == ""):
                temp = "0"
            buffer += temp
        buffer += "\n"
        fh.write(api_client.encode_to(buffer))
    fh.write('log_file = "/var/log/airtime/pypo-liquidsoap/<script>.log"\n')
    fh.close()

PATH_INI_FILE = '/etc/airtime/pypo.cfg'
    
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

logging.basicConfig(format='%(message)s')
ac = api_client.api_client_factory(config, logging.getLogger())
ss = ac.get_stream_setting()

if ss is not None:
    try:
        generate_liquidsoap_config(ss)
    except Exception, e:
        logging.error(e)
else:
    print "Unable to connect to the Airtime server."
    sys.exit(1)
