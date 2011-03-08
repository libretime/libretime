// ------------------------------------------------------------------------
// eca-control-interface_tester.cpp: Runs a set of ECI C++ unit tests.
// Copyright (C) 2002,2009 Kai Vehmanen
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

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "eca-control-interface.h"

/* FIXME: cannot be run on a clean-build as ecasound is not yet
 *        installed */

/* --------------------------------------------------------------------- 
 * Options
 */

#define VERBOSE

/* --------------------------------------------------------------------- 
 * Test util macros
 */

#ifdef VERBOSE
#define ECA_TEST_ENTRY()   printf("%s:%d - Test started", __FILE__, __LINE__)
#define ECA_TEST_SUCCESS() printf("\n%s:%d - Test passed\n", __FILE__, __LINE__); return 0
#define ECA_TEST_FAIL(x,y) printf("\n%s:%d - Test failed: \"%s\"\n", __FILE__, __LINE__, y); return x
#define ECA_TEST_CASE()    printf("."); fflush(stdout)
#define ECA_TEST_TRACE(x)  do { x; } while(0)
#else
#define ECA_TEST_ENTRY()   ((void) 0)
#define ECA_TEST_SUCCESS() return 0
#define ECA_TEST_FAIL(x,y) return x
#define ECA_TEST_CASE()    ((void) 0)
#define ECA_TEST_TRACE(x)  do { ; } while(0)
#endif

/* --------------------------------------------------------------------- 
 * Type definitions
 */

typedef int (*eci_test_t)(void);

/* --------------------------------------------------------------------- 
 * Test case declarations
 */

static int eci_test_1(void);

static eci_test_t eci_funcs[] = { 
  eci_test_1, 
  NULL 
};

/* --------------------------------------------------------------------- 
 * Funtion definitions
 */

int main(int argc, char *argv[])
{
  int n, failed = 0;
  
#ifdef NDEBUG
  const char *binpath = "ECASOUND=" TEST_TOP_BUILDDIR "/ecasound/ecasound";
#else
  const char *binpath = "ECASOUND=" TEST_TOP_BUILDDIR "/ecasound/ecasound_debug";
#endif

  ECA_TEST_TRACE(printf("Running %s tests with %s.\n", __FILE__, binpath));

  putenv((char *)binpath);

  for(n = 0; eci_funcs[n] != NULL; n++) {
    int ret = eci_funcs[n]();
    if (ret != 0) {
      ++failed;
    }
  }

  return failed;
}

static int eci_test_1(void)
{
  ECA_CONTROL_INTERFACE handle;
  int count;

  ECA_TEST_ENTRY();

  handle.command("cs-remove");
  handle.command("cs-status");
  handle.command("cs-list");
  count = handle.last_string_list().size();
  if (count != 0) { ECA_TEST_FAIL(1, "cs-list count not zero"); }

  handle.command("cs-add test_cs2");
  handle.command("cs-list");
  count = handle.last_string_list().size();
  if (count != 1) { ECA_TEST_FAIL(2, "cs-list count not one"); }

  if (handle.last_string_list()[0] != "test_cs2") {
    ECA_TEST_FAIL(3, "cs name does not match");
  }

  ECA_TEST_SUCCESS();
}
