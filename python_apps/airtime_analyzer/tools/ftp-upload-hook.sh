#! /bin/bash

post_file() {
    #kill process after 30 minutes (360*5=30 minutes)
    max_retry=360
    retry_count=0

    file_path=${1}
    filename="${file_path##*/}"
	
    #base_instance_path and airtime_conf_path are common to all saas instances
    base_instance_path=/mnt/airtimepro/instances/
    airtime_conf_path=/etc/airtime/airtime.conf
	
    #maps the instance_path to the url
    vhost_file=/mnt/airtimepro/system/vhost.map

    #instance_path will look like 1/1384, for example
    instance_path=$(echo ${file_path} | grep -Po "(?<=($base_instance_path)).*?(?=/srv)")
	
    #post request url - http://bananas.airtime.pro/rest/media, for example
    url=http://
    url+=$(grep -E $instance_path $vhost_file | awk '{print $1;}')
    url+=/rest/media
	
    #path to specific instance's airtime.conf
    instance_conf_path=$base_instance_path$instance_path$airtime_conf_path
	
    api_key=$(awk -F "= " '/api_key/ {print $2}' $instance_conf_path)

    until curl --max-time 30 $url -u $api_key":" -X POST -F "file=@${file_path}" -F "full_path=${file_path}"
    do
        retry_count=$[$retry_count+1]
        if [ $retry_count -ge $max_retry ]; then
            break
        fi
        sleep 5
    done
}

post_file "${1}" &
