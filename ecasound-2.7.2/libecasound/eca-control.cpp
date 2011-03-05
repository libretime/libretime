// ------------------------------------------------------------------------
// eca-control.cpp: Class for controlling the whole ecasound library
// Copyright (C) 1999-2005,2008,2009 Kai Vehmanen
// Copyright (C) 2005 Stuart Allie
// Copyright (C) 2009 Adam Linson
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
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// ------------------------------------------------------------------------

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cassert>
#include <iostream>
#include <fstream>
#include <string>
#include <vector>
#include <list>
#include <algorithm>
#include <unistd.h>

#ifdef HAVE_LOCALE_H
#include <locale.h>
#endif

#include <kvu_utils.h> /* string_to_vector(), string_to_int_vector() */
#include <kvu_value_queue.h>
#include <kvu_message_item.h>
#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "audioio.h"
#include "eca-chain.h"
#include "eca-chainop.h"
#include "eca-chainsetup.h"
#include "eca-control.h"
#include "eca-control-main.h"
#include "eca-engine.h"
#include "eca-object-factory.h"
#include "eca-object-map.h"
#include "eca-preset-map.h"
#include "eca-session.h"

#include "generic-controller.h"
#include "eca-chainop.h"
#include "audiofx_ladspa.h"
#include "preset.h"
#include "sample-specs.h"
#include "jack-connections.h"

#include "eca-version.h"
#include "eca-error.h"
#include "eca-logger.h"
#include "eca-logger-wellformed.h"

/**
 * Import namespaces
 */
using std::string;
using std::list;
using std::vector;
using std::cerr;
using std::endl;
using namespace ECA;

/**
 * Declarations for private static functions
 */
static string eca_aio_register_sub(ECA_OBJECT_MAP& objmap);

/**
 * Definitions for member functions
 */

ECA_CONTROL::ECA_CONTROL (ECA_SESSION* psession) 
  : ctrl_dump_rep(this),
    wellformed_mode_rep(false)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "ECA_CONTROL constructor");

  session_repp = psession;
  selected_chainsetup_repp = psession->selected_chainsetup_repp;
  engine_repp = 0;
  engine_pid_rep = -1;
  engine_exited_rep.set(0);
  float_to_string_precision_rep = 3;
  joining_rep = false;
  DBC_CHECK(is_engine_created() != true);

  selected_audio_object_repp = 0;
  selected_audio_input_repp = 0;
  selected_audio_output_repp = 0;
}

ECA_CONTROL::~ECA_CONTROL(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "ECA_CONTROL destructor");
  close_engine();
}

void ECA_CONTROL::fill_command_retval(struct eci_return_value *retval) const
{
  if (retval == 0)
    return;

  *retval = last_retval_rep;
}

void ECA_CONTROL::command(const string& cmd_and_args, struct eci_return_value *retval)
{
  clear_last_values();
  clear_action_arguments();

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "processing cmd and arg: " + cmd_and_args);

  vector<string> tokens = kvu_string_to_tokens_quoted(cmd_and_args);
  vector<string>::iterator cmd = tokens.begin();
  if (cmd != tokens.end()) {
    const std::map<std::string,int>& cmdmap = ECA_IAMODE_PARSER::registered_commands();
    if (cmdmap.find(*cmd) == cmdmap.end()) {
      // ---
      // *p is not recognized as a iamode command
      // ---
      if (cmd->size() > 0 && (*cmd)[0] == '-') {
	//  std::cerr << "Note! Direct use of EOS-options (-prefix:arg1,...,argN)" << " as iactive-mode commands is considered deprecated. " << "\tUse the notation 'cs-option -prefix:a,b,x' instead." << std::endl;
	if (*cmd == "-i")
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "WARNING: syntax variant '-i file.ext' not supported, please use 'ai-add file.ext' instead.");
	else if (*cmd == "-o")
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "WARNING: syntax variant '-o file.ext' not supported, please use 'ai-add file.ext' instead.");
	else {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, "passiong to cs-option: " + cmd_and_args);
	  chainsetup_option(cmd_and_args);
	}
      }
      else {
	set_last_error("Unknown command!");
      }
    }
    else {
      int action_id = ECA_IAMODE_PARSER::command_to_action_id(*cmd);
      if (action_id == ec_help) {
	show_controller_help();
      }
      else {
	vector<string>::iterator args = cmd + 1;
	if (args != tokens.end()) {
	  set_action_argument(vector<string> (args, tokens.end()));
	}
	action(action_id);
      }
    }
  }

  fill_command_retval(retval);
}

void ECA_CONTROL::set_action_argument(const string& s)
{
  action_args_rep.resize(0);
  action_args_rep.push_back(s);
  action_arg_f_set_rep = false;
}

void ECA_CONTROL::set_action_argument(const std::vector<std::string>& s)
{
  action_args_rep = s;
  action_arg_f_set_rep = false;
}

void ECA_CONTROL::set_action_argument(double v)
{
  action_arg_f_rep = v;
  action_arg_f_set_rep = true;
}

void ECA_CONTROL::clear_action_arguments(void)
{
  // use resize() instead of clear(); clear() was a late
  // addition to C++ standard and not supported by all
  // compilers (for example egcs-2.91.66) 
  action_args_rep.resize(0);

  action_arg_f_rep = 0.0f;
  action_arg_f_set_rep = false;
}

double ECA_CONTROL::first_action_argument_as_float(void) const
{
  if (action_arg_f_set_rep == true)
    return action_arg_f_rep;
  
  if (action_args_rep.size() == 0)
    return 0.0f;

  return atof(action_args_rep[0].c_str());
}

string ECA_CONTROL::first_action_argument_as_string(void) const
{
  if (action_args_rep.size() == 0)
    return std::string();

  return action_args_rep[0];
}

const vector<string>& ECA_CONTROL::action_arguments_as_vector(void) const
{
  return action_args_rep;
}

int ECA_CONTROL::first_action_argument_as_int(void) const
{
  if (action_args_rep.size() == 0)
    return 0;

  return atoi(action_args_rep[0].c_str());
}

long int ECA_CONTROL::first_action_argument_as_long_int(void) const
{
  if (action_args_rep.size() == 0)
    return 0;

  return atol(action_args_rep[0].c_str());
}

SAMPLE_SPECS::sample_pos_t ECA_CONTROL::first_action_argument_as_samples(void) const
{
  if (action_args_rep.size() == 0)
    return 0;

#ifdef HAVE_ATOLL
  return atoll(action_args_rep[0].c_str());
#else
  return atol(action_args_rep[0].c_str());
#endif
}

