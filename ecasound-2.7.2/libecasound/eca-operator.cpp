// ------------------------------------------------------------------------
// eca-operator.cpp: Operators are ecasound objects which can be used
//                   as targets for dynamic control.
// Copyright (C) 2000 Kai Vehmanen
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

#include <kvu_dbc.h>

#include "eca-operator.h"

OPERATOR::~OPERATOR (void) { }

void OPERATOR::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  DBC_REQUIRE(param > 0);
  DBC_REQUIRE(param <= number_of_params());

  pd->default_value = get_parameter(param);
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = false;
  pd->lower_bound = 0.0f;
  pd->toggled = false;
  pd->integer = false;
  pd->logarithmic = false;
  pd->output = false;
}
