// ------------------------------------------------------------------------
// eca-chain.cpp: Class representing an abstract audio signal chain.
// Copyright (C) 1999-2009 Kai Vehmanen
// Copyright (C) 2005 Stuart Allie
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

#ifndef INCLUDED_CHAIN_H
#define INCLUDED_CHAIN_H

#include <string>
#include <vector>

#include "eca-chainop.h"
#include "eca-audio-position.h"

class GENERIC_CONTROLLER;
class OPERATOR;
class SAMPLE_BUFFER;

/**
 * Class representing an abstract audio signal chain.
 */
class CHAIN : public ECA_AUDIO_POSITION {
 
 public:

  /** @name Object construction and destruction */
  /*@{*/

  CHAIN (void);
  virtual ~CHAIN (void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Chain state */
  /*@{*/

  bool is_initialized(void) const { return initialized_rep; }

  /**
   * Is chain muted? If muted, audio buffers are zeroed during
   * processing.
   */ 
  bool is_muted(void) const { return muted_rep; }

  /**
   * Is processing enabled? If disabled, all chain operators
   * will be skipped during processing. 
   */
  bool is_processing(void) const { return sfx_rep; }

  void toggle_muting(bool v) { muted_rep = v; }
  void toggle_processing(bool v) { sfx_rep = v; }

  std::string name(void) const { return chainname_rep; }
  void name(const std::string& c) { chainname_rep = c; }

  bool is_valid(void) const;

  void init(SAMPLE_BUFFER* sbuf = 0, int in_channels = 0, int out_channels = 0);
  void release(void);
  void process(void);
  void controller_update(void);
  void refresh_parameters(void);

  std::string to_string(void) const;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Input and output */
  /*@{*/

  void connect_input(int input);
  void disconnect_input(void);
  void connect_output(int output);
  void disconnect_output(void);
  void disconnect_buffer(void);

  /**
   * Returns an id number to input connected to this chain. If no input
   * is connected, -1 is returned.
   */
  int connected_input(void) const { return input_id_rep; }

  /**
   * Returns an id number to output connected to this chain. If no input
   * is connected, -1 is returned.
   */
  int connected_output(void) const { return output_id_rep; }

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Access objects via stateless addressing */
  /*@{*/

  void clear(void);

  void add_chain_operator(CHAIN_OPERATOR* chainop);
  void add_controller(GENERIC_CONTROLLER* gcontroller);
  void remove_chain_operator(int op_index);

  void set_parameter(int op_index, int param_index, CHAIN_OPERATOR::parameter_t value);

  int number_of_chain_operators(void) const { return chainops_rep.size(); }
  int number_of_chain_operator_parameters(int index) const;

  const CHAIN_OPERATOR* get_chain_operator(int index) const { return chainops_rep[index]; }
  const GENERIC_CONTROLLER* get_controller(int index) const { return gcontrollers_rep[index]; }

  int number_of_controllers(void) const { return gcontrollers_rep.size(); }
  void set_controller_parameter(int op_index, int param_index, CHAIN_OPERATOR::parameter_t value);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Access objects via stateful addressing */
  /*@{*/

  void select_chain_operator(int op_index);
  void select_chain_operator_parameter(int param_index);

  /** Index (1..N) of selected chain operator */
  int selected_chain_operator(void) const { return selected_chainop_number_rep; }
  int selected_chain_operator_parameter(void) const { return selected_chainop_parameter_rep; }

  int number_of_chain_operator_parameters(void) const;

  CHAIN_OPERATOR::parameter_t get_parameter(void) const;
  std::string chain_operator_name(void) const;
  std::string chain_operator_parameter_name(void) const;

  const CHAIN_OPERATOR* get_selected_chain_operator(void) const;

  void remove_controller(void);
  void select_controller(int index);
  void select_controller_parameter(int index);

  const GENERIC_CONTROLLER* get_selected_controller(void) const { return selected_controller_repp; }
  int number_of_controller_parameters(void) const;
  std::string controller_parameter_name(void) const;
  CHAIN_OPERATOR::parameter_t get_controller_parameter(void) const;

  /** Index (1...N) of selected controller */
  int selected_controller(void) const { return selected_controller_number_rep; }
  int selected_controller_parameter(void) const { return selected_controller_parameter_rep; }

  std::string controller_name(void) const;

  void selected_chain_operator_as_target(void);

  void selected_controller_as_target(void);

  /**
   * Returns the object that is the current target for 
   * parameter control, or 0 if none selected.
   */
  OPERATOR* selected_target(void) const { return selected_dynobj_repp; }

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Functions implemented from ECA_SAMPLERATE_AWARE */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

  /** @name Functions implemented from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);
  virtual bool supports_seeking(void) const { return true; }
  virtual bool supports_seeking_sample_accurate(void) const { return true; }

  /*@}*/

  // -------------------------------------------------------------------

 private:

  bool initialized_rep;
  std::string chainname_rep;
  bool muted_rep;
  bool sfx_rep;
  int in_channels_rep;
  int out_channels_rep;

  std::vector<CHAIN_OPERATOR*> chainops_rep;
  std::vector<GENERIC_CONTROLLER*> gcontrollers_rep;

  GENERIC_CONTROLLER* selected_controller_repp;
  OPERATOR* selected_dynobj_repp;

  int selected_chainop_number_rep;
  int selected_chainop_parameter_rep;
  int selected_controller_number_rep;
  int selected_controller_parameter_rep;

  int input_id_rep;
  int output_id_rep;

  SAMPLE_BUFFER* audioslot_repp;

};

#endif