void ECA_CONTROL::command_float_arg(const string& cmd, double arg, struct eci_return_value *retval)
{
  clear_action_arguments();
  set_action_argument(arg);
  int action_id = ec_unknown;
  action_id = ECA_IAMODE_PARSER::command_to_action_id(cmd);
  action(action_id);
  fill_command_retval(retval);
}

/**
 * Interprets an EOS (ecasound optiont syntax) token  (prefixed with '-').
 */
void ECA_CONTROL::chainsetup_option(const string& cmd)
{
  string prefix = kvu_get_argument_prefix(cmd);
  if (prefix == "el" || prefix == "pn") { // --- LADSPA plugins and presets
    if (selected_chains().size() == 1) 
      add_chain_operator(cmd);
    else
      set_last_error("When adding chain operators, only one chain can be selected.");
  }
  else if (ECA_OBJECT_FACTORY::chain_operator_map().object(prefix) != 0) {
    if (selected_chains().size() == 1) 
      add_chain_operator(cmd);
    else
      set_last_error("When adding chain operators, only one chain can be selected.");
  }
  else if (ECA_OBJECT_FACTORY::controller_map().object(prefix) != 0) {
    if (selected_chains().size() == 1) 
      add_controller(cmd);
    else
      set_last_error("When adding controllers, only one chain can be selected.");
  }
  else {
    set_action_argument(cmd);
    action(ec_cs_option);
  }
}

/**
 * Checks action preconditions.
 *
 * @return Sets status of private data members 'action_ok', 
 *         'action_restart', and 'action_reconnect'.
 */
void ECA_CONTROL::check_action_preconditions(int action_id)
{
  action_ok = true;
  action_reconnect = false;
  action_restart = false;

  /* case 1: action requiring arguments, but not arguments available */
  if (action_arg_f_set_rep == false && 
      first_action_argument_as_string().size() == 0 &&
      action_requires_params(action_id)) {
    set_last_error("Can't perform requested action; argument omitted.");
    action_ok = false;
  }
  /* case 2: action requires an audio input, but no input available */
  else if (is_selected() == true &&
	   get_audio_input() == 0 &&
	   action_requires_selected_audio_input(action_id)) {
    set_last_error("Can't perform requested action; no audio input selected.");
    action_ok = false;
  }
  /* case 3: action requires an audio output, but no output available */
  else if (is_selected() == true &&
	   get_audio_output() == 0 &&
	   action_requires_selected_audio_output(action_id)) {
    set_last_error("Can't perform requested action; no audio output selected.");
    action_ok = false;
  }
  /* case 4: action requires a select chainsetup, but none selected */
  else if (is_selected() == false &&
	   action_requires_selected(action_id)) {
    if (!is_connected()) {
      set_last_error("Can't perform requested action; no chainsetup selected.");
      action_ok = false;
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: No chainsetup selected. Connected chainsetup will be selected.");
      select_chainsetup(connected_chainsetup());
    }
  }
  /* case 5: action requires a connected chainsetup, but none connected */
  else if (is_connected() == false &&
	   action_requires_connected(action_id)) {
    if (!is_selected()) {
      set_last_error("Can't perform requested action; no chainsetup connected.");
      action_ok = false;
    }
    else {
      if (is_valid() == true) {
	ECA_LOG_MSG(ECA_LOGGER::info, 
		    "NOTE: No chainsetup connected. Trying to connect currently selected chainsetup \""
		    + selected_chainsetup_repp->name() 
		    + "\"");
	connect_chainsetup(0);
      }
      if (is_connected() != true) {
	/* connect_chainsetup() sets last_error() so we just add to it */
	set_last_error(last_error() + " Selected chainsetup cannot be connected. Can't perform requested action. ");
	action_ok = false;
      }
    }
  }
  /* case 6: action can't be performed on a connected setup, 
   *         but selected chainsetup is also connected */
  else if (selected_chainsetup() == connected_chainsetup() &&
	   action_requires_selected_not_connected(action_id)) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: This operation requires that chainsetup is disconnected. Temporarily disconnecting...");
    if (is_running()) action_restart = true;
    disconnect_chainsetup();
    action_reconnect = true;
  }
}

void ECA_CONTROL::action(int action_id, 
			 const vector<string>& args) 
{
  ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: ECA_CONTROL::action() method is obsolete.\n");
  clear_action_arguments();
  set_action_argument(kvu_vector_to_string(args, " "));
  action(action_id);
}

bool ECA_CONTROL::action_helper_check_cop_op_args(int copid, int coppid)
{
  DBC_REQUIRE(is_selected() == true);

  const vector<string>& selchains =
    selected_chainsetup_repp->selected_chains();
  bool res = false;

  if (selchains.size() == 0) {
    set_last_error("No chain selected, unable to identify chainop");
  }
  else if (selchains.size() > 1) {
    set_last_error("More than one chain selected, unable to identify chainop");
  }
  else {
    const CHAIN* selch = 
      selected_chainsetup_repp->get_chain_with_name(selchains[0]);
    if (copid < 1 || copid > selch->number_of_chain_operators()) {
      set_last_error("Invalid chainop-id, unable to identify chainop");
    }
    else if (coppid < 1) {
      set_last_error("Invalid copp-id, indexing starts from 1.");
    }
    else {
      res = true;
    }
  }

  return res;
}

