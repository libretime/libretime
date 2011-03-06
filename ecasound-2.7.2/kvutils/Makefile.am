# ----------------------------------------------------------------------
# File: ecasound/kvutils/Makefile.am
# Description: Misc. utility routines (library created by Kai Vehmanen)
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

AUTOMAKE_OPTIONS = foreign

# ---
# Makefile.am for building libkvutils.a
# ---

if ECA_AM_DEBUG_MODE
lib_LTLIBRARIES = libkvutils_debug.la
else
lib_LTLIBRARIES = libkvutils.la
endif

TESTS = libkvutils_tester
check_PROGRAMS = $(TESTS)

INCLUDES = $(ECA_S_EXTRA_CPPFLAGS)

kvutil_sources = 	kvu_dbc.cpp \
			kvu_debug.cpp \
			kvu_com_line.cpp \
			kvu_fd_io.cpp \
			kvu_locks.cpp \
			kvu_message_item.cpp \
			kvu_numtostr.cpp \
			kvu_procedure_timer.cpp \
			kvu_rtcaps.cpp \
			kvu_temporary_file_directory.cpp \
			kvu_threads.cpp \
			kvu_utils.cpp \
			kvu_timestamp.cpp \
		 	kvu_value_queue.cpp

kvutil_headers = 	kvu_dbc.h \
			kvu_debug.h \
			kvu_definition_by_contract.h \
			kvu_com_line.h \
			kvu_fd_io.h \
			kvu_inttypes.h \
			kvu_locks.h \
			kvu_message_item.h \
			kvu_message_queue.h \
			kvu_numtostr.h \
			kvu_object_queue.h \
			kvu_procedure_timer.h \
			kvu_rtcaps.h \
			kvu_temporary_file_directory.h \
			kvu_threads.h \
			kvu_utils.h \
			kvu_timestamp.h \
			kvu_value_queue.h

libkvutils_la_SOURCES = $(kvutil_sources) $(kvutil_headers)
libkvutils_la_LDFLAGS = -version-info @LIBKVUTILS_VERSION@:0:@LIBKVUTILS_VERSION_AGE@ -static

libkvutils_debug_la_SOURCES = $(libkvutils_la_SOURCES)
libkvutils_debug_la_LDFLAGS = $(libkvutils_la_LDFLAGS)

libkvutils_tester_SOURCES = libkvutils_tester.cpp
libkvutils_tester_LDFLAGS = -static
libkvutils_tester_LDADD = $(lib_LTLIBRARIES)

noinst_HEADERS = $(kvutil_headers)

# ---------------------------------------------------------------------
# Install targets
install-data-local:
	$(INSTALL) -d $(DESTDIR)$(includedir)/kvutils
	cd $(srcdir) ; cp  $(kvutil_headers) $(DESTDIR)$(includedir)/kvutils

# ---------------------------------------------------------------------
# Uninstall targets
uninstall-local:
	cd $(DESTDIR)$(includedir)/kvutils && \
	rm -f $(kvutil_headers)
	rmdir $(DESTDIR)$(includedir)/kvutils || echo "Skipping non-empty directory"
