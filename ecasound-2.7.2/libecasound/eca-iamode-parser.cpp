// ------------------------------------------------------------------------
// eca-iamode-parser.cpp: Class that handles registering and querying 
//                        interactive mode commands.
// Copyright (C) 1999-2005,2008 Kai Vehmanen
// Copyright (C) 2005 Stuart Allie
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <map>
#include <vector>
#include <string>
#include <pthread.h>

#include "kvu_message_item.h"
#include "kvu_locks.h"

#include "eca-iamode-parser.h"
#include "eca-logger.h"

using namespace std;

map<string,int>* ECA_IAMODE_PARSER::cmd_map_repp = 0;
pthread_mutex_t ECA_IAMODE_PARSER::lock_rep = PTHREAD_MUTEX_INITIALIZER;

ECA_IAMODE_PARSER::ECA_IAMODE_PARSER(void)
{ 
}

ECA_IAMODE_PARSER::~ECA_IAMODE_PARSER(void)
{
}

const map<string,int>& ECA_IAMODE_PARSER::registered_commands(void)
{
  //
  // Note! Below we use the Double-Checked Locking Pattern
  //       to protect against concurrent access

  if (cmd_map_repp == 0) {
    KVU_GUARD_LOCK guard(&ECA_IAMODE_PARSER::lock_rep);
    if (cmd_map_repp == 0) {
      cmd_map_repp = new map<string,int>;
      register_commands_misc();
      register_commands_cs();
      register_commands_c();
      register_commands_aio();
      register_commands_ai();
      register_commands_ao();
      register_commands_cop();
      register_commands_copp();
      register_commands_ctrl();
      register_commands_ctrlp();
      register_commands_dump();
      register_commands_external();
    }
  }
  return *cmd_map_repp;
}

vector<string> ECA_IAMODE_PARSER::registered_commands_list(void)
{
  vector<string> cmdlist;
  const map<string,int>& map_ref = ECA_IAMODE_PARSER::registered_commands();
  map<string,int>::const_iterator p = map_ref.begin();
  while (p != map_ref.end()) {
    cmdlist.push_back(p->first);
    ++p;
  }
  return cmdlist;
}

void ECA_IAMODE_PARSER::register_commands_misc(void)
{
  (*cmd_map_repp)["help"] = ec_help;
  (*cmd_map_repp)["?"] = ec_help;
  (*cmd_map_repp)["h"] = ec_help;

  (*cmd_map_repp)["quit"] = ec_exit;
  (*cmd_map_repp)["q"] = ec_exit;
   
  (*cmd_map_repp)["start"] = ec_start;
  (*cmd_map_repp)["t"] = ec_start;
  (*cmd_map_repp)["stop"] = ec_stop;
  (*cmd_map_repp)["s"] = ec_stop;
  (*cmd_map_repp)["run"] = ec_run;

  (*cmd_map_repp)["debug"] = ec_debug;
  (*cmd_map_repp)["resource-file"] = ec_resource_file;

  (*cmd_map_repp)["engine-launch"] = ec_engine_launch;
  (*cmd_map_repp)["engine-halt"] = ec_engine_halt;
  (*cmd_map_repp)["engine-status"] = ec_engine_status;

  (*cmd_map_repp)["status"] = ec_cs_status;
  (*cmd_map_repp)["st"] = ec_cs_status;
  (*cmd_map_repp)["cs"] = ec_c_status;
  (*cmd_map_repp)["es"] = ec_cop_status;
  (*cmd_map_repp)["fs"] = ec_aio_status;

  (*cmd_map_repp)["int-cmd-list"] = ec_int_cmd_list;
  (*cmd_map_repp)["int-log-history"] = ec_int_log_history;
  (*cmd_map_repp)["int-output-mode-wellformed"] = ec_int_output_mode_wellformed;
  (*cmd_map_repp)["int-set-float-to-string-precision"] = ec_int_set_float_to_string_precision;
  (*cmd_map_repp)["int-set-log-history-length"] = ec_int_set_log_history_length;

  (*cmd_map_repp)["int-version-string"] = ec_int_version_string;
  (*cmd_map_repp)["int-version-lib-current"] = ec_int_version_lib_current;
  (*cmd_map_repp)["int-version-lib-revision"] = ec_int_version_lib_revision;
  (*cmd_map_repp)["int-version-lib-age"] = ec_int_version_lib_age;

  (*cmd_map_repp)["preset-register"] = ec_preset_register;
  (*cmd_map_repp)["ladspa-register"] = ec_ladspa_register;

  (*cmd_map_repp)["map-cop-list"] = ec_map_cop_list;
  (*cmd_map_repp)["map-preset-list"] = ec_map_preset_list;
  (*cmd_map_repp)["map-ladspa-list"] = ec_map_ladspa_list;
  (*cmd_map_repp)["map-ladspa-id-list"] = ec_map_ladspa_id_list;
  (*cmd_map_repp)["map-ctrl-list"] = ec_map_ctrl_list;
}

