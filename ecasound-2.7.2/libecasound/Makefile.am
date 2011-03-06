# ----------------------------------------------------------------------
# File: ecasound/libecasound/Makefile.am
# Description: Ecasound main library
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

EXTRA_DIST = ChangeLog
AUTOMAKE_OPTIONS = foreign
SUBDIRS = plugins

# ----------------------------------------------------------------------
# build targets and compiler options target defines
# ----------------------------------------------------------------------

if ECA_AM_DEBUG_MODE
lib_LTLIBRARIES = libecasound_debug.la
else
lib_LTLIBRARIES = libecasound.la
endif

TESTS = libecasound_tester

check_PROGRAMS = libecasound_tester

# ----------------------------------------------------------------------
# compiler and linker options
# ----------------------------------------------------------------------

# if ECA_AM_ALL_STATIC
# FIXME: always link ecasound-plugins statically
if ECA_AM_DEBUG_MODE
eca_libadd =  	$(top_builddir)/libecasound/plugins/libecasound_plugins_debug.la $(ECA_S_EXTRA_LIBS)
else
eca_libadd =  	$(top_builddir)/libecasound/plugins/libecasound_plugins.la $(ECA_S_EXTRA_LIBS)
endif

eca_ldflags = -version-info @LIBECASOUND_VERSION@:0:@LIBECASOUND_VERSION_AGE@

if ECA_AM_DEBUG_MODE
libecasound_tester_libs =	$(top_builddir)/libecasound/libecasound_debug.la \
				$(top_builddir)/kvutils/libkvutils_debug.la
else
libecasound_tester_libs =  	$(top_builddir)/libecasound/libecasound.la \
				$(top_builddir)/kvutils/libkvutils.la
endif


# note! Automake >= 1.5 will install stripped libraries
#       with "make install-strip". Older versions won't
#       strip libraries even if INSTALL_STRIP_FLAG is set.
#       also libtool 1.3.2 and older have problems 
#       with stripping libraries.

INCLUDES = 	-I$(srcdir) \
		-I$(top_srcdir) \
		-I$(top_srcdir)/kvutils \
		$(ECA_S_EXTRA_CPPFLAGS)

# ----------------------------------------------------------------------
# header files
# ----------------------------------------------------------------------

ecasound_audiofx_include =  \
			audiofx.h \
			audiofx_misc.h \
			audiofx_amplitude.h \
			audiofx_compressor.h \
			audiofx_analysis.h \
			audiofx_envelope_modulation.h \
			audiofx_filter.h \
			audiofx_rcfilter.h \
			audiofx_reverb.h \
			audiofx_timebased.h \
			audiogate.h \
			audiofx_mixing.h \
			audiofx_ladspa.h \
			audio-stamp.h

ecasound_preset_include = \
			preset.h \
			preset_impl.h \
			file-preset.h \
			global-preset.h

ecasound_audioio_include = 	\
			audioio.h \
			audioio-buffered.h \
			audioio-device.h \
			audioio-plugin.h \
			audioio-manager.h \
			audioio-cdr.h \
			audioio-cdr_impl.h \
			audioio-ewf.h \
			audioio-mp3.h \
			audioio-mp3_impl.h \
			audioio-ogg.h \
			audioio-wave.h \
			audioio-raw.h \
			audioio-oss.h \
			audioio-oss_impl.h \
			audioio-null.h \
			audioio-rtnull.h \
			audioio-mikmod.h \
			audioio-loop.h \
			audioio-forked-stream.h \
			audioio-timidity.h \
			audioio-db-server.h \
			audioio-db-server_impl.h \
			audioio-db-buffer.h \
			audioio-db-client.h \
			audioio-proxy.h \
			audioio-typeselect.h \
			audioio-resample.h \
			audioio-reverse.h \
			audioio-flac.h \
			audioio-aac.h \
			audioio-tone.h \
			audioio-seqbase.h \
			audioio-acseq.h \
			audioio-barrier.h

ecasound_midi_include =	\
			midi-server.h \
			midi-client.h \
			midi-parser.h \
			midiio.h \
			midiio-raw.h \
			midiio-aseq.h

ecasound_controller_include = \
			ctrl-source.h \
			midi-cc.h \
			oscillator.h \
			osc-gen.h \
			osc-gen-file.h \
			osc-sine.h \
			linear-envelope.h \
			two-stage-linear-envelope.h \
			stamp-ctrl.h \
	 		generic-linear-envelope.h

