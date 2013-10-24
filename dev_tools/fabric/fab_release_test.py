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
env.warn_only = True

env.vm_download_url = "http://host.sourcefabric.org/vms/VirtualBox"

#fab -f fab_setup.py ubuntu_lucid_64 airtime_182_tar airtime_190_tar 

def do_sudo(command):
    result = sudo(command)
    if result.return_code != 0:
        print "Error running command: %s" %command
        shutdown()
        sys.exit(1)
    else:
        return result
    
def do_run(command):
    result = run(command)
    if result.return_code != 0:
        print "Error running command: %s" %command
        shutdown()
        sys.exit(1)
    else:
        return result
        
def do_local(command, capture=True):
    result = local(command, capture)
    if result.return_code != 0:
        print "Error running command: %s" %command
        shutdown()
        sys.exit(1)
    else:
        return result

def pause():
    raw_input("--> Press Enter to continue...")
    
def shutdown():
    do_sudo("poweroff")
    time.sleep(45)

def test():
    x = do_sudo('airtime-check-system')
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
        tmpPath = do_local("mktemp", capture=True)
        do_local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_vdi_file, tmpPath))
        os.rename(tmpPath, os.path.join(vdi_dir, vm_vdi_file))
           
    if os.path.exists(os.path.join(xml_dir, vm_xml_file)):
        print "File %s already exists. No need to re-download" % os.path.join(xml_dir, vm_xml_file)
    else:
        do_local("wget %s/%s/%s -O %s"%(env.vm_download_url, vm_name, vm_xml_file, os.path.join(xml_dir, vm_xml_file)))       
   

def create_fresh_os(vm_name, lucid=False, debian=False, icecast2_config=False):
    
    """
    remove known_hosts because if two virtual machines get the same ip address,
    then they will most likey have a different host key, and ssh will fail, warning
    about a possible man in the middle attack.
    """
    do_local("rm -f ~/.ssh/known_hosts")
    
    vm_vdi_file = '%s.vdi'%vm_name
    vm_xml_file = '%s.xml'%vm_name
    vdi_dir = os.path.expanduser('~/tmp/vms/%s'%vm_name)
    vdi_snapshot_dir = os.path.expanduser('~/tmp/vms/%s/Snapshots'%vm_name)
    xml_dir = os.path.expanduser('~/.VirtualBox')
    vm_xml_path = os.path.join(xml_dir, vm_xml_file)
            
    download_if_needed(vdi_dir, xml_dir, vm_name, vm_vdi_file, vm_xml_file)
        
    if not os.path.exists("%s/vm_registered"%vdi_dir):
        do_local("VBoxManage registervm %s"%os.path.join(xml_dir, vm_xml_file), capture=True)
        do_local('VBoxManage storagectl "%s" --name "SATA Controller" --add sata'%vm_name)
        do_local('VBoxManage storageattach "%s" --storagectl "SATA Controller" --port 0 --device 0 --type hdd --medium %s'%(vm_name, os.path.join(vdi_dir, vm_vdi_file)))
        do_local("VBoxManage modifyvm %s --snapshotfolder %s"%(vm_name, vdi_snapshot_dir))
        do_local("VBoxManage snapshot %s take fresh_install"%vm_name)
        do_local("touch %s/vm_registered"%vdi_dir)
        
    do_local('VBoxManage snapshot %s restore fresh_install'%vm_name)
    
    do_local('VBoxManage modifyvm "%s" --bridgeadapter1 eth0'%vm_name)
    do_local('VBoxManage startvm %s'%vm_name)
    print "Please wait while attempting to acquire IP address"
        
    time.sleep(30)

    try_again = True
    while try_again:
        ret = do_local('VBoxManage --nologo guestproperty get "%s" /VirtualBox/GuestInfo/Net/0/V4/IP'%vm_name, capture=True)
        triple = ret.partition(':')
        ip_addr = triple[2].strip(' \r\n')
        print "Address found %s"%ip_addr
        
        try_again = (len(ip_addr) == 0)
        time.sleep(1)
        
    env.hosts.append(ip_addr)
    env.host_string = ip_addr
    
    if lucid:
        print "Lucid detected - updating python virtualenv"
        do_sudo('apt-get update')
        do_sudo('apt-get install -y python-setuptools')
        do_sudo('wget http://apt.sourcefabric.org/pool/main/p/python-virtualenv/python-virtualenv_1.4.9-3_all.deb')

        do_sudo('dpkg -i python-virtualenv_1.4.9-3_all.deb')
        
        #supress rabbitmq bug that makes an upgrade warning pop-up even though it hasn't been 
        #installed before.
        do_sudo('echo "rabbitmq-server rabbitmq-server/upgrade_previous note" | debconf-set-selections')
        
    if debian:
        append('/etc/apt/sources.list', "deb http://backports.debian.org/debian-backports squeeze-backports main", use_sudo=True)

    if icecast2_config:
                print "Updating icecast2 setup settings"
                do_sudo('echo "icecast2 icecast2/icecast-setup  boolean false" | debconf-set-selections')

