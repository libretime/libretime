// ------------------------------------------------------------------------
// eca-control.h: ECA_CONTROL class
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

#ifndef INCLUDED_ECA_CONTROL_H
#define INCLUDED_ECA_CONTROL_H

#include <list>
#include <string>
#include <vector>

#include "kvu_locks.h"

#include "ctrl-source.h"
#include "eca-audio-format.h"
#include "eca-chainop.h"
#include "eca-chainsetup-edit.h"
#include "eca-control-dump.h"
#include "eca-control-main.h"
#include "eca-iamode-parser.h"
#include "sample-specs.h"

class AUDIO_IO;
class CHAIN_OPERATOR;
class GENERIC_CONTROLLER;
class ECA_CHAINSETUP;
class ECA_ENGINE;
class ECA_OBJECT_MAP;
class ECA_SESSION;

/**
 * High-level interface to libecasound functionality
 *
 * @see ECA_CONTROL_MAIN, ECA_CONTROL_MT
 *
 * Related design patters: Facade (GoF185)
 *
 * @author Kai Vehmanen
 */
class ECA_CONTROL : public ECA_IAMODE_PARSER,
                    public ECA_CONTROL_MAIN {


 public:

  /** @name Constructors and dtors */
  /*@{*/

  ECA_CONTROL (ECA_SESSION* psession);
  ~ECA_CONTROL (void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name ECA_CONTROL_MAIN subset */

  virtual bool is_running(void) const;
  virtual bool is_connected(void) const;
  virtual bool is_selected(void) const;
  virtual bool is_finished(void) const;
  virtual bool is_valid(void) const;
  virtual bool is_engine_created(void) const;
  virtual bool is_engine_running(void) const;

  virtual void engine_start(void);
  virtual int start(void);
  virtual void stop(void);
  virtual void stop_on_condition(void);
  virtual int run(bool batchmode = true);
  virtual void quit(void);
  virtual void quit_async(void);

  virtual void connect_chainsetup(struct eci_return_value *retval);
  virtual void disconnect_chainsetup(void);
  virtual const ECA_CHAINSETUP* get_connected_chainsetup(void) const;

  virtual bool execute_edit_on_connected(const ECA::chainsetup_edit_t& edit);
  virtual bool execute_edit_on_selected(const ECA::chainsetup_edit_t& edit, int index = -1);

  virtual void print_last_value(struct eci_return_value *retval) const;
  virtual void command(const std::string& cmd_and_args, struct eci_return_value *retval);
  virtual void command_float_arg(const std::string& cmd, double arg, struct eci_return_value *retval);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Public functions for observing status 
   * (note: implemented in eca-control-base.cpp)
   */
  /*@{*/

  std::string engine_status(void) const;

  SAMPLE_SPECS::sample_pos_t length_in_samples(void) const;
  double length_in_seconds_exact(void) const;
  SAMPLE_SPECS::sample_pos_t position_in_samples(void) const;
  double position_in_seconds_exact(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Public functions for resource file access */
  /*@{*/

  /**
   * Get resource values from ~/.ecasoundrc
   */
  std::string resource_value(const std::string& key) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Public functions for chainsetup setup 
   * (note: implementated in eca-control-objects.cpp)
   */
  /*@{*/

  void add_chainsetup(const std::string& name);
  void remove_chainsetup(void);
  void load_chainsetup(const std::string& filename);
  void save_chainsetup(const std::string& filename);
  void select_chainsetup(const std::string& name);
  void select_chainsetup_by_index(int index);
  void edit_chainsetup(void);

  std::string selected_chainsetup(void) const;
  std::string connected_chainsetup(void) const;

  void change_chainsetup_position(double seconds);
  void change_chainsetup_position_samples(SAMPLE_SPECS::sample_pos_t samples);
  void set_chainsetup_position(double seconds);
  void set_chainsetup_position_samples(SAMPLE_SPECS::sample_pos_t samples);

  double chainsetup_position(double seconds) const;
  const ECA_CHAINSETUP* get_chainsetup(void) const;
  const ECA_CHAINSETUP* get_chainsetup_filename(const std::string& filename) const;
  std::vector<std::string> chainsetup_names(void) const;
  const std::string& chainsetup_filename(void) const;
  int chainsetup_buffersize(void) const;

  void set_chainsetup_filename(const std::string& name);
  void set_chainsetup_parameter(const std::string& name);
  void set_chainsetup_sample_format(const std::string& name);
  void set_chainsetup_processing_length_in_seconds(double value);
  void set_chainsetup_processing_length_in_samples(SAMPLE_SPECS::sample_pos_t value);
  void set_chainsetup_output_mode(int output_mode);
  void toggle_chainsetup_looping(void);
  void set_chainsetup_buffersize(int bsize);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Public functions for chain setup 
   * (note: implemented in eca-control-objects.cpp)
   */
  /*@{*/

  void add_chain(const std::string& names);
  void add_chains(const std::string& names);
  void add_chains(const std::vector<std::string>& names);
  void remove_chains(void);
  void select_chains_by_index(const std::vector<int>& index_numbers);
  void select_chain(const std::string& chain);
  void select_chains(const std::vector<std::string>& chains);
  void deselect_chains(const std::vector<std::string>& chains);
  void select_all_chains(void);

  const std::vector<std::string>& selected_chains(void) const;
  std::vector<std::string> chain_names(void) const;
  const CHAIN* get_chain(void) const;

  void clear_chains(void);
  void rename_chain(const std::string& name);
  void toggle_chain_muting(void);
  void toggle_chain_bypass(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Managing chain operators and controllers 
   * (note: implemented in eca-control-objects.cpp)
   */
  /*@{*/

  void add_audio_input(const std::string& filename);
  void remove_audio_input(void);
  void attach_audio_input(void);
  void select_audio_input(const std::string& name);
  void select_audio_input_by_index(int index);

  void add_audio_output(const std::string& filename);
  void add_default_output(void);
  void remove_audio_output(void);
  void attach_audio_output(void);
  void select_audio_output(const std::string& name);
  void select_audio_output_by_index(int index);
  void set_default_audio_format(const std::string& sfrm, int channels, long int srate, bool interleaving);
  void set_default_audio_format(const ECA_AUDIO_FORMAT& format);
  void set_default_audio_format_to_selected_input(void);
  void set_default_audio_format_to_selected_output(void);

  std::string attached_chains_input(AUDIO_IO* aiod) const;
  std::string attached_chains_output(AUDIO_IO* aiod) const;
  std::vector<std::string> attached_chains(const std::string& name) const;

  const AUDIO_IO* get_audio_input(void);
  std::vector<std::string> audio_input_names(void) const;

  const AUDIO_IO* get_audio_output(void);
  std::vector<std::string> audio_output_names(void) const;

  const ECA_AUDIO_FORMAT& default_audio_format(void) const;
  ECA_AUDIO_FORMAT get_audio_format(AUDIO_IO* aobj) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Managing chain operators and controllers
   * (note: implemented in eca-control-objects.cpp)
   */
  /*@{*/

  void add_chain_operator(const std::string& chainop_params);
  void add_chain_operator(CHAIN_OPERATOR* cotmp);
  void remove_chain_operator(void);
  void select_chain_operator(int chainop_id);
  void select_chain_operator_parameter(int param);
  void set_chain_operator_parameter(CHAIN_OPERATOR::parameter_t value);
  void set_chain_operator_parameter(int chain, int op, int param, CHAIN_OPERATOR::parameter_t value);

  int selected_chain_operator(void) const;
  int selected_chain_operator_parameter(void) const;

  const CHAIN_OPERATOR* get_chain_operator(void) const;
  CHAIN_OPERATOR::parameter_t get_chain_operator_parameter(void) const;
  std::vector<std::string> chain_operator_names(void) const;
  std::vector<std::string> chain_operator_parameter_names(void) const;

  void add_controller(const std::string& gcontrol_params);
  void select_controller(int ctrl_id);
  void select_controller_parameter(int param);
  void set_controller_parameter(CHAIN_OPERATOR::parameter_t value);
  void remove_controller(void);

  int selected_controller(void) const;
  int selected_controller_parameter(void) const;
  int selected_controller_target(void) const;

  const GENERIC_CONTROLLER* get_controller(void) const;
  CONTROLLER_SOURCE::parameter_t get_controller_parameter(void) const;
  std::vector<std::string> controller_names(void) const;
  std::vector<std::string> controller_parameter_names(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Public functions for executing ECI commands */
  /*@{*/

  /**
   * See ECA_IAMODE_PARSER for detailed decsription of 'action_id'.
   *
   * Result of the command can be queried with last_value_to_string().
   */
  void action(int action_id, const std::vector<std::string>& args);

  std::string last_error(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Implemenetations of ECI status commands */
  /*@{*/

  /**
   * Return info about chainsetups
   */
  std::string chainsetup_status(void) const;

  /**
   * Return info about current chain status
   *
   * require:
   *  is_selected() == true
   *  selected_chains().size() > 0
   */
  std::string chain_status(void) const;

  /**
   * Return info about inputs and outputs
   */
  std::string aio_status(void) const;

  /**
   * Return info about chain operators (selected chainsetup)
   *
   * require:
   *  is_selected() == true
   */
  std::string chain_operator_status(void) const;

  /**
   * Return info about controllers (selected chainsetup)
   *
   * require:
   *  is_selected() == true
   */
  std::string controller_status(void) const;

  void aio_register(void); 
  void cop_register(void);
  void preset_register(void); 
  void ladspa_register(void);
  void ctrl_register(void);

  void operator_descriptions_helper(const ECA_OBJECT_MAP& arg, std::string* result);
  void cop_descriptions(void);
  void preset_descriptions(void);
  void ladspa_descriptions(bool use_id);
  void ctrl_descriptions(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Helper functions */
  /*@{*/

  int float_to_string_precision(void) const { return(float_to_string_precision_rep); }

  /*@}*/

  // -------------------------------------------------------------------

 protected:

  void set_float_to_string_precision(int precision);
  std::string float_to_string(double n) const;

 private:

  void set_last_string_list(const std::vector<std::string>& s);
  void set_last_string(const std::list<std::string>& s);
  void set_last_string(const std::string& s);
  void set_last_float(double v);
  void set_last_integer(int v);
  void set_last_long_integer(long int v);
  void set_last_error(const std::string& s);
  void clear_last_values(void);

  static void* start_normal_thread(void *ptr);

  void start_engine_sub(bool batchmode);
  void close_engine(void);
  void run_engine(void);

  std::string chainsetup_details_to_string(const ECA_CHAINSETUP* cs) const;

  void audio_input_as_selected(void);
  void audio_output_as_selected(void);
  void rewind_audio_object(double seconds);
  void forward_audio_object(double seconds);
  void set_audio_object_position(double seconds);
  void set_audio_object_position_samples(SAMPLE_SPECS::sample_pos_t samples);
  void wave_edit_audio_object(void);

  bool cond_stop_for_editing(void);
  void cond_start_after_editing(bool was_running);

  void send_chain_commands_to_engine(int command, double value);

  void action(int action_id);
  void check_action_preconditions(int action_id);
  void chainsetup_option(const std::string& cmd);
  void set_action_argument(const std::string& s);
  void set_action_argument(const std::vector<std::string>& s);
  void set_action_argument(double v);
  void clear_action_arguments(void);
  double first_action_argument_as_float(void) const;
  std::string first_action_argument_as_string(void) const;
  int first_action_argument_as_int(void) const;
  long int first_action_argument_as_long_int(void) const;
  SAMPLE_SPECS::sample_pos_t first_action_argument_as_samples(void) const;
  const std::vector<std::string>& action_arguments_as_vector(void) const;
  void fill_command_retval(struct eci_return_value *retval) const;
  bool action_helper_check_cop_op_args(int copid, int coppid);

  ECA_ENGINE* engine_repp;
  ECA_SESSION* session_repp;
  ECA_CHAINSETUP* selected_chainsetup_repp;
  ECA_CONTROL_DUMP ctrl_dump_rep;

  bool req_batchmode_rep;
  pthread_t th_cqueue_rep;
  ATOMIC_INTEGER engine_exited_rep;
  int engine_pid_rep;
  int last_exec_res_rep;
  bool joining_rep;

  int float_to_string_precision_rep;

  AUDIO_IO* selected_audio_object_repp;
  AUDIO_IO* selected_audio_input_repp;
  AUDIO_IO* selected_audio_output_repp;

  struct eci_return_value last_retval_rep;

  bool wellformed_mode_rep;

  std::vector<std::string> action_args_rep;
  double action_arg_f_rep; 
  bool action_arg_f_set_rep;
  bool action_ok;
  bool action_reconnect;
  bool action_restart;
};

#endif
