#Airtime Liquidsoap compile infrastructure
#author martin.konecny@sourcefabric.org

#Documentation for this page:


import os
import time
import sys
from fabric.api import *
from fabric.contrib.files import comment, sed, append

from ConfigParser import ConfigParser

from xml.dom.minidom import parse
from xml.dom.minidom import Node
from xml.dom.minidom import Element

env.user = 'martin'
env.password = 'test'
env.hosts = []
env.host_string

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

def shutdown():
    do_sudo("poweroff")
    time.sleep(30)

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


def create_fresh_os(vm_name, debian=False):

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

    if debian:
        append('/etc/apt/sources.list', "deb http://backports.debian.org/debian-backports squeeze-backports main", use_sudo=True)

def ubuntu_lucid_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_32')

def ubuntu_lucid_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_10.04_64')

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

def ubuntu_precise_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.04_64')

def ubuntu_precise_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.04_32')

def ubuntu_quantal_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.10_32')

def ubuntu_quantal_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Ubuntu_12.10_64')

def debian_squeeze_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_32', debian=True)

def debian_squeeze_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_64', debian=True)

def debian_wheezy_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Wheezy_32')

def debian_wheezy_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Wheezy_64')


def compile_liquidsoap(filename="liquidsoap"):

    config = ConfigParser()
    config.readfp(open('fab_liquidsoap_compile.cfg'))
    url = config.get('main', 'liquidsoap_tar_url')

    print "Will get liquidsoap from " + url

    do_sudo('apt-get update')
    do_sudo('apt-get upgrade -y --force-yes')
    do_sudo('''apt-get install -y --force-yes ocaml-findlib libao-ocaml-dev libportaudio-ocaml-dev \
libmad-ocaml-dev libtaglib-ocaml-dev libalsa-ocaml-dev libtaglib-ocaml-dev libvorbis-ocaml-dev \
libspeex-dev libspeexdsp-dev speex libladspa-ocaml-dev festival festival-dev \
libsamplerate-dev libxmlplaylist-ocaml-dev libflac-dev \
libxml-dom-perl libxml-dom-xpath-perl patch autoconf libmp3lame-dev \
libcamomile-ocaml-dev libcamlimages-ocaml-dev libtool libpulse-dev libjack-dev \
camlidl libfaad-dev libpcre-ocaml-dev''')
#libxmlrpc-light-ocaml-dev
    root = '/home/martin/src'
    do_run('mkdir -p %s' % root)

    tmpPath = do_local("mktemp", capture=True)
    do_run('wget %s -O %s' % (url, tmpPath))
    do_run('mv %s %s/liquidsoap.tar.gz' % (tmpPath, root))
    do_run('cd %s && tar xzf liquidsoap.tar.gz' % root)

    #do_run('cd %s/liquidsoap-1.0.1-full && cp PACKAGES.minimal PACKAGES' % root)
    #sed('%s/liquidsoap-1.0.1-full/PACKAGES' % root, '#ocaml-portaudio', 'ocaml-portaudio')
    #sed('%s/liquidsoap-1.0.1-full/PACKAGES' % root, '#ocaml-alsa', 'ocaml-alsa')
    #sed('%s/liquidsoap-1.0.1-full/PACKAGES' % root, '#ocaml-pulseaudio', 'ocaml-pulseaudio')
    #sed('%s/liquidsoap-1.0.1-full/PACKAGES' % root, '#ocaml-faad', 'ocaml-faad')
    #do_run('cd %s/liquidsoap-1.0.1-full && ./bootstrap' % root)
    #do_run('cd %s/liquidsoap-1.0.1-full && ./configure' % root)
    #do_run('cd %s/liquidsoap-1.0.1-full && make' % root)
    #get('%s/liquidsoap-1.0.1-full/liquidsoap-1.0.1/src/liquidsoap' % root, filename)

    do_run('cd %s/liquidsoap && cp PACKAGES.minimal PACKAGES' % root)
    sed('%s/liquidsoap/PACKAGES' % root, '#ocaml-portaudio', 'ocaml-portaudio')
    sed('%s/liquidsoap/PACKAGES' % root, '#ocaml-alsa', 'ocaml-alsa')
    sed('%s/liquidsoap/PACKAGES' % root, '#ocaml-pulseaudio', 'ocaml-pulseaudio')
    sed('%s/liquidsoap/PACKAGES' % root, '#ocaml-faad', 'ocaml-faad')
    do_run('cd %s/liquidsoap && ./bootstrap' % root)
    do_run('cd %s/liquidsoap && ./configure' % root)
    do_run('cd %s/liquidsoap && make clean' % root)
    do_run('cd %s/liquidsoap && make' % root)
    do_sudo('chmod 755 %s/liquidsoap/liquidsoap/src/liquidsoap' % root)
    get('%s/liquidsoap/liquidsoap/src/liquidsoap' % root, filename)
