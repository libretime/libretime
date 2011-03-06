# ----------------------------------------------------------------------
# File: ecasound/Documentation/Makefile.am
# Description: Ecasound documentation files
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

prefix = @prefix@
mandir = @mandir@
PACKAGE = @PACKAGE@
VERSION = @VERSION@
SUBDIRS = users_guide programmers_guide

# ---------------------------------------------------------------------
# Files going into distribution:

man1_pages_to_install = \
		ecasound.1 \
		ecasound-iam.1 \
		ecatools.1 \
		ecaconvert.1 \
		ecafixdc.1 \
		ecalength.1 \
		ecaplay.1 \
		ecamonitor.1 \
		ecanormalize.1 \
		ecasignalview.1

man5_pages_to_install = \
		ecasoundrc.5

html_man_pages = \
	 	ecasound_manpage.html \
	    	ecasoundrc_manpage.html \
		ecatools_manpage.html \
		ecalength_manpage.html \
		ecasound-iam_manpage.html

EXTRA_DIST = 	$(man1_pages_to_install) \
		$(man5_pages_to_install) \
		$(html_man_pages)
	    	$(srcdir)/ecasound_manpage.yo \
	    	$(srcdir)/ecasoundrc_manpage.yo \
	    	$(srcdir)/ecasound-iam_manpage.yo \
		$(srcdir)/ecatools_manpage.yo \
	    	$(srcdir)/ecalength_manpage.yo \
		$(srcdir)/index.html \
	    	$(srcdir)/examples.html \
		$(srcdir)/manpages.html \
		$(srcdir)/tutorials.html \
		$(srcdir)/style_ecasound.css \
		$(srcdir)/ecasound_osc_interface.txt

# ---------------------------------------------------------------------
# local targets for generating docs from latex and yodl sources

all:

docs: docs-guides docs-manpages

docs-guides:
	cd programmers_guide; $(MAKE) $(AM_MAKEFLAGS) docs
	cd users_guide; $(MAKE) $(AM_MAKEFLAGS) docs

docs-manpages: 	$(man1_pages_to_install) \
		$(man5_pages_to_install) \
		$(html_man_pages)

# ---------------------------------------------------------------------
# Man pages

# following are copies of ecatools.1
ecaconvert.1: ecatools.1
	cp ecatools.1 ecaconvert.1
ecafixdc.1: ecatools.1
	cp ecatools.1 ecafixdc.1
ecamonitor.1: ecatools.1
	cp -v ecatools.1 ecamonitor.1
ecanormalize.1: ecatools.1
	cp -v ecatools.1 ecanormalize.1
ecaplay.1: ecatools.1
	cp -v ecatools.1 ecaplay.1
ecasignalview.1: ecatools.1
	cp -v ecatools.1 ecasignalview.1

%.1: $(srcdir)/%_manpage.yo
	yodl2man -o $@ $<

%.5: $(srcdir)/%_manpage.yo
	yodl2man -o $@ $<

%.html: $(srcdir)/%.yo
	yodl2html -o $@ $<

# ---------------------------------------------------------------------
# doxygen (target removed 2009/Feb)
#doxygen:
#	doxygen doxygen_libkvutils_config
#	doxygen doxygen_libecasound_config

# ---------------------------------------------------------------------
# Clean targets

clean:

clean-docs: clean-manpages clean-guides

clean-guides:
	cd programmers_guide; $(MAKE) $(AM_MAKEFLAGS) clean-docs
	cd users_guide; $(MAKE) $(AM_MAKEFLAGS) clean-docs

clean-manpages:
	rm -f 	$(man1_pages_to_install) \
		$(man5_pages_to_install) \
		$(html_man_pages)

# ---------------------------------------------------------------------
# Install targets
install-data-local:
	mkdir -p $(DESTDIR)$(mandir)/man1
	mkdir -p $(DESTDIR)$(mandir)/man5
	for f in $(man1_pages_to_install) ; do if test -f $$f ; then cp -v $$f $(DESTDIR)$(mandir)/man1/; fi ; done 
	for f in $(man5_pages_to_install) ; do if test -f $$f ; then cp -v $$f $(DESTDIR)$(mandir)/man5/; fi ; done

# ---------------------------------------------------------------------
# Uninstall targets
uninstall-local:
	for f in $(man1_pages_to_install) ; do if test -f $$f ; then rm -vf $(DESTDIR)$(mandir)/man1/$$f ; fi ; done 
	for f in $(man5_pages_to_install) ; do if test -f $$f ; then rm -vf $(DESTDIR)$(mandir)/man5/$$f ; fi ; done 
