#!/bin/bash
#-------------------------------------------------------------------------------#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the Campcaster project.
#   http://campcaster.campware.org/
#   To report bugs, send an e-mail to bugs@campware.org
#
#   Campcaster is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Campcaster is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU General Public License for more details.
#
#   You should have received a copy of the GNU General Public License
#   along with Campcaster; if not, write to the Free Software
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $URL$
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  System V runlevel style invoke script for the Campcaster Scheduler
#-------------------------------------------------------------------------------


#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
libdir=$basedir/lib
vardir=$basedir/var/Campcaster/scheduler/var


#-------------------------------------------------------------------------------
#  Set up the environment
#-------------------------------------------------------------------------------
export LD_LIBRARY_PATH=$libdir:$LD_LIBRARY_PATH
export GST_REGISTRY=$etcdir/gst-registry.xml
scheduler_exe=$bindir/campcaster-scheduler

if [ -f ~/.campcaster/campcaster-scheduler.xml ]; then
    config_file=~/.campcaster/campcaster-scheduler.xml
elif [ -f $etcdir/campcaster-scheduler.xml ]; then
    config_file=$etcdir/campcaster-scheduler.xml
else
    echo "Can't find configuration file.";
fi

mode=$1


#-------------------------------------------------------------------------------
#  Do what the user asks us to do
#-------------------------------------------------------------------------------
case "$mode" in
    'start')
        echo "Starting the Campcaster scheduler..."
        $scheduler_exe -c $config_file start
        sleep 2
        ;;

    'stop')
        echo "Stopping the Campcaster scheduler..."
        $scheduler_exe -c $config_file stop
        sleep 2
        ;;

    'restart')
        echo "Stopping the Campcaster scheduler..."
        $scheduler_exe -c $config_file stop
        sleep 2
        echo "Starting the Campcaster scheduler..."
        $scheduler_exe -c $config_file start
        sleep 2
        ;;

    'status')
        echo "Checking Campcaster scheduler status..."
        $scheduler_exe -c $config_file status
        ;;

    'install')
        echo "Installing Campcaster scheduler database tables..."
        BASE_DIR=$basedir php $vardir/install/install.php -c $config_file
        ;;

    'uninstall')
        echo "Uninstalling Campcaster scheduler database tables..."
        BASE_DIR=$basedir php $vardir/install/uninstall.php -c $config_file
        ;;

    'kill')
        echo "Killing all Campcaster scheduler processes..."
        killall campcaster-scheduler
        sleep 2
        killall -9 campcaster-scheduler
        ;;

    *)
        echo "Campcaster scheduler System V runlevel init script."
        echo ""
        echo "Usage:"
        echo "  $0 start|stop|restart|status|install|uninstall|kill"
        echo ""

esac

