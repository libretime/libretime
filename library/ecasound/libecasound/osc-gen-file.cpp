// ------------------------------------------------------------------------
// osc-gen-file.cpp: Generic oscillator using envelope presets
// Copyright (C) 1999-2002,2004,2007 Kai Vehmanen
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

#include <cstdlib>
#include <vector>
#include <string>

#include <kvu_numtostr.h>
#include <kvu_utils.h>

#include "resource-file.h"
#include "eca-resources.h"
#include "osc-gen-file.h"
#include "eca-logger.h"

GENERIC_OSCILLATOR_FILE::GENERIC_OSCILLATOR_FILE(double freq, int mode)
  : GENERIC_OSCILLATOR(freq, mode)
{
  set_parameter(1, get_parameter(1));
  set_parameter(2, mode);
}

GENERIC_OSCILLATOR_FILE::~GENERIC_OSCILLATOR_FILE (void)
{
}

void GENERIC_OSCILLATOR_FILE::get_oscillator_preset(int preset)
{
  ECA_RESOURCES ecarc;
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Opening genosc envelope file.");

  std::string user_filename =
    ecarc.resource("user-resource-directory") + "/" + ecarc.resource("resource-file-genosc-envelopes");

  std::string pname = kvu_numtostr(preset);

  RESOURCE_FILE rc;
  rc.resource_file(user_filename);
  rc.load();
  if (rc.has(pname) != true) {
    std::string global_filename =
      ecarc.resource("resource-directory") + "/" + ecarc.resource("resource-file-genosc-envelopes");
    rc.resource_file(global_filename);
    rc.load();
  }

  if (rc.has(pname) == true) {
    parse_envelope(rc.resource(pname));
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::info, "ERROR: Oscillator preset " + pname + " not found!");
  }
}

void GENERIC_OSCILLATOR_FILE::parse_envelope(const std::string& str)
{
  std::vector<std::string> tokens = kvu_string_to_words(str);
  if (tokens.size() > 0) {
    set_start_value(atof(tokens[0].c_str()));
    if (tokens.size() > 1) {
      set_end_value(atof(tokens[1].c_str()));
      ienvelope_rep.resize(tokens.size() - 2);
      for(unsigned int n = 2; n < tokens.size(); n++) {
	ienvelope_rep[n - 2] = atof(tokens[n].c_str());
      }
    }
  }
  prepare_envelope();
}

void GENERIC_OSCILLATOR_FILE::set_parameter(int param, CONTROLLER_SOURCE::parameter_t value)
{
  switch (param) {
  case 1: 
  case 2: 
    GENERIC_OSCILLATOR::set_parameter(param, value);
    break;

  case 3:
    preset_rep = static_cast<int>(value);
    get_oscillator_preset(preset_rep);
    break;
  }
}

CONTROLLER_SOURCE::parameter_t GENERIC_OSCILLATOR_FILE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
  case 2:
    return GENERIC_OSCILLATOR::get_parameter(param);

  case 3:
    return static_cast<parameter_t>(preset_rep);
  }
  return 0.0;
}
