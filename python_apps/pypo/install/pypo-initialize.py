import platform
import shutil
from subprocess import Popen, PIPE
import sys
import os
sys.path.append('/usr/lib/airtime/')
from api_clients import api_client
from configobj import ConfigObj

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
        fh.write(api_client.encode_to(buffer))
    fh.write("log_file = \"/var/log/airtime/pypo-liquidsoap/<script>.log\"\n");
    fh.close()
    
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
    
    print " * Installing Liquidsoap binary"
    
    binary_path = os.path.join(PATH_LIQUIDSOAP_BIN, "liquidsoap_%s_%s" % (codename, arch))
    
    try:
        shutil.copy(binary_path, "%s/liquidsoap"%PATH_LIQUIDSOAP_BIN)
    except IOError, e:
        """
        shutil.copy can throw this exception for two reasons. First reason is that it cannot open the source file.
        This is when the liquidsoap file we requested does not exist, and therefore tells the user we don't support
        their OS/System architecture. The second reason for this exception is the shutil.copy cannot open the target file.
        Since this script is being run as root (and we cannot install to a read-only device), this should never happen. So
        it is safe to assume this exception is a result of the first case. 
        
        Note: We cannot simply use os.path.exists before this, since it sometimes gives us "false" incorrectly
        """
        print "Unsupported OS/system architecture."
        print e
        sys.exit(1)
    
            
    #generate liquidsoap config file
    #access the DB and generate liquidsoap.cfg under /etc/airtime/
    ac = api_client.api_client_factory(config)
    ss = ac.get_stream_setting()
        
    if ss is not None:
        generate_liquidsoap_config(ss)
    else:
        print "Unable to connect to the Airtime server."

    #initialize init.d scripts
    p = Popen("update-rc.d airtime-playout defaults >/dev/null 2>&1", shell=True)
    sts = os.waitpid(p.pid, 0)[1]

    #restart airtime-playout
    print "* Waiting for pypo processes to start..."
    """
    if os.environ["liquidsoap_keep_alive"] == "f":
        print " * Restarting any previous Liquidsoap instances"
        p = Popen("/etc/init.d/airtime-playout stop > /dev/null 2>&1", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
    else:
        print " * Keeping any previous Liquidsoap instances running"
        p = Popen("/etc/init.d/airtime-playout pypo-stop  > /dev/null 2>&1", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
    """
    p = Popen("/etc/init.d/airtime-playout start-no-monit  > /dev/null 2>&1", shell=True)
    sts = os.waitpid(p.pid, 0)[1]
    
except Exception, e:
    print e
