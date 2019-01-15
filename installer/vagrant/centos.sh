#!/bin/bash

# Additional Repos
yum install -y epel-release

# Nux Dextop
yum install -y http://li.nux.ro/download/nux/dextop/el7/x86_64/nux-dextop-release-0-5.el7.nux.noarch.rpm

# We are after PUIAS Unsupported where we get celery from
# the install needs forcing since springdale-core tries to replace centos-release
curl -O http://springdale.math.ias.edu/data/puias/6/x86_64/os/Packages/springdale-unsupported-6-2.sdl6.10.noarch.rpm
rpm -hiv --nodeps springdale-unsupported-6-2.sdl6.10.noarch.rpm 
rm -f springdale-unsupported-6-2.sdl6.10.noarch.rpm 
# we need to install the key manually since it is also part of springdale-core
curl -O http://puias.princeton.edu/data/puias/6/x86_64/os/RPM-GPG-KEY-puias
rpm --import RPM-GPG-KEY-puias 
rm -f RPM-GPG-KEY-puias

# RaBe Liquidsoap Distribution (RaBe LSD)
curl -o /etc/yum.repos.d/home:radiorabe:liquidsoap.repo \
    http://download.opensuse.org/repositories/home:/radiorabe:/liquidsoap/CentOS_7/home:radiorabe:liquidsoap.repo

# RaBe Audio Packages for Enterprise Linux (RaBe APEL)
curl -o /etc/yum.repos.d/home:radiorabe:audio.repo \
    http://download.opensuse.org/repositories/home:/radiorabe:/audio/CentOS_7/home:radiorabe:audio.repo

# Update all the things (just to be sure we are on latest)
yum update -y

# Database
yum install -y postgresql-server patch

postgresql-setup initdb

patch -f /var/lib/pgsql/data/pg_hba.conf << EOD
--- /var/lib/pgsql/data/pg_hba.conf.orig2016-09-01 20:45:11.364000000 -0400
+++ /var/lib/pgsql/data/pg_hba.conf2016-09-01 20:46:17.939000000 -0400
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
 #local   replication     postgres                                peer
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
yum install -y rabbitmq-server

systemctl enable rabbitmq-server
systemctl start rabbitmq-server

rabbitmqctl add_user airtime airtime
rabbitmqctl add_vhost /airtime
rabbitmqctl set_permissions -p /airtime airtime ".*" ".*" ".*"

# LibreTime deps
# TODO: move me to requirements-file ala debian e.a.
yum install -y \
  git \
  php \
  php-xml \
  php-pdo \
  php-pgsql \
  php-bcmath \
  php-mbstring \
  httpd \
  fdk-aac \
  liquidsoap \
  silan \
  ecasound \
  alsa-utils \
  icecast \
  python-pip \
  selinux-policy \
  policycoreutils-python \
  python-celery \
  python2-pika \
  lsof

# for pip ssl install
yum install -y \
  gcc \
  python-devel \
  python-lxml \
  openssl-devel



# SELinux Setup
setsebool -P httpd_can_network_connect 1
setsebool -P httpd_can_network_connect_db 1
setsebool -P httpd_execmem on # needed by liquidsoap to do stuff when called by php
setsebool -P httpd_use_nfs 1 # to get nfs mounted /vagrant
setsebool -P git_system_use_nfs 1 # same for git

semanage port -a -t http_port_t -p tcp 9080 # default vagrant web port

# Allow apache full access to /vagrant and /etc/airtime
semanage fcontext -a -t httpd_sys_rw_content_t "/vagrant(/.*)?"
semanage fcontext -a -t httpd_sys_rw_content_t "/etc/airtime(/.*)?"
semanage fcontext -a -t httpd_sys_rw_content_t "/srv/airtime(/.*)?"

restorecon -Rv /vagrant /etc/airtime /srv/airtime

# Disable default apache page
sed -i -e 's/^/#/' /etc/httpd/conf.d/welcome.conf

# celery will not run unless we install a specific version (https://github.com/pypa/setuptools/issues/942)
# this will need to be figured out later on and will get overriden by the docs installer anyhow :(
pip install setuptools==33.1.1
pip freeze setuptools==33.1.1

# the web will fail badly if this is not set, using my personal default just because
echo 'date.timezone=Europe/Zurich' >> /etc/php.d/timezone.ini
systemctl restart httpd

# icecast needs to be available to everyone
sed -i -e 's@<bind-address>127.0.0.1</bind-address>@<bind-address>0.0.0.0</bind-address>@' /etc/icecast.xml 
systemctl enable --now icecast

# let em use alsa
usermod -a -G audio apache
