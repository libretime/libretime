// ------------------------------------------------------------------------
// eca-chainsetup.h: Class representing an ecasound chainsetup object.
// Copyright (C) 1999-2004,2006 Kai Vehmanen
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

#ifndef INCLUDED_ECA_CHAINSETUP_H
#define INCLUDED_ECA_CHAINSETUP_H

#include <vector>
#include <string>
#include <map>
#include <list>

#include "eca-chainsetup-position.h"
#include "eca-chainsetup-parser.h"
#include "eca-chainsetup-edit.h"
#include "eca-error.h"

class AUDIO_IO;
class AUDIO_IO_MANAGER;
class AUDIO_IO_DB_SERVER;
class CHAIN;
class CHAIN_OPERATOR;
class CONTROLLER_SOURCE;
class ECA_AUDIO_FORMAT;
class ECA_CHAINSETUP_BUFPARAMS;
class ECA_CHAINSETUP_impl;
class ECA_ENGINE_DRIVER;
class ECA_RESOURCES;
class GENERIC_CONTROLLER;
class LOOP_DEVICE;
class MIDI_IO;
class MIDI_SERVER;

using std::map;
using std::list;
using std::string;
using std::vector;

/**
 * Class representing an ecasound chainsetup object.
 *
 * Chainsetup is the central data object. It contains 
 * audio inputs, outputs, chains, operators and also 
 * information about how they are connected.
 * 
 * Notes: ECA_CHAINSETUP is closely coupled to the 
 *        ECA_CHAINSETUP_PARSER and ECA_ENGINE.
 *        In addition, to ease implementation, 
 *        also ECA_CONTROL classes have direct access
 *        to ECA_CHAINSETUP's implementation.
 *
 * @author Kai Vehmanen
 */
class ECA_CHAINSETUP : public ECA_CHAINSETUP_POSITION {

 public:

  friend class ECA_ENGINE;
  friend class ECA_CONTROL;
  friend class ECA_CONTROL_BASE;
  friend class ECA_CONTROL_OBJECTS;
  friend class ECA_CHAINSETUP_PARSER;

  // -------------------------------------------------------------------

  /** @name Public type definitions and constants */
  /*@{*/

  enum Buffering_mode { cs_bmode_auto, cs_bmode_nonrt, cs_bmode_rt, cs_bmode_rtlowlatency, cs_bmode_none };
  enum Mix_mode { cs_mmode_avg, cs_mmode_sum };

  enum Audio_dir { cs_dir_input, cs_dir_output };

  typedef enum Buffering_mode Buffering_mode_t;
  typedef enum Mix_mode Mix_mode_t;

  static const string default_audio_format_const;
  static const string default_bmode_nonrt_const;
  static const string default_bmode_rt_const;
  static const string default_bmode_rtlowlatency_const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for init and cleanup */
  /*@{*/