void ECA_IAMODE_PARSER::register_commands_cs(void)
{
  (*cmd_map_repp)["cs-add"] = ec_cs_add;
  (*cmd_map_repp)["cs-remove"] = ec_cs_remove;
  (*cmd_map_repp)["cs-list"] = ec_cs_list;
  (*cmd_map_repp)["cs-select"] = ec_cs_select;
  (*cmd_map_repp)["cs-selected"] = ec_cs_selected;
  (*cmd_map_repp)["cs-index-select"] = ec_cs_index_select;
  (*cmd_map_repp)["cs-iselect"] = ec_cs_index_select;
  (*cmd_map_repp)["cs-load"] = ec_cs_load;
  (*cmd_map_repp)["cs-save"] = ec_cs_save;
  (*cmd_map_repp)["cs-save-as"] = ec_cs_save_as;
  (*cmd_map_repp)["cs-edit"] = ec_cs_edit;
  (*cmd_map_repp)["cs-is-valid"] = ec_cs_is_valid;
  (*cmd_map_repp)["cs-connect"] = ec_cs_connect;
  (*cmd_map_repp)["cs-connected"] = ec_cs_connected;
  (*cmd_map_repp)["cs-disconnect"] = ec_cs_disconnect;
  (*cmd_map_repp)["cs-set-param"] = ec_cs_set_param;
  (*cmd_map_repp)["cs-set-audio-format"] = ec_cs_set_audio_format;
  (*cmd_map_repp)["cs-status"] = ec_cs_status;
  (*cmd_map_repp)["cs-rewind"] = ec_cs_rewind;
  (*cmd_map_repp)["rewind"] = ec_cs_rewind;
  (*cmd_map_repp)["rw"] = ec_cs_rewind;
  (*cmd_map_repp)["cs-forward"] = ec_cs_forward;
  (*cmd_map_repp)["forward"] = ec_cs_forward;
  (*cmd_map_repp)["fw"] = ec_cs_forward;
  (*cmd_map_repp)["cs-setpos"] = ec_cs_set_position;
  (*cmd_map_repp)["cs-set-position"] = ec_cs_set_position;
  (*cmd_map_repp)["cs-set-position-samples"] = ec_cs_set_position_samples;
  (*cmd_map_repp)["setpos"] = ec_cs_set_position;
  (*cmd_map_repp)["set-position"] = ec_cs_set_position;
  (*cmd_map_repp)["cs-getpos"] = ec_cs_get_position;
  (*cmd_map_repp)["cs-get-position"] = ec_cs_get_position;
  (*cmd_map_repp)["cs-get-position-samples"] = ec_cs_get_position_samples;
  (*cmd_map_repp)["getpos"] = ec_cs_get_position;
  (*cmd_map_repp)["get-position"] = ec_cs_get_position;
  (*cmd_map_repp)["cs-get-length"] = ec_cs_get_length;
  (*cmd_map_repp)["cs-get-length-samples"] = ec_cs_get_length_samples;
  (*cmd_map_repp)["get-length"] = ec_cs_get_length;
  (*cmd_map_repp)["cs-set-length"] = ec_cs_set_length;
  (*cmd_map_repp)["cs-set-length-samples"] = ec_cs_set_length_samples;
  (*cmd_map_repp)["cs-toggle-loop"] = ec_cs_toggle_loop;
  (*cmd_map_repp)["cs-option"] = ec_cs_option;
}