void ECA_CONTROL::action(int action_id)
{
  clear_last_values();
  check_action_preconditions(action_id);

  if (action_ok != true) return;

  switch(action_id) {
  case ec_unknown: { set_last_error("Unknown command!"); break; }

    // ---
    // General
    // ---
  case ec_exit: { quit(); break; }
  case ec_start: 
    { 
      if (is_running() != true) {
	int result = start();
	if (result < 0) {
	  set_last_error("Error, unable to start processing");
	}
      }
      // ECA_LOG_MSG(ECA_LOGGER::info, "Can't perform requested action; no chainsetup connected.");
      break; 
    }
  case ec_stop: { if (is_running() == true) stop(); break; }
  case ec_run: 
    { 
      int result = run(); 
      if (result < 0) {
	set_last_error("Errors during processing");
      }
      break; 
    }
  case ec_debug:
    {
      int level = first_action_argument_as_int();
      ECA_LOGGER::instance().set_log_level_bitmask(level);
      set_last_string("Debug level set to " + kvu_numtostr(level) + ".");
      break;
    }
  case ec_resource_file:
    {
      session_repp->interpret_general_option(string("-R:") + 
					     first_action_argument_as_string());
      break;
    }

    // ---
    // Chainsetups
    // ---
  case ec_cs_add:
    {
      add_chainsetup(first_action_argument_as_string());
      break;
    }
  case ec_cs_remove: { remove_chainsetup(); break; }
  case ec_cs_list: { set_last_string_list(chainsetup_names()); break; }
  case ec_cs_select: { select_chainsetup(first_action_argument_as_string()); break; }
  case ec_cs_selected: { set_last_string(selected_chainsetup()); break; }
  case ec_cs_index_select: { 
    if (first_action_argument_as_string().size() > 0) {
      select_chainsetup_by_index(first_action_argument_as_int());
    }
    break; 
  }
  case ec_cs_edit: { edit_chainsetup(); break; }
  case ec_cs_load: { load_chainsetup(first_action_argument_as_string()); break; }
  case ec_cs_save: { save_chainsetup(""); break; }
  case ec_cs_save_as: { save_chainsetup(first_action_argument_as_string()); break; }
  case ec_cs_is_valid: { 
    if (is_valid() == true) 
      set_last_integer(1);
    else
      set_last_integer(0);
    break;
  }
  case ec_cs_connect: 
    { 
      if (is_valid() != false) {
	connect_chainsetup(0); 
      }
      else {
	set_last_error("Can't connect; chainsetup not valid!");
      }
      break; 
    }
  case ec_cs_connected: { set_last_string(connected_chainsetup()); break; }
  case ec_cs_disconnect: { disconnect_chainsetup(); break; }
  case ec_cs_set_param: { set_chainsetup_parameter(first_action_argument_as_string()); break; }
  case ec_cs_set_audio_format: { set_chainsetup_sample_format(first_action_argument_as_string()); break; }
  case ec_cs_status: { 
    set_last_string(chainsetup_status()); 
    break; 
  }
  case ec_cs_rewind: { change_chainsetup_position(-first_action_argument_as_float()); break; }
  case ec_cs_forward: { change_chainsetup_position(first_action_argument_as_float()); break; }
  case ec_cs_set_position: { set_chainsetup_position(first_action_argument_as_float()); break; }
  case ec_cs_set_position_samples: { set_chainsetup_position_samples(first_action_argument_as_samples()); break; }
  case ec_cs_get_position: { set_last_float(position_in_seconds_exact()); break; }
  case ec_cs_get_position_samples: { set_last_long_integer(selected_chainsetup_repp->position_in_samples()); break; }
  case ec_cs_get_length: { set_last_float(length_in_seconds_exact()); break; }
  case ec_cs_get_length_samples: { set_last_long_integer(length_in_samples()); break; }
  case ec_cs_set_length: 
    { 
      set_chainsetup_processing_length_in_seconds(first_action_argument_as_float()); 
      break; 
    }
  case ec_cs_set_length_samples:
    {
      set_chainsetup_processing_length_in_samples(first_action_argument_as_samples());
      break;
    }
  case ec_cs_toggle_loop: { toggle_chainsetup_looping(); break; } 
  case ec_cs_option: 
    {
      selected_chainsetup_repp->interpret_options(action_arguments_as_vector());
      if (selected_chainsetup_repp->interpret_result() != true) {
	set_last_error(selected_chainsetup_repp->interpret_result_verbose());
      }
      break;
    }

  // ---
  // Chains
  // ---
  case ec_c_add: { add_chains(kvu_string_to_vector(first_action_argument_as_string(), ',')); break; }
  case ec_c_remove: { remove_chains(); break; }
  case ec_c_list: { set_last_string_list(chain_names()); break; }
  case ec_c_select: { select_chains(kvu_string_to_vector(first_action_argument_as_string(), ',')); break; }
  case ec_c_selected: { set_last_string_list(selected_chains()); break; }
  case ec_c_index_select: { select_chains_by_index(kvu_string_to_int_vector(first_action_argument_as_string(), ',')); break; }
  case ec_c_deselect: { deselect_chains(kvu_string_to_vector(first_action_argument_as_string(), ',')); break; }
  case ec_c_select_add: 
    { 
      select_chains(kvu_string_to_vector(first_action_argument_as_string() + "," +
					 kvu_vector_to_string(selected_chains(), ","), ',')); 
      break; 
    }
  case ec_c_select_all: { select_all_chains(); break; }
  case ec_c_clear: { clear_chains(); break; }
  case ec_c_rename: 
    { 
      if (selected_chains().size() != 1) {
	set_last_error("When renaming chains, only one chain canbe selected.");
      }
      else {
	rename_chain(first_action_argument_as_string()); 
      }
      break;
    }
  case ec_c_muting: { toggle_chain_muting(); break; }
  case ec_c_bypass: { toggle_chain_bypass(); break; }
  case ec_c_status: 
    { 
      set_last_string(chain_status()); 
      break; 
    }

    // ---
    // Actions common to audio inputs and outputs
    // ---
  case ec_aio_status:
  case ec_ai_status:
  case ec_ao_status:
    { 
      set_last_string(aio_status()); 
      break; 
    }
  case ec_aio_register: { aio_register(); break; }

    // ---
    // Audio input objects
    // ---
  case ec_ai_add: { add_audio_input(first_action_argument_as_string()); break; }
  case ec_ai_describe: { set_last_string(ECA_OBJECT_FACTORY::audio_object_to_eos(selected_audio_input_repp, "i")); break; }
  case ec_ai_remove: { remove_audio_input(); break; }
  case ec_ai_list: { set_last_string_list(audio_input_names()); break; }
  case ec_ai_select: { select_audio_input(first_action_argument_as_string()); break; }
  case ec_ai_selected: { set_last_string(get_audio_input()->label()); break; }
  case ec_ai_index_select: { 
    if (first_action_argument_as_string().size() > 0) {
	select_audio_input_by_index(first_action_argument_as_int());
    }
    break; 
  }
  case ec_ai_attach: { attach_audio_input(); break; }
  case ec_ai_forward: 
    { 
      audio_input_as_selected();
      forward_audio_object(first_action_argument_as_float()); 
      break; 
    }
  case ec_ai_rewind: 
    { 
      audio_input_as_selected();
      rewind_audio_object(first_action_argument_as_float()); 
      break; 
    }
  case ec_ai_set_position: { audio_input_as_selected(); set_audio_object_position(first_action_argument_as_float()); break; }
  case ec_ai_set_position_samples: { audio_input_as_selected(); set_audio_object_position_samples(first_action_argument_as_long_int()); break; }
  case ec_ai_get_position: { set_last_float(get_audio_input()->position().seconds()); break; }
  case ec_ai_get_position_samples: { set_last_long_integer(get_audio_input()->position().samples()); break; }
  case ec_ai_get_length: { set_last_float(get_audio_input()->length().seconds()); break; }
  case ec_ai_get_length_samples: { set_last_long_integer(get_audio_input()->length().samples()); break; }
  case ec_ai_get_format: {
    set_last_string(get_audio_input()->format_string() + "," +
		    kvu_numtostr(get_audio_input()->channels()) + "," +
		    kvu_numtostr(get_audio_input()->samples_per_second())); 

    break; 
  }

  case ec_ai_wave_edit: { audio_input_as_selected(); wave_edit_audio_object(); break; }

    // ---
    // Audio output objects
    // ---
  case ec_ao_add: { if (first_action_argument_as_string().size() == 0) add_default_output(); else add_audio_output(first_action_argument_as_string()); break; }
  case ec_ao_add_default: { add_default_output(); break; }
  case ec_ao_describe: { set_last_string(ECA_OBJECT_FACTORY::audio_object_to_eos(selected_audio_output_repp, "o")); break; }
  case ec_ao_remove: { remove_audio_output(); break; }
  case ec_ao_list: { set_last_string_list(audio_output_names()); break; }
  case ec_ao_select: { select_audio_output(first_action_argument_as_string()); break; }
  case ec_ao_selected: { set_last_string(get_audio_output()->label()); break; }
  case ec_ao_index_select: { 
    select_audio_output_by_index(first_action_argument_as_int());
    break; 
  }
  case ec_ao_attach: { attach_audio_output(); break; }
  case ec_ao_forward: 
    { 
      audio_output_as_selected();
      forward_audio_object(first_action_argument_as_float()); 
      break; 
    }
  case ec_ao_rewind: 
    { 
      audio_output_as_selected();
      rewind_audio_object(first_action_argument_as_float()); 
      break; 
    }
  case ec_ao_set_position: { audio_output_as_selected(); set_audio_object_position(first_action_argument_as_float()); break; }
  case ec_ao_set_position_samples: { audio_output_as_selected(); set_audio_object_position_samples(first_action_argument_as_long_int()); break; }
  case ec_ao_get_position: { set_last_float(get_audio_output()->position().seconds()); break; }
  case ec_ao_get_position_samples: { set_last_long_integer(get_audio_output()->position().samples()); break; }
  case ec_ao_get_length: { set_last_float(get_audio_output()->length().seconds()); break; }
  case ec_ao_get_length_samples: { set_last_long_integer(get_audio_output()->length().samples()); break; }
  case ec_ao_get_format: { 
    set_last_string(get_audio_output()->format_string() + "," +
		    kvu_numtostr(get_audio_output()->channels()) + "," +
		    kvu_numtostr(get_audio_output()->samples_per_second())); 
    break; 
  }
  case ec_ao_wave_edit: { audio_output_as_selected(); wave_edit_audio_object(); break; }

    // ---
    // Chain operators
    // ---
  case ec_cop_add: { add_chain_operator(first_action_argument_as_string()); break; }
  case ec_cop_describe: 
    { 
      const CHAIN_OPERATOR *t = get_chain_operator();
      set_last_string(t == 0 ? "" : ECA_OBJECT_FACTORY::chain_operator_to_eos(t)); 
      break;
    }
  case ec_cop_remove: { remove_chain_operator(); break; }
  case ec_cop_list: { set_last_string_list(chain_operator_names()); break; }
  case ec_cop_select: { select_chain_operator(first_action_argument_as_int()); break; }
  case ec_cop_selected: { set_last_integer(selected_chain_operator()); break; }
  case ec_cop_set: 
    { 
      vector<string> a = kvu_string_to_vector(first_action_argument_as_string(), ',');
      if (a.size() < 3) {
	set_last_error("Not enough parameters!");
	break;
      }
      int id1 = atoi(a[0].c_str());
      int id2 = atoi(a[1].c_str());
      CHAIN_OPERATOR::parameter_t v = atof(a[2].c_str());

      bool valid = 
	action_helper_check_cop_op_args(id1, id2);
      if (valid == true) {
	select_chain_operator(id1);
	select_chain_operator_parameter(id2);
	set_chain_operator_parameter(v);
      }
      // note: helper func sets the error string if needed
      break; 
    }
  case ec_cop_get:
    {
      vector<string> a =
	kvu_string_to_vector(first_action_argument_as_string(), ',');
      if (a.size() < 2) {
        set_last_error("Not enough parameters!");
        break;
      }
      int id1 = atoi(a[0].c_str());
      int id2 = atoi(a[1].c_str());
      bool valid = 
	action_helper_check_cop_op_args(id1, id2);
      if (valid == true) {
        select_chain_operator(id1);
        select_chain_operator_parameter(id2);
        set_last_float(get_chain_operator_parameter());
      }
      // note: helper func sets the error string if needed
      break;
    }

  case ec_cop_status: 
    { 
      set_last_string(chain_operator_status()); 
      break; 
    }

    // ---
    // Chain operator parameters
    // ---
  case ec_copp_list: { set_last_string_list(chain_operator_parameter_names()); break; }
  case ec_copp_select: { select_chain_operator_parameter(first_action_argument_as_int()); break; }
  case ec_copp_selected: { set_last_integer(selected_chain_operator_parameter()); break; }
  case ec_copp_set: { set_chain_operator_parameter(first_action_argument_as_float()); break; }
  case ec_copp_get: { set_last_float(get_chain_operator_parameter()); break; }

    // ---
    // Controllers
    // ---
  case ec_ctrl_add: { add_controller(first_action_argument_as_string()); break; }
  case ec_ctrl_describe: 
    { 
      const GENERIC_CONTROLLER *t = get_controller();
      set_last_string(t == 0 ? "" : ECA_OBJECT_FACTORY::controller_to_eos(t)); 
      break;
    }
  case ec_ctrl_remove: { remove_controller(); break; }
  case ec_ctrl_list: { set_last_string_list(controller_names()); break; }
  case ec_ctrl_select: { select_controller(first_action_argument_as_int()); break; }
  case ec_ctrl_selected: { set_last_integer(selected_controller()); break; }
  case ec_ctrl_status: 
    { 
      set_last_string(controller_status()); 
      break; 
    }
  case ec_ctrl_get_target: { set_last_integer(selected_controller_target()); break; }
  
    // ---
    // Controller parameters
    // ---
  case ec_ctrlp_list: { set_last_string_list(controller_parameter_names()); break; }
  case ec_ctrlp_select: { select_controller_parameter(first_action_argument_as_int()); break; }
  case ec_ctrlp_selected: { set_last_integer(selected_controller_parameter()); break; }
  case ec_ctrlp_get: { set_last_float(get_controller_parameter()); break; }
  case ec_ctrlp_set: { set_controller_parameter(first_action_argument_as_float()); break; }

  case ec_cop_register: { cop_register(); break; }
  case ec_preset_register: { preset_register(); break; }
  case ec_ladspa_register: { ladspa_register(); break; }
  case ec_ctrl_register: { ctrl_register(); break; }

  case ec_map_cop_list: { cop_descriptions(); break; }
  case ec_map_preset_list: { preset_descriptions(); break; }
  case ec_map_ladspa_list: { ladspa_descriptions(false); break; }
  case ec_map_ladspa_id_list: { ladspa_descriptions(true); break; }
  case ec_map_ctrl_list: { ctrl_descriptions(); break; }

  // ---
  // Engine commands
  // ---
  case ec_engine_launch: { 
    if (is_engine_running() != true) 
      engine_start(); 
    else
      set_last_error("Engine already running, use 'engine-halt' first.");
    break; 
  }
  case ec_engine_halt: {
    if (is_engine_running() == true)
      close_engine(); 
    else
      set_last_error("Engine not running, use 'engine-launch' first.");
    break; 
  }
  case ec_engine_status: { set_last_string(engine_status()); break; }

  // ---
  // Internal commands
  // ---
  case ec_int_cmd_list: { set_last_string_list(registered_commands_list()); break; }
  case ec_int_log_history: { set_last_string(ECA_LOGGER::instance().log_history()); break; }
  case ec_int_output_mode_wellformed: { 
    ECA_LOGGER::attach_logger(new ECA_LOGGER_WELLFORMED()); 
    wellformed_mode_rep = true;
    break; 
  }
  case ec_int_set_float_to_string_precision: { set_float_to_string_precision(first_action_argument_as_int()); break; }
  case ec_int_set_log_history_length: { ECA_LOGGER::instance().set_log_history_length(first_action_argument_as_int()); break; }
  case ec_int_version_string: { set_last_string(ecasound_library_version); break; }
  case ec_int_version_lib_current: { set_last_integer(ecasound_library_version_current); break; }
  case ec_int_version_lib_revision: { set_last_integer(ecasound_library_version_revision); break; }
  case ec_int_version_lib_age: { set_last_integer(ecasound_library_version_age); break; }

  // ---
  // Dump commands
  // ---
  case ec_dump_target: { ctrl_dump_rep.set_dump_target(first_action_argument_as_string()); break; }
  case ec_dump_status: { ctrl_dump_rep.dump_status(); break; }
  case ec_dump_position: { ctrl_dump_rep.dump_position(); break; }
  case ec_dump_length: { ctrl_dump_rep.dump_length(); break; }
  case ec_dump_cs_status: { ctrl_dump_rep.dump_chainsetup_status(); break; }
  case ec_dump_c_selected: { ctrl_dump_rep.dump_selected_chain(); break; }
  case ec_dump_ai_selected: { ctrl_dump_rep.dump_selected_audio_input(); break; }
  case ec_dump_ai_position: { ctrl_dump_rep.dump_audio_input_position(); break; }
  case ec_dump_ai_length: { ctrl_dump_rep.dump_audio_input_length(); break; }
  case ec_dump_ai_open_state: { ctrl_dump_rep.dump_audio_input_open_state(); break; }
  case ec_dump_ao_selected: { ctrl_dump_rep.dump_selected_audio_output(); break; }
  case ec_dump_ao_position: { ctrl_dump_rep.dump_audio_output_position(); break; }
  case ec_dump_ao_length: { ctrl_dump_rep.dump_audio_output_length(); break; }
  case ec_dump_ao_open_state: { ctrl_dump_rep.dump_audio_output_open_state(); break; }
  case ec_dump_cop_value: 
    { 
      vector<string> temp = kvu_string_to_vector(first_action_argument_as_string(), ',');
      if (temp.size() > 1) {
	ctrl_dump_rep.dump_chain_operator_value(atoi(temp[0].c_str()),
						atoi(temp[1].c_str()));
      }
      break; 
    }

    // ---
    // Commands with external dependencies
    // ---
#if ECA_COMPILE_JACK
  case ec_jack_connect: 
    {
      const vector<string>& params = action_arguments_as_vector();
      if (params.size() >= 2) 
	JACK_CONNECTIONS::connect(params[0].c_str(), params[1].c_str());
      break;
    }
  case ec_jack_disconnect:   
    {
      const vector<string>& params = action_arguments_as_vector();
      if (params.size() >= 2) 
	JACK_CONNECTIONS::disconnect(params[0].c_str(), params[1].c_str());
      break;
    }
  case ec_jack_list_connections:   
    {
      string foo;
      if (JACK_CONNECTIONS::list_connections(&foo) == true)
	set_last_string(foo);
      else
	set_last_error("Unable to a list of JACK connections.");
      break;
    }
#endif

  } // <-- switch-case


  if (action_reconnect == true) {
    if (is_selected() == false ||
	is_valid() == false) {
      set_last_error("Can't reconnect chainsetup.");
    }
    else {
      connect_chainsetup(0);
      if (selected_chainsetup() != connected_chainsetup()) {
	set_last_error("Can't reconnect chainsetup.");
      }
      else {
	if (action_restart == true) {
	  DBC_CHECK(is_running() != true);
	  start();
	}
      }
    }
  }
}

