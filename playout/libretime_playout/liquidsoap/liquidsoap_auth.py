import sys

from libretime_api_client.v1 import ApiClient as LegacyClient

legacy_client = LegacyClient()

dj_type = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]

source_type = ""
if dj_type == "--master":
    source_type = "master"
elif dj_type == "--dj":
    source_type = "dj"

response = legacy_client.check_live_stream_auth(username, password, source_type)

if "msg" in response and response["msg"] == True:
    print(response["msg"])
    sys.exit(0)
else:
    print(False)
    sys.exit(1)
