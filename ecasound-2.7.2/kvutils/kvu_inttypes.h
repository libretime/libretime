/**************************************************************************
 * kvu_inttypes.h: Ensure that C99 guaranteed length integer types are
 *                 defined in the current environment
 * Copyright (C) 2003 Kai Vehmanen
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

#ifndef INCLUDED_KVU_INTTYPES_H
#define INCLUDED_KVU_INTTYPES_H

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

/* 1. stdint.h: the C99 standard */
#ifdef HAVE_STDINT_H
#include <stdint.h>
#else

/* 2.1 inttypes.h: C99 and Single Unix Specification v2 */
#if HAVE_INTTYPES_H
#include <inttypes.h>

/* 2.2 sys/types.h: POSIX */
#elif HAVE_SYS_TYPES_H
#include <sys/types.h>
/* Cygwin32 doesn't define all types */
#ifdef __CYGWIN__
typedef u_int8_t uint8_t;
typedef u_int16_t uint16_t;
typedef u_int32_t uint32_t;
#endif

/* 2.3 fallback to x86 defaults */
#else
typedef signed char int8_t;
typedef unsigned char uint8_t;
typedef signed short int int16_t;
typedef unsigned short int uint16_t;
typedef signed int int32_t;
typedef unsigned int uint32_t;

#endif /* 2.x */
#endif /* 1.x */

#endif /* INCLUDED_KVU_INTTYPES_H */
