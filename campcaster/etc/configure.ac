dnl-----------------------------------------------------------------------------
dnl Copyright (c) 2010 Media Development Loan Fund
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
AC_INIT(Campcaster, 1.4.0, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2010 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision$)

AC_CONFIG_SRCDIR(../src/products/scheduler/src/main.cxx)

AC_PROG_CC()
AC_PROG_CXX()


dnl-----------------------------------------------------------------------------
dnl  specify whether debug info should be compiled into the executable
dnl-----------------------------------------------------------------------------
AC_SUBST(DEBUG)

AC_ARG_ENABLE([debug],
            AC_HELP_STRING([--enable-debug], [compile with debug info (no)]),
            [DEBUG=${enable_debug}],
            [DEBUG=no])

AC_MSG_RESULT([compiling with debug info: ${DEBUG}])


dnl-----------------------------------------------------------------------------
dnl  specify whether the Campcaster database and user should be created
dnl-----------------------------------------------------------------------------
AC_SUBST(CREATE_LS_DATABASE)

AC_ARG_WITH([create-database],
            AC_HELP_STRING([--with-create-database],
               [specify whether the Campcaster database and database user
                should be created (no)]),
            [CREATE_LS_DATABASE=${withval}],
            [CREATE_LS_DATABASE=no])

AC_MSG_RESULT([creating Campcaster database: ${CREATE_LS_DATABASE}])


dnl-----------------------------------------------------------------------------
dnl  specify whether the ODBC data source should be created
dnl-----------------------------------------------------------------------------
AC_SUBST(CREATE_ODBC_DATA_SOURCE)

AC_ARG_WITH([create-odbc-data-source],
            AC_HELP_STRING([--with-create-odbc-data-source],
               [specify whether the ODBC data source for Campcaster should be
                created (no)]),
            [CREATE_ODBC_DATA_SOURCE=${withval}],
            [CREATE_ODBC_DATA_SOURCE=no])

AC_MSG_RESULT([creating ODBC data source: ${CREATE_ODBC_DATA_SOURCE}])


dnl-----------------------------------------------------------------------------
dnl  specify whether the Campcaster database tables should be initialized
dnl-----------------------------------------------------------------------------
AC_SUBST(INIT_LS_DATABASE)

AC_ARG_WITH([init-database],
            AC_HELP_STRING([--with-init-database],
               [specify whether the Campcaster database tables should be
                initialized (no)]),
            [INIT_LS_DATABASE=${withval}],
            [INIT_LS_DATABASE=no])

AC_MSG_RESULT([initializing Campcaster database: ${INIT_LS_DATABASE}])


dnl-----------------------------------------------------------------------------
dnl  specify whether apache should be configured through it's conf.d directory
dnl-----------------------------------------------------------------------------
AC_SUBST(CONFIGURE_APACHE)

