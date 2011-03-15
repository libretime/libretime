// ------------------------------------------------------------------------
// osc-gen.cpp: Generic oscillator
// Copyright (C) 1999-2002,2008 Kai Vehmanen
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

#include <iostream>
#include <vector>
#include <string>

#include <kvu_numtostr.h>

#include "eca-object-factory.h"
#include "osc-gen.h"
#include "oscillator.h"
#include "eca-logger.h"

CONTROLLER_SOURCE::parameter_t GENERIC_OSCILLATOR::value(double pos)
{
  if (mode_rep == 0)
    update_current_static();
  else
    update_current_linear();

  /* FIXME: not really seeking-safe */
  loop_pos_rep += pos - last_global_pos_rep;
  last_global_pos_rep = pos;

  if (loop_pos_rep > loop_length_rep) {
    loop_pos_rep = 0.0f;
    pindex_rep = 0;
    eindex_rep = 0;
    if (epairs_rep > 0)
      next_pos_rep = ienvelope_rep[0];
    else
      next_pos_rep = 1.0;
    
    last_pos_rep = 0;
  }

  if ((loop_pos_rep / loop_length_rep) >= next_pos_rep) {
    ++pindex_rep;
    eindex_rep += 2;
    last_pos_rep = next_pos_rep;
    if (eindex_rep + 1 > static_cast<int>(ienvelope_rep.size())) {
      next_pos_rep = 1.0;
    }
    else {
      next_pos_rep = ienvelope_rep[eindex_rep];
    }
  }

  return(current_value_rep);
}

void GENERIC_OSCILLATOR::update_current_static(void)
{
  if (pindex_rep == 0) {
    current_value_rep = start_value_rep;
  }
  else {
    if (eindex_rep - 1 > static_cast<int>(ienvelope_rep.size()))
      current_value_rep = end_value_rep;
    else
      current_value_rep = ienvelope_rep[eindex_rep - 1];
  }
}

void GENERIC_OSCILLATOR::update_current_linear(void)
{
  if (pindex_rep == 0) {
    current_value_rep = start_value_rep;
  }
  else {
    if (eindex_rep - 1 > static_cast<int>(ienvelope_rep.size()))
      current_value_rep = end_value_rep;
    else
      current_value_rep = ienvelope_rep[eindex_rep - 1];
  }

  double next_value = end_value_rep;
  if (epairs_rep != 0 &&
      eindex_rep + 1 < static_cast<int>(ienvelope_rep.size())) {
    next_value = ienvelope_rep[eindex_rep + 1];
  }
  current_value_rep += (next_value - current_value_rep) * (((loop_pos_rep / loop_length_rep) - last_pos_rep) / (next_pos_rep - last_pos_rep));
}

GENERIC_OSCILLATOR::GENERIC_OSCILLATOR(double freq, int mode)
  : OSCILLATOR(freq, 0.0)
{
  start_value_rep = end_value_rep = 0.0f;
  loop_length_rep = 0.0f;
  loop_pos_rep = 0.0f;
  next_pos_rep = 0.0f;
  last_pos_rep = 0.0f;
  last_global_pos_rep = 0.0f;
  epairs_rep = 0;
  eindex_rep = 0;
  pindex_rep = 0;
  current_value_rep = 0.0f;
  set_param_count(0);

  set_parameter(1, get_parameter(1));
  set_parameter(2, mode);

  // std::cerr << "(osc-gen) construct; params " << parameter_names() << ".\n";
}

void GENERIC_OSCILLATOR::init(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,
	      "Generic oscillator init with params: "
	      + ECA_OBJECT_FACTORY::operator_parameters_to_eos(this));
}

GENERIC_OSCILLATOR::~GENERIC_OSCILLATOR (void)
{
}

void GENERIC_OSCILLATOR::set_param_count(int params)
{
  param_names_rep = "freq,mode,pcount,start_val,end_val";
  if (params > 0) {
    for(int n = 0; n < params; n++) {
      std::string num = kvu_numtostr(n + 1);
      param_names_rep += ",pos";
      param_names_rep += num;
      param_names_rep += ",val";
      param_names_rep += num;
    }
  }
}

std::string GENERIC_OSCILLATOR::parameter_names(void) const
{
  return(param_names_rep);
}

void GENERIC_OSCILLATOR::prepare_envelope(void)
{
  if (ienvelope_rep.size() % 2 == 1)
    ienvelope_rep.resize(ienvelope_rep.size() + 1);
  epairs_rep = (ienvelope_rep.size() / 2);
  if (epairs_rep > 0) 
    next_pos_rep = ienvelope_rep[0];
  else
    next_pos_rep = 1.0;
}


void GENERIC_OSCILLATOR::set_parameter(int param, CONTROLLER_SOURCE::parameter_t value)
{
  switch (param) {
  case 1: 
    frequency(value);
    loop_length_rep = 1.0f / frequency(); // length of one wave in seconds
    break;

  case 2: 
    mode_rep = static_cast<int>(value);
    break;

  case 3: 
    set_param_count(static_cast<int>(value));
    break;

  case 4:
    start_value_rep = value;
    current_value_rep = value;
    break;

  case 5:
    end_value_rep = value;
    break;

  default: {
      int pointnum = param - 5;
      if (pointnum > 0) {
	if (pointnum > static_cast<int>(ienvelope_rep.size()))
	  ienvelope_rep.resize(pointnum);
	
	ienvelope_rep[pointnum - 1] = value;
      }

      prepare_envelope();

      // std::cerr << "Added point " << pointnum << ", envelope size " << ienvelope_rep.size() << "." << std::endl;
     
      break;
    }
  }
}

CONTROLLER_SOURCE::parameter_t GENERIC_OSCILLATOR::get_parameter(int param) const
{ 
  switch (param) {
  case 1: 
    return(frequency());

  case 2:
    return(static_cast<parameter_t>(mode_rep));

  case 3:
    return(static_cast<parameter_t>((number_of_params() - 5) / 2));

  case 4:
    return(static_cast<parameter_t>(start_value_rep));

  case 5:
    return(static_cast<parameter_t>(end_value_rep));

  default:
    int pointnum = param - 5;
    if (pointnum > 0) {
      if (pointnum <= static_cast<int>(ienvelope_rep.size())) {
	return(static_cast<parameter_t>(ienvelope_rep[pointnum - 1]));
      }
    }
  }
  return(0.0);
}
