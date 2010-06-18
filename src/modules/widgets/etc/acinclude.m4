dnl-----------------------------------------------------------------------------
dnl Copyright (c) 2004 Media Development Loan Fund
dnl
dnl This file is part of the Campcaster project.
dnl http://campcaster.campware.org/
dnl To report bugs, send an e-mail to bugs@campware.org
dnl
dnl Campcaster is free software; you can redistribute it and/or modify
dnl it under the terms of the GNU General Public License as published by
dnl the Free Software Foundation; either version 2 of the License, or
dnl (at your option) any later version.
dnl
dnl Campcaster is distributed in the hope that it will be useful,
dnl but WITHOUT ANY WARRANTY; without even the implied warranty of
dnl MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
dnl GNU General Public License for more details.
dnl
dnl You should have received a copy of the GNU General Public License
dnl along with Campcaster; if not, write to the Free Software
dnl Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
dnl
dnl
dnl Author   : $Author$
dnl Version  : $Revision$
dnl Location : $URL$
dnl-----------------------------------------------------------------------------

dnl-----------------------------------------------------------------------------
dnl Macro to check for available modules using pkg-conf
dnl
dnl usage:
dnl PKG_CHECK_MODULES(GSTUFF,[gtk+-2.0 >= 1.3], action-if, action-not)
dnl
dnl defines GSTUFF_LIBS, GSTUFF_CFLAGS, see pkg-config man page
dnl also defines GSTUFF_PKG_ERRORS on error
dnl
dnl This function was taken from the glade-- project
dnl-----------------------------------------------------------------------------
AC_DEFUN([PKG_CHECK_MODULES], [
  succeeded=no

  if test -z "$PKG_CONFIG"; then
    AC_PATH_PROG(PKG_CONFIG, pkg-config, no)
  fi

  if test "$PKG_CONFIG" = "no" ; then
     echo "*** The pkg-config script could not be found. Make sure it is"
     echo "*** in your path, or set the PKG_CONFIG environment variable"
     echo "*** to the full path to pkg-config."
     echo "*** Or see http://www.freedesktop.org/software/pkgconfig to get pkg-config."
  else
     PKG_CONFIG_MIN_VERSION=0.9.0
     if $PKG_CONFIG --atleast-pkgconfig-version $PKG_CONFIG_MIN_VERSION; then
        AC_MSG_CHECKING(for $2)

        if $PKG_CONFIG --exists "$2" ; then
            AC_MSG_RESULT(yes)
            succeeded=yes

            AC_MSG_CHECKING($1_CFLAGS)
            $1_CFLAGS=`$PKG_CONFIG --cflags "$2"`
            AC_MSG_RESULT($$1_CFLAGS)

            AC_MSG_CHECKING($1_LIBS)
            $1_LIBS=`$PKG_CONFIG --libs "$2"`
            AC_MSG_RESULT($$1_LIBS)
        else
            $1_CFLAGS=""
            $1_LIBS=""
            ## If we have a custom action on failure, don't print errors, but 
            ## do set a variable so people can do so.
            $1_PKG_ERRORS=`$PKG_CONFIG --errors-to-stdout --print-errors "$2"`
            ifelse([$4], ,echo $$1_PKG_ERRORS,)
        fi

        AC_SUBST($1_CFLAGS)
        AC_SUBST($1_LIBS)
     else
        echo "*** Your version of pkg-config is too old. You need version $PKG_CONFIG_MIN_VERSION or newer."
        echo "*** See http://www.freedesktop.org/software/pkgconfig"
     fi
  fi

  if test $succeeded = yes; then
     ifelse([$3], , :, [$3])
  else
     ifelse([$4], , AC_MSG_ERROR([Library requirements ($2) not met; consider adjusting the PKG_CONFIG_PATH environment variable if your libraries are in a nonstandard prefix so pkg-config can find them.]), [$4])
  fi
])



