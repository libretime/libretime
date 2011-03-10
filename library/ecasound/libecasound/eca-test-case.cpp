// ------------------------------------------------------------------------
// eca-test-case.cpp: Abstract interface for implementing 
//                    test cases for component testing.
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

#include <iostream>
#include "kvu_numtostr.h"
#include "kvu_dbc.h"

#include "eca-test-case.h"

using namespace std;

ECA_TEST_CASE::ECA_TEST_CASE(void)
{
  success_rep = false;
}

ECA_TEST_CASE::~ECA_TEST_CASE(void)
{
}

void ECA_TEST_CASE::run_common_before(void)
{
  failures_rep.clear();
  success_rep = false;
  DBC_CHECK(failures_rep.size() == 0);
}

void ECA_TEST_CASE::run_common_after(void)
{
  if (failures_rep.size() > 0) {
    success_rep = false;
  }
  else {
    success_rep = true;
  }
}

/**
 * Runs the test case.
 */
void ECA_TEST_CASE::run(void)
{
  run_common_before();

  /* actual test implemention defined in a subclass */
  do_run();

  run_common_after();
}

/**
 * Runs the test cases passing a name argument.
 */
void ECA_TEST_CASE::run(const std::string &name)
{
  run_common_before();

  /* actual test implemention defined in a subclass */
  do_run(name);

  run_common_after();
}

/**
 * Returns the test case name.
 */
string ECA_TEST_CASE::name(void) const
{
  return do_name();
}

/**
 * Whether test was run succesfully.
 */
bool ECA_TEST_CASE::success(void) const
{
  return success_rep;
}

/**
 * Returns a list of string describing all
 * failed assertations that occured during
 * testing.
 */
const std::list<std::string>& ECA_TEST_CASE::failures(void) const
{
  return failures_rep;
}

void ECA_TEST_CASE::do_run(const std::string& name)
{
  do_run();
}

/**
 * Reports a failed assertions.
 *
 * @param filename filename where failure occured
 * @param lineno line number 
 * @param description what kind of failure
 *
 * @see ECA_TEST_FAILURE
 */
void ECA_TEST_CASE::report_failure(const string& filename, int lineno, const string& description)
{
  string failure (filename + ":" + kvu_numtostr(lineno) + " " + description);
  cerr << failure << endl;
  failures_rep.push_back(filename + ":" + kvu_numtostr(lineno) + 
			 " " + description);
}
