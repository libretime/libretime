// ------------------------------------------------------------------------
// kvu_debug.cpp: Debug helper functions
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

// References
//  - glibc manual (section "33.1 Backtraces"
//    http://www.gnu.org/software/libc/manual/html_node/index.html#toc_Debugging-Support


#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <iostream>

#ifdef HAVE_FEATURES_H
#include <features.h>
#endif

using namespace std;

/* backtrace is an GNU glibc extension and it was 
 * first added to glibc 2.1 */
#if defined(__GLIBC_PREREQ) 
#if __GLIBC_PREREQ(2,1) && defined(HAVE_EXECINFO_H)

#include <execinfo.h>
#include <stdio.h>
#include <stdlib.h>

void kvu_print_backtrace_stderr(void)
{
  const int NUM_STACK_FRAMES = 10;
  void *array[NUM_STACK_FRAMES];
  size_t size;
  char **strings;
  size_t i;

  size = backtrace (array, NUM_STACK_FRAMES);
  strings = backtrace_symbols (array, size);

  cerr << 
"-----------------------------------------------------------------------"
       << endl
       << "Function call backtrace ("
       << size 
       << " frames):" << endl;

  for (i = 0; i < size; i++)
    cerr << " " << i << ": " << strings[i] << endl;

  free (strings);
  cerr << 
"-----------------------------------------------------------------------"
       << endl;
}

#endif

#else

/* non-glibc case */

void kvu_print_backtrace_stderr(void)
{
  cout << "ERROR (kvu_print_backtrace_stderr): backtrace printing only supported with glibc" << endl;
}

#endif
