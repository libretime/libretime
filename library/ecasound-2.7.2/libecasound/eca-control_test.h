// ------------------------------------------------------------------------
// eca-control_test.h: Unit test for ECA_CONTROL
// Copyright (C) 2002 Kai Vehmanen
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

#include "eca-session.h"
#include "eca-control.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_CONTROL.
 *
 * FIXME: implementation not ready
 */
class ECA_CONTROL_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for ECA_CONTROL"); }
  virtual void do_run(void);

public:

  virtual ~ECA_CONTROL_TEST(void) { }

private:

  void do_run_chainsetup_creation(void);

};

void ECA_CONTROL_TEST::do_run(void)
{
  cout << "libecasound_tester: eca-control - chainsetup creation stress test" << endl;
  do_run_chainsetup_creation();
}

void ECA_CONTROL_TEST::do_run_chainsetup_creation(void)
{
  ECA_SESSION *esession = new ECA_SESSION();
  ECA_CONTROL *ectrl = new ECA_CONTROL(esession);

  int iterations = 15;

  for(int i = 0; i < iterations; i++) {
    cout << "libecasound_tester: do_run_chainsetup_creation() iteration " 
	 << i + 1 << " of " << iterations << "." << endl;

    ectrl->add_chainsetup("default");
    if (ectrl->is_selected() != true) ECA_TEST_FAILURE("Chainsetup creation failed.");
    ectrl->add_chain("default");
    if (ectrl->selected_chains().size() != 1 ||
	ectrl->selected_chains()[0] != "default") ECA_TEST_FAILURE("Chain addition failed.");
    ectrl->add_audio_input("null");
    ectrl->add_audio_output("null");
    ectrl->add_chain_operator("-ea:100");
    if (ectrl->selected_chain_operator() < 0 ||
	ectrl->selected_chain_operator() > 1) ECA_TEST_FAILURE("Chain operator addition failed.");
    ectrl->connect_chainsetup(0);
    if (ectrl->is_connected() != true) ECA_TEST_FAILURE("Chainsetup connection failed.");
    ectrl->start();
    kvu_sleep(0, 200000000); /* 200ms */
    if (ectrl->is_running() != true) ECA_TEST_FAILURE("Chainsetup start failed.");
    ectrl->stop_on_condition();
    if (ectrl->is_running() == true) ECA_TEST_FAILURE("Chainsetup stop failed.");
    ectrl->disconnect_chainsetup();
    if (ectrl->is_connected() == true) ECA_TEST_FAILURE("Chainsetup disconnection failed.");
    ectrl->remove_chainsetup();
    if (ectrl->chainsetup_names().size() > 0) ECA_TEST_FAILURE("Chainsetup removal failed.");
  }

  delete ectrl;
  delete esession;
}
