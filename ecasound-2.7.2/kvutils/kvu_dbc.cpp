/**************************************************************************
 * dbc.h: A barebones design-by-contract framework for C and C++
 * Copyright (C) 2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include "kvu_dbc.h"
#include "kvu_debug.h"

#ifdef ENABLE_DBC

#include <cstdio>

void kvu_dbc_report_failure(const char *action, const char* expr, const char* file, const char* func, int lineno)
{

#ifndef NDEBUG
  kvu_print_backtrace_stderr();
#endif

  std::fprintf(stderr, 
	       "Warning: type %s soft-assert '%s' failed at\n -> %s:%d [%s]\n", action, expr, file, lineno, func);
}

#endif
