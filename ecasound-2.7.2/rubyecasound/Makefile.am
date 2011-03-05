# ----------------------------------------------------------------------
# File: ecasound/pyecasound/Makefile.am
# Description: Ruby implmentation of the Ecasound Control Interface
# License: LGPL (see ecasound/{AUTHORS,COPYING})
# ----------------------------------------------------------------------

AUTOMAKE_OPTIONS 		= foreign
EXTRA_DIST 			= README \
				  ecasound.rb \
				  stresstest.rb

# ----------------------------------------------------------------------
# defines
# ----------------------------------------------------------------------

if ECA_AM_RUBYECASOUND_INSTALL
rubyecasound_install_list1 = 	$(srcdir)/ecasound.rb
rubyecasound_uninstall_list1 =  $(DESTDIR)$(ECA_S_RUBY_SITEDIR)/ecasound.rb
endif

if ECA_AM_RUBYECASOUND_INSTALL
TESTS				= stresstest.rb
endif

# ----------------------------------------------------------------------
# hooks
# ----------------------------------------------------------------------

# unit test requires "ecasound.rb" in the build directory
stresstest.rb: ecasound.rb-STAMP
ecasound.rb-STAMP: $(srcdir)/ecasound.rb
	if test ! -e ecasound.rb ; then $(LN_S) $(srcdir)/ecasound.rb . ; fi
	touch ecasound.rb-STAMP

if ECA_AM_RUBYECASOUND_INSTALL
install-exec-hook: $(rubyecasound_install_list1) $(rubyecasound_install_list2)
	$(INSTALL) -d $(DESTDIR)$(ECA_S_RUBY_SITEDIR)
	$(INSTALL) $(rubyecasound_install_list1) $(rubyecasound_install_list2) $(DESTDIR)$(ECA_S_RUBY_SITEDIR)/
else
install-exec-hook:
endif

if ECA_AM_RUBYECASOUND_INSTALL
uninstall-local:
	rm -f $(rubyecasound_uninstall_list1) $(rubyecasound_uninstall_list2)
	rmdir $(DESTDIR)$(ECA_S_RUBY_SITEDIR) || echo "Skipping non-empty directory"
else
uninstall-local:
endif

clean-local:
	rm -vf ecasound.rb-STAMP