import os
import time
from fabric.api import *
from fabric.contrib.files import comment, sed, append


# Download tar file
#

# Globals

env.user = 'martin'
env.hosts = ['192.168.5.36']

"""
Main dispatcher function to be called from the command-line. Allows us to specify source and target version of Airtime,
to test upgrade scripts, along with whether we should load a fresh version of the OS (from a VM snapshot), the OS version
and architecture. 
"""
def dispatcher(source_version="182", target_version="194", fresh_os=True, os_version='10.04', os_arch='amd64'):
    if (fresh_os):
        create_fresh_os(os_version, os_arch)
    globals()["airtime_%s"%source_version]()
    globals()["airtime_%s"%target_version]()


def test():
    x = sudo('airtime-check-system')
    print x.failed
    print x.succeeded
    print x.return_code

def create_fresh_os(os_version, os_arch):
    ret = local('VBoxManage snapshot ubuntu_64_server restore Fresh', capture=True)
    if (ret.failed):
        print ret
        print "Restoring snapshot failed, are you sure it's not already running?"
        
    ret = local('VBoxManage startvm ubuntu_64_server', capture=True)
    if (ret.failed):
        print ret
        print "Starting Virtual Machine failed, are you sure it's not already running?"
        
    time.sleep(15)

def airtime_182():
    sudo('apt-get update')
    sudo('apt-get install -y tar gzip unzip apache2 php5-pgsql libapache2-mod-php5 ' + \
        'php-pear php5-gd postgresql odbc-postgresql python python-configobj poc-streamer ' + \
        'lame daemontools daemontools-run python-mutagen libsoundtouch-ocaml sudo ' + \
        'libtaglib-ocaml libao-ocaml libmad-ocaml libesd0 icecast2 oggvideotools ' + \
        'libportaudio2 libsamplerate0 libcamomile-ocaml-dev ecasound php5-curl mpg123 ' + \
        'python-setuptools python-pip rabbitmq-server libvorbis-ocaml-dev libmp3lame-dev flac')
        
    sudo('pip install kombu')
    sudo('pip install poster')

    sudo('mkdir -p /tmp/pear/cache')
    sudo('pear channel-discover pear.phing.info || true')
    sudo('pear install phing/phing-2.4.2')
    
    sudo('ln -sf /etc/apache2/mods-available/php5.* /etc/apache2/mods-enabled')
    sudo('ln -sf /etc/apache2/mods-available/rewrite.* /etc/apache2/mods-enabled')

    sed('/etc/php5/apache2/php.ini', ";upload_tmp_dir =", "upload_tmp_dir = /tmp", use_sudo=True)
    sed('/etc/php5/apache2/php.ini', ";date.timezone =", 'date.timezone = "America/Toronto"', use_sudo=True)

    put('airtime.vhost', '/etc/apache2/sites-available/airtime', use_sudo=True)
    sudo('a2dissite default')
    sudo('ln -sf /etc/apache2/sites-available/airtime /etc/apache2/sites-enabled/airtime')
    sudo('a2enmod rewrite')
    sudo('service apache2 restart')

    sed('/etc/default/icecast2', 'ENABLE=false', 'ENABLE=true', use_sudo=True)
    sudo('service icecast2 start')
    
    run('wget http://downloads.sourceforge.net/project/airtime/1.8.2/airtime-1.8.2.tar.gz')
    run('tar xfz airtime-1.8.2.tar.gz')
    sudo('cd ~/airtime-1.8.2/install && php airtime-install.php')

    reboot(45)
    sudo('airtime-check-system')

def airtime_194():
    run('wget http://downloads.sourceforge.net/project/airtime/1.9.4/airtime-1.9.4.tar.gz')
    run('tar xfz airtime-1.9.4.tar.gz')
    sudo('cd ~/airtime-1.9.4/install_full/ubuntu && ./airtime-full-install')


def airtime_200():
    pass
