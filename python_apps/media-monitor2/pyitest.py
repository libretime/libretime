#!/usr/bin/python
import sys
import os
import getopt
import pyinotify
import pprint

# a little script to test out pyinotify events

class AT(pyinotify.ProcessEvent):
    def process_default(self, event):
        pprint.pprint(event)

def main():
    optlist, arguments  = getopt.getopt(sys.argv[1:], '', ["dir="])
    ldir = ""
    for k,v in optlist:
        if k == '--dir':
            ldir = v
            break
    if not os.path.exists(ldir):
        print("can't pyinotify dir: '%s'. it don't exist" % ldir)
        sys.exit(0)
    wm = pyinotify.WatchManager()
    notifier = pyinotify.Notifier(wm)
    print("Watching: '%s'" % ldir)
    wm.add_watch(ldir, pyinotify.ALL_EVENTS, auto_add=True, rec=True, proc_fun=AT())
    notifier.loop()

if __name__ == '__main__': main()
