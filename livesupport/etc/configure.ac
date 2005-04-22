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
dnl Author   : $Author: maroy $
dnl Version  : $Revision: 1.2 $
dnl Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/etc/configure.ac,v $
dnl-----------------------------------------------------------------------------

dnl-----------------------------------------------------------------------------
dnl NOTE: Run all configure related scripts from the tmp directory of the
dnl project.
dnl This is due to the fact that configure spreads a lot of trash around,
dnl like atom4te cache directories, config.* files, etc. into the directory
dnl it is being run from. We clearly don't want these in our base directory.
dnl-----------------------------------------------------------------------------
AC_INIT(LiveSupport, 0.9, bugs@campware.org)
AC_PREREQ(2.59)
AC_COPYRIGHT([Copyright (c) 2004 Media Development Loan Fund under the GNU GPL])
AC_REVISION($Revision: 1.2 $)

AC_CONFIG_SRCDIR(../products/scheduler/src/main.cxx)

AC_PROG_CXX()

AC_CONFIG_FILES(../Makefile:../etc/Makefile.in)

AC_OUTPUT()
