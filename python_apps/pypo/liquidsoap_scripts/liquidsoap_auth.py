from api_clients import *
from configobj import ConfigObj
import sys
import json

try:
    config = ConfigObj('/etc/airtime/pypo.cfg')
    
except Exception, e:
    print 'error: ', e
    sys.exit()

api_clients = api_client.api_client_factory(config)

username = sys.argv[1]
password = sys.argv[2]
response = api_clients.check_live_stream_auth(username, password)

print response['msg']