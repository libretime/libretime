# ----------------------------------------------------------------------
# File: ecasound/Makefile.am
# Description: Ecasound multitrack audio processing tool
# License: GPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

EXTRA_DIST = AUTHORS BUGS COPYING.GPL COPYING.LGPL NEWS TODO ecasoundrc.in ecasound.spec effect_presets generic_oscillators

SUBDIRS = kvutils libecasound ecasound libecasoundc pyecasound rubyecasound ecatools Documentation examples

AUTOMAKE_OPTIONS = foreign

PACKAGE = @PACKAGE@
VERSION = @VERSION@
RELEASE = 1

docs:
	cd Documentation ; $(MAKE) $(AM_MAKEFLAGS) docs

install-data-local: ecasoundrc
	$(INSTALL) -d $(DESTDIR)$(pkgdatadir)
	$(INSTALL_DATA) $(top_builddir)/ecasoundrc $(DESTDIR)$(pkgdatadir)/ecasoundrc
	$(INSTALL_DATA) $(top_srcdir)/effect_presets $(DESTDIR)$(pkgdatadir)/effect_presets
	$(INSTALL_DATA) $(top_srcdir)/ecatools/ecasound.el $(DESTDIR)$(pkgdatadir)/ecasound.el
	$(INSTALL_DATA) $(top_srcdir)/generic_oscillators $(DESTDIR)$(pkgdatadir)/generic_oscillators

uninstall-local:
	rm -f	$(DESTDIR)$(pkgdatadir)/effect_presets \
		$(DESTDIR)$(pkgdatadir)/ecasound.el \
		$(DESTDIR)$(pkgdatadir)/generic_oscillators \
		$(DESTDIR)$(pkgdatadir)/ecasoundrc
	rmdir $(DESTDIR)$(pkgdatadir) || echo "Skipping non-empty directory"

ecasoundrc: ecasoundrc.in Makefile.am
	sed -e "s%[@]VERSION[@]%$(VERSION)%" \
            -e "s%[@]prefix[@]%$(prefix)%" \
            -e "s%[@]pkgdatadir[@]%$(pkgdatadir)%" \
            < $(top_srcdir)/ecasoundrc.in > ecasoundrc 

clean-local:
	rm -fv ecasoundrc

dist-hook: docs
