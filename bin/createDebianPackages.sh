#!/bin/bash
#-------------------------------------------------------------------------------
#   Copyright (c) 2004 Media Development Loan Fund
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
#  This script creates Debian packages from Campcaster tarballs.
#  To create the tarballs first, see the dist.sh script.
#
#  Invoke as:
#  ./bin/createDebianPackages.sh
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

usrdir=`cd $basedir/usr; pwd;`


#-------------------------------------------------------------------------------
#  Print the usage information for this script.
#-------------------------------------------------------------------------------
printUsage()
{
    echo "Campcaster debian source package creation script";
    echo "parameters";
    echo "";
    echo "  -d, --directory     Place to look for the campcaster source";
    echo "                      tarballs [default: current directory]";
    echo "  -m, --maintainer    The name and e-mail address of the package";
    echo "                      maintainer.";
    echo "  -o, --output-directory      the output directory for the files";
    echo "                              [default: current directory]";
    echo "  -v, --version       The version number of the created packages.";
    echo "                      From package_x.y-z_i386.deb, this is x.y";
    echo "  -h, --help          Print this message and exit.";
    echo "";
}


#-------------------------------------------------------------------------------
#  Process command line parameters
#-------------------------------------------------------------------------------
CMD=${0##*/}

opts=$(getopt -o d:hm:o:v: -l directory:,help,maintainer:,output-directory,version: -n $CMD -- "$@") || exit 1
eval set -- "$opts"
while true; do
    case "$1" in
        -d|--directory)
            directory=$2;
            shift; shift;;
        -h|--help)
            printUsage;
            exit 0;;
        -m|--maintainer)
            maintainer=$2;
            shift; shift;;
        -o|--output-directory)
            outdir=$2;
            shift; shift;;
        -v|--version)
            version=$2;
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

if [ "x$maintainer" == "x" ]; then
    echo "Required parameter maintainer not specified.";
    printUsage;
    exit 1;
fi

if [ "x$version" == "x" ]; then
    echo "Required parameter version not specified.";
    printUsage;
    exit 1;
fi

if [ "x$directory" == "x" ]; then
    directory=`pwd`;
else
    directory=`cd $directory; pwd;`
fi

if [ "x$outdir" == "x" ]; then
    outdir=`pwd`;
else
    outdir=`cd $outdir; pwd;`
fi


echo "Creating Debian source packages for Campcaster.";
echo "";
echo "Using the following parameters:";
echo "";
echo "  tarball directory:     $directory";
echo "  maintainer:            $maintainer";
echo "  package version:       $version";
echo "  output directory:      $outdir";
echo ""


#-------------------------------------------------------------------------------
#  Function to check for the existence of an executable on the PATH
#
#  @param $1 the name of the exectuable
#  @return 0 if the executable exists on the PATH, non-0 otherwise
#-------------------------------------------------------------------------------
check_exe() {
    if [ -x "`which $1 2> /dev/null`" ]; then
        echo "Executable $1 found...";
        return 0;
    else
        echo "Executable $1 not found...";
        return 1;
    fi
}


#-------------------------------------------------------------------------------
#   Check for executables needed by this script
#-------------------------------------------------------------------------------
echo "Checking for tools used by this script...";
check_exe "tar" || exit 1;
check_exe "dpkg-source" || exit 1;
check_exe "sed" || exit 1;


#-------------------------------------------------------------------------------
#   More definitions
#-------------------------------------------------------------------------------
tarball=$directory/campcaster-$version.tar.bz2
tarball_libs=$directory/campcaster-libraries-$version.tar.bz2

if [ ! -f $tarball ]; then
    echo "source tarball $tarball not found in directory $directory";
    exit 1;
fi

if [ ! -f $tarball_libs ]; then
    echo "source tarball $tarball_libs not found in directory $directory";
    exit 1;
fi


packageName=campcaster-$version
packageNameOrig=$packageName.orig
workdir=$tmpdir/debianize

replace_sed_string="s/ls_maintainer/$maintainer/;"


#-------------------------------------------------------------------------------
#   Create the environment
#-------------------------------------------------------------------------------
rm -rf $workdir
mkdir -p $workdir
cd $workdir


#-------------------------------------------------------------------------------
#   Untar the source tarballs
#-------------------------------------------------------------------------------
echo "Extracting source tarballs...";

# untar first, and rename as campcaster-$version.orig
tar xfj $tarball
tar xfj $tarball_libs
mv $packageName $packageNameOrig

# untar again, and leave it as campcaster-$version
tar xfj $tarball
tar xfj $tarball_libs

#-------------------------------------------------------------------------------
#   Debianize the campcaster-$version sources
#-------------------------------------------------------------------------------
echo "Debianizing sources...";

cp -pPR $etcdir/debian $packageName

# customize the control file, with the maintainer name
cat $etcdir/debian/control | sed -e "$replace_sed_string" \
    > $packageName/debian/control

# get rid of the remnants of the subversion system
rm -rf `find $packageName -name .svn -type d`


#-------------------------------------------------------------------------------
#   Create a debianized source package.
#-------------------------------------------------------------------------------
echo "Creating debian source package...";

dpkg-source -b $packageName $packageNameOrig


#-------------------------------------------------------------------------------
#   Copy the resulting files to the target directory
#-------------------------------------------------------------------------------
echo "Moving debian source package files to target directory...";

mv -f campcaster_$version* $outdir


#-------------------------------------------------------------------------------
#   Clean up
#-------------------------------------------------------------------------------
echo "Cleaning up...";

cd $basedir
rm -rf $workdir


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