dnl-----------------------------------------------------------------------------
dnl Macro to check for C++ namespaces
dnl for more information on this macro, see
dnl http://autoconf-archive.cryp.to/ac_cxx_namespaces.html
dnl
dnl usage:
dnl  If the compiler can prevent names clashes using namespaces,
dnl  define HAVE_NAMESPACES.
dnl-----------------------------------------------------------------------------
AC_DEFUN([AC_CXX_NAMESPACES],
[AC_CACHE_CHECK(whether the compiler implements namespaces,
ac_cv_cxx_namespaces,
[AC_LANG_SAVE
 AC_LANG_CPLUSPLUS
 AC_TRY_COMPILE([namespace Outer { namespace Inner { int i = 0; }}],
                [using namespace Outer::Inner; return i;],
 ac_cv_cxx_namespaces=yes, ac_cv_cxx_namespaces=no)
 AC_LANG_RESTORE
])
if test "$ac_cv_cxx_namespaces" = yes; then
  AC_DEFINE(HAVE_NAMESPACES,,[define if the compiler implements namespaces])
fi
])


dnl-----------------------------------------------------------------------------
dnl Test for the Boost C++ libraries of a particular version (or newer).
dnl for more information on boost, see http://www.boost.org/
dnl for more information on this macro, see
dnl http://autoconf-archive.cryp.to/ax_boost_base.html
dnl
dnl usage:
dnl If no path to the installed boost library is given the macro searches
dnl under ${prefix}, /usr, /usr/local, and /opt, and evaluates the $BOOST_ROOT
dnl environment variable. Further documentation is available at
dnl http://randspringer.de/boost/index.html
dnl 
dnl This macro calls: AC_SUBST(BOOST_CPPFLAGS) and AC_SUBST(BOOST_LDFLAGS)
dnl and sets: HAVE_BOOST
dnl
dnl Modified for Campcaster:
dnl * --with-boost default changed to Yes;
dnl * if the library is not found, it does not die, just prints "no", leaves
dnl HAVE_BOOST undefined, and sets the BOOST_CPPFLAGS and BOOST_LDFLAGS
dnl variables to "";
dnl * ${prefix} is prepended to the search path.
dnl
dnl Author: Thomas Porschberg <thomas@randspringer.de>
dnl
dnl License:
dnl Copyright © 2006 Thomas Porschberg <thomas@randspringer.de>
dnl Copying and distribution of this file, with or without modification, 
dnl are permitted in any medium without royalty provided the copyright notice
dnl and this notice are preserved.
dnl-----------------------------------------------------------------------------
AC_DEFUN([AX_BOOST_BASE],
[
AC_ARG_WITH([boost],
        AS_HELP_STRING([--with-boost@<:@=DIR@:>@], [use boost (default is Yes) - it is possible to specify the root directory for boost (optional)]),
        [
    if test "$withval" = "no"; then
                want_boost="no"
    elif test "$withval" = "yes"; then
        want_boost="yes"
        ac_boost_path=""
    else
            want_boost="yes"
        ac_boost_path="$withval"
        fi
    ],
    [want_boost="yes"])

if test "x$want_boost" = "xyes"; then
        boost_lib_version_req=ifelse([$1], ,1.20.0,$1)
        boost_lib_version_req_shorten=`expr $boost_lib_version_req : '\([[0-9]]*\.[[0-9]]*\)'`
        boost_lib_version_req_major=`expr $boost_lib_version_req : '\([[0-9]]*\)'`
        boost_lib_version_req_minor=`expr $boost_lib_version_req : '[[0-9]]*\.\([[0-9]]*\)'`
        boost_lib_version_req_sub_minor=`expr $boost_lib_version_req : '[[0-9]]*\.[[0-9]]*\.\([[0-9]]*\)'`
        if test "x$boost_lib_version_req_sub_minor" = "x" ; then
                boost_lib_version_req_sub_minor="0"
        fi
        WANT_BOOST_VERSION=`expr $boost_lib_version_req_major \* 100000 \+  $boost_lib_version_req_minor \* 100 \+ $boost_lib_version_req_sub_minor`
        AC_MSG_CHECKING(for boostlib >= $boost_lib_version_req)
        succeeded=no

        dnl first we check the system location for boost libraries
        dnl this location ist chosen if boost libraries are installed with the --layout=system option
        dnl or if you install boost with RPM
        if test "$ac_boost_path" != ""; then
                BOOST_LDFLAGS="-L$ac_boost_path/lib"
                BOOST_CPPFLAGS="-I$ac_boost_path/include"
        else
                for ac_boost_path_tmp in ${prefix} /usr /usr/local /opt ; do
                        if test -d "$ac_boost_path_tmp/include/boost" && test -r "$ac_boost_path_tmp/include/boost"; then
                                BOOST_LDFLAGS="-L$ac_boost_path_tmp/lib"
                                BOOST_CPPFLAGS="-I$ac_boost_path_tmp/include"
                                break;
                        fi
                done
        fi

        CPPFLAGS_SAVED="$CPPFLAGS"
        CPPFLAGS="$CPPFLAGS $BOOST_CPPFLAGS"
        export CPPFLAGS

        LDFLAGS_SAVED="$LDFLAGS"
        LDFLAGS="$LDFLAGS $BOOST_LDFLAGS"
        export LDFLAGS

        AC_LANG_PUSH(C++)
        AC_COMPILE_IFELSE([AC_LANG_PROGRAM([[
        @%:@include <boost/version.hpp>
        ]], [[
        #if BOOST_VERSION >= $WANT_BOOST_VERSION
        // Everything is okay
        #else
        #  error Boost version is too old
        #endif
        ]])],[
        AC_MSG_RESULT(yes)
        succeeded=yes
        found_system=yes
        ],[
        ])
        AC_LANG_POP([C++])



        dnl if we found no boost with system layout we search for boost libraries
        dnl built and installed without the --layout=system option or for a staged(not installed) version
        if test "x$succeeded" != "xyes"; then
                _version=0
                if test "$ac_boost_path" != ""; then
                        BOOST_LDFLAGS="-L$ac_boost_path/lib"
                        if test -d "$ac_boost_path" && test -r "$ac_boost_path"; then
                                for i in `ls -d $ac_boost_path/include/boost-* 2>/dev/null`; do
                                        _version_tmp=`echo $i | sed "s#$ac_boost_path##" | sed 's/\/include\/boost-//' | sed 's/_/./'`
                                        V_CHECK=`expr $_version_tmp \> $_version`
                                        if test "$V_CHECK" = "1" ; then
                                                _version=$_version_tmp
                                        fi
                                        VERSION_UNDERSCORE=`echo $_version | sed 's/\./_/'`
                                        BOOST_CPPFLAGS="-I$ac_boost_path/include/boost-$VERSION_UNDERSCORE"
                                done
                        fi
                else
                        for ac_boost_path in /usr /usr/local /opt ; do
                                if test -d "$ac_boost_path" && test -r "$ac_boost_path"; then
                                        for i in `ls -d $ac_boost_path/include/boost-* 2>/dev/null`; do
                                                _version_tmp=`echo $i | sed "s#$ac_boost_path##" | sed 's/\/include\/boost-//' | sed 's/_/./'`
                                                V_CHECK=`expr $_version_tmp \> $_version`
                                                if test "$V_CHECK" = "1" ; then
                                                        _version=$_version_tmp
                                                        best_path=$ac_boost_path
                                                fi
                                        done
                                fi
                        done

                        VERSION_UNDERSCORE=`echo $_version | sed 's/\./_/'`
                        BOOST_CPPFLAGS="-I$best_path/include/boost-$VERSION_UNDERSCORE"
                        BOOST_LDFLAGS="-L$best_path/lib"

                        if test "x$BOOST_ROOT" != "x"; then
                                if test -d "$BOOST_ROOT" && test -r "$BOOST_ROOT" && test -d "$BOOST_ROOT/stage/lib" && test -r "$BOOST_ROOT/stage/lib"; then
                                        version_dir=`expr //$BOOST_ROOT : '.*/\(.*\)'`
                                        stage_version=`echo $version_dir | sed 's/boost_//' | sed 's/_/./g'`
                                        stage_version_shorten=`expr $stage_version : '\([[0-9]]*\.[[0-9]]*\)'`
                                        V_CHECK=`expr $stage_version_shorten \>\= $_version`
                                        if test "$V_CHECK" = "1" ; then
                                                AC_MSG_NOTICE(We will use a staged boost library from $BOOST_ROOT)
                                                BOOST_CPPFLAGS="-I$BOOST_ROOT"
                                                BOOST_LDFLAGS="-L$BOOST_ROOT/stage/lib"
                                        fi
                                fi
                        fi
                fi

                CPPFLAGS="$CPPFLAGS $BOOST_CPPFLAGS"
                export CPPFLAGS
                LDFLAGS="$LDFLAGS $BOOST_LDFLAGS"
                export LDFLAGS

                AC_LANG_PUSH(C++)
                AC_COMPILE_IFELSE([AC_LANG_PROGRAM([[
                @%:@include <boost/version.hpp>
                ]], [[
                #if BOOST_VERSION >= $WANT_BOOST_VERSION
                // Everything is okay
                #else
                #  error Boost version is too old
                #endif
                ]])],[
                AC_MSG_RESULT(yes)
                succeeded=yes
                found_system=yes
                ],[
                ])
                AC_LANG_POP([C++])
        fi

        if test "$succeeded" != "yes" ; then
                BOOST_CPPFLAGS=""
                BOOST_LDFLAGS=""
                AC_MSG_RESULT(no)
        else
                AC_SUBST(BOOST_CPPFLAGS)
                AC_SUBST(BOOST_LDFLAGS)
                AC_DEFINE(HAVE_BOOST,,[define if the Boost library is available])
        fi

        CPPFLAGS="$CPPFLAGS_SAVED"
        LDFLAGS="$LDFLAGS_SAVED"
fi

])