void ECA_IAMODE_PARSER::register_commands_c(void)
{
  (*cmd_map_repp)["c-add"] = ec_c_add;
  (*cmd_map_repp)["c-remove"] = ec_c_remove;
  (*cmd_map_repp)["c-list"] = ec_c_list;
  (*cmd_map_repp)["c-select"] = ec_c_select;
  (*cmd_map_repp)["c-selected"] = ec_c_selected;
  (*cmd_map_repp)["c-index-select"] = ec_c_index_select;
  (*cmd_map_repp)["c-iselect"] = ec_c_index_select;
  (*cmd_map_repp)["c-deselect"] = ec_c_deselect;
  (*cmd_map_repp)["c-selected"] = ec_c_selected;
  (*cmd_map_repp)["c-select-all"] = ec_c_select_all;
  (*cmd_map_repp)["c-select-add"] = ec_c_select_add;
  (*cmd_map_repp)["c-clear"] = ec_c_clear;
  (*cmd_map_repp)["c-rename"] = ec_c_rename;
  (*cmd_map_repp)["c-muting"] = ec_c_muting;
  (*cmd_map_repp)["c-mute"] = ec_c_muting;
  (*cmd_map_repp)["c-bypass"] = ec_c_bypass;
  (*cmd_map_repp)["c-status"] = ec_c_status;
}

void ECA_IAMODE_PARSER::register_commands_aio(void)
{
  (*cmd_map_repp)["aio-register"] = ec_aio_register;
  (*cmd_map_repp)["aio-status"] = ec_aio_status;
}

void ECA_IAMODE_PARSER::register_commands_ai(void)
{
  (*cmd_map_repp)["ai-add"] = ec_ai_add;
  (*cmd_map_repp)["ai-describe"] = ec_ai_describe;
  (*cmd_map_repp)["ai-remove"] = ec_ai_remove;
  (*cmd_map_repp)["ai-list"] = ec_ai_list;
  (*cmd_map_repp)["ai-select"] = ec_ai_select;
  (*cmd_map_repp)["ai-index-select"] = ec_ai_index_select;
  (*cmd_map_repp)["ai-iselect"] = ec_ai_index_select;
  (*cmd_map_repp)["ai-selected"] = ec_ai_selected;
  (*cmd_map_repp)["ai-attach"] = ec_ai_attach;
  (*cmd_map_repp)["ai-status"] = ec_ai_status;
  (*cmd_map_repp)["ai-forward"] = ec_ai_forward;
  (*cmd_map_repp)["ai-rewind"] = ec_ai_rewind;
  (*cmd_map_repp)["ai-setpos"] = ec_ai_set_position;
  (*cmd_map_repp)["ai-set-position"] = ec_ai_set_position;
  (*cmd_map_repp)["ai-set-position-samples"] = ec_ai_set_position_samples;
  (*cmd_map_repp)["ai-getpos"] = ec_ai_get_position;
  (*cmd_map_repp)["ai-get-position"] = ec_ai_get_position;
  (*cmd_map_repp)["ai-get-position-samples"] = ec_ai_get_position_samples;
  (*cmd_map_repp)["ai-get-length"] = ec_ai_get_length;
  (*cmd_map_repp)["ai-get-length-samples"] = ec_ai_get_length_samples;
  (*cmd_map_repp)["ai-get-format"] = ec_ai_get_format;
  (*cmd_map_repp)["ai-wave-edit"] = ec_ai_wave_edit;
}

