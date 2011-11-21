#Airtime Liquidsoap compile infrastructure
#author martin.konecny@sourcefabric.org

#Documentation for this page:


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
   

def create_fresh_os(vm_name, debian=False):
    
    vm_vdi_file = '%s.vdi'%vm_name
    vm_xml_file = '%s.xml'%vm_name
    vdi_dir = os.path.expanduser('~/tmp/vms/%s'%vm_name)
    vdi_snapshot_dir = os.path.expanduser('~/tmp/vms/%s/Snapshots'%vm_name)
    xml_dir = os.path.expanduser('~/.VirtualBox')
    vm_xml_path = os.path.join(xml_dir, vm_xml_file)
            
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
    
    if debian:
        append('/etc/apt/sources.list', "deb http://www.debian-multimedia.org squeeze main non-free", use_sudo=True)
    
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
        
def debian_squeeze_32(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_32', debian=True)
        
def debian_squeeze_64(fresh_os=True):
    if (fresh_os):
        create_fresh_os('Debian_Squeeze_64', debian=True)


def compile_liquidsoap(filename="liquidsoap"):

    sudo('apt-get update')
    sudo('apt-get upgrade -y --force-yes')
    sudo('sudo apt-get install -y --force-yes ocaml-findlib libao-ocaml-dev libportaudio-ocaml-dev ' + \
        'libmad-ocaml-dev libtaglib-ocaml-dev libalsa-ocaml-dev libtaglib-ocaml-dev libvorbis-ocaml-dev ' + \
        'libspeex-dev libspeexdsp-dev speex libladspa-ocaml-dev festival festival-dev ' + \
        'libsamplerate-dev libxmlplaylist-ocaml-dev libxmlrpc-light-ocaml-dev libflac-dev ' + \
        'libxml-dom-perl libxml-dom-xpath-perl icecast2 patch autoconf libmp3lame-dev ' + \
        'libcamomile-ocaml-dev libcamlimages-ocaml-dev libtool libpulse-dev libjack-dev camlidl')
        
        #libocamlcvs-ocaml-dev

    root = '/home/martin/src'
    run('mkdir -p %s' % root)
    
    tmpPath = local("mktemp", capture=True)
    run('wget %s -O %s' % ('https://downloads.sourceforge.net/project/savonet/liquidsoap/1.0.0/liquidsoap-1.0.0-full.tar.bz2', tmpPath))
    run('mv %s %s/liquidsoap-1.0.0-full.tar.bz2' % (tmpPath, root))
    run('cd %s &&  bunzip2 liquidsoap-1.0.0-full.tar.bz2 && tar xf liquidsoap-1.0.0-full.tar' % root)
    run('cd %s/liquidsoap-1.0.0-full && cp PACKAGES.minimal PACKAGES' % root)
    run('cd %s/liquidsoap-1.0.0-full && ./configure' % root)
    run('cd %s/liquidsoap-1.0.0-full && make' % root)
    get('%s/liquidsoap-1.0.0-full/liquidsoap-1.0.0/src/liquidsoap' % root, filename)