def ubuntu_lucid_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_32', lucid=True)

def ubuntu_lucid_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_64', lucid=True)
        
def ubuntu_maverick_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.10_32')

def ubuntu_maverick_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.10_64')

def ubuntu_natty_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.04_32')
    
def ubuntu_natty_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.04_64')
        
def ubuntu_oneiric_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.10_32')
    
def ubuntu_oneiric_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_11.10_64')

def ubuntu_precise_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.04_32', icecast2_config=True)
    
def ubuntu_precise_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.04_64', icecast2_config=True)

def ubuntu_quantal_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.10_32', icecast2_config=True)
    
def ubuntu_quantal_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.10_64', icecast2_config=True)
        
def ubuntu_raring_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_13.04_32', icecast2_config=True)
    
def ubuntu_raring_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_13.04_64', icecast2_config=True)
        
def ubuntu_saucy_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_13.10_32', icecast2_config=True)
    
def ubuntu_saucy_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_13.10_64', icecast2_config=True)
        
def debian_squeeze_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_32', debian=True)
        
def debian_squeeze_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_64', debian=True)

def debian_wheezy_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Wheezy_32', icecast2_config=True)
    
def debian_wheezy_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Wheezy_64', icecast2_config=True)
        
def airtime_180_tar():
    airtime_18x_tar("airtime", "1.8.0")
    
def airtime_181_tar():
    airtime_18x_tar("airtime", "1.8.1")
    
def airtime_182_tar():
    airtime_18x_tar("airtime-1.8.2", "1.8.2")
        
def airtime_18x_tar(root_dir, version):
    do_sudo('apt-get update')
    do_sudo('apt-get install -y --force-yes tar gzip unzip apache2 php5-pgsql libapache2-mod-php5 ' + \
        'php-pear php5-gd postgresql odbc-postgresql python python-configobj poc-streamer ' + \
        'lame daemontools daemontools-run python-mutagen libsoundtouch-ocaml sudo ' + \
        'libtaglib-ocaml libao-ocaml libmad-ocaml libesd0 icecast2 oggvideotools ' + \
        'libportaudio2 libsamplerate0 libcamomile-ocaml-dev ecasound php5-curl mpg123 ' + \
        'python-setuptools python-pip rabbitmq-server libvorbis-ocaml-dev libmp3lame-dev flac')
        
    do_sudo('pip install kombu')
    do_sudo('pip install poster')

    do_sudo('mkdir -p /tmp/pear/cache')
    do_sudo('pear channel-discover pear.phing.info || true')
    do_sudo('pear install phing/phing-2.4.2 || true')
    
    do_sudo('ln -sf /etc/apache2/mods-available/php5.* /etc/apache2/mods-enabled')
    do_sudo('ln -sf /etc/apache2/mods-available/rewrite.* /etc/apache2/mods-enabled')

    sed('/etc/php5/apache2/php.ini', ";upload_vdi_dir =", "upload_vdi_dir = /tmp", use_sudo=True)
    sed('/etc/php5/apache2/php.ini', ";date.timezone =", 'date.timezone = "America/Toronto"', use_sudo=True)

    put('airtime.vhost', '/etc/apache2/sites-available/airtime', use_sudo=True)
    do_sudo('a2dissite default')
    do_sudo('ln -sf /etc/apache2/sites-available/airtime /etc/apache2/sites-enabled/airtime')
    do_sudo('a2enmod rewrite')
    do_sudo('service apache2 restart')

    sed('/etc/default/icecast2', 'ENABLE=false', 'ENABLE=true', use_sudo=True)
    do_sudo('service icecast2 start')
    
    #these are do_sudo instead of do_run because in Debian we would be working with different home directores (/home/martin and /root in debian)
    do_sudo('wget http://downloads.sourceforge.net/project/airtime/%s/airtime-%s.tar.gz' % (version, version))
    do_sudo('tar xfz airtime-%s.tar.gz' % version)
    
    #do_sudo('cd ~/%s/install && php airtime-install.php' % root_dir)
    do_sudo('php ~/%s/install/airtime-install.php' % root_dir)

    #need to reboot because of daemon-tools.
    reboot(45)
    do_sudo('airtime-check-system')
    