void ECA_IAMODE_PARSER::register_commands_ao(void)
{
  (*cmd_map_repp)["ao-add"] = ec_ao_add;
  (*cmd_map_repp)["ao-add-default"] = ec_ao_add_default;
  (*cmd_map_repp)["ao-describe"] = ec_ao_describe;
  (*cmd_map_repp)["ao-list"] = ec_ao_list;
  (*cmd_map_repp)["ao-select"] = ec_ao_select;
  (*cmd_map_repp)["ao-index-select"] = ec_ao_index_select;
  (*cmd_map_repp)["ao-iselect"] = ec_ao_index_select;
  (*cmd_map_repp)["ao-selected"] = ec_ao_selected;
  (*cmd_map_repp)["ao-attach"] = ec_ao_attach;
  (*cmd_map_repp)["ao-remove"] = ec_ao_remove;
  (*cmd_map_repp)["ao-status"] = ec_ao_status;
  (*cmd_map_repp)["ao-forward"] = ec_ao_forward;
  (*cmd_map_repp)["ao-rewind"] = ec_ao_rewind;
  (*cmd_map_repp)["ao-setpos"] = ec_ao_set_position;
  (*cmd_map_repp)["ao-set-position"] = ec_ao_set_position;
  (*cmd_map_repp)["ao-set-position-samples"] = ec_ao_set_position_samples;
  (*cmd_map_repp)["ao-getpos"] = ec_ao_get_position;
  (*cmd_map_repp)["ao-get-position"] = ec_ao_get_position;
  (*cmd_map_repp)["ao-get-position-samples"] = ec_ao_get_position_samples;
  (*cmd_map_repp)["ao-get-length"] = ec_ao_get_length;
  (*cmd_map_repp)["ao-get-length-samples"] = ec_ao_get_length_samples;
  (*cmd_map_repp)["ao-get-format"] = ec_ao_get_format;
  (*cmd_map_repp)["ao-wave-edit"] = ec_ao_wave_edit;
}

void ECA_IAMODE_PARSER::register_commands_cop(void)
{
  (*cmd_map_repp)["cop-add"] = ec_cop_add;
  (*cmd_map_repp)["cop-describe"] = ec_cop_describe;
  (*cmd_map_repp)["cop-remove"] = ec_cop_remove;
  (*cmd_map_repp)["cop-list"] = ec_cop_list;
  (*cmd_map_repp)["cop-select"] = ec_cop_select;
  (*cmd_map_repp)["cop-index-select"] = ec_cop_select;
  (*cmd_map_repp)["cop-iselect"] = ec_cop_select;
  (*cmd_map_repp)["cop-register"] = ec_cop_register;
  (*cmd_map_repp)["cop-selected"] = ec_cop_selected;
  (*cmd_map_repp)["cop-set"] = ec_cop_set;
  (*cmd_map_repp)["cop-get"] = ec_cop_get;
  (*cmd_map_repp)["cop-status"] = ec_cop_status;
}

void ECA_IAMODE_PARSER::register_commands_copp(void)
{
  (*cmd_map_repp)["copp-list"] = ec_copp_list;
  (*cmd_map_repp)["copp-select"] = ec_copp_select;
  (*cmd_map_repp)["copp-index-select"] = ec_copp_select;
  (*cmd_map_repp)["copp-iselect"] = ec_copp_select;
  (*cmd_map_repp)["copp-selected"] = ec_copp_selected;
  (*cmd_map_repp)["copp-set"] = ec_copp_set;
  (*cmd_map_repp)["copp-get"] = ec_copp_get;
}

