#!/usr/bin/env bash

set -xv

post_file() {
  #kill process after 30 minutes (360*5=30 minutes)
  max_retry=5
  retry_count=0

  file_path="${1}"
  # Give us write permissions on the file to prevent problems if the user
  # uploads a read-only file.
  chmod +w "${file_path}"

  #We must remove commas because CURL can't upload files with commas in the name
  # http://curl.haxx.se/mail/archive-2009-07/0029.html
  stripped_file_path=${file_path//','/''}
  mv "${file_path}" "${stripped_file_path}"
  file_path="${stripped_file_path}"
  # filename="${file_path##*/}"

  airtime_conf_path=/etc/libretime/config.yml

  #instance_path will look like 1/1384, for example
  http_path=$(grep base_url ${airtime_conf_path} | awk '{print $3;}')
  http_port=$(grep base_port ${airtime_conf_path} | awk '{print $3;}')

  #post request url - http://bananas.airtime.pro/rest/media, for example
  url=http://
  url+=$http_path
  url+=:
  url+=$http_port
  url+=/rest/media

  api_key=$(grep api_key ${airtime_conf_path} | awk '{print $3;}')

  # -f is needed to make curl fail if there's an HTTP error code
  # -L is needed to follow redirects! (just in case)
  until curl -fL --max-time 30 $url -u $api_key":" -X POST -F "file=@${file_path}"; do
    retry_count=$((retry_count + 1))
    if [ $retry_count -ge $max_retry ]; then
      break
    fi
    sleep 5
  done
}

post_file "${1}" &
