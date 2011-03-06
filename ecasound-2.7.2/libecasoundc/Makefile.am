# ----------------------------------------------------------------------
# File: ecasound/libecasoundc/Makefile.am
# Description: Ecasound Control Interface C implementation.
# License: LGPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

EXTRA_DIST = ChangeLog
AUTOMAKE_OPTIONS = foreign

# ----------------------------------------------------------------------
# defines
# ----------------------------------------------------------------------

# !!!
# remember to update eca-version.cpp
if ECA_AM_DEBUG_MODE
eca_ldflags = -version-info @LIBECASOUNDC_VERSION@:0:@LIBECASOUNDC_VERSION_AGE@ -static
else
eca_ldflags = -s -version-info @LIBECASOUNDC_VERSION@:0:@LIBECASOUNDC_VERSION_AGE@ -static
endif

INCLUDES = -I$(srcdir)
# -I$(top_srcdir)/libecasound

# ----------------------------------------------------------------------
# build targets and compiler options target defines
# ----------------------------------------------------------------------

if ECA_AM_DEBUG_MODE
lib_LTLIBRARIES = libecasoundc_debug.la
else
lib_LTLIBRARIES = libecasoundc.la
endif

TESTS = libecasoundc_tester ecicpp_tester

check_PROGRAMS = $(TESTS)

# ----------------------------------------------------------------------
# header files
# ----------------------------------------------------------------------

ecasoundc_includes =	ecasoundc.h \
			eca-control-interface.h
noinst_HEADERS = $(ecasoundc_includes)

# ----------------------------------------------------------------------
# source files
# ----------------------------------------------------------------------

# standalone implementation (forks and execs the ecasound binary)
ecasoundc_src =	ecasoundc_sa.c \
		eca-control-interface.cpp

# ----------------------------------------------------------------------
# library definitons
# ----------------------------------------------------------------------

libecasoundc_la_SOURCES = $(ecasoundc_src)
libecasoundc_la_CFLAGS = -prefer-pic
libecasoundc_la_CXXFLAGS = -prefer-pic
libecasoundc_la_LIBADD =
libecasoundc_la_LDFLAGS = -export-dynamic $(eca_ldflags)
libecasoundc_debug_la_SOURCES = $(libecasoundc_la_SOURCES)
libecasoundc_debug_la_CFLAGS = $(libecasoundc_la_CFLAGS)
libecasoundc_debug_la_CXXFLAGS = $(libecasoundc_la_CXXFLAGS)
libecasoundc_debug_la_LIBADD = $(libecasoundc_la_LIBADD)
libecasoundc_debug_la_LDFLAGS = $(libecasoundc_la_LDFLAGS)

libecasoundc_tester_SOURCES = libecasoundc_tester.c
libecasoundc_tester_LDADD = $(lib_LTLIBRARIES)

ecicpp_tester_SOURCES = ecicpp_tester.cpp
ecicpp_tester_LDADD = $(lib_LTLIBRARIES)

AM_CPPFLAGS += "-DTEST_TOP_BUILDDIR=\"$(top_builddir)\""

# ---------------------------------------------------------------------
# Install targets
install-data-local:
	$(INSTALL) -d $(DESTDIR)$(includedir)/libecasoundc
	cd $(srcdir) ; \
	$(INSTALL_DATA) $(ecasoundc_includes) \
		$(DESTDIR)$(includedir)/libecasoundc
	$(INSTALL) -d $(DESTDIR)$(bindir)
	$(INSTALL_SCRIPT) libecasoundc-config $(DESTDIR)$(bindir)/

# ---------------------------------------------------------------------
# Uninstall targets
uninstall-local:
	cd $(DESTDIR)$(includedir)/libecasoundc && \
	rm -f   $(ecasoundc_includes)
	rmdir $(DESTDIR)$(includedir)/libecasoundc || echo "Skipping non-empty directory"
	rm -f 	$(DESTDIR)$(bindir)/libecasoundc-config
	rmdir $(DESTDIR)$(bindir) || echo "Skipping non-empty directory"