AC_ARG_WITH([configure-apache],
            AC_HELP_STRING([--with-configure-apache],
               [specify whether apache should be configured for Campcaster
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
                   [deploy Campcaster under the specified docroot (/var/www)]),
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
dnl  specify the audio output device for the scheduler
dnl-----------------------------------------------------------------------------
AC_SUBST(STATION_AUDIO_OUT)

AC_ARG_WITH([station-audio-out],
            AC_HELP_STRING([--with-station-audio-out],
               [use the specified audio output device for the station,
                either ALSA or OSS (default)]),
            [STATION_AUDIO_OUT=${withval}], [STATION_AUDIO_OUT=default])

AC_MSG_RESULT([using audio output device for the station: ${STATION_AUDIO_OUT}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device for the studio
dnl-----------------------------------------------------------------------------
AC_SUBST(STUDIO_AUDIO_OUT)

AC_ARG_WITH([studio-audio-out],
            AC_HELP_STRING([--with-studio-audio-out],
               [use the specified audio output device for the studio,
                either ALSA or OSS (default)]),
            [STUDIO_AUDIO_OUT=${withval}], [STUDIO_AUDIO_OUT=default])

AC_MSG_RESULT([using audio output device for studio: ${STUDIO_AUDIO_OUT}])


dnl-----------------------------------------------------------------------------
dnl  specify the audio output device for the studio for cueing
dnl-----------------------------------------------------------------------------
AC_SUBST(STUDIO_AUDIO_CUE)

AC_ARG_WITH([studio-audio-cue],
            AC_HELP_STRING([--with-studio-audio-cue],
               [use the specified audio cue device for the studio,
                either ALSA or OSS (default)]),
            [STUDIO_AUDIO_CUE=${withval}], [STUDIO_AUDIO_CUE=default])

AC_MSG_RESULT([using audio cue device for studio: ${STUDIO_AUDIO_CUE}])


dnl-----------------------------------------------------------------------------
dnl  set up the individual components
dnl
dnl unfortunately for both AC_CONFIG_COMMANDS and AC_CONFIG_SUBDIRS, the
dnl directories have to be literally included, so we can't use any
dnl fancy variables to avoid duplication of hard-coded values :(
dnl-----------------------------------------------------------------------------

dnl set up the alib module
AC_CONFIG_COMMANDS([../src/modules/alib/tmp/configure],
                   [../src/modules/alib/bin/autogen.sh])

dnl set up the archive server module
AC_CONFIG_COMMANDS([../src/modules/archiveServer/tmp/configure],
                   [../src/modules/archiveServer/bin/autogen.sh])

dnl set up the getid3 module
AC_CONFIG_COMMANDS([../src/modules/getid3/tmp/configure],
                   [../src/modules/getid3/bin/autogen.sh])

dnl set up the htmlUI module
AC_CONFIG_COMMANDS([../src/modules/htmlUI/tmp/configure],
                   [../src/modules/htmlUI/bin/autogen.sh])

dnl set up the storage admin module
AC_CONFIG_COMMANDS([../src/modules/storageAdmin/tmp/configure],
                   [../src/modules/storageAdmin/bin/autogen.sh])

dnl set up the storage server module
AC_CONFIG_COMMANDS([../src/modules/storageServer/tmp/configure],
                   [../src/modules/storageServer/bin/autogen.sh])

dnl set up the core module
AC_CONFIG_COMMANDS([../src/modules/core/tmp/configure],
                   [../src/modules/core/bin/autogen.sh])

dnl set up the authentication module
AC_CONFIG_COMMANDS([../src/modules/authentication/tmp/configure],
                   [../src/modules/authentication/bin/autogen.sh])

dnl set up the db module
AC_CONFIG_COMMANDS([../src/modules/db/tmp/configure],
                   [../src/modules/db/bin/autogen.sh])

dnl set up the storage client module
AC_CONFIG_COMMANDS([../src/modules/storageClient/tmp/configure],
                   [../src/modules/storageClient/bin/autogen.sh])

dnl set up the playlist executor module
AC_CONFIG_COMMANDS([../src/modules/playlistExecutor/tmp/configure],
                   [../src/modules/playlistExecutor/bin/autogen.sh])

dnl set up the event scheduler module
AC_CONFIG_COMMANDS([../src/modules/eventScheduler/tmp/configure],
                   [../src/modules/eventScheduler/bin/autogen.sh])

dnl set up the scheduler client module
AC_CONFIG_COMMANDS([../src/modules/schedulerClient/tmp/configure],
                   [../src/modules/schedulerClient/bin/autogen.sh])

dnl set up the  widgets module
AC_CONFIG_COMMANDS([../src/modules/widgets/tmp/configure],
                   [../src/modules/widgets/bin/autogen.sh])

dnl set up the scheduler product
AC_CONFIG_COMMANDS([../src/products/scheduler/tmp/configure],
                   [../src/products/scheduler/bin/autogen.sh])

dnl set up the gLiveSupport product
AC_CONFIG_COMMANDS([../src/products/gLiveSupport/tmp/configure],
                   [../src/products/gLiveSupport/bin/autogen.sh])



dnl display status info on what libraries will get compiled

AC_MSG_NOTICE(
[compiling the following external libraries that are needed
by Campcaster:

    libodbc++ 0.2.5
    taglib 1.5
    xmlrpc++ 2004-07-13

using the following configuration settings:

    hostname:                               ${HOSTNAME}
    apache group:                           ${APACHE_GROUP}
    www document root:                      ${WWW_DOCROOT}
    www port:                               ${WWW_PORT}
    scheduler port:                         ${SCHEDULER_PORT}
    database server:                        ${DB_SERVER}
    database name:                          ${DATABASE}
    database user:                          ${DB_USER}
    database user password:                 ${DB_PASSWORD}
    station audio output device:            ${STATION_AUDIO_OUT}
    studio audio output device:             ${STUDIO_AUDIO_OUT}
    studio audio cue device:                ${STUDIO_AUDIO_CUE}
    creating Campcaster database:           ${CREATE_LS_DATABASE}
    creating ODBC data source:              ${CREATE_ODBC_DATA_SOURCE}
    initialize Campcaster database:         ${INIT_LS_DATABASE}
    configuring apache:                     ${CONFIGURE_APACHE}

])


AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)


AC_OUTPUT()