/**
 * Executes chainsetup edit on connect chainsetup.
 * 
 * @pre is_connected()
 */
bool ECA_CONTROL::execute_edit_on_connected(const chainsetup_edit_t& edit)
{
  DBC_REQUIRE(is_connected() == true);

  bool retval = false;
  
  if (is_engine_running() == true) {
    ECA_ENGINE::complex_command_t engine_cmd;
    engine_cmd.type = ECA_ENGINE::ep_exec_edit;
    engine_cmd.m.cs = edit;
    engine_repp->command(engine_cmd);
    retval = true;
  }
  else {
    /* note: engine not yet running, execute edit directly */
    retval = session_repp->connected_chainsetup_repp->execute_edit(edit);
  }

  return retval;
}

/**
 * Executes chainsetup edit on selected chainsetup.
 * 
 * @param edit object specifying the edit action
 * @param index if non-negative, override the chainsetup selection
 */
bool ECA_CONTROL::execute_edit_on_selected(const chainsetup_edit_t& edit, int index)
{
  bool retval = false;

  ECA_CHAINSETUP *csetup = 0;

  if (index < 0) {
    csetup = selected_chainsetup_repp;
  }
  else {
    if (index >= 0 &&
	index < static_cast<int>(session_repp->chainsetups_rep.size())) {
      csetup = session_repp->chainsetups_rep[index];
    }
  }

  /* note: make sure that if the selected chainsetup is 
   *       in use by the engine, the edit is performed 
   *       by the engine thread! 
   */
  if (csetup != 0) {
    if (csetup->is_enabled() == true &&
	is_engine_running() == true) {
      execute_edit_on_connected(edit);
    }
    else {
      csetup->execute_edit(edit);
    }
  }

  return retval;
}

