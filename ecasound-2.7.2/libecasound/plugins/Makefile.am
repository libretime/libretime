# ----------------------------------------------------------------------
# File: ecasound/libecasound/plugins/Makefile.am
# Description: Audio I/O implementations that depend on external 
#              libraries
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

AUTOMAKE_OPTIONS = foreign

# ----------------------------------------------------------------------
# defines
# ----------------------------------------------------------------------

#common_clags = -DECA_ENABLE_AUDIOIO_PLUGINS

all_arts_src = audioio_arts.cpp
if ECA_AM_COMPILE_ARTS
arts_src    = $(all_arts_src)
arts_target = libaudioio_arts.la
else
arts_target =
endif

all_alsa_src    = audioio_alsa.cpp audioio_alsa_named.cpp
if ECA_AM_COMPILE_ALSA
alsa_src    = $(all_alsa_src)
alsa_target = libaudioio_alsa.la \
	      libaudioio_alsa_named.la
else
alsa_target = 
endif

all_af_src = audioio_af.cpp
if ECA_AM_COMPILE_AUDIOFILE
af_src    = $(all_af_src)
af_target = libaudioio_af.la
else
af_target = 
endif

all_sndfile_src = audioio_sndfile.cpp
if ECA_AM_COMPILE_SNDFILE
sndfile_src    = $(all_sndfile_src)
sndfile_target = libaudioio_sndfile.la
else
sndfile_target = 
endif

all_jack_src = audioio_jack.cpp audioio_jack_manager.cpp
if ECA_AM_COMPILE_JACK
jack_src    = $(all_jack_src)
jack_target = libaudioio_jack.la
else
jack_target =
endif

INCLUDES =	-I$(srcdir) \
		-I$(top_srcdir) \
		-I$(top_srcdir)/libecasound \
		-I$(top_srcdir)/kvutils \
		$(ECA_S_EXTRA_CPPFLAGS)

#libdir = 	$(exec_prefix)/lib/libecasound@LIBECASOUND_VERSION@-plugins

# ----------------------------------------------------------------------
# header files
# ----------------------------------------------------------------------

plugin_includes = 	\
			audioio_af.h \
			audioio_alsa.h \
			audioio_alsa_named.h \
			audioio_arts.h \
			audioio_jack.h \
			audioio_jack_manager.h \
			audioio_sndfile.h

noinst_HEADERS =   	$(plugin_includes)

# ----------------------------------------------------------------------
# build targets and compiler options target defines
# ----------------------------------------------------------------------

if ECA_AM_DEBUG_MODE
noinst_LTLIBRARIES = libecasound_plugins_debug.la
else
noinst_LTLIBRARIES = libecasound_plugins.la
endif

plugin_cond_sources = 	$(af_src) \
                        $(alsa_src) \
			$(arts_src) \
			$(jack_src) \
			$(sndfile_src)
plugin_all_sources = 	$(all_af_src) \
			$(all_alsa_src) \
			$(all_arts_src) \
			$(all_jack_src) \
			$(all_sndfile_src)

# ----------------------------------------------------------------------
# source files
# ----------------------------------------------------------------------

libecasound_plugins_la_SOURCES          = audioio_dummy.cpp $(plugin_cond_sources)
EXTRA_libecasound_plugins_la_SOURCES    = $(plugin_all_sources)
libecasound_plugins_la_LIBADD		= $(ECA_S_EXTRA_LIBS)
libecasound_plugins_la_LDFLAGS 		= -static

libecasound_plugins_debug_la_SOURCES	= $(libecasound_plugins_la_SOURCES)
EXTRA_libecasound_plugins_debug_la_SOURCES = $(EXTRA_libecasound_plugins_la_SOURCES)
libecasound_plugins_debug_la_LIBADD	= $(libecasound_plugins_la_LIBADD)
libecasound_plugins_debug_la_LDFLAGS	= $(libecasound_plugins_la_LDFLAGS)

# ----------------------------------------------------------------------
# separate rules for compiling non-libtool plugins
# ----------------------------------------------------------------------
