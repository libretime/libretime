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
dnl Location : $URL$
dnl-----------------------------------------------------------------------------

dnl-----------------------------------------------------------------------------
dnl NOTE: Run all configure related scripts from the tmp directory of the
dnl project.
dnl This is due to the fact that configure spreads a lot of trash around,
dnl like atom4te cache directories, config.* files, etc. into the directory
dnl it is being run from. We clearly don't want these in our base directory.
dnl-----------------------------------------------------------------------------
AC_INIT(LiveSupport, 1.0, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2004 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision$)

AC_CONFIG_SRCDIR(../products/scheduler/src/main.cxx)

AC_PROG_CC()
AC_PROG_CXX()


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
dnl  specify wether apache should be configured through it's conf.d directory
dnl-----------------------------------------------------------------------------
AC_SUBST(CONFIGURE_APACHE)

AC_ARG_WITH([configure-apache],
            AC_HELP_STRING([--with-configure-apache],
               [specify wether apache should be configured for LiveSupport
                through its conf.d directory (no)]),
            [CONFIGURE_APACHE=${withval}],
            [CONFIGURE_APACHE=no])

AC_MSG_RESULT([configure apache: ${CONFIGURE_APACHE}])


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
dnl  specify group in which apache is running
dnl-----------------------------------------------------------------------------
AC_SUBST(APACHE_GROUP)

AC_ARG_WITH([apache-group],
            AC_HELP_STRING([--with-apache-group],
                          [use apache running in the specified group (apache)]),
            [APACHE_GROUP=${withval}], [APACHE_GROUP=apache])

AC_MSG_RESULT([using apache group: ${APACHE_GROUP}])


dnl-----------------------------------------------------------------------------
dnl  specify web document root
dnl-----------------------------------------------------------------------------
AC_SUBST(WWW_DOCROOT)

AC_ARG_WITH([www-docroot],
            AC_HELP_STRING([--with-www-docroot],
                   [deploy LiveSupport under the specified docroot (/var/www)]),
            [WWW_DOCROOT=${withval}], [WWW_DOCROOT=/var/www])

AC_MSG_RESULT([using www document root: ${WWW_DOCROOT}])


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
dnl  specify the audio output device for the scheduler
dnl-----------------------------------------------------------------------------
AC_SUBST(STATION_AUDIO_OUT)

AC_ARG_WITH([station-audio-out],
            AC_HELP_STRING([--with-station-audio-out],
               [use the specified audio output device for the station,
                either ALSA or OSS (plughw:0)]),
            [STATION_AUDIO_OUT=${withval}], [STATION_AUDIO_OUT=plughw:0])

