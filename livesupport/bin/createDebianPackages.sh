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
#   Version  : $Revision: 1.1 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/bin/createDebianPackages.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
#  This script creates Debian packages from LiveSupport tarballs.
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
    echo "LiveSupport debian source package creation script";
    echo "parameters";
    echo "";
    echo "  -d, --directory     Place to look for the livesupport source";
    echo "                      tarballs [default: current directory]";
    echo "  -m, --maintainer    The name and e-mail address of the package";
    echo "                      maintainer.";
    echo "  -o, --output-directory      the output directory for the files";
    echo "                              [default: current directory]";
    echo "  -v, --version       The version number of the created packages.";
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


echo "Creating Debian source packages for LiveSupport.";
echo "";
echo "Using the following parameters:";
echo "";
echo "  tarball directory:         $directory";
echo "  maintainer:                $maintainer";
echo "  package version number:    $version";
echo "  output directory:          $outdir";
echo ""


#-------------------------------------------------------------------------------
#   More definitions
#-------------------------------------------------------------------------------
tarball=$directory/livesupport-$version.tar.bz2
tarball_libs=$directory/livesupport-libraries-$version.tar.bz2

if [ ! -f $tarball ]; then
    echo "source tarball $tarball not found in directory $directory";
    exit 1;
fi

if [ ! -f $tarball_libs ]; then
    echo "source tarball $tarball_libs not found in directory $directory";
    exit 1;
fi


# TODO: maybe read debianVersion as a command line parameter
debianVersion=1
packageName=livesupport-$version
packageNameOrig=$packageName.orig
workdir=$tmpdir/debianize
fullVersion=$version-$debianVersion
diffGz=livesupport_$fullVersion.diff.gz
origTarGz=livesupport_$fullVersion.orig.tar.gz
dsc=livesupport_$fullVersion.dsc

replace_sed_string="s/ls_version/$version/; \
                    s/ls_debianVersion/$debianVersion/; \
                    s/ls_maintainer/$maintainer/;"


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

# untar first, and rename as livesupport-$version.orig
tar xfj $tarball
tar xfj $tarball_libs
mv $packageName $packageNameOrig

# untar again, and leave it as livesupport-$version
tar xfj $tarball
tar xfj $tarball_libs


#-------------------------------------------------------------------------------
#   Debianize the livesupport-$version sources
#-------------------------------------------------------------------------------
echo "Debianizing sources...";

cp -pPR $etcdir/debian $packageName

# get rid of the remnants of the CVS system
rm -rf `find $packageName -name CVS -type d`


#-------------------------------------------------------------------------------
#   Create a debianized source package.
#-------------------------------------------------------------------------------
echo "Creating debian source package...";

diff -Naur $packageNameOrig $packageName | gzip -9 > $diffGz

# create the original source tarball
tar cfz $origTarGz $packageNameOrig

# customize the dsc file
cat $etcdir/livesupport.dsc.template | sed -e "$replace_sed_string" > $dsc

# append with checksums, sizes and source file names
md5sum=`md5sum $origTarGz | cut -d" " -f1`
size=`find . -name $origTarGz -printf "%s"`
echo " $md5sum $size $origTarGz" >> $dsc

md5sum=`md5sum $diffGz | cut -d" " -f1`
size=`find . -name $diffGz -printf "%s"`
echo " $md5sum $size $diffGz" >> $dsc


#-------------------------------------------------------------------------------
#   Copy the resulting files to the target directory
#-------------------------------------------------------------------------------
echo "Moving debian source package files to target directory...";

mv -f $origTarGz $diffGz $dsc $outdir


#-------------------------------------------------------------------------------
#   Clean up
#-------------------------------------------------------------------------------
echo "Cleaning up...";

cd $basedir
#rm -rf $workdir


#-------------------------------------------------------------------------------
#  Say goodbye
#-------------------------------------------------------------------------------
echo "Done."

