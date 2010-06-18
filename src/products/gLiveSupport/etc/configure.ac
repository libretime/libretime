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
AC_INIT(Campcaster-Studio, 0.0, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2004 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision$)

AC_CONFIG_SRCDIR(../src/main.cxx)

AC_CONFIG_HEADERS(configure.h)
AC_PROG_CXX()

AC_CHECK_HEADERS(sys/types.h unistd.h getopt.h signal.h sys/stat.h time.h)
AC_CHECK_HEADERS(stdio.h fcntl.h sys/time.h pwd.h)


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
    AC_DEFINE( YDEBUG, 1, [Debug is on] )
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

PKG_CHECK_MODULES(GTKMM,[gtkmm-2.4 >= 2.5.5])
AC_SUBST(GTKMM_CFLAGS)
AC_SUBST(GTKMM_LIBS)

PKG_CHECK_MODULES(LIBGLADEMM,[libglademm-2.4 >= 2.6.2])
AC_SUBST(LIBGLADEMM_CFLAGS)
AC_SUBST(LIBGLADEMM_LIBS)

PKG_CHECK_MODULES(GSTREAMER,[gstreamer-0.10 >= 0.10])
AC_SUBST(GSTREAMER_CFLAGS)
AC_SUBST(GSTREAMER_LIBS)

PKG_CHECK_MODULES(GSTCONTROLLER,[gstreamer-controller-0.10 >= 0.10])
AC_SUBST(GSTCONTROLLER_CFLAGS)
AC_SUBST(GSTCONTROLLER_LIBS)

PKG_CHECK_MODULES(LIBSERIAL,[libserial >= 0.5.2])
AC_SUBST(LIBSERIAL_CFLAGS)
AC_SUBST(LIBSERIAL_LIBS)

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


dnl-----------------------------------------------------------------------------
dnl  specify the FQDN
dnl-----------------------------------------------------------------------------
AC_SUBST(HOSTNAME)

AC_ARG_WITH([hostname],
            AC_HELP_STRING([--with-hostname],
                           [use the specified hostname (guess)]),
            [HOSTNAME=${withval}], [HOSTNAME=`hostname -f`])

AC_MSG_RESULT([using hostname: ${HOSTNAME}])


dnl-----------------------------------------------------------------------------
dnl  specify the web server port
dnl-----------------------------------------------------------------------------
AC_SUBST(WWW_PORT)

AC_ARG_WITH([www-port],
            AC_HELP_STRING([--with-www-port],
                           [use the specified www port (80)]),
            [WWW_PORT=${withval}], [WWW_PORT=80])

AC_MSG_RESULT([using www port: ${WWW_PORT}])


dnl-----------------------------------------------------------------------------
dnl  specify the scheduler server port
dnl-----------------------------------------------------------------------------
AC_SUBST(SCHEDULER_PORT)

AC_ARG_WITH([scheduler-port],
            AC_HELP_STRING([--with-scheduler-port],
                           [use the specified scheduler port (3344)]),
            [SCHEDULER_PORT=${withval}], [SCHEDULER_PORT=3344])

AC_MSG_RESULT([using scheduler port: ${SCHEDULER_PORT}])


dnl-----------------------------------------------------------------------------
dnl  specify the database server name
dnl-----------------------------------------------------------------------------
AC_SUBST(DB_SERVER)

AC_ARG_WITH([database-server],
            AC_HELP_STRING([--with-database-server],
                           [use the specified database server (localhost)]),
            [DB_SERVER=${withval}], [DB_SERVER=localhost])

AC_MSG_RESULT([using database server: ${DB_SERVER}])


dnl-----------------------------------------------------------------------------
dnl get the name of the Campcaster database
dnl-----------------------------------------------------------------------------
AC_SUBST(DATABASE)

AC_ARG_WITH([database],
            AC_HELP_STRING([--with-database],
                    [the name of the postgresql database to use (Campcaster)]),
            [DATABASE=${withval}], [DATABASE=Campcaster])

AC_MSG_RESULT([using database: ${DATABASE}])


dnl-----------------------------------------------------------------------------
dnl  specify the database server user
dnl-----------------------------------------------------------------------------
AC_SUBST(DB_USER)

AC_ARG_WITH([database-user],
            AC_HELP_STRING([--with-database-user],
                       [use the specified database server user (campcaster)]),
            [DB_USER=${withval}], [DB_USER=campcaster])

AC_MSG_RESULT([using database server user: ${DB_USER}])


dnl-----------------------------------------------------------------------------
dnl  specify the database server user password
dnl-----------------------------------------------------------------------------
AC_SUBST(DB_PASSWORD)

AC_ARG_WITH([database-password],
            AC_HELP_STRING([--with-database-password],
               [use the specified database server user password (campcaster)]),
            [DB_PASSWORD=${withval}], [DB_PASSWORD=campcaster])

AC_MSG_RESULT([using database server user password: ${DB_PASSWORD}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device
dnl-----------------------------------------------------------------------------
AC_SUBST(AUDIO_OUT)

AC_ARG_WITH([audio-out],
            AC_HELP_STRING([--with-audio-out],
               [use the specified audio output device, either ALSA or OSS
                (default)]),
            [AUDIO_OUT=${withval}], [AUDIO_OUT=default])

AC_MSG_RESULT([using audio output device: ${AUDIO_OUT}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio cue device
dnl-----------------------------------------------------------------------------
AC_SUBST(AUDIO_CUE)

AC_ARG_WITH([audio-cue],
            AC_HELP_STRING([--with-audio-cue],
               [use the specified audio cue device, either ALSA or OSS
                (default)]),
            [AUDIO_CUE=${withval}], [AUDIO_CUE=default])

AC_MSG_RESULT([using audio cue device: ${AUDIO_CUE}])


dnl display status info on what libraries will get compiled

AC_MSG_NOTICE(
[using the following configuration settings:

    hostname:                  ${HOSTNAME}
    www port:                  ${WWW_PORT}
    scheduler port:            ${SCHEDULER_PORT}
    database server:           ${DB_SERVER}
    database name:             ${DATABASE}
    database user:             ${DB_USER}
    database user password:    ${DB_PASSWORD}
    audio output device:       ${AUDIO_OUT}
    audio cue device:          ${AUDIO_CUE}

])



AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)

AC_OUTPUT()
