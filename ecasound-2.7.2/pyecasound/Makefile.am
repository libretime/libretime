# ----------------------------------------------------------------------
# File: ecasound/pyecasound/Makefile.am
# Description: Python implmentation of the Ecasound Control Interface
# License: LGPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

AUTOMAKE_OPTIONS 		= foreign
EXTRA_DIST 			= eci.py pyeca.py ecacontrol.py \
				  test1_stresstest.py \
				  test2_stresstest.py

# ----------------------------------------------------------------------
# defines
# ----------------------------------------------------------------------

if ECA_AM_DEBUG_MODE
ecasoundc_libs 			= ${top_builddir}/libecasoundc/libecasoundc_debug.la
else
ecasoundc_libs 			= ${top_builddir}/libecasoundc/libecasoundc.la
endif

if ECA_AM_PYECASOUND_INSTALL
pyecasound_install_list1 = 	$(srcdir)/eci.py \
				$(srcdir)/pyeca.py \
				$(srcdir)/ecacontrol.py
pyecasound_uninstall_list1 =    $(DESTDIR)$(ECA_S_PYTHON_DLMODULES)/eci.py \
				$(DESTDIR)$(ECA_S_PYTHON_DLMODULES)/pyeca.py \
				$(DESTDIR)$(ECA_S_PYTHON_DLMODULES)/ecacontrol.py
endif
if ECA_AM_PYECASOUND_CEXT
pyecasound_install_list2 = 	pyecasound.so
pyecasound_uninstall_list2 =   	$(DESTDIR)$(ECA_S_PYTHON_DLMODULES)/pyecasound.so
endif

INCLUDES 			= -I$(srcdir) \
				-I$(top_srcdir)/libecasoundc \
				-I$(ECA_S_PYTHON_INCLUDES)

if ECA_AM_PYECASOUND_INSTALL
TESTS				= test1_stresstest.py \
				  test2_stresstest.py
endif

# ----------------------------------------------------------------------
# header files
# ----------------------------------------------------------------------

pyecasound_includes 		= pyecasound.h
noinst_HEADERS 			= $(pyecasound_includes)

# ----------------------------------------------------------------------
# source files
# ----------------------------------------------------------------------

if ECA_AM_PYECASOUND_CEXT
noinst_LTLIBRARIES 		= libpyecasound.la
endif

libpyecasound_la_SOURCES	= pyecasound.c

# libtool options to build for dlopen
libpyecasound_la_LDFLAGS        = -avoid-version -export-dynamic

if ECA_AM_PYECASOUND_CEXT
all: pyecasound.so

pyecasound.so: pyecasound.lo
	$(LIBTOOL) --mode=link $(CCLD) $(AM_CFLAGS) $(CFLAGS) ${libpyecasound_la_LDFLAGS} -shared -nostartfiles -o pyecasound.so pyecasound.lo $(ecasoundc_libs)
endif

# ----------------------------------------------------------------------
# hooks
# ----------------------------------------------------------------------

if ECA_AM_PYECASOUND_CEXT
CLEANFILES=pyecasound.so
endif

if ECA_AM_PYECASOUND_INSTALL
install-exec-hook: $(pyecasound_install_list1) $(pyecasound_install_list2)
	$(INSTALL) -d $(DESTDIR)$(ECA_S_PYTHON_DLMODULES)
	$(INSTALL) $(pyecasound_install_list1) $(pyecasound_install_list2) $(DESTDIR)$(ECA_S_PYTHON_DLMODULES)
else
install-exec-hook:
endif

if ECA_AM_PYECASOUND_INSTALL
uninstall-local:
	rm -f $(pyecasound_uninstall_list1) $(pyecasound_uninstall_list2)
	rmdir $(DESTDIR)$(ECA_S_PYTHON_DLMODULES) || echo "Skipping non-empty directory"
else
uninstall-local:
endif
