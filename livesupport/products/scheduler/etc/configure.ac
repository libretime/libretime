dnl-----------------------------------------------------------------------------
dnl Copyright (c) 2004 Media Development Loan Fund
dnl
dnl This file is part of the LiveSupport project.
dnl http://livesupport.campware.org/
dnl To report bugs, send an e-mail to bugs@campware.org
dnl
dnl LiveSupport is free software; you can redistribute it and/or modify
dnl it under the terms of the GNU General Public License as published by
dnl the Free Software Foundation; either version 2 of the License, or
dnl (at your option) any later version.
dnl
dnl LiveSupport is distributed in the hope that it will be useful,
dnl but WITHOUT ANY WARRANTY; without even the implied warranty of
dnl MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
dnl GNU General Public License for more details.
dnl
dnl You should have received a copy of the GNU General Public License
dnl along with LiveSupport; if not, write to the Free Software
dnl Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
dnl
dnl
dnl Author   : $Author$
dnl Version  : $Revision$
dnl Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/scheduler/etc/configure.ac,v $
dnl-----------------------------------------------------------------------------

dnl-----------------------------------------------------------------------------
dnl NOTE: Run all configure related scripts from the tmp directory of the
dnl project.
dnl This is due to the fact that configure spreads a lot of trash around,
dnl like atom4te cache directories, config.* files, etc. into the directory
dnl it is being run from. We clearly don't want these in our base directory.
dnl-----------------------------------------------------------------------------
AC_INIT(Scheduler, 0.1, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2004 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision$)

AC_CONFIG_SRCDIR(../src/main.cxx)

AC_CONFIG_HEADERS(configure.h)
AC_PROG_CXX()

AC_CHECK_HEADERS(sys/types.h unistd.h getopt.h signal.h sys/stat.h time.h)
AC_CHECK_HEADERS(stdio.h fcntl.h sys/time.h)

dnl-----------------------------------------------------------------------------
dnl  specify the pkg-config path
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([pkg-config-path],
            AC_HELP_STRING([--with-pkg-config-path],
                           [use the pkg-config path (prefix/lib/pkgconfig)]),
            [PKG_CONFIG_PATH=${withval}],
            [PKG_CONFIG_PATH=${prefix}/lib/pkgconfig])

AC_MSG_RESULT([using pkg-config path: ${PKG_CONFIG_PATH}])

export PKG_CONFIG_PATH


AX_BOOST_DATE_TIME()

if test "$BOOST_DATE_TIME_LIB" = "" ; then
    BOOST_DATE_TIME_LIB=boost_date_time-gcc
    AC_SUBST(BOOST_DATE_TIME_LIB)
fi


PKG_CHECK_MODULES(LIBXMLPP,[libxml++-2.6 >= 2.8.1])
AC_SUBST(LIBXMLPP_CFLAGS)
AC_SUBST(LIBXMLPP_LIBS)

PKG_CHECK_MODULES(LIBODBCXX,[libodbc++])

PKG_CHECK_MODULES(GSTREAMER,[gstreamer-0.8 >= 0.8])
AC_SUBST(GSTREAMER_CFLAGS)
AC_SUBST(GSTREAMER_LIBS)


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
dnl  specify wether the LiveSupport database and user should be created
dnl-----------------------------------------------------------------------------
AC_SUBST(CREATE_LS_DATABASE)

AC_ARG_WITH([create-database],
            AC_HELP_STRING([--with-create-database],
               [specify wether the LiveSupport database and database user
                should be created (no)]),
            [CREATE_LS_DATABASE=${withval}],
            [CREATE_LS_DATABASE=no])

AC_MSG_RESULT([creating LiveSupport database: ${CREATE_LS_DATABASE}])


dnl-----------------------------------------------------------------------------
dnl  specify wether the ODBC data source should be created
dnl-----------------------------------------------------------------------------
AC_SUBST(CREATE_ODBC_DATA_SOURCE)

AC_ARG_WITH([create-odbc-data-source],
            AC_HELP_STRING([--with-create-odbc-data-source],
               [specify wether the ODBC data source for LiveSupport should be
                created (no)]),
            [CREATE_ODBC_DATA_SOURCE=${withval}],
            [CREATE_ODBC_DATA_SOURCE=no])

AC_MSG_RESULT([creating ODBC data source: ${CREATE_ODBC_DATA_SOURCE}])


dnl-----------------------------------------------------------------------------
dnl  specify wether the LiveSupport database tables should be initialized
dnl-----------------------------------------------------------------------------
AC_SUBST(INIT_LS_DATABASE)

AC_ARG_WITH([init-database],
            AC_HELP_STRING([--with-init-database],
               [specify wether the LiveSupport database tables should be
                initialized (no)]),
            [INIT_LS_DATABASE=${withval}],
            [INIT_LS_DATABASE=no])

AC_MSG_RESULT([initializing LiveSupport database: ${INIT_LS_DATABASE}])


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
dnl get the name of the LiveSupport database
dnl-----------------------------------------------------------------------------
AC_SUBST(DATABASE)

AC_ARG_WITH([database],
            AC_HELP_STRING([--with-database],
                    [the name of the postgresql database to use (LiveSupport)]),
            [DATABASE=${withval}], [DATABASE=LiveSupport])

AC_MSG_RESULT([using database: ${DATABASE}])


dnl-----------------------------------------------------------------------------
dnl  specify the database server user
dnl-----------------------------------------------------------------------------
AC_SUBST(DB_USER)

AC_ARG_WITH([database-user],
            AC_HELP_STRING([--with-database-user],
                       [use the specified database server user (livesupport)]),
            [DB_USER=${withval}], [DB_USER=livesupport])

AC_MSG_RESULT([using database server user: ${DB_USER}])


dnl-----------------------------------------------------------------------------
dnl  specify the database server user password
dnl-----------------------------------------------------------------------------
AC_SUBST(DB_PASSWORD)

AC_ARG_WITH([database-password],
            AC_HELP_STRING([--with-database-password],
               [use the specified database server user password (livesupport)]),
            [DB_PASSWORD=${withval}], [DB_PASSWORD=livesupport])

AC_MSG_RESULT([using database server user password: ${DB_PASSWORD}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device
dnl-----------------------------------------------------------------------------
AC_SUBST(AUDIO_OUT)

AC_ARG_WITH([audio-out],
            AC_HELP_STRING([--with-audio-out],
               [use the specified audio output device, either ALSA or OSS
                (plughw:0)]),
            [AUDIO_OUT=${withval}], [AUDIO_OUT=plughw:0])

AC_MSG_RESULT([using audio output device: ${AUDIO_OUT}])


dnl display status info on what libraries will get compiled

AC_MSG_NOTICE(
[using the following configuration settings:

    hostname:                        ${HOSTNAME}
    www port:                        ${WWW_PORT}
    scheduler port:                  ${SCHEDULER_PORT}
    database server:                 ${DB_SERVER}
    database name:                   ${DATABASE}
    database user:                   ${DB_USER}
    database user password:          ${DB_PASSWORD}
    audio output device:             ${AUDIO_OUT}
    creating LiveSupport database:   ${CREATE_LS_DATABASE}
    creating ODBC data source:       ${CREATE_ODBC_DATA_SOURCE}
    initialize LiveSupport database: ${INIT_LS_DATABASE}

])


AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)

AC_OUTPUT()
