"""
# eci.ECI -- A higher-level interface to pyeca.
# Copyright 2001 Eric S. Tiedemann (est@hyperreal.org)
# GPLed

some updates by Janne Halttunen
"""

import pyeca as _pyeca
import types as _types

class ECIError(Exception):
    def __init__(self, what):
        Exception.__init__(self, what)
        self.what = what

    def __str__(self):
        return '<ECIException %s>' % self.what

class ECI:
    """An ECI is and ECA Control Interface object.

    It can be called with ECI command strings (and an optional
    float value) as arguments.  A list or tuple of command
    strings is also accepted and commands can be separated
    by newlines within a single string.

    The value of a command (or of the last command in a sequence)
    if returned as a value of the appropriate Python type
    (possibly None).
    
    On errors, an ECIException is raised that has a `what'
    member with the exception message.  These exceptions also
    stringify prettily.
    """
    
    def __init__(self, *args):
        self.e = apply(_pyeca.ECA_CONTROL_INTERFACE, args)

    def __call__(self, cmd, f=None):
        if f != None:
            self.e.command_float_arg(cmd, f)
        else:
            if type(cmd) == _types.ListType or type(cmd) == _types.TupleType:
                v = None
                for c in cmd:
                    v = self(c)
                return v
            else:
                cmds = cmd.split('\n')
                if len(cmds) > 1:
                    v = None
                    for c in cmds:
                        v = self(c)
                    return v
                else:
                    self.e.command(cmd)
            
        t = self.e.last_type()
        if not t or t == '-':
            return None
        elif t == 'S':
            return self.e.last_string_list()
        elif t == 's':
            return self.e.last_string()
        elif t == 'f':
            return self.e.last_float()
        elif t == 'i':
            return self.e.last_integer()
        elif t == 'li':
            return self.e.last_long_integer()
	elif t == 'e' or self.e.error():
	    raise ECIError, '%s: %s' % (self.e.last_error(), cmd)
        else:
            raise ECIError, "unknown return type '%s'!" % t

if __name__ == '__main__':
    import time, sys

    file = sys.argv[1]
    e = ECI()

    # uncomment to raise an error :)
    #e('foo')
    
    e("""
    cs-add play_chainsetup
    c-add 1st_chain
    ai-add %s
    ao-add /dev/dsp
    cop-add -efl:100
    cop-select 1
    copp-select 1
    cs-connect
    start"""
      % file)

    cutoff_inc = 500.0

    while 1:
        time.sleep(1)
        if e("engine-status") != "running" or e("get-position") > 15:
            break
        e("copp-set", cutoff_inc + e("copp-get"))

    e("""stop
         cs-disconnect""")

    print "Chain operator status: ", e("cop-status")
