// ------------------------------------------------------------------------
// eca-chainsetup-bufparams.cpp: Container for chainsetup buffering params.
// Copyright (C) 2001, 2007 Kai Vehmanen
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

#include <cstdlib>

#include <kvu_utils.h> /* kvu_get_argument_number */
#include <kvu_numtostr.h>

#include "eca-chainsetup-bufparams.h"

ECA_CHAINSETUP_BUFPARAMS::ECA_CHAINSETUP_BUFPARAMS(void)
{
  set_buffersize_rep = false;
  set_raisedpriority_rep = false;
  set_sched_priority_rep = false;
  set_double_buffering_rep = false;
  set_double_buffer_size_rep = false;
  set_max_buffers_rep = false;
}

/**
 * Sets all buffering parameters based on 'paramstr'.
 *
 * @arg paramstr "int_buffersize, bool_raisedprio, int_schedprio, 
 *                bool_db, int_db-bufsize, int_buffersize"
 */
void ECA_CHAINSETUP_BUFPARAMS::set_all(const string& paramstr)
{
  set_buffersize(atoi(kvu_get_argument_number(1, paramstr).c_str()));
  toggle_raised_priority(kvu_get_argument_number(2, paramstr) == "true");
  set_sched_priority(atoi(kvu_get_argument_number(3, paramstr).c_str()));
  toggle_double_buffering(kvu_get_argument_number(4, paramstr) == "true");
  set_double_buffer_size(atol(kvu_get_argument_number(5, paramstr).c_str()));
  toggle_max_buffers(kvu_get_argument_number(6, paramstr) == "true");
}

/**
 * Returns number of distinct set parameters.
 */
int ECA_CHAINSETUP_BUFPARAMS::number_of_set(void) const
{
  int res = 0;

  if (set_buffersize_rep == true) res++;
  if (set_raisedpriority_rep == true) res++;
  if (set_sched_priority_rep == true) res++;
  if (set_double_buffering_rep == true) res++;
  if (set_double_buffer_size_rep == true) res++;
  if (set_max_buffers_rep == true) res++;

  return res;
}

string ECA_CHAINSETUP_BUFPARAMS::to_string(void) const 
{
  string result;
  result += "\nbuffersize: ";
  result += kvu_numtostr(buffersize_rep);
  result += "\nraised_priority: ";
  result += kvu_numtostr((int)raisedpriority_rep);
  result += "\nsched_priority: ";
  result += kvu_numtostr((int)sched_priority_rep);
  result += "\ndouble buffering: ";
  result += kvu_numtostr((int)double_buffering_rep);
  result += "\ndouble buffer size: ";
  result += kvu_numtostr((int)double_buffer_size_rep);
  result += "\nmax buffers: ";
  result += kvu_numtostr(max_buffers_rep);
  return result;
}



bool ECA_CHAINSETUP_BUFPARAMS::are_all_set(void) const
{
  if (set_buffersize_rep == true &&
      set_raisedpriority_rep == true &&
      set_sched_priority_rep == true &&
      set_double_buffering_rep == true &&
      set_double_buffer_size_rep == true &&
      set_max_buffers_rep == true) return true;
  
  return false;
}

void ECA_CHAINSETUP_BUFPARAMS::set_buffersize(long int value)
{
  buffersize_rep = value;
  set_buffersize_rep = true;
}

void ECA_CHAINSETUP_BUFPARAMS::toggle_raised_priority(bool value)
{
  raisedpriority_rep = value; 
  set_raisedpriority_rep = true; 
}

void ECA_CHAINSETUP_BUFPARAMS::set_sched_priority(int prio)
{
  sched_priority_rep = prio; 
  set_sched_priority_rep = true; 
}

void ECA_CHAINSETUP_BUFPARAMS::toggle_double_buffering(bool value)
{
  double_buffering_rep = value;
  set_double_buffering_rep = true;
}

void ECA_CHAINSETUP_BUFPARAMS::set_double_buffer_size(long int v)
{
  double_buffer_size_rep = v; 
  set_double_buffer_size_rep = true; 
}

void ECA_CHAINSETUP_BUFPARAMS::toggle_max_buffers(bool v)
{
  max_buffers_rep = v; 
  set_max_buffers_rep = true; 
}