void ECA_IAMODE_PARSER::register_commands_ctrl(void)
{
  (*cmd_map_repp)["ctrl-add"] = ec_ctrl_add;
  (*cmd_map_repp)["ctrl-describe"] = ec_ctrl_describe;
  (*cmd_map_repp)["ctrl-remove"] = ec_ctrl_remove;
  (*cmd_map_repp)["ctrl-list"] = ec_ctrl_list;
  (*cmd_map_repp)["ctrl-select"] = ec_ctrl_select;
  (*cmd_map_repp)["ctrl-index-select"] = ec_ctrl_select;
  (*cmd_map_repp)["ctrl-iselect"] = ec_ctrl_select;
  (*cmd_map_repp)["ctrl-register"] = ec_ctrl_register;
  (*cmd_map_repp)["ctrl-selected"] = ec_ctrl_selected;
  (*cmd_map_repp)["ctrl-status"] = ec_ctrl_status;
  (*cmd_map_repp)["ctrl-get-target"] = ec_ctrl_get_target;
}

void ECA_IAMODE_PARSER::register_commands_ctrlp(void)
{
  (*cmd_map_repp)["ctrlp-list"] = ec_ctrlp_list;
  (*cmd_map_repp)["ctrlp-select"] = ec_ctrlp_select;
  (*cmd_map_repp)["ctrlp-selected"] = ec_ctrlp_selected;
  (*cmd_map_repp)["ctrlp-get"] = ec_ctrlp_get;
  (*cmd_map_repp)["ctrlp-set"] = ec_ctrlp_set;
}

void ECA_IAMODE_PARSER::register_commands_dump(void)
{
  (*cmd_map_repp)["dump-target"] = ec_dump_target;
  (*cmd_map_repp)["dump-status"] = ec_dump_status;
  (*cmd_map_repp)["dump-position"] = ec_dump_position;
  (*cmd_map_repp)["dump-length"] = ec_dump_length;
  (*cmd_map_repp)["dump-cs-status"] = ec_dump_cs_status;
  (*cmd_map_repp)["dump-c-selected"] = ec_dump_c_selected;
  (*cmd_map_repp)["dump-ai-selected"] = ec_dump_ai_selected;
  (*cmd_map_repp)["dump-ai-position"] = ec_dump_ai_position;
  (*cmd_map_repp)["dump-ai-length"] = ec_dump_ai_length;
  (*cmd_map_repp)["dump-ai-open-state"] = ec_dump_ai_open_state;
  (*cmd_map_repp)["dump-ao-selected"] = ec_dump_ao_selected;
  (*cmd_map_repp)["dump-ao-position"] = ec_dump_ao_position;
  (*cmd_map_repp)["dump-ao-length"] = ec_dump_ao_length;
  (*cmd_map_repp)["dump-ao-open-state"] = ec_dump_ao_open_state;
  (*cmd_map_repp)["dump-cop-value"] = ec_dump_cop_value;
}

void ECA_IAMODE_PARSER::register_commands_external(void)
{
#if ECA_COMPILE_JACK
  (*cmd_map_repp)["jack-connect"] = ec_jack_connect;
  (*cmd_map_repp)["jack-disconnect"] = ec_jack_disconnect;
  (*cmd_map_repp)["jack-list-connections"] = ec_jack_list_connections;
#endif
}

int ECA_IAMODE_PARSER::command_to_action_id(const std::string& cmdstring)
{
  return (*cmd_map_repp)[cmdstring];
}

