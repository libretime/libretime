#!/usr/bin/python
#
# ./postCommitBuildTrigger.py http://bamoo.atlassian.com/bamboo/ myBuildName

import sys
import urllib;

baseUrl = sys.argv[1]
buildKey = sys.argv[2]

remoteCall = baseUrl + "/api/rest/updateAndBuild.action?buildKey=" + buildKey
fileHandle = urllib.urlopen(remoteCall)
fileHandle.close()