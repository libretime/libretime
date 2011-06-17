import os
import sys
import time

def remove_user(username):
    os.system("killall -u %s 1>/dev/null 2>&1" % username)
    
    #allow all process to be completely closed before we attempt to delete user
    print "Waiting for processes to close..."
    time.sleep(3)
    
    os.system("deluser --remove-home " + username + " 1>/dev/null 2>&1")

if __name__ == "__main__":
    if os.geteuid() != 0:
        print "Please run this as root."
        sys.exit(1)
        
    remove_user("pypo")
