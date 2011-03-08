// ------------------------------------------------------------------------
// eca-chainsetup_test.h: Unit test for ECA_CHAINSETUP
// Copyright (C) 2002,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
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

#include <string>

#include "eca-chainsetup.h"
#include "kvu_numtostr.h"

#include "eca-logger.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_CHAINSETUP.
 *
 * FIXME: implementation not ready
 */
class ECA_CHAINSETUP_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for ECA_CHAINSETUP"); }
  virtual void do_run(void);

public:

  virtual ~ECA_CHAINSETUP_TEST(void) { }

private:

  void do_run_save_and_restore(void);

};

void ECA_CHAINSETUP_TEST::do_run(void)
{
  do_run_save_and_restore();
}

void ECA_CHAINSETUP_TEST::do_run_save_and_restore(void)
{
  /**
   * FIXME:
   * - create simple setup with multiple instance of all basic
   *   object types and operations
   * - save to temp .ecs
   * - clear chainseup
   * - load saved .ecs
   * - verify chainsetup integrity and contents
   * - use -y to set offsets for audio objects; verify after reload
   * - save and test again (to get the whole: 'manual definition ->
   *   save -> load -> save -> load' cycle)
   */
}