AC_MSG_RESULT([using audio output device for the station: ${STAITON_AUDIO_OUT}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device for the studio
dnl-----------------------------------------------------------------------------
AC_SUBST(STUDIO_AUDIO_OUT)

AC_ARG_WITH([studio-audio-out],
            AC_HELP_STRING([--with-studio-audio-out],
               [use the specified audio output device for the studio,
                either ALSA or OSS (plughw:0)]),
            [STUDIO_AUDIO_OUT=${withval}], [STUDIO_AUDIO_OUT=plughw:0])

AC_MSG_RESULT([using audio output device for studio: ${STUDIO_AUDIO_OUT}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device for the studio for cueing
dnl-----------------------------------------------------------------------------
AC_SUBST(STUDIO_AUDIO_CUE)

AC_ARG_WITH([studio-audio-cue],
            AC_HELP_STRING([--with-studio-audio-cue],
               [use the specified audio cue device for the studio,
                either ALSA or OSS (plughw:0)]),
            [STUDIO_AUDIO_CUE=${withval}], [STUDIO_AUDIO_CUE=plughw:0])

AC_MSG_RESULT([using audio cue device for studio: ${STUDIO_AUDIO_CUE}])



dnl unfortunately for both AC_CONFIG_COMMANDS and AC_CONFIG_SUBDIRS, the
dnl directories have to be literally included, so we can't use any
dnl fancy variables to avoid duplication of hard-coded values :(


dnl-----------------------------------------------------------------------------
dnl  specify if boost library should be looked for on the system
dnl-----------------------------------------------------------------------------

AC_ARG_WITH([check-boost],
            AC_HELP_STRING([--with-check-boost],
               [check for the availability of the boost library on the
                system, and use it if available (yes)]),
            [CHECK_BOOST_LIB=${withval}], [CHECK_BOOST_LIB=yes])

AC_MSG_RESULT([checking for boost library on system: ${CHECK_BOOST_LIB}])

dnl-----------------------------------------------------------------------------
dnl  specify if gtk+ library should be looked for on the system
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([check-gtk],
            AC_HELP_STRING([--with-check-gtk],
               [check for the availability of the gtk+ library on the
                system, and use it if available (yes)]),
            [CHECK_GTK_LIB=${withval}], [CHECK_GTK_LIB=yes])

AC_MSG_RESULT([checking for gtk+ library on system: ${CHECK_GTK_LIB}])


dnl-----------------------------------------------------------------------------
dnl  specify if gtk-- library should be looked for on the system
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([check-gtkmm],
            AC_HELP_STRING([--with-check-gtkmm],
               [check for the availability of the gtk-- library on the
                system, and use it if available (yes)]),
            [CHECK_GTKMM_LIB=${withval}], [CHECK_GTKMM_LIB=yes])

AC_MSG_RESULT([checking for gtk-- library on system: ${CHECK_GTKMM_LIB}])


dnl-----------------------------------------------------------------------------
dnl  specify if icu library should be looked for on the system
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([check-icu],
            AC_HELP_STRING([--with-check-icu],
               [check for the availability of the icu library on the
                system, and use it if available (yes)]),
            [CHECK_ICU_LIB=${withval}], [CHECK_ICU_LIB=yes])

AC_MSG_RESULT([checking for icu library on system: ${CHECK_ICU_LIB}])


dnl-----------------------------------------------------------------------------
dnl  specify if libxml++ library should be looked for on the system
dnl-----------------------------------------------------------------------------
AC_ARG_WITH([check-libxmlpp],
            AC_HELP_STRING([--with-check-libxmlpp],
               [check for the availability of the xml++ library on the
                system, and use it if available (yes)]),
            [CHECK_XMLPP_LIB=${withval}], [CHECK_XMLPP_LIB=yes])

AC_MSG_RESULT([checking for xml++ library on system: ${CHECK_XMLPP_LIB}])


dnl check for the boost library
AX_BOOST_DATE_TIME()

if test "$CHECK_BOOST_LIB" = "yes" ; then
    if test "$BOOST_DATE_TIME_LIB" = "" ; then
        AC_MSG_RESULT([not found boost library of sufficient version, will compile from our own])
        COMPILE_BOOST="yes"
    else
dnl     COMPILE_BOOST="no"
dnl force compilation of boost, as recognizing a pre-existing version
dnl does not seem to work -- fgerlits, 2005-08-08
        AC_MSG_RESULT([found boost library, but recompiling anyway])
        BOOST_DATE_TIME_LIB=""
        COMPILE_BOOST="yes"
    fi
else
    COMPILE_BOOST="yes"
fi


if test "$CHECK_GTK_LIB" = "yes" ; then
    dnl check for gtk+ 2.6.1 or more recent
    PKG_CHECK_MODULES(GTK,[gtk+-2.0 >= 2.6.1],
    [
        AC_MSG_RESULT([using gtk+ found on the system])
        COMPILE_GTK="no"
    ],
    [
        AC_MSG_RESULT([not found gtk+ of sufficient version, will compile from our own])
        COMPILE_GTK="yes"
    ])
else
    COMPILE_GTK="yes"
fi


if test "$CHECK_GTKMM_LIB" = "yes" ; then
    dnl check for gtk-- 2.5.5 or more recent
    PKG_CHECK_MODULES(GTKMM,[gtkmm-2.4 >= 2.5.5],
    [
        AC_MSG_RESULT([using gtk-- found on the system])
        COMPILE_GTKMM="no"
    ],
    [
        AC_MSG_RESULT([not found gtk-- of sufficient version, will compile from our own])
        COMPILE_GTKMM="yes"
    ])
else
    COMPILE_GTKMM="yes"
fi


if test "$CHECK_ICU_LIB" = "yes" ; then
    dnl check for ICU 3.0 or more recent
    AC_CHECK_ICU(3.0,
    [
dnl     AC_MSG_RESULT([using ICU found on the system])
dnl     COMPILE_ICU="no"
dnl force compilation of ICU, as recognizing a pre-existing version
dnl does not seem to work -- fgerlits, 2005-08-08
    AC_MSG_RESULT([found ICU on the system, but recompiling anyway])
    COMPILE_ICU="yes"
    ],
    [
        AC_MSG_RESULT([not found ICU of sufficient version, will compile from our own])
        COMPILE_ICU="yes"
    ])
else
    COMPILE_ICU="yes"
fi


if test "$CHECK_XMLPP_LIB" = "yes" ; then
    dnl check for libxml++ 2.8.1 or more recent
    PKG_CHECK_MODULES(LIBXMLPP,[libxml++-2.6 >= 2.8.1],
    [
        AC_MSG_RESULT([using libxml++ found on the system])
        COMPILE_LIBXMLPP="no"
    ],
    [
        AC_MSG_RESULT([not found libxml++ of sufficient version, will compile from our own])
        COMPILE_LIBXMLPP="yes"
    ])
else
    COMPILE_LIBXMLPP="yes"
fi


dnl set up the alib module
AC_CONFIG_COMMANDS([../modules/alib/tmp/configure],
                   [../modules/alib/bin/autogen.sh])

dnl set up the archive server module
AC_CONFIG_COMMANDS([../modules/archiveServer/tmp/configure],
                   [../modules/archiveServer/bin/autogen.sh])

dnl set up the getid3 module
AC_CONFIG_COMMANDS([../modules/getid3/tmp/configure],
                   [../modules/getid3/bin/autogen.sh])

dnl set up the htmlUI module
AC_CONFIG_COMMANDS([../modules/htmlUI/tmp/configure],
                   [../modules/htmlUI/bin/autogen.sh])

dnl set up the storage admin module
AC_CONFIG_COMMANDS([../modules/storageAdmin/tmp/configure],
                   [../modules/storageAdmin/bin/autogen.sh])

dnl set up the storage server module
AC_CONFIG_COMMANDS([../modules/storageServer/tmp/configure],
                   [../modules/storageServer/bin/autogen.sh])

dnl set up the core module
AC_CONFIG_COMMANDS([../modules/core/tmp/configure],
                   [../modules/core/bin/autogen.sh])

dnl set up the authentication module
AC_CONFIG_COMMANDS([../modules/authentication/tmp/configure],
                   [../modules/authentication/bin/autogen.sh])

dnl set up the db module
AC_CONFIG_COMMANDS([../modules/db/tmp/configure],
                   [../modules/db/bin/autogen.sh])

dnl set up the storage module
AC_CONFIG_COMMANDS([../modules/storage/tmp/configure],
                   [../modules/storage/bin/autogen.sh])

dnl set up the gstreamer elements module
AC_CONFIG_COMMANDS([../modules/gstreamerElements/tmp/configure],
                   [../modules/gstreamerElements/bin/autogen.sh])

dnl set up the playlist executor module
AC_CONFIG_COMMANDS([../modules/playlistExecutor/tmp/configure],
                   [../modules/playlistExecutor/bin/autogen.sh])

dnl set up the event scheduler module
AC_CONFIG_COMMANDS([../modules/eventScheduler/tmp/configure],
                   [../modules/eventScheduler/bin/autogen.sh])

dnl set up the scheduler client module
AC_CONFIG_COMMANDS([../modules/schedulerClient/tmp/configure],
                   [../modules/schedulerClient/bin/autogen.sh])

dnl set up the  widgets module
AC_CONFIG_COMMANDS([../modules/widgets/tmp/configure],
                   [../modules/widgets/bin/autogen.sh])

dnl set up the scheduler product
AC_CONFIG_COMMANDS([../products/scheduler/tmp/configure],
                   [../products/scheduler/bin/autogen.sh])

dnl set up the gLiveSupport product
AC_CONFIG_COMMANDS([../products/gLiveSupport/tmp/configure],
                   [../products/gLiveSupport/bin/autogen.sh])



dnl display status info on what libraries will get compiled

AC_MSG_NOTICE(
[compiling the following external libraries that are needed
by LiveSupport:

    boost 1.31                 ${COMPILE_BOOST}
    cppunit 1.10.2             yes
    curl 7.12.3                yes
    gstreamer 0.8.10           yes
    gtk+ 2.6.1                 ${COMPILE_GTK}
    gtk-- 2.5.5                ${COMPILE_GTKMM}
    icu 3.0                    ${COMPILE_ICU}
    lcov 1.3                   yes
    libodbc++ 0.2.3            yes
    libxml++ 2.8.1             ${COMPILE_LIBXMLPP}
    taglib 1.3.1               yes
    xmlrpc++ 2004-07-13        yes


using the following configuration settings:

    hostname:                        ${HOSTNAME}
    apache group:                    ${APACHE_GROUP}
    www document root:               ${WWW_DOCROOT}
    www port:                        ${WWW_PORT}
    scheduler port:                  ${SCHEDULER_PORT}
    database server:                 ${DB_SERVER}
    database name:                   ${DATABASE}
    database user:                   ${DB_USER}
    database user password:          ${DB_PASSWORD}
    station audio output device:     ${STATION_AUDIO_OUT}
    studio audio output device:      ${STUDIO_AUDIO_OUT}
    studio audio cue device:         ${STUDIO_AUDIO_CUE}
    creating LiveSupport database:   ${CREATE_LS_DATABASE}
    creating ODBC data source:       ${CREATE_ODBC_DATA_SOURCE}
    initialize LiveSupport database: ${INIT_LS_DATABASE}
    configuring apache:              ${CONFIGURE_APACHE}

])


AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)


AC_OUTPUT()

