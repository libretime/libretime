# ----------------------------------------------------------------------
# File: ecasound/ecatools/Makefile.am
# Description: Utility applications built on Ecasound libraries
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

EXTRA_DIST = ecasound.el ecamonitor

if ECA_AM_DEBUG_MODE
libkvutils_path = $(top_builddir)/kvutils/libkvutils_debug.la
libecasoundc_path = $(top_builddir)/libecasoundc/libecasoundc_debug.la
else
libkvutils_path = $(top_builddir)/kvutils/libkvutils.la
libecasoundc_path = $(top_builddir)/libecasoundc/libecasoundc.la
endif

if ECA_AM_USE_NCURSES
ncurses_library = -lncurses
endif

if ECA_AM_USE_TERMCAP
termcap_library = -ltermcap
endif

INCLUDES = -I$(srcdir) -I$(top_srcdir) -I$(top_srcdir)/kvutils -I$(top_srcdir)/libecasound -I$(top_srcdir)/libecasoundc

bin_SCRIPTS = ecamonitor

if ECA_AM_DEBUG_MODE
bin_PROGRAMS = 	ecaconvert_debug \
		ecafixdc_debug \
	        ecalength_debug \
		ecanormalize_debug \
		ecaplay_debug \
		ecasignalview_debug
else
bin_PROGRAMS = 	ecaconvert \
		ecafixdc \
	        ecalength \
		ecanormalize \
		ecaplay \
		ecasignalview
endif

# --

noinst_HEADERS = ecicpp_helpers.h

ecaconvert_SOURCES = ecaconvert.cpp ecicpp_helpers.cpp
ecaconvert_LDADD = $(libecasoundc_path) $(libkvutils_path)

ecafixdc_SOURCES = ecafixdc.cpp ecicpp_helpers.cpp
ecafixdc_LDADD = $(libecasoundc_path) $(libkvutils_path)

ecalength_SOURCES = ecalength.c
ecalength_LDADD = $(libecasoundc_path)

ecanormalize_SOURCES = ecanormalize.cpp ecicpp_helpers.cpp
ecanormalize_LDADD = $(libecasoundc_path) $(libkvutils_path)

ecaplay_SOURCES = ecaplay.c
ecaplay_LDADD = $(libecasoundc_path)

ecasignalview_SOURCES = ecasignalview.cpp ecicpp_helpers.cpp
ecasignalview_LDADD =  $(libecasoundc_path) $(libkvutils_path) $(termcap_library) $(ncurses_library)

# --

ecaconvert_debug_SOURCES = $(ecaconvert_SOURCES)
ecaconvert_debug_LDADD = $(ecaconvert_LDADD)

ecafixdc_debug_SOURCES = $(ecafixdc_SOURCES)
ecafixdc_debug_LDADD = $(ecafixdc_LDADD)

ecalength_debug_SOURCES = $(ecalength_SOURCES)
ecalength_debug_LDADD = $(ecalength_LDADD)

ecanormalize_debug_SOURCES = $(ecanormalize_SOURCES)
ecanormalize_debug_LDADD = $(ecanormalize_LDADD)
ecanormalize_debug_LDFLAGS = $(ecanormalize_LDFLAGS)

ecaplay_debug_SOURCES = $(ecaplay_SOURCES)
ecaplay_debug_LDADD = $(ecaplay_LDADD)

ecasignalview_debug_SOURCES = $(ecasignalview_SOURCES)
ecasignalview_debug_LDADD = $(ecasignalview_LDADD)

# --

# special targets with dependency tracking
ecainstall:
	$(MAKE) -C $(srcdir)/../libecasound -q 2>/dev/null || make -C $(srcdir)/../libecasound
	$(MAKE) -C $(srcdir)/../kvutils -q 2>/dev/null || make -C $(srcdir)/../kvutils
	$(MAKE) install
