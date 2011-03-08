// ------------------------------------------------------------------------
// global_preset.cpp: Global effect preset
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

#include <kvu_utils.h>

#include "resource-file.h"
#include "eca-resources.h"
#include "eca-logger.h"
#include "eca-error.h"
#include "global-preset.h"

GLOBAL_PRESET::GLOBAL_PRESET(const std::string& preset_name) 
  : preset_name_rep(preset_name) {
  ECA_RESOURCES ecarc;
  ECA_LOG_MSG(ECA_LOGGER::system_objects,"(global-preset) Opening sc-preset file.");

  /* FIXME: is this correct; user resources should be nowadays handled
     in eca-resources.cpp */

  std::string user_filename =
    ecarc.resource("user-resource-directory") + "/" + ecarc.resource("resource-file-effect-presets");

  RESOURCE_FILE rc;
  rc.resource_file(user_filename);
  rc.load();
  std::string raw = rc.resource(preset_name);
  if (raw == "") {
    std::string global_filename =
      ecarc.resource("resource-directory") + "/" + ecarc.resource("resource-file-effect-presets");
    rc.resource_file(global_filename);
    rc.load();
    raw = rc.resource(preset_name);
  }

  if (raw != "") {
    parse(raw);
    set_name(preset_name);
  }
  else {
    set_name("empty");
    throw(ECA_ERROR("GLOBAL_PRESET", "requested preset \"" + preset_name + "\" was not found."));
  }
}

GLOBAL_PRESET* GLOBAL_PRESET::clone(void) const {
  std::vector<parameter_t> param_values;
  for(int n = 0; n < number_of_params(); n++) {
    param_values.push_back(get_parameter(n + 1));
  }
  GLOBAL_PRESET* preset = new GLOBAL_PRESET(preset_name_rep);
  for(int n = 0; n < preset->number_of_params(); n++) {
    preset->set_parameter(n + 1, param_values[n]);
  }
  return(preset);
}
