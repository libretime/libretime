#!/bin/bash

showhelp() {
    echo "Usage: debian.sh [options]
    -h, --help, -?
                Display usage information
    --hostname=HOSTNAME
                Appends the hostname to the guest's /etc/hosts
                "
    exit 0
}

# On some boxes, the hostname is not set correctly, causing RabbitMQ to fail to
# start. This adds the hostname to /etc/hosts, which allows it to work
# correctly.
hostname=
while :; do
    case "$1" in
        -h|--help|\?)
            showhelp
            ;;
        --hostname|-n)
            if [ "$2" ]; then
                hostname=$2
                shift 2
                continue
            else
                echo 'ERROR: Must specify a non-empty "--hostname HOSTNAME" argument.' >&2
                exit 1
            fi
            ;;
        --hostname=?*)
            hostname=${1#*=} # Delete everything up to = and assign the remainder
            ;;
        --hostname=)
            echo 'ERROR: Must specify a non-empty "--hostname HOSTNAME" argument.' >&2
            exit 1
            ;;
        *)
            break
    esac
    shift
done

exists=$(grep "$hostname" /etc/hosts)
if [ -n "$hostname" -a -z "$exists" ]; then
    echo "Appending $hostname to /etc/hosts"
    echo "$hostname" >> /etc/hosts
fi


DEBIAN_FRONTEND=noninteractive apt-get -y -m --force-yes install alsa-utils
usermod -a -G audio vagrant
usermod -a -G audio www-data

# Fix wrong default locale
update-locale LC_ALL=en_US.UTF-8 LANG=en_US.UTF-8 LANGUAGE=en_US.UTF-8

if [ ! -e /etc/systemd/system/airtime-playout.unit.d ]; then
    mkdir /etc/systemd/system/airtime-playout.unit.d/
fi
cat <<EOF > /etc/systemd/system/airtime-playout.unit.d/overide.conf
Environment="PYTHONIOENCODING=UTF-8"
Environment="LANG=en_US.UTF-8"
Environment="LC_ALL=en_US.UTF-8"
Environment="LANGUAGE=en_US.UTF-8"
EOF
systemctl daemon-reload
