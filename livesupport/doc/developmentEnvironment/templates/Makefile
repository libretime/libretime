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
#   Author   : $Author$
#   Version  : $Revision$
#   Location : $Source$
#
#-------------------------------------------------------------------------------

#-------------------------------------------------------------------------------
#   General command definitions
#-------------------------------------------------------------------------------
MKDIR   = mkdir -p
RM      = rm -f
RMDIR   = rm -rf
DOXYGEN = doxygen


#-------------------------------------------------------------------------------
#   Basic directory and file definitions
#-------------------------------------------------------------------------------
BASE_DIR    = .
DOC_DIR     = ${BASE_DIR}/doc
DOXYGEN_DIR = ${DOC_DIR}/doxygen
ETC_DIR     = ${BASE_DIR}/etc
SRC_DIR     = ${BASE_DIR}/src
TMP_DIR     = ${BASE_DIR}/tmp

VPATH    = ${SRC_DIR}

MODULES_DIR = ${BASE_DIR}/../../modules

HELLOLIB_DIR         = ${MODULES_DIR}/hello
HELLOLIB_INCLUDE_DIR = ${HELLOLIB_DIR}/include
HELLOLIB_LIB_DIR     = ${HELLOLIB_DIR}/lib
HELLOLIB_LIB         = livesupport_hello

HELLO_EXE = ${TMP_DIR}/hello

DOXYGEN_CONFIG = ${ETC_DIR}/doxygen.config


#-------------------------------------------------------------------------------
#  	Configuration parameters
#-------------------------------------------------------------------------------
CPPFLAGS = 
CXXFLAGS = -pedantic -Wall \
           -I${TMP_DIR} -I${HELLOLIB_INCLUDE_DIR}
LDFLAGS  = -L${HELLOLIB_LIB_DIR}


#-------------------------------------------------------------------------------
#	Dependencies
#-------------------------------------------------------------------------------
HELLO_EXE_OBJS = ${TMP_DIR}/main.o


#-------------------------------------------------------------------------------
#   Targets
#-------------------------------------------------------------------------------
.PHONY: all dir_setup doc clean docclean depclean distclean

all: ${HELLOLIB_LIB} dir_setup ${HELLO_EXE}

dir_setup: ${TMP_DIR} ${DOXYGEN_DIR}

doc:
	${DOXYGEN} ${DOXYGEN_CONFIG}

clean:
	${RM} ${HELLO_EXE_OBJS} ${HELLO_EXE}

docclean:
	${RMDIR} ${DOXYGEN_DIR}/html

depclean: clean
	${MAKE} -C ${HELLOLIB_DIR} clean

distclean: clean docclean
	${RMDIR} ${TMP_DIR}/config* ${TMP_DIR}/autom4te*


#-------------------------------------------------------------------------------
#   Specific targets
#-------------------------------------------------------------------------------
${HELLO_EXE}: ${HELLO_EXE_OBJS}
	${CXX} ${LDFLAGS} -o $@ $^ -l${HELLOLIB_LIB}

${TMP_DIR}:
	${MKDIR} ${TMP_DIR}

${DOXYGEN_DIR}:
	${MKDIR} ${DOXYGEN_DIR}

${HELLOLIB_LIB}:
	${MAKE} -C ${HELLOLIB_DIR} all


#-------------------------------------------------------------------------------
#   Pattern rules
#-------------------------------------------------------------------------------
${TMP_DIR}/%.o : ${SRC_DIR}/%.cxx
	${CXX} ${CPPFLAGS} ${CXXFLAGS} -c -o $@ $<

