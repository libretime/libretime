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
# This script generates code coverage data for all modules
#-------------------------------------------------------------------------------
module="Campcaster"

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=$basedir/bin
docdir=$basedir/doc
srcdir=$basedir/src
tmpdir=$basedir/tmp
modules_dir=$srcdir/modules
products_dir=$srcdir/products

usrdir=`cd $basedir/usr; pwd;`

coverage_report_dir=$docdir/coverage

core_coverage_file=$modules_dir/core/tmp/coverage.info
authentication_coverage_file=$modules_dir/authentication/tmp/coverage.info
db_coverage_file=$modules_dir/db/tmp/coverage.info
storageClient_coverage_file=$modules_dir/storageClient/tmp/coverage.info
eventScheduler_coverage_file=$modules_dir/eventScheduler/tmp/coverage.info
schedulerClient_coverage_file=$modules_dir/schedulerClient/tmp/coverage.info
playlistExecutor_coverage_file=$modules_dir/playlistExecutor/tmp/coverage.info
scheduler_coverage_file=$products_dir/scheduler/tmp/coverage.info

coverage_file=$tmpdir/coverage.info

lcov=$usrdir/bin/lcov
genhtml=$usrdir/bin/genhtml


#-------------------------------------------------------------------------------
# Execute the coverage tests one by one
#-------------------------------------------------------------------------------
$modules_dir/core/bin/gen_coverage_data.sh
$modules_dir/authentication/bin/gen_coverage_data.sh
$modules_dir/db/bin/gen_coverage_data.sh
$modules_dir/storageClient/bin/gen_coverage_data.sh
$modules_dir/eventScheduler/bin/gen_coverage_data.sh
$modules_dir/schedulerClient/bin/gen_coverage_data.sh
$modules_dir/playlistExecutor/bin/gen_coverage_data.sh
$products_dir/scheduler/bin/gen_coverage_data.sh


#-------------------------------------------------------------------------------
# Gather all the coverage information into one file
# remove references to the tmp directories, and replace them with the module
# directories themselves. this way the source files are found easlity by lcov
#-------------------------------------------------------------------------------
echo "" > $coverage_file
cat $core_coverage_file | sed -e "s/core\/tmp\//core\//g" >> $coverage_file
cat $authentication_coverage_file | sed -e "s/authentication\/tmp\//authentication\//g" >> $coverage_file
cat $db_coverage_file | sed -e "s/db\/tmp\//db\//g" >> $coverage_file
cat $storageClient_coverage_file | sed -e "s/storageClient\/tmp\//storageClient\//g" >> $coverage_file
cat $eventScheduler_coverage_file | sed -e "s/eventScheduler\/tmp\//eventScheduler\//g" >> $coverage_file
cat $schedulerClient_coverage_file | sed -e "s/schedulerClient\/tmp\//schedulerClient\//g" >> $coverage_file
cat $playlistExecutor_coverage_file | sed -e "s/playlistExecutor\/tmp\//playlistExecutor\//g" >> $coverage_file
cat $scheduler_coverage_file | sed -e "s/scheduler\/tmp\//scheduler\//g" >> $coverage_file

rm -rf $coverage_report_dir
mkdir -p $coverage_report_dir
$genhtml -t "$module" -o $coverage_report_dir $coverage_file

