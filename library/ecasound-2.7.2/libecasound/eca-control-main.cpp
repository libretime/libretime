// ------------------------------------------------------------------------
// eca-control-main.h: ECA_CONTROL_MAIN
// Copyright (C) 2009 Kai Vehmanen
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
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// ------------------------------------------------------------------------

#include <string>
#include <vector>

#include "kvu_dbc.h"
#include "kvu_numtostr.h"
#include "kvu_utils.h"

#include "eca-control-main.h"
#include "eca-logger.h"

ECA_CONTROL_MAIN::~ECA_CONTROL_MAIN (void)
{
}

std::string ECA_CONTROL_MAIN::return_value_to_string(const struct eci_return_value *retval, int float_precision)
{
  std::string result;

  if (retval->type == eci_return_value::retval_none) {
    ; /* nop */
  }
  else if (retval->type == eci_return_value::retval_error) {
    result = retval->string_val;
  }
  else if (retval->type == eci_return_value::retval_string) {
    result = retval->string_val;
  }
  else if (retval->type == eci_return_value::retval_string_list) {
    result = 
      kvu_vector_to_string(kvu_vector_search_and_replace(retval->string_list_val, ",", "\\,"), ",");
  }
  else if (retval->type == eci_return_value::retval_integer) {
    result = kvu_numtostr(retval->m.int_val);
  }
  else if (retval->type == eci_return_value::retval_long_integer) {
    result = kvu_numtostr(retval->m.long_int_val);
  }
  else if (retval->type == eci_return_value::retval_float) {
    result = kvu_numtostr(retval->m.float_val, float_precision);
  }
  else {
    DBC_NEVER_REACHED();
  }

  return result;
}

const char* ECA_CONTROL_MAIN::return_value_type_to_string(const struct eci_return_value *retval)
{
  if (retval->type == eci_return_value::retval_none)
    return "-";
  else if (retval->type == eci_return_value::retval_error)
    return "e";
  else if (retval->type == eci_return_value::retval_string)
    return "s";
  else if (retval->type == eci_return_value::retval_string_list)
    return "S";
  else if (retval->type == eci_return_value::retval_integer)
    return "i";
  else if (retval->type == eci_return_value::retval_long_integer)
    return "li";
  else if (retval->type == eci_return_value::retval_float)
    return "f";
  else
    DBC_NEVER_REACHED();

  return NULL;
}

void ECA_CONTROL_MAIN::clear_return_value(struct eci_return_value *retval)
{
  if (retval == 0)
    return;

  retval->type = eci_return_value::retval_none;
  retval->string_list_val.resize(0);
  retval->string_val.resize(0);
  retval->m.long_int_val = 0;
}
