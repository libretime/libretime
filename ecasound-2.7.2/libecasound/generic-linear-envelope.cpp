// ------------------------------------------------------------------------
// generic-linear-envelope.cpp: Generic linear envelope
// Copyright (C) 2000-2002,2006,2008 Kai Vehmanen
// Copyright (C) 2001 Arto Hamara
//
// Attributes:
//     eca-style-version: 3
//
// This program is fre software; you can redistribute it and/or modify
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

#include <kvu_numtostr.h>
#include <kvu_message_item.h>

#include "generic-linear-envelope.h"
#include "eca-logger.h"

/* Debug controller source values */ 
// #define DEBUG_CONTROLLERS

#ifdef DEBUG_CONTROLLERS
#define DEBUG_CTRL_STATEMENT(x) x
#else
#define DEBUG_CTRL_STATEMENT(x) ((void)0)
#endif

GENERIC_LINEAR_ENVELOPE::GENERIC_LINEAR_ENVELOPE(void)
{
  pos_rep.resize(1);
  val_rep.resize(1);
  curstage = -2; /* processing not yet started */
  pos_rep[0] = 0;
  val_rep[0] = 0;
  set_param_count(0);
} 

GENERIC_LINEAR_ENVELOPE::~GENERIC_LINEAR_ENVELOPE(void)
{
} 

CONTROLLER_SOURCE::parameter_t GENERIC_LINEAR_ENVELOPE::value(double curpos)
{
  /* FIXME: not really seeking-safe */

  if (curpos < pos_rep[0] || curstage == -2) {
    /* not reached the first position yet, or processing not 
       started yet */
    curval = val_rep[0];
  } else if (curstage < static_cast<int>(pos_rep.size())-1) {
    if (curpos >= pos_rep[curstage+1]) {
      ++curstage;
      if (curstage < static_cast<int>(pos_rep.size())-1) {
	curval = (((curpos - pos_rep[curstage]) * val_rep[curstage+1] +
		   (pos_rep[curstage+1] - curpos) * val_rep[curstage]) /
		   (pos_rep[curstage+1] - pos_rep[curstage]));
      }
      else {
	curval = val_rep.back();
      }
    } else {
      curval = (((curpos - pos_rep[curstage]) * val_rep[curstage+1] +
		 (pos_rep[curstage+1] - curpos) * val_rep[curstage]) /
		(pos_rep[curstage+1] - pos_rep[curstage]));
    }
  }

  DEBUG_CTRL_STATEMENT(std::cerr << "generic-linear-envelope: type '"
		       << name() << "', pos_sec " 
		       << position_in_seconds_exact() 
		       << ", curpos " << curpos
		       << ", curval " << curval
		       << ", curstage " << curstage << "." << std::endl);
  
  return curval;
} 

void GENERIC_LINEAR_ENVELOPE::init(void)
{
  curval = 0.0f;
  curstage = -1; /* processing started */
  
  ECA_LOG_MSG(ECA_LOGGER::info, "Envelope created.");
}

void GENERIC_LINEAR_ENVELOPE::set_param_count(int params)
{
  param_names_rep = "point_count";
  if (params > 0) {
    for(int n = 0; n < params; ++n) {
      param_names_rep += ",pos";
      param_names_rep += kvu_numtostr(n + 1);
      param_names_rep += ",val";
      param_names_rep += kvu_numtostr(n + 1);
    }
  }
}

std::string GENERIC_LINEAR_ENVELOPE::parameter_names(void) const 
{
  return param_names_rep;
}

void GENERIC_LINEAR_ENVELOPE::set_parameter(int param, parameter_t value)
{
  switch(param) {
  case 1:
    set_param_count(static_cast<int>(value));
    pos_rep.resize(static_cast<int>(value));
    val_rep.resize(static_cast<int>(value));
    break;
  default:
    int pointnum = param/2 - 1;
    if (param % 2 == 0)
      pos_rep[pointnum] = value;
    else
      val_rep[pointnum] = value;
  }
}

CONTROLLER_SOURCE::parameter_t GENERIC_LINEAR_ENVELOPE::get_parameter(int param) const
{
  switch(param) {
  case 1:
    return static_cast<parameter_t>(pos_rep.size());
    break;
  default:
    int pointnum = param/2 - 1;
    if (pointnum >= static_cast<int>(pos_rep.size())) {
      return 0.0f;
    }
    
    if (param % 2 == 0)
      return pos_rep[pointnum];
    else
      return val_rep[pointnum];
  }
}