def airtime_190_tar():
    #1.9.0 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    do_sudo('apt-get update')
    
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.0/airtime-1.9.0.tar.gz')
    do_run('tar xfz airtime-1.9.0.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.0/install_full/ubuntu && ./airtime-full-install')
    
def airtime_191_tar():
    #1.9.0 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    do_sudo('apt-get update')
    
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.1/airtime-1.9.1.tar.gz')
    do_run('tar xfz airtime-1.9.1.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.1/install_full/ubuntu && ./airtime-full-install')
    
def airtime_192_tar():
    #1.9.2 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    do_sudo('apt-get update')
    
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.2/airtime-1.9.2.tar.gz')
    do_run('tar xfz airtime-1.9.2.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.2/install_full/ubuntu && ./airtime-full-install')
    
def airtime_193_tar():
    #1.9.3 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    do_sudo('apt-get update')
    
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.3/airtime-1.9.3.tar.gz')
    do_run('tar xfz airtime-1.9.3.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.3/install_full/ubuntu && ./airtime-full-install')
    
def airtime_194_tar():
    #1.9.4 doesn't do apt-get update during install, and therefore the package index
    #files are not resynchronized. Need to do this here.
    do_sudo('apt-get update')
    
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.4/airtime-1.9.4.tar.gz')
    do_run('tar xfz airtime-1.9.4.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.4/install_full/ubuntu && ./airtime-full-install')
    
def airtime_195_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/1.9.5/airtime-1.9.5.tar.gz')
    do_run('tar xfz airtime-1.9.5.tar.gz')
    do_sudo('cd /home/martin/airtime-1.9.5/install_full/ubuntu && ./airtime-full-install')

def airtime_200_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.0.0/airtime-2.0.0.tar.gz')
    do_run('tar xfz airtime-2.0.0.tar.gz')
    do_sudo('cd /home/martin/airtime-2.0.0/install_full/ubuntu && ./airtime-full-install')

def airtime_201_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.0.1/airtime-2.0.1.tar.gz')
    do_run('tar xfz airtime-2.0.1.tar.gz')
    do_sudo('cd /home/martin/airtime-2.0.1/install_full/ubuntu && ./airtime-full-install')

def airtime_202_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.0.2/airtime-2.0.2.tar.gz')
    do_run('tar xfz airtime-2.0.2.tar.gz')
    do_sudo('cd /home/martin/airtime-2.0.2/install_full/ubuntu && ./airtime-full-install')

    
def airtime_203_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.0.3/airtime-2.0.3.tar.gz')
    do_run('tar xfz airtime-2.0.3.tar.gz')
    do_sudo('cd /home/martin/airtime-2.0.3/install_full/ubuntu && ./airtime-full-install')
    