void ECA_CONTROL::print_last_value(struct eci_return_value *retval) const
{
  std::string result;

  if (retval->type == eci_return_value::retval_error) {
    result += "ERROR: ";
  }

  result += ECA_CONTROL_MAIN::return_value_to_string(retval);

  if (wellformed_mode_rep != true) {
    if (result.size() > 0) 
      ECA_LOG_MSG(ECA_LOGGER::eiam_return_values, result);
  }
  else {
    /* in wellformed-output-mode we always create return output */
    ECA_LOG_MSG(ECA_LOGGER::eiam_return_values, 
		std::string(return_value_type_to_string(retval)) +
		" " + result);
  }
}

string ECA_CONTROL::chainsetup_details_to_string(const ECA_CHAINSETUP* cs) const
{
  string result;
  vector<CHAIN*>::const_iterator chain_citer;

  result += "\n -> Objects..: " + kvu_numtostr(cs->inputs.size());
  result += " inputs, " + kvu_numtostr(cs->outputs.size());
  result += " outputs, " + kvu_numtostr(cs->chains.size());
  result += " chains";

  // FIXME: add explanations on why the chainsetup cannot be
  //        connected
  
  result += "\n -> State....: ";

  if (cs->is_locked()) {
    result += "connected to engine (engine status: ";
    result += engine_status() + ")";
  }
  else if (cs->is_enabled() && is_engine_created() == true) {
    result += "connected (engine status: ";
    result += engine_status() + ")";
  }
  else if (cs->is_enabled())
    result += "connected (engine not yet running)";
  else if (cs->is_valid()) 
    result += "valid (can be connected)";
  else
    result += "not valid (cannot be connected)";
  
  result += "\n -> Position.:  ";
  result += kvu_numtostr(cs->position_in_seconds_exact(), 3);
  result += " / ";
  if (cs->length_set())
    result += kvu_numtostr(cs->length_in_seconds_exact(), 3);
  else
    result += "inf";

  result += "\n -> Options..: ";
  result += cs->options_to_string();

  for(chain_citer = cs->chains.begin();
      chain_citer != cs->chains.end();) {
    result += "\n -> Chain \"" + (*chain_citer)->name() + "\": ";
    int idx =
      (*chain_citer)->connected_input();
    if (idx >= 0)
      result += ECA_OBJECT_FACTORY::audio_object_to_eos(cs->inputs[idx], "i");
    result += " ";
    result += (*chain_citer)->to_string();
    idx = (*chain_citer)->connected_output();
    if (idx >= 0)
      result += ECA_OBJECT_FACTORY::audio_object_to_eos(cs->outputs[idx], "o");

    ++chain_citer;
  }
  
  return result;
}

