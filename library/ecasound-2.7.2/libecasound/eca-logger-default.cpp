// ------------------------------------------------------------------------
// eca-logger-default.cpp: Default logging subsystem implementation.
// Copyright (C) 2002-2004,2008 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
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

#include <algorithm> /* find() */
#include <string>

#include "eca-logger-default.h"

ECA_LOGGER_DEFAULT::ECA_LOGGER_DEFAULT(std::ostream& output)
  : output_rep(output)
{
}

void ECA_LOGGER_DEFAULT::do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message)
{
  if (is_log_level_set(level) == true) {
    if (level == ECA_LOGGER::subsystems) {
      output_rep << "[* ";
    }
    else if (module_name.size() > 0 &&
	     is_log_level_set(ECA_LOGGER::module_names) == true &&
	     level != ECA_LOGGER::eiam_return_values) {
      output_rep << "(" 
		<< ECA_LOGGER_INTERFACE::filter_module_name(module_name)
		<< ") ";
    }
    
    output_rep << log_message;
    
    if (level == ECA_LOGGER::subsystems) {
      output_rep << " *]";
    }
    output_rep << std::endl;
  }
}

void ECA_LOGGER_DEFAULT::do_flush(void)
{
}

void ECA_LOGGER_DEFAULT::do_log_level_changed(void)
{
}

ECA_LOGGER_DEFAULT::~ECA_LOGGER_DEFAULT(void)
{
}
