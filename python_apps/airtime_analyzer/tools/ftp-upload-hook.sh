#! /bin/bash

path=$1
filename="${path##*/}"
curl http://localhost/rest/media -u 3188BDIMPJROQP89Z0OX: -X POST -F "file=@$path" -F "name=$filename"
