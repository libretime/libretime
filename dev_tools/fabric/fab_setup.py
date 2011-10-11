import os
import time
from fabric.api import *
from fabric.contrib.files import comment, sed, append


# Download tar file
#

# Globals

env.user = 'martin'
env.hosts = []
env.host_string

env.vm_download_url = "http://host.sourcefabric.org/vms/VirtualBox"

#fab -f fab_setup.py ubuntu_lucid_64 airtime_182_tar airtime_190_tar 


def test():
    x = sudo('airtime-check-system')
    print x.failed
    print x.succeeded
    print x.return_code
    
def download_if_needed(vdi_tmp_dir, xml_tmp_dir, vm_name, vm_vdi_file, vm_xml_file):
    if not os.path.exists(vdi_tmp_dir):
        os.makedirs(vdi_tmp_dir)
    
    if os.path.exists(os.path.join(vdi_tmp_dir, vm_vdi_file)):
        print "File %s already exists. No need to re-download" % os.path.join(vdi_tmp_dir, vm_vdi_file)
    else:
        print "File %s not found. Downloading" % vm_vdi_file
        local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_vdi_file, os.path.join(vdi_tmp_dir, vm_vdi_file)))  
        
    if os.path.exists(os.path.join(xml_tmp_dir, vm_xml_file)):
        print "File %s already exists. No need to re-download" % os.path.join(xml_tmp_dir, vm_xml_file)
    else:
        print "File %s not found. Downloading" % vm_xml_file 
        local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_xml_file, os.path.join(xml_tmp_dir, vm_xml_file)))   

def create_fresh_os(os_version, os_arch):
    
    vdi_tmp_dir = os.path.expanduser('~/tmp/vms/')
    xml_tmp_dir = os.path.expanduser('~/.VirtualBox')
    vm_name = 'Ubuntu_%s_%s'%(os_version, os_arch)
    vm_vdi_file = 'Ubuntu_%s_%s.vdi'%(os_version, os_arch)
    vm_xml_file = 'Ubuntu_%s_%s.xml'%(os_version, os_arch)
    
    downloaded = download_if_needed(vdi_tmp_dir, xml_tmp_dir, vm_name, vm_vdi_file, vm_xml_file)
        
    local("VBoxManage registervm %s"%os.path.join(xml_tmp_dir, vm_xml_file))
    local('VBoxManage storagectl "%s" --name "SATA Controller" --add sata'%vm_name)
    local('VBoxManage storageattach "%s" --storagectl "SATA Controller" --port 0 --type hdd --medium %s'%(vm_name, os.path.join(vdi_tmp_dir, vm_vdi_file)))
    
    #if downloaded:
    local('VBoxManage snapshot "%s" take "fresh_install_test2"'%vm_name)
    #else:
    #    local('VBoxManage snapshot %s restore fresh_install'%vm_name)
        
    local('VBoxManage startvm %s'%vm_name)
        
    time.sleep(20)
    
    ret = local('VBoxManage --nologo guestproperty get "%s" /VirtualBox/GuestInfo/Net/0/V4/IP'%vm_name)
    
    triple = ret.partition(':')
    ip_addr = triple[2].strip(' \r\n')
    print "Address found %s"%ip_addr
    env.hosts.append(ip_addr)
    env.host_string = ip_addr
    

def ubuntu_lucid_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('10.04', '32')

def ubuntu_lucid_64(fresh_os=True):
    pass

def ubuntu_natty_32(fresh_os=True):
    pass
    
def ubuntu_natty_64(fresh_os=True):
    pass

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
    sudo('pear install phing/phing-2.4.2 || true')
    
    sudo('ln -sf /etc/apache2/mods-available/php5.* /etc/apache2/mods-enabled')
    sudo('ln -sf /etc/apache2/mods-available/rewrite.* /etc/apache2/mods-enabled')

    sed('/etc/php5/apache2/php.ini', ";upload_vdi_tmp_dir =", "upload_vdi_tmp_dir = /tmp", use_sudo=True)
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