bool ECA_IAMODE_PARSER::action_requires_params(int id)
{
  switch(id) {
  case ec_debug:

  case ec_cs_add:
  case ec_cs_select:
  case ec_cs_index_select:
  case ec_cs_load: 
  case ec_cs_save_as: 
  case ec_cs_set_param:
  case ec_cs_set_audio_format:
  case ec_cs_set_length:
  case ec_cs_set_length_samples:
  case ec_cs_rewind:
  case ec_cs_forward:
  case ec_cs_set_position:
  case ec_cs_option:

  case ec_c_add:
  case ec_c_select:
  case ec_c_index_select:
  case ec_c_deselect:
  case ec_c_select_add:
  case ec_c_rename:

  case ec_ai_add:
  case ec_ai_select:
  case ec_ai_index_select:
  case ec_ai_forward:
  case ec_ai_rewind:
  case ec_ai_set_position:
  case ec_ai_set_position_samples:

  case ec_ao_add:
  case ec_ao_select:
  case ec_ao_index_select:
  case ec_ao_forward:
  case ec_ao_rewind:
  case ec_ao_set_position:
  case ec_ao_set_position_samples:

  case ec_cop_add:
  case ec_cop_select:
  case ec_cop_set:
  case ec_cop_get:

  case ec_copp_select:
  case ec_copp_set:

  case ec_ctrl_add:
  case ec_ctrl_select:

  case ec_int_set_float_to_string_precision:

  case ec_dump_target:
  case ec_dump_cop_value:

  case ec_jack_connect:
  case ec_jack_disconnect:

    return true;
    
  default: 
    break;
  }
  return false;
}

bool ECA_IAMODE_PARSER::action_requires_connected(int id)
{
  switch(id) {
  case ec_engine_launch:
  case ec_engine_halt:
  case ec_start:
  case ec_run:

  case ec_cs_disconnect:
  case ec_cs_set_position:
  case ec_cs_set_position_samples:
    return true;
    
  default: 
    break;
  }
  return false;
}

bool ECA_IAMODE_PARSER::action_requires_selected(int id)
{
  switch(id) {

  case ec_cs_remove: 
  case ec_cs_edit:
  case ec_cs_is_valid:
  case ec_cs_save: 
  case ec_cs_save_as: 
  case ec_cs_connect: 
  case ec_cs_set_param:
  case ec_cs_rewind:
  case ec_cs_forward:
  case ec_cs_set_position:
  case ec_cs_set_position_samples:
  case ec_cs_get_position:
  case ec_cs_get_position_samples:
  case ec_cs_get_length:
  case ec_cs_get_length_samples:
  case ec_cs_set_length:
  case ec_cs_set_length_samples:
  case ec_cs_toggle_loop:
  case ec_cs_option:

  case ec_c_remove:
  case ec_c_clear:
  case ec_c_rename:
  case ec_c_muting:
  case ec_c_bypass:
  case ec_c_status:
  case ec_c_list:
  case ec_c_select:
  case ec_c_selected:

  case ec_aio_status:

  case ec_ai_add:
  case ec_ai_select:
  case ec_ai_selected:
  case ec_ai_index_select:
  case ec_ai_remove:
  case ec_ai_attach:
  case ec_ai_status:
  case ec_ai_forward:
  case ec_ai_rewind:
  case ec_ai_set_position:
  case ec_ai_set_position_samples:
  case ec_ai_get_position:
  case ec_ai_get_position_samples:
  case ec_ai_get_length:
  case ec_ai_get_length_samples:
  case ec_ai_get_format:
  case ec_ai_wave_edit:

  case ec_ao_add:
  case ec_ao_add_default:
  case ec_ao_select:
  case ec_ao_selected:
  case ec_ao_index_select:
  case ec_ao_remove:
  case ec_ao_attach:
  case ec_ao_status:
  case ec_ao_forward:
  case ec_ao_rewind:
  case ec_ao_set_position:
  case ec_ao_set_position_samples:
  case ec_ao_get_position:
  case ec_ao_get_position_samples:
  case ec_ao_get_length:
  case ec_ao_get_length_samples:
  case ec_ao_get_format:
  case ec_ao_wave_edit:

  case ec_cop_add:
  case ec_cop_list:
  case ec_cop_select:
  case ec_cop_selected:
  case ec_cop_set:
  case ec_cop_get:
  case ec_cop_status:

  case ec_copp_list:
  case ec_copp_select:
  case ec_copp_selected:
  case ec_copp_set:

  case ec_ctrl_add:
  case ec_ctrl_select:
  case ec_ctrl_selected:
  case ec_ctrl_status:

    return true;

  default: break;
  }
  
  return false;
}

