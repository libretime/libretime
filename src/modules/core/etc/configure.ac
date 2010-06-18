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
dnl NOTE: Run all configure related scripts from the tmp directory of the
dnl project.
dnl This is due to the fact that configure spreads a lot of trash around,
dnl like atom4te cache directories, config.* files, etc. into the directory
dnl it is being run from. We clearly don't want these in our base directory.
dnl-----------------------------------------------------------------------------
AC_INIT(Core, 0.0, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2004 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision$)

AC_CONFIG_SRCDIR(../src/UniqueId.cxx)

AC_CONFIG_HEADERS(configure.h)
AC_PROG_CXX()

AC_CHECK_TYPES([uint64_t, uint32_t, uint16_t, uint8_t],
                HAVE_INTEGRAL_TYPES="yes", HAVE_INTEGRAL_TYPES="no")
if test "x${HAVE_INTEGRAL_TYPES}" = "xno" ; then
    AC_MSG_ERROR([a required integral type is not available])
fi

AC_CHECK_HEADERS(getopt.h sys/time.h time.h sys/types.h pwd.h errno.h stdint.h)


dnl-----------------------------------------------------------------------------
dnl  specify whether debug info should be compiled into the executable
dnl-----------------------------------------------------------------------------
AC_SUBST(CXXFLAGS)

AC_ARG_ENABLE([debug],
            AC_HELP_STRING([--enable-debug], [compile with debug info (no)]),
            [],
            [enable_debug=no])

if test "x${enable_debug}" = "xyes"; then
    CXXFLAGS="-g -O0"
    AC_DEFINE(YDEBUG, 1, [Debug is on])
else
    CXXFLAGS="-O3"
fi

AC_MSG_RESULT([using compiler options: ${CXXFLAGS}])


dnl-----------------------------------------------------------------------------
dnl  specify the pkg-config path
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([pkg-config-path],
            AC_HELP_STRING([--with-pkg-config-path],
                           [use the pkg-config path (prefix/lib/pkgconfig)]),
            [PKG_CONFIG_PATH=${withval}],
            [PKG_CONFIG_PATH=${prefix}/lib/pkgconfig:$PKG_CONFIG_PATH])

AC_MSG_RESULT([using pkg-config path: ${PKG_CONFIG_PATH}])

export PKG_CONFIG_PATH

export PATH=${prefix}/bin:${PATH}


AX_BOOST_BASE([1.33.1])
if test "x${BOOST_CPPFLAGS}" != "x"; then
    AX_BOOST_DATE_TIME
    if test "x${BOOST_DATE_TIME_LIB}" = "x"; then
        AC_MSG_ERROR([Boost date-time library not found])
    fi
else
    AC_MSG_ERROR([Boost library >= 1.33.1 not found])
fi

PKG_CHECK_MODULES(LIBXMLPP,[libxml++-2.6 >= 2.8.1])
AC_SUBST(LIBXMLPP_CFLAGS)
AC_SUBST(LIBXMLPP_LIBS)

AC_CHECK_CURL(7.12.3)
AC_SUBST(CURL_CFLAGS)
AC_SUBST(CURL_LIBS)

AC_CHECK_TAGLIB(1.3.1)
AC_SUBST(TAGLIB_CFLAGS)
AC_SUBST(TAGLIB_LIBS)

AC_CHECK_ICU(3.0)
AC_SUBST(ICU_CFLAGS)
AC_SUBST(ICU_CXXFLAGS)
AC_SUBST(ICU_LIBS)

AC_PATH_PROG(GENRB, genrb)
AC_SUBST(GENRB)


dnl-----------------------------------------------------------------------------
dnl enable compilaton for code coverage data
dnl-----------------------------------------------------------------------------
AC_SUBST(COVERAGE_CXXFLAGS)

AC_ARG_ENABLE( coverage,
[  --enable-coverage       enable code coverage data generaton (no) ],
    ENABLE_COVERAGE=${enableval}, ENABLE_COVERAGE="no" )

if test "x${ENABLE_COVERAGE}" == "xyes" ; then
    COVERAGE_CXXFLAGS="-fprofile-arcs -ftest-coverage"
    AC_MSG_RESULT([compiling in code coverage mode])
else
    AC_MSG_RESULT([not compiling in code coverage mode])
fi


AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)

AC_OUTPUT()
