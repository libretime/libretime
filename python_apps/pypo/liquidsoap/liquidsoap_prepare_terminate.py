from configobj import ConfigObj
import telnetlib
import sys

try:
    config = ConfigObj('/etc/airtime/airtime.conf')
    LS_HOST = config['pypo']['ls_host']
    LS_PORT = config['pypo']['ls_port']

    tn = telnetlib.Telnet(LS_HOST, LS_PORT)
    tn.write("master_harbor.stop\n")
    tn.write("live_dj_harbor.stop\n")
    tn.write('exit\n')
    tn.read_all()

except Exception as e:
    print(('Error loading config file: %s', e))
    sys.exit()

