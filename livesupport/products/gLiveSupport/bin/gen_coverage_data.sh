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
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/bin/gen_coverage_data.sh,v $
#-------------------------------------------------------------------------------                                                                                
#-------------------------------------------------------------------------------
# This script generates code coverage data for the module
#-------------------------------------------------------------------------------
module="LiveSupport GUI"

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd; cd -`
bindir=$basedir/bin
docdir=$basedir/doc
tmpdir=$basedir/tmp

usrdir=`cd $basedir/../../usr; pwd; cd -`

coverage_report_dir=$docdir/coverage

raw_coverage_file=$tmpdir/raw_coverage.info
coverage_file=$tmpdir/coverage.info

lcov=$usrdir/bin/lcov
genhtml=$usrdir/bin/genhtml


cd $basedir

#-------------------------------------------------------------------------------
# Re-configure with covarege collection enabled, compile and run the tests
#-------------------------------------------------------------------------------
$bindir/autogen.sh --enable-coverage
make clean
make check

#-------------------------------------------------------------------------------
# Generate some symlinks so that the sources are visible from tmpdir
#-------------------------------------------------------------------------------
ln -s $basedir/include $tmpdir/include
ln -s $basedir/src $tmpdir/src


#-------------------------------------------------------------------------------
# Use lcov to generate an HTML report on the coverage data
#-------------------------------------------------------------------------------
$lcov -d $tmpdir -c > $raw_coverage_file
$lcov -e $raw_coverage_file "$tmpdir/*" > $coverage_file
rm -rf $coverage_report_dir
mkdir -p $coverage_report_dir
$genhtml -t "$module" -o $coverage_report_dir $coverage_file


#-------------------------------------------------------------------------------
# Clean up
#-------------------------------------------------------------------------------
rm -f $tmpdir/include
rm -f $tmpdir/src
