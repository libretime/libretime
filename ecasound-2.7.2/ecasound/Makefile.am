INCLUDES = -I$(ECA_S_READLINE_INCLUDES) -I$(srcdir) -I$(top_srcdir) -I$(top_srcdir)/libecasound -I$(top_srcdir)/kvutils

if ECA_AM_USE_NCURSES
termcap_library_ncurses = -lncurses
endif

if ECA_AM_USE_TERMCAP
termcap_library_termcap = -ltermcap
endif

termcap_library = $(termcap_library_ncurses) $(termcap_library_termcap)

if ECA_AM_DEBUG_MODE
bin_PROGRAMS = ecasound_debug
else
bin_PROGRAMS = ecasound
endif

ecasound_SOURCES = 	ecasound.cpp \
			ecasound.h \
			eca-console.h \
			eca-curses.h \
			eca-curses.cpp \
			eca-comhelp.cpp \
			eca-comhelp.h \
			eca-neteci-server.cpp \
			eca-neteci-server.h \
			eca-plaintext.h \
			eca-plaintext.cpp \
			textdebug.cpp \
			textdebug.h

ecasound_debug_SOURCES = $(ecasound_SOURCES)

ecasound_LDFLAGS = -export-dynamic
ecasound_LDADD = $(termcap_library) \
		$(ECA_S_READLINE_LIBS) \
		$(top_builddir)/libecasound/libecasound.la \
		$(top_builddir)/kvutils/libkvutils.la
ecasound_debug_LDFLAGS = $(ecasound_LDFLAGS)
ecasound_debug_LDADD = $(termcap_library) \
		$(ECA_S_READLINE_LIBS) \
	 	$(top_builddir)/libecasound/libecasound_debug.la \
		$(top_builddir)/kvutils/libkvutils_debug.la

# --

# special targets with dependency tracking
ecainstall:
	$(MAKE) -C $(srcdir)/../libecasound -q 2>/dev/null || make -C $(srcdir)/../libecasound
	$(MAKE) -C $(srcdir)/../kvutils -q 2>/dev/null || make -C $(srcdir)/../kvutils
	$(MAKE) install