ecasound_general_include = 	\
			eca-chain.h \
			eca-chainop.h \
			eca-chainsetup-edit.h \
			eca-error.h \
			eca-logger.h \
			eca-logger-default.h \
			eca-logger-interface.h \
			eca-logger-wellformed.h \
			eca-engine.h \
			eca-engine-driver.h \
			eca-engine_impl.h \
			eca-session.h \
			eca-resources.h \
			resource-file.h \
			layer.h \
			eca-static-object-maps.h \
			eca-object-map.h \
			eca-preset-map.h \
			samplebuffer.h \
			samplebuffer_impl.h \
			samplebuffer_functions.h \
			samplebuffer_iterators.h \
			sample-specs.h \
			sample-ops_impl.h \
			eca-sample-conversion.h \
			eca-version.h \
			eca-object-factory.h \
			eca-chainsetup.h \
			eca-chainsetup_impl.h \
			eca-chainsetup-bufparams.h \
			eca-chainsetup-parser.h \
			eca-chainsetup-position.h \
			eca-control.h \
			eca-control-dump.h \
			eca-control-main.h \
			eca-control-mt.h \
			eca-iamode-parser.h \
			eca-iamode-parser_impl.h \
			eca-audio-position.h \
			eca-fileio.h  \
			eca-fileio-stream.h \
			eca-fileio-mmap.h \
			eca-osc.h \
			dynamic-parameters.h \
			dynamic-object.h \
			eca-object.h \
			eca-operator.h \
			generic-controller.h \
			eca-samplerate-aware.h \
			eca-audio-format.h \
			eca-audio-time.h \
			jack-connections.h

ecasound_extra_include = \
			ladspa.h

ecasound_test_framework_include = \
			eca-test-repository.h \
			eca-test-case.h \
			audiofx_amplitude_test.h \
			audioio_test.h \
			audioio-device_test.h \
			eca-audio-time_test.h \
			eca-chainsetup_test.h \
			eca-chainsetup-parser_test.h \
			eca-control_test.h \
			eca-session_test.h \
			eca-object-factory_test.h \
			eca-sample-conversion_test.h \
			samplebuffer_test.h

# note! also remembers to update install-data-local and 
#       uninstall-data targets
noinst_HEADERS = 	$(ecasound_audiofx_include) \
			$(ecasound_preset_include) \
			$(ecasound_audioio_include) \
			$(ecasound_midi_include) \
		 	$(ecasound_controller_include) \
			$(ecasound_general_include) \
			$(ecasound_extra_include) \
			$(ecasound_test_framework_include)

# ----------------------------------------------------------------------
# source files
# ----------------------------------------------------------------------

ecasound_audiofx_src =  audiofx.cpp \
			audiofx_misc.cpp \
			audiofx_amplitude.cpp \
			audiofx_compressor.cpp \
			audiofx_analysis.cpp \
			audiofx_envelope_modulation.cpp \
			audiofx_filter.cpp \
			audiofx_rcfilter.cpp \
			audiofx_reverb.cpp \
			audiofx_timebased.cpp \
			audiogate.cpp \
			audiofx_mixing.cpp \
			audiofx_ladspa.cpp \
			audio-stamp.cpp

ecasound_preset_src =  	global-preset.cpp \
			preset.cpp \
			file-preset.cpp

ecasound_audioio1_src = audioio-cdr.cpp \
			audioio-ewf.cpp \
			audioio-mp3.cpp \
			audioio-ogg.cpp \
			audioio-wave.cpp \
			audioio.cpp \
			audioio-buffered.cpp \
			audioio-device.cpp \
			audioio-null.cpp \
			audioio-raw.cpp \
			audioio-mikmod.cpp \
			audioio-rtnull.cpp \
			audioio-loop.cpp \
			audioio-forked-stream.cpp \
			audioio-timidity.cpp \
			audioio-db-server.cpp \
			audioio-db-buffer.cpp \
			audioio-db-client.cpp \
			audioio-typeselect.cpp \
			audioio-resample.cpp \
			audioio-reverse.cpp \
			audioio-proxy.cpp \
			audioio-flac.cpp \
			audioio-aac.cpp \
			audioio-tone.cpp \
			audioio-seqbase.cpp \
			audioio-acseq.cpp

if ECA_AM_COMPILE_OSS
ecasound_audioio2_src = audioio-oss.cpp
else
ecasound_audioio2_src = 
endif

if ECA_AM_COMPILE_JACK
jack_connections_src =  jack-connections.cpp
else
jack_connections_src =  
endif

ecasound_midi_src =	\
			midi-server.cpp \
			midi-client.cpp \
			midi-parser.cpp \
			midiio.cpp \
			midiio-raw.cpp \
			midiio-aseq.cpp