dnl-----------------------------------------------------------------------------
dnl Test for Date_Time library from the Boost C++ libraries.
dnl for more information on boost, see http://www.boost.org/
dnl for more information on this macro, see
dnl http://autoconf-archive.cryp.to/ax_boost_date_time.html
dnl
dnl usage:
dnl The macro requires a preceding call to AX_BOOST_BASE.
dnl Further documentation is available at
dnl <http://randspringer.de/boost/index.html>.
dnl 
dnl This macro calls: AC_SUBST(BOOST_DATE_TIME_LIB)
dnl and sets: HAVE_BOOST_DATE_TIME
dnl
dnl Modified for Campcaster:
dnl * --with-boost-date-time default changed to Yes.
dnl * added some more recognized suffixes to the library's name, incl. "-st".
dnl
dnl Authors:
dnl Thomas Porschberg <thomas@randspringer.de>
dnl Michael Tindal <mtindal@paradoxpoint.com>
dnl
dnl License:
dnl Copyright © 2006 Thomas Porschberg <thomas@randspringer.de>
dnl Copying and distribution of this file, with or without modification, 
dnl are permitted in any medium without royalty provided the copyright notice
dnl and this notice are preserved.
dnl-----------------------------------------------------------------------------
AC_DEFUN([AX_BOOST_DATE_TIME],
[
        AC_ARG_WITH([boost-date-time],
        AS_HELP_STRING([--with-boost-date-time@<:@=special-lib@:>@],
                   [use the Date_Time library from boost - it is possible to specify a certain library for the linker
                        e.g. --with-boost-date-time=boost_date_time-gcc-mt-d-1_33_1 ]),
        [
        if test "$withval" = "no"; then
                        want_boost="no"
        elif test "$withval" = "yes"; then
            want_boost="yes"
            ax_boost_user_date_time_lib=""
        else
                    want_boost="yes"
                ax_boost_user_date_time_lib="$withval"
                fi
        ],
        [want_boost="yes"]
        )

        if test "x$want_boost" = "xyes"; then
        AC_REQUIRE([AC_PROG_CC])
                CPPFLAGS_SAVED="$CPPFLAGS"
                CPPFLAGS="$CPPFLAGS $BOOST_CPPFLAGS"
                export CPPFLAGS

                LDFLAGS_SAVED="$LDFLAGS"
                LDFLAGS="$LDFLAGS $BOOST_LDFLAGS"
                export LDFLAGS

        AC_CACHE_CHECK(whether the Boost::Date_Time library is available,
                                           ax_cv_boost_date_time,
        [AC_LANG_PUSH([C++])
                 AC_COMPILE_IFELSE(AC_LANG_PROGRAM([[@%:@include <boost/date_time/gregorian/gregorian_types.hpp>]],
                                   [[using namespace boost::gregorian; date d(2002,Jan,10);
                                     return 0;
                                   ]]),
         ax_cv_boost_date_time=yes, ax_cv_boost_date_time=no)
         AC_LANG_POP([C++])
                ])
                if test "x$ax_cv_boost_date_time" = "xyes"; then
                        AC_DEFINE(HAVE_BOOST_DATE_TIME,,[define if the Boost::Date_Time library is available])
                        BN=boost_date_time
            if test "x$ax_boost_user_date_time_lib" = "x"; then
                           for ax_lib in $BN $BN-st $BN-mt $BN-mt-s $BN-s \
                               $BN-$CC $BN-$CC-st $BN-$CC-mt $BN-$CC-mt-s $BN-$CC-s \
                               lib$BN lib$BN-st lib$BN-mt lib$BN-mt-s lib$BN-s \
                               lib$BN-$CC lib$BN-$CC-st lib$BN-$CC-mt lib$BN-$CC-mt-s lib$BN-$CC-s \
                               $BN-mgw $BN-mgw $BN-mgw-st $BN-mgw-mt $BN-mgw-mt-s $BN-mgw-s ; do
                              AC_CHECK_LIB($ax_lib, main, [BOOST_DATE_TIME_LIB="-l$ax_lib" AC_SUBST(BOOST_DATE_TIME_LIB) link_date_time="yes" break],
                               [link_date_time="no"])
                           done
            else
               for ax_lib in $ax_boost_user_date_time_lib $BN-$ax_boost_user_date_time_lib; do
                                      AC_CHECK_LIB($ax_lib, main,
                                   [BOOST_DATE_TIME_LIB="-l$ax_lib" AC_SUBST(BOOST_DATE_TIME_LIB) link_date_time="yes" break],
                                   [link_date_time="no"])
                  done

            fi
                        if test "x$link_date_time" = "xno"; then
                                AC_MSG_ERROR(Could not link against $ax_lib !)
                        fi
                fi

                CPPFLAGS="$CPPFLAGS_SAVED"
        LDFLAGS="$LDFLAGS_SAVED"
        fi
])