string ECA_CONTROL::chainsetup_status(void) const 
{
  vector<ECA_CHAINSETUP*>::const_iterator cs_citer = session_repp->chainsetups_rep.begin();
  int index = 0;
  string result ("### Chainsetup status ###\n");

  while(cs_citer != session_repp->chainsetups_rep.end()) {
    result += "Chainsetup ("  + kvu_numtostr(++index) + ") \"";
    result += (*cs_citer)->name() + "\" ";

    if (*cs_citer ==
	selected_chainsetup_repp)
      result += "[selected] ";
    if (*cs_citer ==
	session_repp->connected_chainsetup_repp)
      result += "[connected] ";

    if ((*cs_citer == selected_chainsetup_repp) ||
	(*cs_citer == session_repp->connected_chainsetup_repp))
      result += chainsetup_details_to_string((*cs_citer));
    else
      result += ": <detailed status omitted -- set as selected to see full status>";

    ++cs_citer;
    if (cs_citer != session_repp->chainsetups_rep.end()) result += "\n";
  }

  return result;
}

string ECA_CONTROL::chain_status(void) const
{
  // --------
  DBC_REQUIRE(is_selected() == true);
  // --------
  
  MESSAGE_ITEM mitem;
  vector<CHAIN*>::const_iterator chain_citer;
  const vector<string>& schains = selected_chainsetup_repp->selected_chains();
  mitem << "### Chain status (chainsetup '" 
	<< selected_chainsetup()
	<< "') ###\n";

  for(chain_citer = selected_chainsetup_repp->chains.begin(); chain_citer != selected_chainsetup_repp->chains.end();) {
    mitem << "Chain \"" << (*chain_citer)->name() << "\" ";
    if ((*chain_citer)->is_muted()) mitem << "[muted] ";
    if ((*chain_citer)->is_processing() == false) mitem << "[bypassed] ";
    if (find(schains.begin(), schains.end(), (*chain_citer)->name()) != schains.end()) mitem << "[selected] ";
    for(int n = 0; n < (*chain_citer)->number_of_chain_operators(); n++) {
      mitem << "\"" << (*chain_citer)->get_chain_operator(n)->name() << "\"";
      if (n == (*chain_citer)->number_of_chain_operators()) mitem << " -> ";
    }
    ++chain_citer;
    if (chain_citer != selected_chainsetup_repp->chains.end()) mitem << "\n";
  }

  return mitem.to_string();
}

string ECA_CONTROL::chain_operator_status(void) const
{
  // --------
  DBC_REQUIRE(is_selected() == true);
  // --------

  MESSAGE_ITEM msg;
  string st_info_string;
  vector<CHAIN*>::const_iterator chain_citer = selected_chainsetup_repp->chains.begin();

  msg << "### Chain operator status (chainsetup '" 
      << selected_chainsetup() 
      << "') ###\n";

  while(chain_citer != selected_chainsetup_repp->chains.end()) {
    msg << "Chain \"" << (*chain_citer)->name() << "\":\n";
    for(int p = 0; p < (*chain_citer)->number_of_chain_operators(); p++) {
      const CHAIN_OPERATOR* cop = (*chain_citer)->get_chain_operator(p);
      msg << "\t" << p + 1 << ". " <<	cop->name();
      for(int n = 0; n < cop->number_of_params(); n++) {
	if (n == 0) msg << ": ";
	msg << "[" << n + 1 << "] ";
	msg << cop->get_parameter_name(n + 1);
	msg << " ";
	msg << float_to_string(cop->get_parameter(n + 1));
	if (n + 1 < cop->number_of_params()) msg <<  ", ";
      }
      st_info_string = cop->status();
      if (st_info_string.empty() == false) {
	msg << "\n\tStatus info:\n" << st_info_string;
      }
      if (p + 1 < (*chain_citer)->number_of_chain_operators()) msg << "\n";
    }
    ++chain_citer;
    if (chain_citer != selected_chainsetup_repp->chains.end()) msg << "\n";
  }
  return msg.to_string();
}