ecasound_controller_src = \
			midi-cc.cpp \
			osc-gen.cpp \
			osc-gen-file.cpp \
			osc-sine.cpp \
			linear-envelope.cpp \
			two-stage-linear-envelope.cpp \
			stamp-ctrl.cpp \
			generic-linear-envelope.cpp

ecasound_general_src = 	eca-chain.cpp \
			eca-engine.cpp \
			samplebuffer.cpp \
			samplebuffer_functions.cpp \
			eca-session.cpp \
			eca-resources.cpp \
			resource-file.cpp \
			eca-logger.cpp \
			eca-logger-default.cpp \
			eca-logger-interface.cpp \
			eca-logger-wellformed.cpp \
			layer.cpp \
			samplebuffer_iterators.cpp \
			eca-version.cpp  \
			eca-operator.cpp \
			generic-controller.cpp \
			eca-object-factory.cpp \
			eca-chainsetup.cpp \
			eca-chainsetup-bufparams.cpp \
			eca-chainsetup-parser.cpp \
			eca-chainsetup-position.cpp \
			eca-control.cpp \
			eca-control-base.cpp \
			eca-control-dump.cpp \
			eca-control-main.cpp \
			eca-control-mt.cpp \
			eca-control-objects.cpp \
			eca-iamode-parser.cpp \
			eca-samplerate-aware.cpp \
			eca-audio-position.cpp \
			eca-audio-format.cpp \
			eca-audio-time.cpp \
			eca-fileio-stream.cpp \
			eca-fileio-mmap.cpp \
			eca-osc.cpp \
			eca-static-object-maps.cpp \
			eca-object-map.cpp \
			eca-preset-map.cpp

ecasound_common1_src = 	$(ecasound_audioio1_src) \
			$(ecasound_audioio2_src) \
			$(jack_connections_src) \
			$(ecasound_midi_src) \
			$(ecasound_general_src)

if ECA_AM_DISABLE_EFFECTS
ecasound_common2_src = 	
else
ecasound_common2_src = 	$(ecasound_audiofx_src) \
			$(ecasound_preset_src) \
		      	$(ecasound_controller_src)
endif

libecasound_tester_src = \
			libecasound_tester.cpp \
			eca-test-repository.cpp \
			eca-test-case.cpp

libecasound_la_SOURCES = $(ecasound_common1_src) $(ecasound_common2_src)
libecasound_debug_la_SOURCES = $(ecasound_common1_src) $(ecasound_common2_src)
libecasound_la_LDFLAGS = -export-dynamic $(eca_ldflags) -static
libecasound_la_LIBADD = $(eca_libadd)
libecasound_debug_la_LDFLAGS = $(libecasound_la_LDFLAGS)
libecasound_debug_la_LIBADD = $(libecasound_la_LIBADD)

libecasound_tester_SOURCES = $(libecasound_tester_src)
#libecasound_tester_CFLAGS =  $(AM_CFLAGS)
libecasound_tester_LDADD = $(libecasound_tester_libs)

# Pass pkgdatadir to CPPFLAGS
AM_CPPFLAGS += "-DECA_PKGDATADIR=\"${pkgdatadir}\""

# ---------------------------------------------------------------------
# Install targets - note! we don't install $ecasound_extra_include
#                         nor $ecasound_test_framework_include
install-data-local:
	$(INSTALL) -d $(DESTDIR)$(includedir)/libecasound
	cd $(srcdir) ; \
	$(INSTALL_DATA) \
		$(ecasound_audiofx_include) \
		$(ecasound_preset_include) \
		$(ecasound_audioio_include) \
		$(ecasound_controller_include) \
		$(ecasound_midi_include) \
		$(ecasound_general_include) \
		$(DESTDIR)$(includedir)/libecasound
	$(INSTALL) -d $(DESTDIR)$(bindir)
	$(INSTALL_SCRIPT) libecasound-config $(DESTDIR)$(bindir)/libecasound-config

# ---------------------------------------------------------------------
# Uninstall targets
uninstall-local:
	cd $(DESTDIR)$(includedir)/libecasound && \
	rm -f   $(ecasound_audiofx_include) \
		$(ecasound_preset_include) \
		$(ecasound_audioio_include) \
		$(ecasound_controller_include) \
		$(ecasound_midi_include) \
		$(ecasound_general_include)
	rmdir $(DESTDIR)$(includedir)/libecasound || echo "Skipping"
	rm -f 	$(DESTDIR)$(bindir)/libecasound-config
	rmdir $(DESTDIR)$(bindir) || echo "Skipping"
