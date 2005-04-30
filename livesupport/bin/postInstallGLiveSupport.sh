#!/bin/sh
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
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
#   Author   : $Author: maroy $
#   Version  : $Revision: 1.3 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/Attic/postInstallGLiveSupport.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script makes post-installation steps for GLiveSupport.
#
#  Invoke as:
#  ./bin/postInstallGLiveSupport.sh
#
#  To get usage help, try the -h option
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#  Determine directories, files
#-------------------------------------------------------------------------------
reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
etcdir=$basedir/etc
docdir=$basedir/doc
tmpdir=$basedir/tmp


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "GLiveSupport post-install script.";
    echo "parameters";
    echo "";
    echo "  -d, --directory     The installation directory, required.";
    echo "  -H, --host          The fully qualified host name of the system";
    echo "                      [default: guess].";
    echo "  -p, --port          The port of the apache web server [default: 80]"
    echo "  -P, --scheduler-port    The port of the scheduler daemon to install"
    echo "                          [default: 3344]";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:H:hp:P: -l directory:,host:,help,port:,scheduler-port: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            installdir=$2;
            shift; shift;;
        -H|--host)
            hostname=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -p|--port)
            http_port=$2;
            shift; shift;;
        -P|--scheduler-port)
            scheduler_port=$2;
            shift; shift;;
        --)
            shift;
            break;;
        *)
            echo "Unrecognized option $1.";
            printUsage;
            exit 1;
    esac
done

if [ "x$installdir" == "x" ]; then
    echo "Required parameter install directory not specified.";
    printUsage;
    exit 1;
fi

if [ "x$hostname" == "x" ]; then
    hostname=`hostname -f`;
fi

if [ "x$http_port" == "x" ]; then
    http_port=80;
fi

if [ "x$scheduler_port" == "x" ]; then
    scheduler_port=3344;
fi


echo "Making post-install steps for GLiveSupport.";
echo "";
echo "Using the following installation parameters:";
echo "";
echo "  installation directory: $installdir";
echo "  host name:              $hostname";
echo "  web server port:        $http_port";
echo "  scheduler port:         $scheduler_port";
echo ""

#-------------------------------------------------------------------------------
#  The details of installation
#-------------------------------------------------------------------------------
ls_php_host=$hostname
ls_php_port=$http_port
ls_php_urlPrefix=livesupport

ls_alib_xmlRpcPrefix="xmlrpc/xrLocStor.php"
ls_storage_xmlRpcPrefix="xmlrpc/xrLocStor.php"

ls_scheduler_host=$hostname
ls_scheduler_port=$scheduler_port
ls_scheduler_urlPrefix=
ls_scheduler_xmlRpcPrefix=RC2


install_bin=$installdir/bin
install_etc=$installdir/etc
install_lib=$installdir/lib
install_tmp=$installdir/tmp
install_var=$installdir/var


# replace / characters with a \/ sequence, for sed below
# the sed statement is really "s/\//\\\\\//g", but needs escaping because of
# bash, hence the extra '\' characters
installdir_s=`echo $installdir | sed -e "s/\//\\\\\\\\\//g"`
ls_storage_xmlRpcPrefix_s=`echo $ls_storage_xmlRpcPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`
ls_alib_xmlRpcPrefix_s=`echo $ls_alib_xmlRpcPrefix | sed -e "s/\//\\\\\\\\\//g"`
ls_php_urlPrefix_s=`echo $ls_php_urlPrefix | sed -e "s/\//\\\\\\\\\//g"`
ls_scheduler_urlPrefix_s=`echo $ls_scheduler_urlPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`
ls_scheduler_xmlRpcPrefix_s=`echo $ls_scheduler_xmlRpcPrefix | \
                                sed -e "s/\//\\\\\\\\\//g"`

replace_sed_string="s/ls_install_dir/$installdir_s/; \
              s/ls_storageUrlPath/\/$ls_php_urlPrefix_s\/storageServer\/var/; \
              s/ls_php_urlPrefix/$ls_php_urlPrefix_s/; \
              s/ls_storage_xmlRpcPrefix/$ls_storage_xmlRpcPrefix_s/; \
              s/ls_alib_xmlRpcPrefix/$ls_alib_xmlRpcPrefix_s/; \
              s/ls_php_host/$ls_php_host/; \
              s/ls_php_port/$ls_php_port/; \
              s/ls_archiveUrlPath/\/$ls_php_urlPrefix_s\/archiveServer\/var/; \
              s/ls_scheduler_urlPrefix/$ls_scheduler_urlPrefix_s/; \
              s/ls_scheduler_xmlRpcPrefix/$ls_scheduler_xmlRpcPrefix_s/; \
              s/ls_scheduler_host/$ls_scheduler_host/; \
              s/ls_scheduler_port/$ls_scheduler_port/;"



#-------------------------------------------------------------------------------
#  Function to check for the existence of an executable on the PATH
#
#  @param $1 the name of the exectuable
#  @return 0 if the executable exists on the PATH, non-0 otherwise
#-------------------------------------------------------------------------------
check_exe() {
    if [ -x "`which $1 2> /dev/null`" ]; then
        echo "Exectuable $1 found...";
        return 0;
    else
        echo "Exectuable $1 not found...";
        return 1;
    fi
}


#-------------------------------------------------------------------------------
#  Check for required tools
#-------------------------------------------------------------------------------
echo "Checking for required tools..."

check_exe "sed" || exit 1;


#-------------------------------------------------------------------------------
#  Customize the configuration files with the appropriate values
#-------------------------------------------------------------------------------
echo "Customizing configuration files..."

tmp_file=/tmp/livesupport_setup.$$

# customize the GTK- config files, which have hard-coded values in them :(
cat $install_etc/gtk-2.0/gdk-pixbuf.loaders \
    | sed -e "s/^\"\/.*\/usr\/lib/\"$installdir_s\/lib/g" \
    > $tmp_file
mv -f $tmp_file $install_etc/gtk-2.0/gdk-pixbuf.loaders

# customize the pango config files, which have hard-coded values in them :(
pango_rc=$install_etc/pango/pango.rc
echo "[Pango]" > $pango_rc
echo "ModuleFiles=$install_etc/pango/pango.modules" >> $pango_rc
cat $install_etc/pango/pango.modules \
    | sed -e "s/^\/.*\/usr\/lib/$installdir_s\/lib/g" \
    > $tmp_file
mv -f $tmp_file $install_etc/pango/pango.modules

# customize the gLiveSupport config file
cat $install_etc/gLiveSupport.xml.template \
    | sed -e "$replace_sed_string" \
    > $install_etc/gLiveSupport.xml


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

