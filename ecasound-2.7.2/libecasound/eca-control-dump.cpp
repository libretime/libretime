// ------------------------------------------------------------------------
// eca-control-dump.cpp: Class for dumping status information to 
//                       a standard output stream
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

#include <iostream>
#include <fstream>
#include <string>
#include <vector>

#include <kvu_utils.h>
#include <kvu_numtostr.h>

#include "audioio.h"
#include "eca-chainop.h"
#include "eca-control.h"
#include "eca-control-dump.h"

void ECA_CONTROL_DUMP::dump_status(void) { 
  dump("dump-status", ctrl_repp->engine_status());
}

void ECA_CONTROL_DUMP::dump_position(void) { 
  dump("dump-position", kvu_numtostr(ctrl_repp->position_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
}

void ECA_CONTROL_DUMP::dump_length(void) { 
  dump("dump-length", kvu_numtostr(ctrl_repp->length_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
}

void ECA_CONTROL_DUMP::dump_chainsetup_status(void) { 
  if (ctrl_repp->is_connected() == true) 
    dump("dump-cs-status", "connected");
  else if (ctrl_repp->is_selected() == true) 
    dump("dump-cs-status", "selected");
  else
    dump("dump-cs-status", "");
}

void ECA_CONTROL_DUMP::dump_selected_chain(void) { 
  const std::vector<std::string>& t = ctrl_repp->selected_chains();
  if (t.empty() == false) {
    dump("dump-c-selected", kvu_vector_to_string(t, ","));
  }
  else
    dump("dump-c-selected", "");
}

void ECA_CONTROL_DUMP::dump_selected_audio_input(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_input();
  if (t != 0) {
    dump("dump-ai-selected", t->label());
  }
  else
    dump("dump-ai-selected", "");
}

void ECA_CONTROL_DUMP::dump_selected_audio_output(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_output();
  if (t != 0) {
    dump("dump-ao-selected", t->label());
  }
  else
    dump("dump-ao-selected", "");
}

void ECA_CONTROL_DUMP::dump_audio_input_position(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_input();
  if (t != 0) {
    dump("dump-ai-position", kvu_numtostr(t->position_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
  }
  else
    dump("dump-ai-position", "");
}

void ECA_CONTROL_DUMP::dump_audio_output_position(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_output();
  if (t != 0) {
    dump("dump-ao-position", kvu_numtostr(t->position_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
  }
  else
    dump("dump-ao-position", "");
}

void ECA_CONTROL_DUMP::dump_audio_input_length(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_input();
  if (t != 0) {
    dump("dump-ai-length", kvu_numtostr(t->length_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
  }
  else
    dump("dump-ai-length", "");
}

void ECA_CONTROL_DUMP::dump_audio_output_length(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_output();
  if (t != 0) {
    dump("dump-ao-length", kvu_numtostr(t->length_in_seconds_exact(), ctrl_repp->float_to_string_precision()));
  }
  else
    dump("dump-ao-length", "");
}

void ECA_CONTROL_DUMP::dump_audio_input_open_state(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_input();
  if (t != 0) {
    if (t->is_open() == true) 
      dump("dump-ai-open-state", "open");
    else
      dump("dump-ai-open-state", "closed");
  }
  else
    dump("dump-ai-open-state", "");
}

void ECA_CONTROL_DUMP::dump_audio_output_open_state(void) { 
  const AUDIO_IO* t = ctrl_repp->get_audio_output();
  if (t != 0) {
    if (t->is_open() == true) 
      dump("dump-ao-open-state", "open");
    else
      dump("dump-ao-open-state", "closed");
  }
  else
    dump("dump-ao-open-state", "");
}

void ECA_CONTROL_DUMP::dump_chain_operator_value(int chainop, int param) { 
  ctrl_repp->select_chain_operator(chainop);
  const CHAIN_OPERATOR* t = ctrl_repp->get_chain_operator();
  if (t != 0) {
    dump("dump-cop-value", kvu_numtostr(t->get_parameter(param), ctrl_repp->float_to_string_precision()));
  }
  else
    dump("dump-cop-value", "");
}