  ECA_CHAINSETUP(void);
  ECA_CHAINSETUP(const vector<string>& options);
  ECA_CHAINSETUP(const string& setup_file);
  virtual ~ECA_CHAINSETUP(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for handling audio objects */
  /*@{*/

  void add_input(AUDIO_IO* aiod);
  void add_output(AUDIO_IO* aiod, bool truncate);
  void add_default_output(void);
  void remove_audio_input(const string& label);
  void remove_audio_output(const string& label);
  void attach_input_to_selected_chains(const AUDIO_IO* obj);
  void attach_output_to_selected_chains(const AUDIO_IO* obj);
  bool ok_audio_object(const AUDIO_IO* aobj) const;
  bool is_realtime_target_output(int output_id) const;
  vector<string> audio_input_names(void) const;
  vector<string> audio_output_names(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for handling chains */
  /*@{*/

  void add_default_chain(void);
  void add_new_chains(const vector<string>& newchains);
  void remove_chains(void);
  void select_chains(const vector<string>& chainsarg) { selected_chainids = chainsarg; }
  void select_all_chains(void);
  void clear_chains(void);
  void rename_chain(const string& name);
  void toggle_chain_muting(void);
  void toggle_chain_bypass(void);

  const vector<string>& selected_chains(void) const { return selected_chainids; }
  unsigned int first_selected_chain(void) const; 
  vector<string> chain_names(void) const;
  vector<string> get_attached_chains_to_iodev(const string& filename) const;
  const CHAIN* get_chain_with_name(const string& name) const;
  int get_chain_index(const string& name) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for handling MIDI-objects */
  /*@{*/

  void add_midi_device(MIDI_IO* mididev);
  void remove_midi_device(const string& name);
  void add_default_midi_device(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for chain operators */
  /*@{*/

  void add_chain_operator(CHAIN_OPERATOR* cotmp);
  void add_controller(GENERIC_CONTROLLER* csrc);
  void set_target_to_controller(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for configuration (default values, settings) */
  /*@{*/

  void toggle_precise_sample_rates(bool value) { precise_sample_rates_rep = value; }
  void toggle_ignore_xruns(bool v) { ignore_xruns_rep = v; }
  void set_output_openmode(int value) { output_openmode_rep = value; }
  void set_default_audio_format(ECA_AUDIO_FORMAT& value);
  void set_default_midi_device(const string& name) { default_midi_device_rep = name; }
  void set_buffering_mode(Buffering_mode_t value);
  void set_audio_io_manager_option(const string& mgrname, const string& optionstr);
  void set_mix_mode(Mix_mode_t value) { mix_mode_rep = value; }

  bool precise_sample_rates(void) const { return precise_sample_rates_rep; }
  bool ignore_xruns(void) const { return ignore_xruns_rep; }
  const ECA_AUDIO_FORMAT& default_audio_format(void) const;
  const string& default_midi_device(void) const { return default_midi_device_rep; }
  int output_openmode(void) const { return output_openmode_rep; }
  Buffering_mode_t buffering_mode(void) const { return buffering_mode_rep; }
  bool is_valid_for_connection(bool verbose) const;
  bool multitrack_mode(void) const { return multitrack_mode_rep; }
  long int multitrack_mode_offset(void) const { return multitrack_mode_offset_rep; } 
  Mix_mode_t mix_mode(void) const { return mix_mode_rep; }

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for overriding current buffering mode parameters */
  /*@{*/

  void set_buffersize(long int value);
  void toggle_raised_priority(bool value);
  void set_sched_priority(int value);
  void toggle_double_buffering(bool value);
  void set_double_buffer_size(long int v);
  void toggle_max_buffers(bool v);

  long int buffersize(void) const;
  bool raised_priority(void) const;
  int get_sched_priority(void) const;
  bool double_buffering(void) const;
  long int double_buffer_size(void) const;
  bool max_buffers(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions that modify current state  */
  /*@{*/

  void set_name(const string& str) { setup_name_rep = str; }
  void set_filename(const string& str) { setup_filename_rep = str; }
  void enable(void) throw(ECA_ERROR&);
  void disable(void);

  const string& name(void) const { return setup_name_rep; }
  const string& filename(void) const { return setup_filename_rep; }

  bool execute_edit(const ECA::chainsetup_edit_t& edit);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions implemented from ECA_SAMPLERATE_AWARE */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions implemented from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for observing current state */
  /*@{*/

  /**
   * Checks whether chainsetup is enabled (devices ready for use).
   */
  bool is_enabled(void) const { return is_enabled_rep; }

  /** 
   * Checks whether chainsetup is locked by ECA_ENGINE. 
   * If locked, only a limited access to the chainsetup
   * data is allowed.
   */
  bool is_locked(void) const { return is_locked_rep; }

  bool is_valid(void) const;
  bool has_realtime_objects(void) const;
  bool has_nonrealtime_objects(void) const;
  string options_to_string(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for string->state conversions */
  /*@{*/

  /**
   * Returns the result of last call to interpret_option(), interpret_global_option() 
   * or interpret_object_option().
   *
   * @result true if options interpreted succesfully, otherwise false
   */
  bool interpret_result(void) const { return cparser_rep.interpret_result(); }
  const string& interpret_result_verbose(void) const { return cparser_rep.interpret_result_verbose(); }

  void interpret_option(const string& arg);
  void interpret_global_option(const string& arg);
  void interpret_object_option(const string& arg);
  void interpret_options(const vector<string>& opts);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for string<->state conversions */
  /*@{*/

  void save(void) throw(ECA_ERROR&);
  void save_to_file(const string& filename) throw(ECA_ERROR&);

  /*@}*/

  // -------------------------------------------------------------------

 private:

  /** @name Configuration data (settings and values)  */
  /*@{*/
  
  ECA_CHAINSETUP_impl* impl_repp;
  ECA_CHAINSETUP_PARSER cparser_rep;

  bool precise_sample_rates_rep;
  bool ignore_xruns_rep;
  bool rtcaps_rep;
  int output_openmode_rep;
  long int double_buffer_size_rep;
  string default_midi_device_rep;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Current setup data (internal state, objects) */
  /*@{*/

  bool is_locked_rep;
  bool is_enabled_rep;
  bool multitrack_mode_rep;
  bool multitrack_mode_override_rep;
  bool memory_locked_rep;
  bool midi_server_needed_rep;

  /* FIXME: only needed by ECA_ENGINE */
  int selected_chain_index_rep;
  int selected_cop_index_rep;
  int selected_cop_param_index_rep;
  int selected_ctrl_index_rep;
  int selected_ctrl_param_index_rep;

  int db_clients_rep;
  long int multitrack_mode_offset_rep;
  string setup_name_rep;
  string setup_filename_rep;

  vector<string> selected_chainids;
  map<string,LOOP_DEVICE*> loop_map;

  vector<double> input_start_pos;
  vector<double> output_start_pos;

  Buffering_mode_t buffering_mode_rep;
  Buffering_mode_t active_buffering_mode_rep;
  Mix_mode_t mix_mode_rep;

  vector<AUDIO_IO*> inputs;
  vector<AUDIO_IO*> inputs_direct_rep;
  vector<AUDIO_IO*> outputs;
  vector<AUDIO_IO*> outputs_direct_rep;
  vector<AUDIO_IO_MANAGER*> aio_managers_rep;
  map<string,string> aio_manager_option_map_rep;
  vector<CHAIN*> chains;
  vector<MIDI_IO*> midi_devices;

  AUDIO_IO_DB_SERVER* pserver_repp;
  MIDI_SERVER* midi_server_repp;
  ECA_ENGINE_DRIVER* engine_driver_repp;

  /*@}*/
  // -------------------------------------------------------------------

  /** @name Functions for handling audio objects */
  /*@{*/

  AUDIO_IO_MANAGER* get_audio_object_manager(AUDIO_IO* aio) const;
  AUDIO_IO_MANAGER* get_audio_object_type_manager(AUDIO_IO* aio) const;
  void register_engine_driver(AUDIO_IO_MANAGER* amgr);
  void register_audio_object_to_manager(AUDIO_IO* aio);
  void unregister_audio_object_from_manager(AUDIO_IO* aio);
  void propagate_audio_io_manager_options(void);
  AUDIO_IO* add_audio_object_helper(AUDIO_IO* aio);
  void remove_audio_object_proxy(AUDIO_IO* aio);
  void remove_audio_object_loop(const string& label, AUDIO_IO* aio, int dir);
  void remove_audio_object_impl(const string& label, int dir, bool destroy);

  // -------------------------------------------------------------------

  /** @name Functions for state<->string conversions */
  /*@{*/

  void load_from_file(const string& filename, vector<string>& opts) const throw(ECA_ERROR&);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions for internal state changes */
  /*@{*/

  void select_active_buffering_mode(void);
  void enable_active_buffering_mode(void);
  void switch_to_direct_mode(void);
  void switch_to_direct_mode_helper(vector<AUDIO_IO*>* objs, const vector<AUDIO_IO*>& directobjs);
  void switch_to_db_mode(void);
  void switch_to_db_mode_helper(vector<AUDIO_IO*>* objs, const vector<AUDIO_IO*>& directobjs);
  void lock_all_memory(void);
  void unlock_all_memory(void);
  void set_defaults (void);
  int number_of_realtime_inputs(void) const;
  int number_of_realtime_outputs(void) const;
  int number_of_non_realtime_inputs(void) const;
  int number_of_non_realtime_outputs(void) const;
  int number_of_chain_operators(void) const;
  void toggle_locked_state(bool value) { is_locked_rep = value; }
  long int check_for_locked_buffersize(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Private helper functions */
  /*@{*/

  const ECA_CHAINSETUP_BUFPARAMS& active_buffering_parameters(void) const;
  const ECA_CHAINSETUP_BUFPARAMS& override_buffering_parameters(void) const;
  vector<string> get_attached_chains_to_input(AUDIO_IO* aiod) const;
  vector<string> get_attached_chains_to_output(AUDIO_IO* aiod) const;
  int number_of_attached_chains_to_input(AUDIO_IO* aiod) const;
  int number_of_attached_chains_to_output(AUDIO_IO* aiod) const;
  void add_chain_helper(const string& name);
  void enable_audio_object_helper(AUDIO_IO* aobj) const;
  void calculate_processing_length(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Static private helper functions */
  /*@{*/

  static bool ok_audio_object_helper(const AUDIO_IO* aobj, const vector<AUDIO_IO*>& aobjs);
  static void check_object_samplerate(const AUDIO_IO* obj,
				      SAMPLE_SPECS::sample_rate_t srate) throw(ECA_ERROR&);
  static string set_resource_helper(const ECA_RESOURCES& ecaresources, const string& tag, const string& alternative);
  static void audio_object_open_info(const AUDIO_IO* aio);

  /*@}*/

};

#endif