def airtime_210_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.1.0/airtime-2.1.0.tar.gz')
    do_run('tar xfz airtime-2.1.0.tar.gz')
    do_sudo('cd /home/martin/airtime-2.1.0/install_full/ubuntu && ./airtime-full-install')

def airtime_211_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.1.1/airtime-2.1.1.tar.gz')
    do_run('tar xfz airtime-2.1.1.tar.gz')
    do_sudo('cd /home/martin/airtime-2.1.1/install_full/ubuntu && ./airtime-full-install')

def airtime_212_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.1.2/airtime-2.1.2.tar.gz')
    do_run('tar xfz airtime-2.1.2.tar.gz')
    do_sudo('cd /home/martin/airtime-2.1.2/install_full/ubuntu && ./airtime-full-install')

def airtime_213_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.1.3/airtime-2.1.3.tar.gz')
    do_run('tar xfz airtime-2.1.3.tar.gz')
    do_sudo('cd /home/martin/airtime-2.1.3/install_full/ubuntu && ./airtime-full-install')

def airtime_220_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.2.0/airtime-2.2.0.tar.gz')
    do_run('tar xfz airtime-2.2.0.tar.gz')
    do_sudo('cd /home/martin/airtime-2.2.0/install_full/ubuntu && ./airtime-full-install')

def airtime_221_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.2.1/airtime-2.2.1.tar.gz')  
    do_run('tar xfz airtime-2.2.1.tar.gz')
    do_sudo('cd /home/martin/airtime-2.2.1/install_full/ubuntu && ./airtime-full-install')

def airtime_230_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.3.0/airtime-2.3.0.tar.gz')
    do_run('tar xfz airtime-2.3.0.tar.gz')
    do_sudo('cd /home/martin/airtime-2.3.0/install_full/ubuntu && ./airtime-full-install')

def airtime_231_tar():
    do_run('wget http://downloads.sourceforge.net/project/airtime/2.3.1/airtime-2.3.1-ga.tar.gz')
    do_run('tar xfz airtime-2.3.1-ga.tar.gz')
    do_sudo('cd /home/martin/airtime-2.3.1/install_full/ubuntu && ./airtime-full-install')

def airtime_240_tar():
    do_run('wget http://sourceforge.net/projects/airtime/files/2.4.0/airtime-2.4.0-ga.tar.gz')
    do_run('tar xfz airtime-2.4.0-ga.tar.gz')
    do_sudo('cd /home/martin/airtime-2.4.0/install_full/ubuntu && ./airtime-full-install')

def airtime_241_tar():
    do_run('wget http://sourceforge.net/projects/airtime/files/2.4.1/airtime-2.4.1-ga.tar.gz')
    do_run('tar xfz airtime-2.4.1-ga.tar.gz')
    do_sudo('cd /home/martin/Airtime-airtime-2.4.1-ga/install_full/ubuntu && ./airtime-full-install')

def airtime_latest_deb():
    append('/etc/apt/sources.list', "deb http://apt.sourcefabric.org/ lucid main", use_sudo=True)
    append('/etc/apt/sources.list', "deb http://archive.ubuntu.com/ubuntu/ lucid multiverse", use_sudo=True)
    do_sudo('apt-get update')
    do_sudo('apt-get install -y --force-yes sourcefabric-keyring')
    do_sudo('apt-get install -y postgresql')
    do_sudo('apt-get install -y icecast2')
    do_sudo('apt-get purge -y pulseaudio')
    do_sudo('apt-get install -y --force-yes airtime')
    
def airtime_git_branch(branch="2.5.x"):
    do_sudo('apt-get update')
    do_sudo('apt-get install -y git-core')
    do_run('git clone https://github.com/sourcefabric/Airtime.git ~/airtime_git')
    do_sudo('cd /home/martin/airtime_git && git checkout %s && install_full/ubuntu/airtime-full-install || true' % branch)


#def airtime_200():
#    pass
