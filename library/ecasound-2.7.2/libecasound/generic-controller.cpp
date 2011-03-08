// ------------------------------------------------------------------------
// generic_controller.cpp: General sources for control signals
// Copyright (C) 1999-2002,2005,2008 Kai Vehmanen
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

#include <cmath>
#include <iostream>
#include <string>

#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "generic-controller.h"
#include "eca-chainop.h"

#include "eca-logger.h"

/* Debug controller source values */ 
// #define DEBUG_CONTROLLERS 1

#ifdef DEBUG_CONTROLLERS
#define DEBUG_CTRL_STATEMENT(x) x
#else
#define DEBUG_CTRL_STATEMENT(x) ((void)0)
#endif

GENERIC_CONTROLLER::GENERIC_CONTROLLER(CONTROLLER_SOURCE* src, OPERATOR* dobj, int par_id, double range_low, double range_high)
{
  source = src;
  target = dobj;
  init_called_rep = false;
  param_id_rep = par_id;
  rangelow_rep = range_low;
  rangehigh_rep = range_high;
  last_value_pos_rep = -1;
}

void GENERIC_CONTROLLER::init(void)
{ 
  source->init();

  double init_value = target->get_parameter(param_id_rep);
  init_value = (init_value - rangelow_rep) / (rangehigh_rep - rangelow_rep);
  source->set_initial_value(init_value);

  DEBUG_CTRL_STATEMENT(std::cerr << "generic-controller: init type '"
		       << source->name() << "', init_value " 
		       << init_value << "." << std::endl);

  init_called_rep = true;
  last_value_pos_rep = -1;
}

CONTROLLER_SOURCE::parameter_t GENERIC_CONTROLLER::value(double pos)
{
  // --------
  DBC_REQUIRE(is_valid() == true);
  // --------

  double new_value = rangelow_rep +
    (source->value(pos) * (rangehigh_rep - rangelow_rep));

  DEBUG_CTRL_STATEMENT(std::cerr << "generic-controller: type '"
		       << source->name() << "', pos_sec " << pos 
		       << ", source_value " << source->value(pos) 
		       << ", scaled_value " << new_value << "." << std::endl);

  target->set_parameter(param_id_rep, new_value);

  last_value_pos_rep = pos;

  return new_value;
}

string GENERIC_CONTROLLER::status(void) const
{
  if (is_valid() == true) {
    double value = -1.0f;

    if (last_value_pos_rep > 0) {
      value = source->value(last_value_pos_rep);
    }

    return "Source \"" + source->name() + 
	   "\" connected to target \"" +
	   target->name() + "\"." +
	   " Current source value is " + 
	   kvu_numtostr(value) + 
	   " and target " + 
	   kvu_numtostr(target->get_parameter(param_id_rep)) + ".";
  }
  else {
    return "Controller not valid.";
  }
}

void GENERIC_CONTROLLER::assign_target(OPERATOR* obj)
{ 
  target  = obj; 
}

void GENERIC_CONTROLLER::assign_source(CONTROLLER_SOURCE* obj)
{ 
  if (init_called_rep == true &&
      source != obj)
    init();

  source = obj;
}

void GENERIC_CONTROLLER::set_parameter(int param, CHAIN_OPERATOR::parameter_t v)
{
  switch (param) {
  case 1: 
    param_id_rep = static_cast<int>(v);
    break;
  case 2: 
    rangelow_rep = v;
    break;
  case 3: 
    rangehigh_rep = v;
    break;
  default:
    source->set_parameter(param - 3, v);
  }
}

CHAIN_OPERATOR::parameter_t GENERIC_CONTROLLER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return static_cast<parameter_t>(param_id_rep);
  case 2: 
    return rangelow_rep;
  case 3: 
    return rangehigh_rep;
  default:
    return source->get_parameter(param - 3);
  }
  return 0.0;
}
