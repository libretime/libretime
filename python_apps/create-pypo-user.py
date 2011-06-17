import os
import sys
from subprocess import Popen, PIPE, STDOUT

def create_user(username):
  print "Checking for user "+username
  
  p = Popen('id '+username, shell=True, stdin=PIPE, stdout=PIPE, stderr=STDOUT, close_fds=True)
  output = p.stdout.read()
  
  if (output[0:3] != "uid"):
    # Make the pypo user
    print "Creating user "+username
    os.system("adduser --system --quiet --group --shell /bin/bash "+username)
    
    #set pypo password
    p = os.popen('/usr/bin/passwd pypo 1>/dev/null 2>&1', 'w')
    p.write('pypo\n')
    p.write('pypo\n')
    p.close()
  else:
    print "User already exists."
  #add pypo to audio group
  os.system("adduser " + username + " audio 1>/dev/null 2>&1")
  #add pypo to www-data group
  os.system("adduser " + username + " www-data 1>/dev/null 2>&1")


if __name__ == "__main__":
    if os.geteuid() != 0:
        print "Please run this as root."
        sys.exit(1)
    
    create_user("pypo")
