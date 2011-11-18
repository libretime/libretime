import platform
import shutil
from subprocess import Popen, PIPE
import sys
import os
sys.path.append('/usr/lib/airtime/api_clients/')
import api_client
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

"""
def is_natty():
    try:
        f = open('/etc/lsb-release')
    except IOError as e:
        #File doesn't exist, so we're not even dealing with Ubuntu
        return False

    for line in f:
        split = line.split("=")
        split[0] = split[0].strip(" \r\n")
        split[1] = split[1].strip(" \r\n")
        if split[0] == "DISTRIB_CODENAME" and (split[1] == "natty" or split[1] == "oneiric"):
            return True
    return False
"""

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
        
def get_current_script_dir():
    current_script_dir = os.path.realpath(__file__)
    index = current_script_dir.rindex('/')
    return current_script_dir[0:index]
    
def generate_liquidsoap_config(ss):
    data = ss['msg']
    fh = open('/etc/airtime/liquidsoap.cfg', 'w')
    fh.write("################################################\n")
    fh.write("# THIS FILE IS AUTO GENERATED. DO NOT CHANGE!! #\n")
    fh.write("################################################\n")
    for d in data:
        buffer = d[u'keyname'] + " = "
        if(d[u'type'] == 'string'):
            temp = d[u'value']
            if(temp == ""):
                temp = ""
            buffer += "\"" + temp + "\""
        else:
            temp = d[u'value']
            if(temp == ""):
                temp = "0"
            buffer += temp
        buffer += "\n"
        fh.write(buffer)
    fh.write("log_file = \"/var/log/airtime/pypo-liquidsoap/<script>.log\"\n");
    fh.close()
    
PATH_INI_FILE = '/etc/airtime/pypo.cfg'

#any debian/ubuntu codename in this et will automatically use the natty liquidsoap binary
codenames_for_natty_binary = set(["natty", "oneiric", "precise", "squeeze"])

# load config file
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)
    
try:
    current_script_dir = get_current_script_dir()
    
    #select appropriate liquidsoap file for given system os/architecture
    architecture = platform.architecture()[0]
    #natty = is_natty()
        
    print "* Detecting OS: ...",
    (codename, fullname) = get_os_codename()
    print " Found %s" % fullname
    natty = codename in codenames_for_natty_binary
        
    if architecture == '64bit' and natty:
        print " * Installing 64-bit liquidsoap binary (Natty)"
        shutil.copy("%s/../liquidsoap_bin/liquidsoap-natty-amd64"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
    elif architecture == '32bit' and natty:
        print " * Installing 32-bit liquidsoap binary (Natty)"
        shutil.copy("%s/../liquidsoap_bin/liquidsoap-natty-i386"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
    elif architecture == '64bit' and not natty:
        print " * Installing 64-bit liquidsoap binary"
        shutil.copy("%s/../liquidsoap_bin/liquidsoap-amd64"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
    elif architecture == '32bit' and not natty:
        print " * Installing 32-bit liquidsoap binary"
        shutil.copy("%s/../liquidsoap_bin/liquidsoap-i386"%current_script_dir, "%s/../liquidsoap_bin/liquidsoap"%current_script_dir)
    else:
        print "Unknown system architecture."
        sys.exit(1)
    
    #initialize init.d scripts
    p = Popen("update-rc.d airtime-playout defaults >/dev/null 2>&1", shell=True)
    sts = os.waitpid(p.pid, 0)[1]

    #generate liquidsoap config file
    #access the DB and generate liquidsoap.cfg under /etc/airtime/
    api_client = api_client.api_client_factory(config)
    ss = api_client.get_stream_setting()
        
    if ss is not None:
        generate_liquidsoap_config(ss)
    else:
        print "Unable to connect to the Airtime server."

    #restart airtime-playout   
    print "* Waiting for pypo processes to start..."
    p = Popen("/etc/init.d/airtime-playout stop", shell=True)
    sts = os.waitpid(p.pid, 0)[1]
    p = Popen("/etc/init.d/airtime-playout start-no-monit", shell=True)
    sts = os.waitpid(p.pid, 0)[1]
    
except Exception, e:
    print e
