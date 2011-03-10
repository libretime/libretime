"""Wrapper module which loads pyecasound 
(python module for Ecasound Control Interface).

To use C version of pyecasound, you have to enable global share of symbols.

Quote from python docs:

--cut--

    setdlopenflags(n)
    
    Set the flags used by the interpreter for dlopen() calls, 
    such as when the interpreter loads extension modules. 
    Among other things, this will enable a lazy resolving of symbols 
    when importing a module, if called as sys.setdlopenflags(0). 
    To share symbols across extension modules, call as 
    sys.setdlopenflags(dl.RTLD_NOW | dl.RTLD_GLOBAL). 
    Symbolic names for the flag modules can be either found in the dl module, 
    or in the DLFCN module. If DLFCN is not available, 
    it can be generated from /usr/include/dlfcn.h using the h2py script. 
    Availability: Unix. New in version 2.2.
    
--cut--
    

Otherwise falling back to native python version (possibly slower float-handling).
"""

import sys

if hasattr(sys, 'version_info'): # attribute available from python 2.0
    if sys.version_info[1] >=2:
	try:
	    import dl 
	    sys.setdlopenflags(dl.RTLD_LAZY|dl.RTLD_GLOBAL)	
	    
	    from pyecasound import *
	except:
	    pass
	
	try:
	    import DLFCN
	    sys.setdlopenflags(DLFCN.RTLD_LAZY|DLFCN.RTLD_GLOBAL)	
	    
	    from pyecasound import *
	except:
	    from ecacontrol import *		
    else:
	from ecacontrol import *	
else:
    from ecacontrol import *

