#-------------------------------------------------------------------------------
#   Storage - file storage component
#   Copyright (c) 2004 Media Development Loan Fund
#
#   This file is part of the LiveSupport project.
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
#   Author   : $Author: tomas $
#   Version  : $Revision: 1.2 $
#   Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/storageServer/Attic/Makefile,v $
#
#   @configure_input@
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#   General command definitions
#-------------------------------------------------------------------------------
MKDIR   = mkdir -p
RM      = rm -f
RMDIR   = rm -rf
DOXYGEN = doxygen

#-------------------------------------------------------------------------------
#   Misc
#-------------------------------------------------------------------------------

MODULE_NAME = storageServer
TAR_C       = tar -cj --exclude CVS --exclude '*~' -C ${BASE_DIR} -f
DIST_EXT    = .tgz
DATE        = `date +%y%m%d`

#-------------------------------------------------------------------------------
#   Basic directory and file definitions
#-------------------------------------------------------------------------------
#BASE_DIR    = @builddir@
BASE_DIR    = .
DOC_DIR     = ${BASE_DIR}/doc
DOXYGEN_DIR = ${DOC_DIR}/doxygen
ETC_DIR     = ${BASE_DIR}/etc
INCLUDE_DIR = ${BASE_DIR}/include
LIB_DIR     = ${BASE_DIR}/lib
SRC_DIR     = ${BASE_DIR}/src
TMP_DIR     = ${BASE_DIR}/tmp

USR_DIR         = ${BASE_DIR}/../../usr
USR_INCLUDE_DIR = ${USR_DIR}/include
USR_LIB_DIR     = ${USR_DIR}/lib

DOXYGEN_CONFIG  = ${ETC_DIR}/doxygen.config

HTTP_GROUP   = apache

PHP_DIR 	= ${BASE_DIR}/var
INSTALL_DIR = ${PHP_DIR}/install
STOR_DIR 	= ${PHP_DIR}/stor
ACCESS_DIR 	= ${PHP_DIR}/access
BUFF_DIR 	= ${STOR_DIR}/buffer
TEST_RUNNER 	= ${PHP_DIR}/xmlrpc/testRunner.sh

#-------------------------------------------------------------------------------
#  	Configuration parameters
#-------------------------------------------------------------------------------
#CPPFLAGS = @CPPFLAGS@
#CXXFLAGS = @CXXFLAGS@ @DEFS@ -I${USR_INCLUDE_DIR} -I${INCLUDE_DIR} -I${TMP_DIR}\
#                             -pedantic -Wall
#LDFLAGS  = @LDFLAGS@ -L${USR_LIB_DIR} -L${LIB_DIR}


#-------------------------------------------------------------------------------
#	Dependencies
#-------------------------------------------------------------------------------
#HELLO_LIB_OBJS = ${TMP_DIR}/Hello.o
#TEST_RUNNER_OBJS = ${TMP_DIR}/HelloTest.o ${TMP_DIR}/TestRunner.o


#-------------------------------------------------------------------------------
#   Targets
#-------------------------------------------------------------------------------
.PHONY: all dir_setup doc clean docclean depclean distclean dist db_init db_clean

all: dir_setup db_init

dir_setup: ${DOXYGEN_DIR} ${STOR_DIR} ${ACCESS_DIR}

doc:
	${DOXYGEN} ${DOXYGEN_CONFIG}

clean: db_clean

docclean:
	${RMDIR} ${DOXYGEN_DIR}/html

depclean: clean

dist:
	${TAR_C} ${MODULE_NAME}${DATE}${DIST_EXT} *

distclean: clean docclean

check: all ${TEST_RUNNER}
	${TEST_RUNNER}

#-------------------------------------------------------------------------------
#   Specific targets
#-------------------------------------------------------------------------------
db_init:
	cd var/install; php -q install.php

db_clean:
	cd var/install; php -q uninstall.php

${TMP_DIR}:
	${MKDIR} ${TMP_DIR}

${DOXYGEN_DIR}:
	${MKDIR} ${DOXYGEN_DIR}

${STOR_DIR}:
	${MKDIR} ${STOR_DIR}
	chown .${HTTP_GROUP} ${STOR_DIR}
	chmod g+ws ${STOR_DIR}

${ACCESS_DIR}:
	${MKDIR} ${ACCESS_DIR}
	chown .${HTTP_GROUP} ${ACCESS_DIR}
	chmod g+ws ${ACCESS_DIR}

${BUFF_DIR}:
	${MKDIR} ${BUFF_DIR}
	chmod g+w ${BUFF_DIR}

${TEST_RUNNER}:

#-------------------------------------------------------------------------------
#   Pattern rules
#-------------------------------------------------------------------------------
#${TMP_DIR}/%.o : ${SRC_DIR}/%.cxx
#	${CXX} ${CPPFLAGS} ${CXXFLAGS} -c -o $@ $<

