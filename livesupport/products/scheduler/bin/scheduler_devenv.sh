#!/bin/sh
#-------------------------------------------------------------------------------#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
#   http://livesupport.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   LiveSupport is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   LiveSupport is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with LiveSupport; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  System V runlevel style invoke script for the LiveSupport Scheduler
#  This script is only used in the LiveSupport development environment
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
libdir=$basedir/lib
tmpdir=$basedir/tmp

usrdir=`cd $basedir/../../usr; pwd;`


#-------------------------------------------------------------------------------
#  Set up the environment
#-------------------------------------------------------------------------------
export LD_LIBRARY_PATH=$usrdir/lib:$LD_LIBRARY_PATH

if [ -x $bindir/scheduler ]; then
    scheduler_exe=$bindir/scheduler
elif [ -x $tmpdir/scheduler ]; then
    scheduler_exe=$tmpdir/scheduler
else
    echo "Can't find scheduler executable.";
fi

if [ -f ~/.livesupport/scheduler.xml ]; then
    config_file=~/.livesupport/scheduler.xml
elif [ -f $etcdir/scheduler.xml ]; then
    config_file=$etcdir/scheduler.xml
else
    echo "Can't find configuration file.";
fi

mode=$1

#echo "Using scheduler:           $scheduler_exe";
#echo "      configuration file:  $config_file";


#-------------------------------------------------------------------------------
#  Do what the user asks us to do
#-------------------------------------------------------------------------------
case "$mode" in
    'start')
        echo "Starting the LiveSupport scheduler..."
        $scheduler_exe -c $config_file start
        sleep 2
        ;;

    'run')
        echo "Running the LiveSupport scheduler..."
        $scheduler_exe -c $config_file --debug start
        sleep 2
        ;;

    'stop')
        echo "Stopping the LiveSupport scheduler..."
        $scheduler_exe -c $config_file stop
        sleep 2
        ;;

    'restart')
        echo "Stopping the LiveSupport scheduler..."
        $scheduler_exe -c $config_file stop
        sleep 2
        echo "Starting the LiveSupport scheduler..."
        $scheduler_exe -c $config_file start
        sleep 2
        ;;

    'status')
        echo "Checking LiveSupport scheduler status..."
        $scheduler_exe -c $config_file status
        ;;

    'install')
        echo "Installing LiveSupport scheduler database tables..."
        $scheduler_exe -c $config_file install
        ;;

    'uninstall')
        echo "Uninstalling LiveSupport scheduler database tables..."
        $scheduler_exe -c $config_file uninstall
        ;;

    'kill')
        echo "Killing all LiveSupport scheduler processes..."
        killall scheduler
        sleep 2
        killall -9 scheduler
        ;;

    *)
        echo "LiveSupport scheduler System V runlevel init script."
        echo ""
        echo "Usage:"
        echo "  $0 start|run|stop|restart|status|install|uninstall|kill"
        echo ""

esac

