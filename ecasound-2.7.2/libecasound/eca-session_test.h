// ------------------------------------------------------------------------
// eca-session_test.h: Unit test for ECA_SESSION
// Copyright (C) 2003,2009 Kai Vehmanen
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

#include "kvu_utils.h" /* kvu_sleep() */

#include "eca-control.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_SESSION.
 *
 * FIXME: implementation not ready
 */
class ECA_SESSION_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for ECA_SESSION"); }
  virtual void do_run(void);

public:

  virtual ~ECA_SESSION_TEST(void) { }

private:

  void do_run_chainsetup_creation(void);

};

void ECA_SESSION_TEST::do_run(void)
{
  cout << "libecasound_tester: eca-session - exception test" << endl;
  COMMAND_LINE cmdline;
  cmdline.push_back("-d:511");
  cmdline.push_back("-i:invalidinvalidinvalid");

  bool exceptionseen = false;

  try {
    ECA_SESSION *esession = new ECA_SESSION(cmdline);
    delete esession;
  }
  catch(...) {
    cerr << "libecasound_tester: catched exception" << endl;
    exceptionseen = true;
  }

  if (exceptionseen != true) {
    ECA_TEST_FAILURE("No exception raised when parsing an invalid chainsetup.");
  }
}
