/**************************************************************************
 * dbc.h: A barebones design-by-contract framework for C and C++
 * Copyright (C) 2001,2002,2009 Kai Vehmanen
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
 **************************************************************************/

#ifndef INCLUDED_DBC_H
#define INCLUDED_DBC_H

#ifdef ENABLE_DBC

#include <stdio.h>

void kvu_dbc_report_failure(const char *action, const char* expr, const char* file, const char* func, int lineno);

/**
 * Declare a boolean precondition assertion. A warning is emitted
 * if expr is false.
 */
#define DBC_REQUIRE(expr) \
   (expr) ? (void)(0) :	kvu_dbc_report_failure("DBC_REQUIRE", #expr, __FILE__, __KVU_FUNCTION,  __LINE__)

/**
 * Declare a boolean postcondition assertion. A warning is emitted
 * if expr is false.
 */
#define DBC_ENSURE(expr) \
   (expr) ? (void)(0) :	kvu_dbc_report_failure("DBC_ENSURE", #expr, __FILE__, __KVU_FUNCTION,  __LINE__)

/**
 * Declare a boolean invariant assertion. A warning is emitted
 * if expr is false.
 */
#define DBC_CHECK(expr)	\
   (expr) ? (void)(0) :	kvu_dbc_report_failure("DBC_CHECK", #expr, __FILE__, __KVU_FUNCTION,  __LINE__)

/**
 * Emits warning if macro is ever executed.
 */
#define DBC_NEVER_REACHED(expr) \
   kvu_dbc_report_failure("DBC_NEVER_REACHED", NULL, __FILE__, __KVU_FUNCTION,  __LINE__)

/**
 * A helper macro for declaring variables, etc that will be
 * only defined when ENABLE_DBC is enabled.
 */
#define DBC_DECLARE(expr)               expr

#else /* DBC DISABLED --> */

/* When ENABLE_DBC is not defined, the macros expand to no-op
   statements */

#define DBC_REQUIRE(expr)		((void) 0)
#define DBC_ENSURE(expr)		((void) 0)
#define DBC_CHECK(expr)		        ((void) 0)
#define DBC_NEVER_REACHED(x)            ((void) 0)
#define DBC_DECLARE(expr)               ((void) 0)

#endif /* <-- DBC DISABLED */

/* Adapted from glibc 2.9 assert.h (LGPL 2.1)
  
   Version 2.4 and later of GCC define a magical variable `__PRETTY_FUNCTION__'
   which contains the name of the function currently being defined.
   This is broken in G++ before version 2.6.
   C9x has a similar variable called __func__, but prefer the GCC one since
   it demangles C++ function names.  */

#undef __KVU_FUNCTION

#if defined HAVE_FEATURES_H
#    include <features.h>
#endif

#if defined __GNUC_PREREQ
/*   1.  gcc pretty-printer */
#    if __GNUC_PREREQ(2, 6)
#        define __KVU_FUNCTION  __PRETTY_FUNCTION__
#    endif
#elif defined __STDC_VERSION__ && __STDC_VERSION__ >= 199901L
/*   2. check for C99 (not defined at all in C90, nor in C++1998) */
#    define __KVU_FUNCTION	__func__
#elif defined __GNUC__ && __GNUC__ >= 2
/*   3. as per http://gcc.gnu.org/onlinedocs/gcc/Function-Names.html */
#    define __KVU_FUNCTION       __FUNCTION__
#else
/*   4. not supported */
#    define __KVU_FUNCTION	"__func__"
#endif

#endif /* INCLUDED_DBC_H */
