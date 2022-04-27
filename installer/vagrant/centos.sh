#!/usr/bin/env bash

# Additional Repos
yum install -y epel-release

# RPMfusion (for ffmpeg) - needs PowerTools
dnf install -y https://mirrors.rpmfusion.org/free/el/rpmfusion-free-release-8.noarch.rpm
dnf config-manager --enable powertools

# xiph multimedia (for icecast)
curl -o /etc/yum.repos.d/multimedia:xiph.repo \
  https://download.opensuse.org/repositories/multimedia:/xiph/CentOS_8/multimedia:xiph.repo

# RaBe Liquidsoap Distribution (RaBe LSD)
curl -o /etc/yum.repos.d/home:radiorabe:liquidsoap.repo \
  https://download.opensuse.org/repositories/home:/radiorabe:/liquidsoap/CentOS_8/home:radiorabe:liquidsoap.repo

# RaBe Audio Packages for Enterprise Linux (RaBe APEL)
curl -o /etc/yum.repos.d/home:radiorabe:audio.repo \
  https://download.opensuse.org/repositories/home:/radiorabe:/audio/CentOS_8/home:radiorabe:audio.repo

# Update all the things (just to be sure we are on latest)
yum update -y

# Database
yum install -y postgresql-server patch

postgresql-setup --initdb

patch -f /var/lib/pgsql/data/pg_hba.conf << EOD
--- pg_hba.conf.orig	2020-12-19 13:10:46.828960307 +0000
+++ pg_hba.conf	2020-12-19 13:11:37.356290128 +0000
@@ -78,10 +78,11 @@

 # "local" is for Unix domain socket connections only
 local   all             all                                     peer
+local   all             all                                     md5
 # IPv4 local connections:
-host    all             all             127.0.0.1/32            ident
+host    all             all             127.0.0.1/32            md5
 # IPv6 local connections:
-host    all             all             ::1/128                 ident
+host    all             all             ::1/128                 md5
 # Allow replication connections from localhost, by a user with the
 # replication privilege.
 local   replication     all                                     peer
EOD

systemctl enable postgresql
systemctl start postgresql
# create database user airtime with password airtime
useradd airtime
echo "airtime:airtime" | chpasswd

su -l postgres bash -c 'createuser airtime'
su -l postgres bash -c 'createdb -O airtime airtime'

echo "ALTER USER airtime WITH PASSWORD 'airtime';" | su -l postgres bash -c psql
echo "GRANT ALL PRIVILEGES ON DATABASE airtime TO airtime;" | su -l postgres bash -c psql

# RabbitMQ
curl -s https://packagecloud.io/install/repositories/rabbitmq/rabbitmq-server/script.rpm.sh | sudo bash
yum install -y rabbitmq-server

systemctl enable rabbitmq-server
systemctl start rabbitmq-server

rabbitmqctl add_user airtime airtime
rabbitmqctl add_vhost /airtime
rabbitmqctl set_permissions -p /airtime airtime ".*" ".*" ".*"

# LibreTime deps
# some of these are needed to build pip deps and as such should no be installed
# on production grade systems (mostly the -devel packages)
yum install -y \
  cairo-gobject-devel \
  gcc \
  git \
  glib2-devel \
  gobject-introspection-devel \
  openssl-devel \
  php \
  php-xml \
  php-pdo \
  php-pgsql \
  php-bcmath \
  php-mbstring \
  php-json \
  php-process \
  python38-devel \
  python38-psycopg2 \
  httpd \
  icecast \
  liquidsoap \
  alsa-utils \
  selinux-policy \
  policycoreutils-python-utils \
  lsof \
  xmlstarlet

# replace icecast init system with proper systemd unit ("ported" from CentOS 7)
cat > /etc/systemd/system/icecast.service << 'EOD'
[Unit]
Description=Icecast Network Audio Streeaming Server
After=network.target

[Service]
ExecStart=/usr/bin/icecast -c /etc/icecast.xml
ExecReload=/bin/kill -HUP $MAINPID

[Install]
WantedBy=multi-user.target
EOD

# install manually since it isn't required somewhere later
python3 -mpip install pycairo

# SELinux Setup
setsebool -P httpd_can_network_connect 1
setsebool -P httpd_can_network_connect_db 1
setsebool -P httpd_execmem on # needed by liquidsoap to do stuff when called by php
setsebool -P httpd_use_nfs 1 # to get nfs mounted /vagrant
setsebool -P httpd_graceful_shutdown 1 # to allow prefork to shutdown gracefully
setsebool -P git_system_use_nfs 1 # same for git

semanage port -a -t http_port_t -p tcp 9080 # default vagrant web port

# Allow apache full access to /vagrant and /etc/libretime
semanage fcontext -a -t httpd_sys_rw_content_t "/vagrant(/.*)?"
semanage fcontext -a -t httpd_sys_rw_content_t "/etc/libretime(/.*)?"
semanage fcontext -a -t httpd_sys_rw_content_t "/srv/airtime(/.*)?"

restorecon -Rv /vagrant /etc/libretime /srv/airtime

# Disable default apache page
sed -i -e 's/^/#/' /etc/httpd/conf.d/welcome.conf

# Switch to prefork since CentOS will disable mod_php if we use mpm_event
sed -i \
  -e 's/#LoadModule mpm_prefork_module/LoadModule mpm_prefork_module/' \
  -e 's/LoadModule mpm_event_module/#LoadModule mpm_event_module/' \
  /etc/httpd/conf.modules.d/00-mpm.conf

# celery will not run unless we install a specific version (https://github.com/pypa/setuptools/issues/942)
# this will need to be figured out later on and will get overridden by the docs installer anyhow :(
pip3 install setuptools==33.1.1
pip3 freeze setuptools==33.1.1

# the web will fail badly if this is not set, using my personal default just because
echo 'date.timezone=Europe/Zurich' >> /etc/php.d/timezone.ini
systemctl restart httpd

# icecast needs to be available to everyone
sed -i -e 's@<bind-address>127.0.0.1</bind-address>@<bind-address>0.0.0.0</bind-address>@' /etc/icecast.xml
systemctl enable --now icecast

# let em use alsa
usermod -a -G audio apache