string ECA_CONTROL::controller_status(void) const
{
  // --------
  DBC_REQUIRE(is_selected() == true);
  // --------

  MESSAGE_ITEM mitem;
  string st_info_string;
  vector<CHAIN*>::const_iterator chain_citer;

  mitem << "### Controller status (chainsetup '"
	<< selected_chainsetup()
	<< "') ###\n";

  for(chain_citer = selected_chainsetup_repp->chains.begin(); chain_citer != selected_chainsetup_repp->chains.end();) {
    mitem << "Chain \"" << (*chain_citer)->name() << "\":\n";
    for(int p = 0; p < (*chain_citer)->number_of_controllers(); p++) {
      const GENERIC_CONTROLLER* gtrl = (*chain_citer)->get_controller(p);
      mitem << "\t" << p + 1 << ". " << gtrl->name() << ": ";
      for(int n = 0; n < gtrl->number_of_params(); n++) {
	mitem << "\n\t\t[" << n + 1 << "] ";
	mitem << gtrl->get_parameter_name(n + 1);
	mitem << " ";
	mitem << float_to_string(gtrl->get_parameter(n + 1));
	if (n + 1 < gtrl->number_of_params()) mitem <<  ", ";
      }
      st_info_string = gtrl->status();
      if (st_info_string.empty() == false) {
	mitem << "\n\t -- Status info: " << st_info_string;
      }
      if (p + 1 < (*chain_citer)->number_of_controllers()) mitem << "\n";
    }
    ++chain_citer;
    if (chain_citer != selected_chainsetup_repp->chains.end()) mitem << "\n";
  }
  return mitem.to_string();
}

string ECA_CONTROL::aio_status(void) const
{
  // --------
  DBC_REQUIRE(is_selected() == true);
  // --------

  string st_info_string;
  vector<AUDIO_IO*>::size_type adev_sizet = 0;
  vector<AUDIO_IO*>::const_iterator adev_citer = selected_chainsetup_repp->inputs.begin();

  st_info_string += "### Audio input/output status (chainsetup '" +
    selected_chainsetup() + "') ###\n";

  while(adev_citer != selected_chainsetup_repp->inputs.end()) {
    st_info_string += "Input (" + kvu_numtostr(adev_sizet + 1) + "): \"";
    for(int n = 0; n < (*adev_citer)->number_of_params(); n++) {
      st_info_string += (*adev_citer)->get_parameter(n + 1);
      if (n + 1 < (*adev_citer)->number_of_params()) st_info_string += ",";
    }
    st_info_string += "\" - [" + (*adev_citer)->name() + "]";
    if ((*adev_citer) == selected_audio_input_repp) st_info_string += " [selected]";
    st_info_string += "\n -> connected to chains \"";
    vector<string> temp = selected_chainsetup_repp->get_attached_chains_to_input((selected_chainsetup_repp->inputs)[adev_sizet]);
    vector<string>::const_iterator p = temp.begin();
    while (p != temp.end()) {
      st_info_string += *p; 
      ++p;
      if (p != temp.end())  st_info_string += ",";
    }
    st_info_string += "\": " + (*adev_citer)->status() + "\n";
    ++adev_sizet;
    ++adev_citer;
  }

  adev_sizet = 0;
  adev_citer = selected_chainsetup_repp->outputs.begin();
  while(adev_citer != selected_chainsetup_repp->outputs.end()) {
    st_info_string += "Output (" + kvu_numtostr(adev_sizet + 1) + "): \"";
    for(int n = 0; n < (*adev_citer)->number_of_params(); n++) {
      st_info_string += (*adev_citer)->get_parameter(n + 1);
      if (n + 1 < (*adev_citer)->number_of_params()) st_info_string += ",";
    }
    st_info_string += "\" - [" + (*adev_citer)->name() + "]";
    if ((*adev_citer) == selected_audio_output_repp) st_info_string += " [selected]";
    st_info_string += "\n -> connected to chains \"";
    vector<string> temp = selected_chainsetup_repp->get_attached_chains_to_output((selected_chainsetup_repp->outputs)[adev_sizet]);
    vector<string>::const_iterator p = temp.begin();
    while (p != temp.end()) {
      st_info_string += *p; 
      ++p;
      if (p != temp.end())  st_info_string += ",";
    }
    st_info_string += "\": ";
    st_info_string += (*adev_citer)->status();
    ++adev_sizet;
    ++adev_citer;
    if (adev_sizet < selected_chainsetup_repp->outputs.size()) st_info_string += "\n";
  }
  return st_info_string;
}

void ECA_CONTROL::aio_register(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Registered audio object types:\n");
  string result (eca_aio_register_sub(ECA_OBJECT_FACTORY::audio_io_nonrt_map()));

  result += "\n";

  result += eca_aio_register_sub(ECA_OBJECT_FACTORY::audio_io_rt_map());

  set_last_string(result);  
}

static string eca_aio_register_sub(ECA_OBJECT_MAP& objmap)
{
  string result;
  const list<string>& objlist = objmap.registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    string temp;
    const AUDIO_IO* q = dynamic_cast<const AUDIO_IO*>(objmap.object_expr(*p));
    
    DBC_CHECK(q != 0);

    if (q != 0) {
      int params = q->number_of_params();
      if (params > 0) {
	temp += ": ";
	for(int n = 0; n < params; n++) {
	  temp += q->get_parameter_name(n + 1);
	  if (n + 1 < params) temp += ",";
	}
      }

      result += kvu_numtostr(count) + ". " + q->name() + ", regex: " + 
	        objmap.keyword_to_expr(*p) + ", params" + temp;
      result += "\n";

      ++count;
    }
    //  else std::cerr << "Failed obj: " << *p << "." << std::endl;

    ++p;
  }
  return result;
}

void ECA_CONTROL::cop_register(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Registered chain operators:\n");
  string result;
  const list<string>& objlist = ECA_OBJECT_FACTORY::chain_operator_map().registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    string temp;
    const CHAIN_OPERATOR* q = dynamic_cast<const CHAIN_OPERATOR*>(ECA_OBJECT_FACTORY::chain_operator_map().object(*p));
    if (q != 0) {
      int params = q->number_of_params();
      for(int n = 0; n < params; n++) {
	if (n == 0) temp += ":";
	temp += q->get_parameter_name(n + 1);
	if (n + 1 < params) temp += ",";
      }
      result += kvu_numtostr(count) + ". " + q->name() + ", -" + *p + temp;
      result += "\n";
      ++count;
    }
    ++p;
  }
  set_last_string(result);
}

