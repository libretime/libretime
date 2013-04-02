import platform
import shutil
from subprocess import Popen, PIPE
import subprocess
import sys
import os
sys.path.append('/usr/lib/airtime/')
from api_clients import api_client
from configobj import ConfigObj

import logging

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

"""
    This function returns the codename of the host OS by querying lsb_release.
    If lsb_release does not exist, or an exception occurs the codename returned
    is 'unknown'
"""
def get_os_codename():
    try:
        p = Popen("which lsb_release > /dev/null", shell=True)
        sts = os.waitpid(p.pid, 0)[1]

        if (sts == 0):
            #lsb_release is available on this system. Let's get the os codename
            p = Popen("lsb_release -sc", shell=True, stdout=PIPE)
            codename = p.communicate()[0].strip('\r\n')

            p = Popen("lsb_release -sd", shell=True, stdout=PIPE)
            fullname = p.communicate()[0].strip('\r\n')

            return (codename, fullname)
    except Exception, e:
        pass

    return ("unknown", "unknown")

PATH_INI_FILE = '/etc/airtime/pypo.cfg'
PATH_LIQUIDSOAP_BIN = '/usr/lib/airtime/pypo/bin/liquidsoap_bin'

#any debian/ubuntu codename in this et will automatically use the natty liquidsoap binary
arch_map = dict({"32bit":"i386", "64bit":"amd64"})

# load config file
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

try:
    #select appropriate liquidsoap file for given system os/architecture
    architecture = platform.architecture()[0]
    arch = arch_map[architecture]

    print "* Detecting OS: ...",
    (codename, fullname) = get_os_codename()
    print " Found %s (%s) on %s architecture" % (fullname, codename, arch)

    print " * Creating symlink to Liquidsoap binary"

    p = Popen("which liquidsoap", shell=True, stdout=PIPE)
    liq_path = p.communicate()[0].strip()
    symlink_path = "/usr/bin/airtime-liquidsoap"

    if p.returncode == 0:
        try:
            os.unlink(symlink_path)
        except Exception:
            #liq_path DNE, which is OK.
            pass

        os.symlink(liq_path, symlink_path)
    else:
        print " * Liquidsoap binary not found!"
        sys.exit(1)

    #initialize init.d scripts
    subprocess.call("update-rc.d airtime-playout defaults >/dev/null 2>&1", shell=True)
    subprocess.call("update-rc.d airtime-liquidsoap defaults >/dev/null 2>&1", shell=True)

    #clear out an previous pypo cache
    print "* Clearing previous pypo cache"
    subprocess.call("rm -rf /var/tmp/airtime/pypo/cache/scheduler/* >/dev/null 2>&1", shell=True)

    if "airtime_service_start" in os.environ and os.environ["airtime_service_start"] == "t":
        print "* Waiting for pypo processes to start..."
        subprocess.call("invoke-rc.d airtime-playout start > /dev/null 2>&1", shell=True)
        subprocess.call("invoke-rc.d airtime-liquidsoap start > /dev/null 2>&1", shell=True)

except Exception, e:
    print e