dnl-----------------------------------------------------------------------------
dnl Macro to check for ICU of sufficient version by looking at icu-config
dnl
dnl usage:
dnl AC_CHECK_ICU(version, action-if, action-not)
dnl
dnl defines ICU_LIBS, ICU_CFLAGS, ICU_CXXFLAGS, see icu-config man page
dnl-----------------------------------------------------------------------------
AC_DEFUN([AC_CHECK_ICU], [
  succeeded=no

  if test -z "$ICU_CONFIG"; then
    AC_PATH_PROG(ICU_CONFIG, icu-config, no)
  fi

  if test "$ICU_CONFIG" = "no" ; then
    echo "*** The icu-config script could not be found. Make sure it is"
    echo "*** in your path, and that taglib is properly installed."
    echo "*** Or see http://ibm.com/software/globalization/icu/"
  else
    ICU_VERSION=`$ICU_CONFIG --version`
    AC_MSG_CHECKING(for ICU >= $1)
        VERSION_CHECK=`expr $ICU_VERSION \>\= $1`
        if test "$VERSION_CHECK" = "1" ; then
            AC_MSG_RESULT(yes)
            succeeded=yes

            AC_MSG_CHECKING(ICU_CFLAGS)
            ICU_CFLAGS=`$ICU_CONFIG --cflags`
            AC_MSG_RESULT($ICU_CFLAGS)

            AC_MSG_CHECKING(ICU_CXXFLAGS)
            ICU_CXXFLAGS=`$ICU_CONFIG --cxxflags`
            AC_MSG_RESULT($ICU_CXXFLAGS)

            AC_MSG_CHECKING(ICU_LIBS)
            ICU_LIBS=`$ICU_CONFIG --ldflags`
            AC_MSG_RESULT($ICU_LIBS)
        else
            ICU_CFLAGS=""
            ICU_CXXFLAGS=""
            ICU_LIBS=""
            ## If we have a custom action on failure, don't print errors, but 
            ## do set a variable so people can do so.
            ifelse([$3], ,echo "can't find ICU >= $1",)
        fi

        AC_SUBST(ICU_CFLAGS)
        AC_SUBST(ICU_CXXFLAGS)
        AC_SUBST(ICU_LIBS)
  fi

  if test $succeeded = yes; then
     ifelse([$2], , :, [$2])
  else
     ifelse([$3], , AC_MSG_ERROR([Library requirements (ICU) not met.]), [$3])
  fi
])