void ECA_CONTROL::preset_register(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Registered effect presets:\n");
  string result;
#ifndef ECA_DISABLE_EFFECTS
  const list<string>& objlist = ECA_OBJECT_FACTORY::preset_map().registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    string temp;
    const PRESET* q = dynamic_cast<const PRESET*>(ECA_OBJECT_FACTORY::preset_map().object(*p));
    if (q != 0) {
      int params = q->number_of_params();
      for(int n = 0; n < params; n++) {
	if (n == 0) temp += ":";
	temp += q->get_parameter_name(n + 1);
	if (n + 1 < params) temp += ",";
      }

      result += kvu_numtostr(count) + ". " + q->name() + ", -pn:" + *p + temp;
      result += "\n";

      ++count;
    }
    ++p;
  }
#endif
  set_last_string(result);
}

void ECA_CONTROL::ladspa_register(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Registered LADSPA plugins:\n");
  string result;
#ifndef ECA_DISABLE_EFFECTS
  const list<string>& objlist = ECA_OBJECT_FACTORY::ladspa_plugin_map().registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    const EFFECT_LADSPA* q = dynamic_cast<const EFFECT_LADSPA*>(ECA_OBJECT_FACTORY::ladspa_plugin_map().object(*p));
    if (q != 0) {
      string temp = "\n\t-el:" + q->unique() + ",";
      int params = q->number_of_params();
      for(int n = 0; n < params; n++) {
	temp += "'" + q->get_parameter_name(n + 1) + "'";
	if (n + 1 < params) temp += ",";
      }
      
      result += kvu_numtostr(count) + ". " + q->name() + "" + temp;
      result += "\n";

      ++count;
    }
    ++p;
  }
#endif
  set_last_string(result);
}

void ECA_CONTROL::ctrl_register(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Registered controllers:\n");
  string result;
  const list<string>& objlist = ECA_OBJECT_FACTORY::controller_map().registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    string temp;
    const GENERIC_CONTROLLER* q = dynamic_cast<const GENERIC_CONTROLLER*>(ECA_OBJECT_FACTORY::controller_map().object(*p));
    if (q != 0) {
      int params = q->number_of_params();
      for(int n = 0; n < params; n++) {
	if (n == 0) temp += ":";
	temp += q->get_parameter_name(n + 1);
	if (n + 1 < params) temp += ",";
      }

      result += kvu_numtostr(count) + ". " + q->name() + ", -" + *p +
	temp;
      result += "\n";

      ++count;
    }
    ++p;
  }
  set_last_string(result);
}

/**
 * Print description of all chain operators and 
 * their parameters.
 */
void ECA_CONTROL::operator_descriptions_helper(const ECA_OBJECT_MAP& arg, string* result)
{
  /* switch to "C" locale to avoid strange floating point 
   * presentations that could break the output format
   * (for example "a,b" insteof of "a.b" */
#if defined(HAVE_LOCALE_H) && defined(HAVE_SETLOCALE)
  string old_locale (setlocale(LC_ALL, "C"));
#endif

  const list<string>& objlist = arg.registered_objects();
  list<string>::const_iterator p = objlist.begin();
  int count = 1;
  while(p != objlist.end()) {
    string temp;
    const OPERATOR* q = dynamic_cast<const OPERATOR*>(arg.object(*p));
    if (q != 0) {
      /* FIXME: prefer backslash escaped over '_' to handle commas */
      
      /* 1. keyword */
      *result += kvu_string_search_and_replace(*p, ',', '_');
      /* 2. name */
      *result += "," + kvu_string_search_and_replace(q->name(), ',', '_');
      /* 3. description */
      *result += "," + kvu_string_search_and_replace(q->description(), ',', '_');

      int params = q->number_of_params();

      /* 4. number of params */
      *result += "," + kvu_numtostr(params);

      /* 5. description of params (for all params) */
      for(int n = 0; n < params; n++) {
	struct OPERATOR::PARAM_DESCRIPTION pd;
	q->parameter_description(n + 1, &pd);

	/* 5.1 name of param */
	*result += "," + kvu_string_search_and_replace(q->get_parameter_name(n + 1), ',', '_');
	/* 5.2 description */
	*result += "," + kvu_string_search_and_replace(pd.description, ',', '_');
	/* 5.3 default value */
	*result += "," + float_to_string(pd.default_value);
	/* 5.4 is bounded above (1=yes, 0=no) */
	*result += ",above=" + kvu_numtostr(static_cast<int>(pd.bounded_above));
	/* 5.5 upper bound */
	*result += ",upper=" + float_to_string(pd.upper_bound);
	/* 5.6 is bounded below (1=yes, 0=no) */
	*result += ",below=" + kvu_numtostr(static_cast<int>(pd.bounded_below));
	/* 5.7 lower bound */
	*result += ",lower=" + float_to_string(pd.lower_bound);
	/* 5.8. is toggled (1=yes, 0=no) */
	*result += "," + kvu_numtostr(static_cast<int>(pd.toggled));
	/* 5.9. is integer value (1=yes, 0=no) */
	*result += "," + kvu_numtostr(static_cast<int>(pd.integer));
	/* 5.10. is logarithmis value (1=yes, 0=no) */
	*result += "," + kvu_numtostr(static_cast<int>(pd.logarithmic));
	/* 5.11. is output value (1=yes, 0=no) */
	*result += ",output=" + kvu_numtostr(static_cast<int>(pd.output));
      }
      *result += "\n";
      ++count;
    }
    ++p;
  }

  /* see above */
#if defined(HAVE_LOCALE_H) && defined(HAVE_SETLOCALE)
  setlocale(LC_ALL, old_locale.c_str());
#endif
}

/**
 * Print the description of all chain operators and 
 * their parameters.
 */
void ECA_CONTROL::cop_descriptions(void)
{
  string result;
  operator_descriptions_helper(ECA_OBJECT_FACTORY::chain_operator_map(), &result);
  set_last_string(result);
}

/**
 * Prints the description of all effect presets and 
 * their parameters.
 */
void ECA_CONTROL::preset_descriptions(void)
{
  string result;
  operator_descriptions_helper(ECA_OBJECT_FACTORY::preset_map(), &result);
  set_last_string(result);
}

/**
 * Prints the description of all LADSPA plugins and 
 * their parameters.
 */
void ECA_CONTROL::ladspa_descriptions(bool use_id)
{
  string result;
  if (use_id) {
    operator_descriptions_helper(ECA_OBJECT_FACTORY::ladspa_plugin_id_map(), &result);
  }
  else {
    operator_descriptions_helper(ECA_OBJECT_FACTORY::ladspa_plugin_map(), &result);
  }
  set_last_string(result);
}

/**
 * Print the description of all controllers and 
 * their parameters.
 */
void ECA_CONTROL::ctrl_descriptions(void)
{
  string result;
  operator_descriptions_helper(ECA_OBJECT_FACTORY::controller_map(), &result);
  set_last_string(result);
}
