from api_clients import *
import sys

api_clients = api_client.AirtimeApiClient()

dj_type = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]

source_type = ''
if dj_type == '--master':
    source_type = 'master'
elif dj_type == '--dj':
    source_type = 'dj'

response = api_clients.check_live_stream_auth(username, password, source_type)

if 'msg' in response and response['msg'] == True:
    print response['msg']
    sys.exit(0)
else:
    print False
    sys.exit(1)
