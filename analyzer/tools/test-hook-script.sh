#! /bin/bash

post_file() {
  file_path=${1}
  filename="${file_path##*/}"

  #kill process after 30 minutes (360*5=30 minutes)
  max_retry=10
  retry_count=0

  until curl --max-time 30 http://localhost/rest/media -u 3188BDIMPJROQP89Z0OX: -X POST -F "file=@${file_path}" -F "name=${filename}"; do
    retry_count=$((retry_count + 1))
    if [ $retry_count -ge $max_retry ]; then
      break
    fi
    sleep 1
  done
}

post_file "${1}" &
