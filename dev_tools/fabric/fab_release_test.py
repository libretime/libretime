#Airtime install/upgrade infrastructure
#author martin.konecny@sourcefabric.org

#Documentation for this page:
#http://wiki.sourcefabric.org/x/OwCD


import os
import time
import sys
from fabric.api import *
from fabric.contrib.files import comment, sed, append

from xml.dom.minidom import parse
from xml.dom.minidom import Node
from xml.dom.minidom import Element

env.user = 'martin'
env.password = 'test'
env.hosts = []
env.host_string

env.vm_download_url = "http://host.sourcefabric.org/vms/VirtualBox"

#fab -f fab_setup.py ubuntu_lucid_64 airtime_182_tar airtime_190_tar 


def shutdown():
    sudo("shutdown -hP now")
    time.sleep(30)

def test():
    x = sudo('airtime-check-system')
    print x.failed
    print x.succeeded
    print x.return_code
    
def download_if_needed(vdi_dir, xml_dir, vm_name, vm_vdi_file, vm_xml_file):
    if not os.path.exists(vdi_dir):
        os.makedirs(vdi_dir)
    
    if os.path.exists(os.path.join(vdi_dir, vm_vdi_file)):
        print "File %s already exists. No need to re-download" % os.path.join(vdi_dir, vm_vdi_file)
    else:
        print "File %s not found. Downloading" % vm_vdi_file
        tmpPath = local("mktemp", capture=True)
        local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_vdi_file, tmpPath))
        os.rename(tmpPath, os.path.join(vdi_dir, vm_vdi_file))
           
    local("rm -f %s"%os.path.join(xml_dir, vm_xml_file))
    local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_xml_file, os.path.join(xml_dir, vm_xml_file)))
   

def create_fresh_os(vm_name, update_virtualenv=False, debian=False):
    
    vm_vdi_file = '%s.vdi'%vm_name
    vm_xml_file = '%s.xml'%vm_name
    vdi_dir = os.path.expanduser('~/tmp/vms/%s'%vm_name)
    vdi_snapshot_dir = os.path.expanduser('~/tmp/vms/%s/Snapshots'%vm_name)
    xml_dir = os.path.expanduser('~/.VirtualBox')
    vm_xml_path = os.path.join(xml_dir, vm_xml_file)
    
    """
    if not os.path.exists("%s/vm_registered"%vdi_dir) and os.path.exists(vm_xml_path):
        #vm_xml file exists, but it wasn't registered. Did something go wrong on a previous attempt?
        #Let's attempt to correct this by completely removing the virtual machine.
        
        dom = parse(vm_xml_path)
        root = dom.childNodes[0]
        rootChildren = root.childNodes
        
        #manually remove all snapshots before removing virtual machine
        for rc in rootChildren:
            if rc.nodeType == Node.ELEMENT_NODE and rc.localName == "Machine":
                snapshotNodes = rc.getElementsByTagName("Snapshot")
                for sn in snapshotNodes:
                    local("VBoxManage snapshot %s delete %s"% (vm_name, sn.getAttribute("uuid")[1:-1]))
    
        os.remove(vm_xml_path)
        local("VBoxManage unregistervm %s --delete"% vm_name)
    """
        
    download_if_needed(vdi_dir, xml_dir, vm_name, vm_vdi_file, vm_xml_file)
        
    if not os.path.exists("%s/vm_registered"%vdi_dir):
        local("VBoxManage registervm %s"%os.path.join(xml_dir, vm_xml_file), capture=True)
        local('VBoxManage storagectl "%s" --name "SATA Controller" --add sata'%vm_name)
        local('VBoxManage storageattach "%s" --storagectl "SATA Controller" --port 0 --device 0 --type hdd --medium %s'%(vm_name, os.path.join(vdi_dir, vm_vdi_file)))
        local("VBoxManage modifyvm %s --snapshotfolder %s"%(vm_name, vdi_snapshot_dir))
        local("VBoxManage snapshot %s take fresh_install"%vm_name)
        local("touch %s/vm_registered"%vdi_dir)


    local('VBoxManage snapshot %s restore fresh_install'%vm_name)
    local('VBoxManage startvm %s'%vm_name)
    print "Please wait while attempting to acquire IP address"
        
    time.sleep(15)

    try_again = True
    while try_again:
        ret = local('VBoxManage --nologo guestproperty get "%s" /VirtualBox/GuestInfo/Net/0/V4/IP'%vm_name, capture=True)
        triple = ret.partition(':')
        ip_addr = triple[2].strip(' \r\n')
        print "Address found %s"%ip_addr
        
        try_again = (len(ip_addr) == 0)
        time.sleep(1)
        
    env.hosts.append(ip_addr)
    env.host_string = ip_addr
    
    if update_virtualenv:
        print "Lucid detected - updating python virtualenv"
        sudo('apt-get update')
        sudo('apt-get install -y python-setuptools')
        sudo('wget http://apt.sourcefabric.org/pool/main/p/python-virtualenv/python-virtualenv_1.4.9-3_all.deb')

        sudo('dpkg -i python-virtualenv_1.4.9-3_all.deb')
        
    if debian:
        append('/etc/apt/sources.list', "deb http://www.debian-multimedia.org squeeze main non-free", use_sudo=True)

def ubuntu_lucid_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_32', update_virtualenv=True)

def ubuntu_lucid_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_64', update_virtualenv=True)

def ubuntu_natty_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.04_32')
    
def ubuntu_natty_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.04_64')
        
def debian_squeeze_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_32', debian=True)
        
def debian_squeeze_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_64', debian=True)
        
def airtime_182_tar():
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

    sed('/etc/php5/apache2/php.ini', ";upload_vdi_dir =", "upload_vdi_dir = /tmp", use_sudo=True)
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

    #need to reboot because of daemon-tools.
    reboot(45)
    sudo('airtime-check-system')
    
def airtime_194_tar():
    #1.9.4 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    sudo('apt-get update')
    
    run('wget http://downloads.sourceforge.net/project/airtime/1.9.4/airtime-1.9.4.tar.gz')
    run('tar xfz airtime-1.9.4.tar.gz')
    sudo('cd /home/martin/airtime-1.9.4/install_full/ubuntu && ./airtime-full-install')
    
def airtime_195_tar():
    run('wget http://downloads.sourceforge.net/project/airtime/1.9.5-RC5/airtime-1.9.5-RC5.tar.gz')
    run('tar xfz airtime-1.9.5-RC5.tar.gz')
    sudo('cd /home/martin/airtime-1.9.5/install_full/ubuntu && ./airtime-full-install')

def airtime_latest_deb():
    append('/etc/apt/sources.list', "deb http://apt.sourcefabric.org/ lucid main", use_sudo=True)
    append('/etc/apt/sources.list', "deb http://archive.ubuntu.com/ubuntu/ lucid multiverse", use_sudo=True)
    sudo('apt-get update')
    sudo('apt-get install -y --force-yes sourcefabric-keyring')
    sudo('apt-get install -y postgresql')
    sudo('apt-get install -y icecast2')
    sudo('apt-get purge -y pulseaudio')
    sudo('apt-get install -y --force-yes airtime')
    
def airtime_git_branch(branch="devel"):
    sudo('apt-get update')
    sudo('apt-get install -y git-core')
    run('git clone https://github.com/sourcefabric/Airtime.git ~/airtime')
    sudo('cd /home/martin/airtime && git checkout %s && install_full/ubuntu/airtime-full-install || true' % branch)


def airtime_200():
    pass
