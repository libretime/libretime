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
#   This script generates the nightly builds and logs.
#-------------------------------------------------------------------------------

reldir=`dirname $0`/..
basedir=`cd $reldir; pwd;`
bindir=${basedir}/bin
tmpdir=${basedir}/tmp
logdir=${basedir}/tmp

cd ${basedir}


#-------------------------------------------------------------------------------
#   Update the source from the repository.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlySvnUpdate.log ${logdir}/nightlySvnUpdate.log~
svn update &> ${logdir}/nightlySvnUpdate.log
ls -l ${logdir}/nightlySvnUpdate.log >> ${logdir}/nightlySvnUpdate.log


#-------------------------------------------------------------------------------
#   Generate the documentation.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlyMakeDoxygen.log ${logdir}/nightlyMakeDoxygen.log~
make doxygen &> ${logdir}/nightlyMakeDoxygen.log
ls -l ${logdir}/nightlyMakeDoxygen.log >> ${logdir}/nightlyMakeDoxygen.log


#-------------------------------------------------------------------------------
#   Recompile the code.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlyMakeRecompile.log ${logdir}/nightlyMakeRecompile.log~
make recompile &> ${logdir}/nightlyMakeRecompile.log
ls -l ${logdir}/nightlyMakeRecompile.log >> ${logdir}/nightlyMakeRecompile.log


#-------------------------------------------------------------------------------
#   Run the unit tests.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlyMakeCheck.log ${logdir}/nightlyMakeCheck.log~
make check &> ${logdir}/nightlyMakeCheck.log
ls -l ${logdir}/nightlyMakeCheck.log >> ${logdir}/nightlyMakeCheck.log


#-------------------------------------------------------------------------------
#   Generate the test results report.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlyMakeTestResults.log ${logdir}/nightlyMakeTestResults.log~
make testresults &> ${logdir}/nightlyMakeTestResults.log
ls -l ${logdir}/nightlyMakeTestResults.log >> ${logdir}/nightlyMakeTestResults.log


#-------------------------------------------------------------------------------
#   Generate the flawfinder report.
#-------------------------------------------------------------------------------
mv -f ${logdir}/nightlyMakeFlawfinder.log ${logdir}/nightlyMakeFlawfinder.log~
make flawfinder &> ${logdir}/nightlyMakeFlawfinder.log
ls -l ${logdir}/nightlyMakeFlawfinder.log >> ${logdir}/nightlyMakeFlawfinder.log

