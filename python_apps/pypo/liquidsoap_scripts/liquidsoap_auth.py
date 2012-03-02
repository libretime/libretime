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

dj_type = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]

type = ''
if dj_type == '--master':
    type = 'master'
elif dj_type == '--dj':
    type = 'dj'
    
response = api_clients.check_live_stream_auth(username, password, type)

print response['msg']