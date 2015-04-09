#!/bin/bash -xv

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
    filename="${file_path##*/}"
	
    #base_instance_path and airtime_conf_path are common to all saas instances
    base_instance_path=/mnt/airtimepro/instances/
    airtime_conf_path=/etc/airtime/airtime.conf
	
    #maps the instance_path to the url
    vhost_file=/etc/apache2/airtime/vhost.map

    #instance_path will look like 1/1384, for example
    instance_path=$(echo ${file_path} | grep -Po "(?<=($base_instance_path)).*?(?=/srv)")
	
    #post request url - http://bananas.airtime.pro/rest/media, for example
    url=http://
    url+=$(grep -E $instance_path $vhost_file | awk '{print $1;}')
    url+=/rest/media
	
    #path to specific instance's airtime.conf
    instance_conf_path=$base_instance_path$instance_path$airtime_conf_path
	
    api_key=$(awk -F "= " '/api_key/ {print $2}' $instance_conf_path)

    # -f is needed to make curl fail if there's an HTTP error code
    # -L is needed to follow redirects! (just in case)
    until curl -fL --max-time 30 $url -u $api_key":" -X POST -F "file=@${file_path}" 
    do
        retry_count=$[$retry_count+1]
        if [ $retry_count -ge $max_retry ]; then
            break
        fi
        sleep 5
    done
}

post_file "${1}" &
