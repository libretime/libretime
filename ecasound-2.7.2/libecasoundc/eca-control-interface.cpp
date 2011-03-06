// ------------------------------------------------------------------------
// eca-control-interface.cpp: C++ implementation of the Ecasound
//                            Control Interface
// Copyright (C) 2000,2002,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
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
#include <cassert>

#include <ecasoundc.h>

#include "eca-control-interface.h"

using std::string;
using std::vector;

/**
 * Class constructor.
 */
ECA_CONTROL_INTERFACE::ECA_CONTROL_INTERFACE (void)
{ 
  eci_repp = eci_init_r();
}

/**
 * Desctructor.
 */
ECA_CONTROL_INTERFACE::~ECA_CONTROL_INTERFACE (void)
{
  eci_cleanup_r(eci_repp);
}

/**
 * Checks whether ECI is ready for use.
 */
bool ECA_CONTROL_INTERFACE::ready(void)
{
  return (eci_ready_r(eci_repp) != 0);
}

/**
 * Parse string mode command and act accordingly.
 */
void ECA_CONTROL_INTERFACE::command(const string& cmd)
{
  eci_command_r(eci_repp, cmd.c_str());
}

void ECA_CONTROL_INTERFACE::command_float_arg(const string& cmd, double arg)
{
  eci_command_float_arg_r(eci_repp, cmd.c_str(), arg);
}

const vector<string>& ECA_CONTROL_INTERFACE::last_string_list(void) const
{
  strlist_rep.clear();
  int count = eci_last_string_list_count_r(eci_repp);
  for(int n = 0; n < count; n++) {
    const char* next = eci_last_string_list_item_r(eci_repp, n);
    assert(next != NULL);
    strlist_rep.push_back(string(next));
  }
  
  return strlist_rep;
}

const string& ECA_CONTROL_INTERFACE::last_string(void) const
{
  str_rep = string(eci_last_string_r(eci_repp));
  return str_rep;
}

double ECA_CONTROL_INTERFACE::last_float(void) const
{
  return eci_last_float_r(eci_repp);
}

int ECA_CONTROL_INTERFACE::last_integer(void) const
{
  return eci_last_integer_r(eci_repp);
}

long int ECA_CONTROL_INTERFACE::last_long_integer(void) const
{
  return eci_last_long_integer_r(eci_repp);
}

const string& ECA_CONTROL_INTERFACE::last_error(void) const
{
  str_rep = string(eci_last_error_r(eci_repp));
  return str_rep;
}

const string& ECA_CONTROL_INTERFACE::last_type(void) const
{
  str_rep = string(eci_last_type_r(eci_repp));
  return str_rep;
}

bool ECA_CONTROL_INTERFACE::error(void) const
{
  return ((eci_error_r(eci_repp) != 0) ? true : false);
}

bool ECA_CONTROL_INTERFACE::events_available(void)
{
  return false;
}

void ECA_CONTROL_INTERFACE::next_event(void)
{
}

const string& ECA_CONTROL_INTERFACE::current_event(void)
{
  str_rep = "";
  return str_rep;
}