bool ECA_IAMODE_PARSER::action_requires_selected_not_connected(int id)
{
  switch(id) {
  case ec_cs_remove:
  case ec_cs_set_length:
  case ec_cs_set_length_samples:
  case ec_cs_toggle_loop:
  case ec_cs_set_param:
  case ec_cs_option:

  case ec_c_add:
  case ec_c_remove:
  case ec_c_rename:
  case ec_c_clear:

  case ec_ai_add:
  case ec_ai_remove:
  case ec_ai_attach:
  case ec_ai_forward:
  case ec_ai_rewind:
  case ec_ai_set_position:
  case ec_ai_set_position_samples:
  case ec_ai_wave_edit:

  case ec_ao_add:
  case ec_ao_add_default:
  case ec_ao_remove:
  case ec_ao_attach:
  case ec_ao_forward:
  case ec_ao_rewind:
  case ec_ao_set_position:
  case ec_ao_set_position_samples:
  case ec_ao_wave_edit:

    return true;
    
  default: 
    break;
  }
  return false;

}

bool ECA_IAMODE_PARSER::action_requires_selected_audio_input(int id)
{
  switch(id) {
  case ec_ai_describe:
  case ec_ai_remove:
  case ec_ai_attach:
  case ec_ai_forward:
  case ec_ai_rewind:
  case ec_ai_set_position:
  case ec_ai_set_position_samples:
  case ec_ai_get_position:
  case ec_ai_get_position_samples:
  case ec_ai_selected:
  case ec_ai_get_length:
  case ec_ai_get_length_samples:
  case ec_ai_get_format:
  case ec_ai_wave_edit:
    return true;
    
  default: 
    break;
  }
  return false;

}

bool ECA_IAMODE_PARSER::action_requires_selected_audio_output(int id)
{
  switch(id) {
  case ec_ao_describe:
  case ec_ao_remove:
  case ec_ao_attach:
  case ec_ao_forward:
  case ec_ao_rewind:
  case ec_ao_set_position:
  case ec_ao_set_position_samples:
  case ec_ao_get_position:
  case ec_ao_get_position_samples:
  case ec_ao_selected:
  case ec_ao_get_length:
  case ec_ao_get_length_samples:
  case ec_ao_get_format:
  case ec_ao_wave_edit:
    return true;
    
  default: 
    break;
  }
  return false;

}

void show_controller_help(void)
{
  MESSAGE_ITEM mitem; 

  mitem << "\n-------------------------------------------------------------------";
  mitem << "\n ecasound interactive-mode - command reference";
  mitem << "\n-------------------------------------------------------------------";

  mitem << "\n'q' - Quits ecasound";
  mitem << "\n'start', 't' - Processing is started (play)";
  mitem << "\n'stop', 's' - Stops processing"; 
  mitem << "\n'rewind time-in-seconds', 'rw time-in-seconds' - Rewind";
  mitem << "\n'forward time-in-seconds', 'fw time-in-seconds' - Forward";
  mitem << "\n'setpos time-in-seconds' - Sets the current position to 'time-in-seconds' seconds from the beginning.";
  mitem << "\n'engine-launch' - Initialize and start engine";
  mitem << "\n'engine-status' - Engine status";
  mitem << "\n'cs-status', 'st' - Chainsetup status";
  mitem << "\n'c-status', 'cs' - Chain status";
  mitem << "\n'cop-status', 'es' - Chain operator status";
  mitem << "\n'ctrl-status' - Controller status"; 
  mitem << "\n'aio-status', 'fs' - Audio input/output status";

  mitem << "\n--- see ecasound-iam(1) manual page for more info -----------------\n";
  //  mitem << "\n'chain chainname', 'c chainname' - Enable/disable the the chain 'chainname'";
 
  ECA_LOG_MSG(ECA_LOGGER::info, mitem.to_string());
}
