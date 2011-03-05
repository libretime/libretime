// ------------------------------------------------------------------------
// libecasound_tester.cpp: Runs all tests registered to ECA_TEST_REPOSITORY
// Copyright (C) 2002-2003,2009 Kai Vehmanen
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

#include <iostream>
#include <string>

#include <signal.h>    /* POSIX: various signal functions */
#include <unistd.h>    /* POSIX: sleep() */

#include "eca-logger.h"
#include "eca-test-repository.h"

using namespace std;

/**
 * See also 'ecasound/testsuite/eca_test1.cpp'
 */

int main(int argc, char *argv[]) {

  ECA_LOGGER::instance().set_log_level_bitmask(ECA_LOGGER::errors | ECA_LOGGER::info);

  /**
   * Uncomment to enable libecasound log messages
   */
  //ECA_LOGGER::instance().set_log_level(ECA_LOGGER::user_objects, true);

  ECA_TEST_REPOSITORY& repo = ECA_TEST_REPOSITORY::instance();

#ifdef __FreeBSD__
  {
    /* on FreeBSD, SIGFPEs are not ignored by default */
    struct sigaction blockaction;
    blockaction.sa_flags = 0;
    blockaction.sa_handler = SIG_IGN;   
    sigaction(SIGFPE, &blockaction, 0);
  }
#endif

  cout << "-------------------------------------------------------------------------" << endl;
  cout << "libecasound_tester start:" << endl;
  cout << "-------------------------------------------------------------------------" << endl;

  if (argc > 1)
    /* note: run one test case */
    repo.run(std::string(argv[1]));
  else
    /* note: run all test cases */
    repo.run();

  cout << "-------------------------------------------------------------------------" << endl;
  cout << "libecasound_tester summary:" << endl;
  cout << "-------------------------------------------------------------------------" << endl;
  cout << endl;

  if (repo.success() != true) {
    cout << repo.failures().size() << " failed test cases ";
    cout << "in ECA_TEST_REPOSITORY:" << endl << endl;

    const list<string>& failures = repo.failures();
    list<string>::const_iterator q = failures.begin();
    int n = 1;
    while(q != failures.end()) {
      cout << n++ << ". " << *q << endl;
      ++q;
    }
    
    return -1;
  }
  else {
    cout << "All tests succesful." << endl;
  }

  cout << endl;
  cout << "-------------------------------------------------------------------------";
  cout << endl << endl;

  return 0;
}
