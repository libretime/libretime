// ------------------------------------------------------------------------
// eca-chain.cpp: Class representing an abstract audio signal chain.
// Copyright (C) 1999-2009 Kai Vehmanen
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
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// ------------------------------------------------------------------------

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cassert>
#include <cctype>
#include <string>
#include <vector>

#include <unistd.h>

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "samplebuffer.h"
#include "generic-controller.h"
#include "eca-chainop.h"
#include "audioio.h"
#include "file-preset.h"
#include "global-preset.h"
#include "audiofx_ladspa.h"
#include "eca-object-factory.h"
#include "eca-object-map.h"
#include "eca-preset-map.h"
#include "eca-chain.h"
#include "eca-chainop.h"

#include "eca-error.h"
#include "eca-logger.h"

/* Debug controller source values */ 
// #define DEBUG_CONTROLLERS

#ifdef DEBUG_CONTROLLERS
#define DEBUG_CTRL_STATEMENT(x) x
#else
#define DEBUG_CTRL_STATEMENT(x) ((void)0)
#endif

CHAIN::CHAIN (void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "constructor: CHAIN");
  muted_rep = false;
  sfx_rep = true;
  initialized_rep = false;
  input_id_rep = output_id_rep = -1;

  /* FIXME: remove these and only store the index */
  selected_controller_repp = 0;
  selected_dynobj_repp = 0;

  selected_chainop_number_rep = 0;
  selected_controller_number_rep = 0;
  selected_chainop_parameter_rep = 0;
  selected_controller_parameter_rep = 0;
}

CHAIN::~CHAIN (void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "CHAIN destructor!");

  if (is_initialized())
    release();

  for(std::vector<CHAIN_OPERATOR*>::iterator p = chainops_rep.begin(); p !=
	chainops_rep.end(); p++) {

    string tmp = (*p)->status();
    if (tmp.size() > 0) {
      ECA_LOG_MSG(ECA_LOGGER::info, tmp);
    }

    delete *p;
  }

  for(std::vector<GENERIC_CONTROLLER*>::iterator p = gcontrollers_rep.begin(); p !=
	gcontrollers_rep.end(); p++) {
    delete *p;
  }
}

/**
 * Whether chain is in a valid state (= ready for processing)?
 */
bool CHAIN::is_valid(void) const
{
  if (input_id_rep == -1 ||
      output_id_rep == -1) {
    return false;
  }
  return true;
}

/**
 * Connects input to chain
 */
void CHAIN::connect_input(int input) { input_id_rep = input; }

/**
 * Connects output to chain
 */
void CHAIN::connect_output(int output) { output_id_rep = output; }

/**
 * Disconnects input
 */
void CHAIN::disconnect_input(void) { input_id_rep = -1; initialized_rep = false; }

/**
 * Disconnects output
 */
void CHAIN::disconnect_output(void) { output_id_rep = -1; initialized_rep = false; }

/**
 * Disconnects the sample buffer
 */
void CHAIN::disconnect_buffer(void) { audioslot_repp = 0; initialized_rep = false; release(); }

/**
 * Adds the chain operator to the end of the chain
 *
 * require:
 *  chainop != 0
 *
 * ensure:
 *  selected_chain_operator() == number_of_chain_operators()
 *  is_initialized() != true
 */
void CHAIN::add_chain_operator(CHAIN_OPERATOR* chainop)
{
  // --------
  DBC_REQUIRE(chainop != 0);
  // --------

  ECA_SAMPLERATE_AWARE* srateobj = dynamic_cast<ECA_SAMPLERATE_AWARE*>(chainop);
  if (srateobj != 0) {
    srateobj->set_samples_per_second(samples_per_second());
  }

  chainops_rep.push_back(chainop);
  selected_chainop_number_rep = chainops_rep.size();
  initialized_rep = false;

  // --------
  DBC_ENSURE(selected_chain_operator() == number_of_chain_operators());
  DBC_ENSURE(is_initialized() != true);
  // --------
}

/**
 * Removes the selected chain operator
 *
 * @param op_index operator index (1...N), or -1 to use the selected op
 *
 * ensure:
 *  is_initialized() != true
 */
void CHAIN::remove_chain_operator(int op_index)
{
  if (op_index < 0)
    op_index = selected_chainop_number_rep;

  CHAIN_OPERATOR *to_remove = 0;

  if (op_index > 0 &&
      op_index <= static_cast<int>(chainops_rep.size())) 
    to_remove = chainops_rep[op_index - 1];

  if (to_remove != 0) {
    for(std::vector<CHAIN_OPERATOR*>::iterator p = chainops_rep.begin(); 
	p != chainops_rep.end(); 
	p++) {
      
      if (*p == to_remove) {
	for(std::vector<GENERIC_CONTROLLER*>::iterator q = gcontrollers_rep.begin(); 
	    q != gcontrollers_rep.end();) {
	  if ((*p) == (*q)->target_pointer()) {
	    
	    /* step: if the deleted controller is selected, unselect it */ 
	    if (selected_controller_repp == *q)
	      selected_controller_repp = 0;
	    
	    /* step: remove the related controller */
	    delete *q;
	    gcontrollers_rep.erase(q);

	    /* step: in case there are multiple controllers per chainop */
	    q = gcontrollers_rep.begin();
	  }
	  else
	    ++q;
	}

	/* step: delete and remove from the list */
	delete *p;
	chainops_rep.erase(p);

	/* step: invalidate selection if the selected cop
	 *       was affected */
	if (op_index >= selected_chainop_number_rep) {
	  selected_chainop_number_rep = -1;

	  break;
	}
      }
    }
    
    initialized_rep = false; 
  }

  // --------
  DBC_ENSURE(is_initialized() != true || to_remove == 0); 
  // --------
}

/**
 * Returns the name of selected chain operator.
 *
 * require:
  *  selected_chain_operator() != 0
 */
string CHAIN::chain_operator_name(void) const
{
  assert(selected_chainop_number_rep > 0);
  assert(selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()));
  return chainops_rep[selected_chainop_number_rep - 1]->name();
}

/**
 * Returns the name of selected chain operator parameter.
 *
 * require:
  *  selected_chain_operator() != 0
  *  selected_chain_operator_parameter() != 0
 */
string CHAIN::chain_operator_parameter_name(void) const
{
  assert(selected_chainop_number_rep > 0);
  assert(selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()));

  return chainops_rep[selected_chainop_number_rep - 1]->get_parameter_name(selected_chain_operator_parameter());
}

/**
 * Returns the name of selected controller parameter.
 *
 * require:
  *  selected_controller() != 0
  *  selected_controller_parameter() != 0
 */
string CHAIN::controller_parameter_name(void) const
{
  // --------
  DBC_REQUIRE(selected_controller() > 0);
  DBC_REQUIRE(selected_controller_parameter() > 0);
  // --------
  return selected_controller_repp->get_parameter_name(selected_controller_parameter());
}

/**
 * Returns the total number of parameters for the 
 * selected chain operator.
 *
 * require:
 *  selected_chain_operator() != 0
 */
int CHAIN::number_of_chain_operator_parameters(void) const
{
  assert(selected_chainop_number_rep > 0);
  assert(selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()));

  return chainops_rep[selected_chainop_number_rep - 1]->number_of_params();
}


/**
 * Returns the total number of parameters for the 
 * chain operator 'index' ([1...N]).
 *
 * require:
 *  index < number_of_chain_operators()
 */
int CHAIN::number_of_chain_operator_parameters(int index) const
{
  DBC_REQUIRE(index > 0);
  DBC_REQUIRE(index <= number_of_chain_operators());
  if (index > 0 &&
      index <= number_of_chain_operators())
    return chainops_rep[index - 1]->number_of_params();

  return -1;
}

/**
 * Returns the total number of parameters for the selected controller.
 *
 * require:
  *  selected_controller() != 0
 */
int CHAIN::number_of_controller_parameters(void) const
{
  // --------
  DBC_REQUIRE(selected_controller() > 0);
  // --------
  return selected_controller_repp->number_of_params();
}

/**
 * Returns the name of selected controller.
 *
 * require:
 *  selected_controller() != 0
 */
string CHAIN::controller_name(void) const
{
  // --------
  DBC_REQUIRE(selected_controller() > 0);
  // --------
  return selected_controller_repp->name();
}

/**
 * Sets the parameter value (selected chain operator) 
 *
 * @param op_index operator index (1...N), or -1 to use the selected op
 * @param param_index param index (1...N), or -1 to use the selected param
 * @param value new value
 */
void CHAIN::set_parameter(int op_index, int param_index, CHAIN_OPERATOR::parameter_t value)
{
  CHAIN_OPERATOR *cop = 0;

  if (op_index < 0) {
    if (selected_chainop_number_rep > 0 &&
	selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()))
      cop = chainops_rep[selected_chainop_number_rep - 1];
  }
  else if (op_index > 0 &&
	   op_index <= static_cast<int>(chainops_rep.size())) 
    cop = chainops_rep[op_index - 1];

  if (param_index < 0)
    param_index = selected_chainop_parameter_rep;

  if (cop)
    cop->set_parameter(param_index, value);
}

/**
 * Gets the parameter value (selected chain operator) 
 *
 * @param index parameter number
 *
 * require:
 *  selected_chain_operator_parameter() > 0 &&
 *  selected_chain_operator() != 0
 */
CHAIN_OPERATOR::parameter_t CHAIN::get_parameter(void) const
{
  DBC_CHECK(selected_chainop_number_rep > 0);
  DBC_CHECK(selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()));

  if (selected_chainop_number_rep > 0 &&
      selected_chainop_number_rep <= static_cast<int>(chainops_rep.size())) {
    return chainops_rep[selected_chainop_number_rep - 1]->get_parameter(selected_chainop_parameter_rep);
  }

  return 0.0f;
}

/**
 * Adds a generic controller and assign it to selected dynamic object
 *
 * require:
 *  gcontroller != 0
 *  selected_dynobj != 0
 */
void CHAIN::add_controller(GENERIC_CONTROLLER* gcontroller)
{
  // --------
  DBC_REQUIRE(gcontroller != 0);
  DBC_REQUIRE(selected_dynobj_repp != 0);
  // --------

#ifndef ECA_DISABLE_EFFECTS
  gcontroller->assign_target(selected_dynobj_repp);
  ECA_LOG_MSG(ECA_LOGGER::user_objects, gcontroller->status());
#endif
  gcontrollers_rep.push_back(gcontroller);
  selected_controller_repp = gcontroller;
  selected_controller_number_rep = gcontrollers_rep.size();
}

const CHAIN_OPERATOR* CHAIN::get_selected_chain_operator(void) const
{
  if (selected_chainop_number_rep > 0 &&
      selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()))
    return chainops_rep[selected_chainop_number_rep - 1];
  return 0;
}

/**
 * Removes the selected controller
 *
 * require:
 *  selected_controller() <= number_of_controllers();
 *  selected_controller() > 0
 */
void CHAIN::remove_controller(void)
{
  // --------
  DBC_REQUIRE(selected_controller() > 0);
  DBC_REQUIRE(selected_controller() <= number_of_controllers());
  // --------

  int n = 0;
  for(std::vector<GENERIC_CONTROLLER*>::iterator q = gcontrollers_rep.begin(); 
      q != gcontrollers_rep.end(); 
      q++) {
    if ((n + 1) == selected_controller()) {
      delete *q;
      gcontrollers_rep.erase(q);
      select_controller(-1);
      break;
    }
    ++n;
  }
}

/**
 * Clears chain (removes all chain operators and controllers)
 */
void CHAIN::clear(void)
{
  for(std::vector<CHAIN_OPERATOR*>::iterator p = chainops_rep.begin(); p != chainops_rep.end(); p++) {
    delete *p;
    *p = 0;
  }
  chainops_rep.resize(0);
  for(std::vector<GENERIC_CONTROLLER*>::iterator p = gcontrollers_rep.begin(); p !=
	gcontrollers_rep.end(); p++) {
    delete *p;
    *p = 0;
  }
  gcontrollers_rep.resize(0);

  initialized_rep = false;
}

/**
 * Selects a chain operator. If no chain operators
 * are found with 'index', with index 'index'. 
 *
 * @param index 1...N
 *
 * ensure:
 *  index == selected_chain_operator() || 
 *  selected_chain_operator() == 0
 */
void CHAIN::select_chain_operator(int index)
{
  if (index > 0 &&
      index <= static_cast<int>(chainops_rep.size())) {
    selected_chainop_number_rep = index;
    selected_chain_operator_as_target();
  }
}

/**
 * Selects a chain operator parameter. Index of zero clears out 
 * current selection.
 *
 * require:
 *  index > 0
 *  selected_chain_operator() != 0 || index == 0
 *  index <= selected_chain_operator()->number_of_params()
 *
 * ensure:
 *  index == selected_chain_operator_parameter()
 */
void CHAIN::select_chain_operator_parameter(int index)
{
  DBC_REQUIRE(index > 0);
  selected_chainop_parameter_rep = index;
}


/**
 * Selects a controller. Index of zero clears out 
 * current selection.
 *
 * @param index 1...N, or negative to clear selection
 *
 * ensure:
 *  index == selected_controller() ||
 *  selected_controller() == 0
 */
void CHAIN::select_controller(int index)
{
  DBC_REQUIRE(index != 0);

  selected_controller_repp = 0;
  selected_controller_number_rep = 0;
  for(int gcontroller_sizet = 0; gcontroller_sizet != static_cast<int>(gcontrollers_rep.size()); gcontroller_sizet++) {
    if (gcontroller_sizet + 1 == index) {
      selected_controller_repp = gcontrollers_rep[gcontroller_sizet];
      selected_controller_number_rep = index;
    }
  }
}

/**
 * Selects a controller parameter. Index of zero clears out 
 * current selection.
 *
 * require:
 *  index > 0
 *  selected_controller() != 0 || index == 0
 *  index <= selected_controller()->number_of_params()
 *
 * ensure:
 *  index == selected_controller_parameter()
 */
void CHAIN::select_controller_parameter(int index)
{
  selected_controller_parameter_rep = index;
}

/**
 * Gets the value of the currently selected controller parameter.
 *
 * require:
 *  selected_controller() != 0
 *  selected_controller_parameter() != 0
 */
CHAIN_OPERATOR::parameter_t CHAIN::get_controller_parameter(void) const
{
  // --------
  DBC_REQUIRE(selected_controller_parameter() > 0);
  DBC_REQUIRE(selected_controller() != 0);
  // --------
  return selected_controller_repp->get_parameter(selected_controller_parameter_rep);
}

/**
 * Sets the value of the currently selected controller parameter.
 *
 * @param ctrl_index operator index (1...N), or -1 to use the selected ctrl
 * @param param_index param index (1...N), or -1 to use the selected param
 * @param value new value
 */
void CHAIN::set_controller_parameter(int ctrl_index, int param_index, CHAIN_OPERATOR::parameter_t value) 
{
  GENERIC_CONTROLLER *ctrl = 0;

  if (ctrl_index < 0) {
    if (selected_controller_number_rep > 0 &&
	selected_controller_number_rep <= static_cast<int>(gcontrollers_rep.size()))
      ctrl = gcontrollers_rep[selected_controller_number_rep - 1];
  }
  else if (ctrl_index > 0 &&
	   ctrl_index <= static_cast<int>(gcontrollers_rep.size())) 
    ctrl = gcontrollers_rep[ctrl_index - 1];

  if (param_index < 0)
    param_index = selected_controller_parameter_rep;

  DBC_CHECK(param_index > 0);

  if (ctrl) 
    ctrl->set_parameter(param_index, value);
}

/**
 * Use current selected chain operator as 
 * target for parameters control.
 *
 * require:
 *   selected_chain_operator() != 0
 *
 * ensure:
 *   selected_target() == selected_chain_operator()
 */
void CHAIN::selected_chain_operator_as_target(void)
{
  assert(selected_chainop_number_rep > 0);
  assert(selected_chainop_number_rep <= static_cast<int>(chainops_rep.size()));

  selected_dynobj_repp = chainops_rep[selected_chainop_number_rep - 1];

  // --------
  DBC_ENSURE(selected_dynobj_repp == chainops_rep[selected_chainop_number_rep - 1]);
  // --------
}

/**
 * Use current selected controller as 
 * target for parameter control.
 *
 * require:
 *   selected_controller() != 0
 *
 * ensure:
 *   selected_target() == selected_controller()
 */
void CHAIN::selected_controller_as_target(void)
{
  // --------
  DBC_REQUIRE(selected_controller_repp != 0);
  // --------
  selected_dynobj_repp = selected_controller_repp;
  // --------
  DBC_ENSURE(selected_dynobj_repp == selected_controller_repp);
  // --------
}

/**
 * Prepares chain for processing. All further processing
 * will be done using the buffer pointer by 'sbuf'.
 * If all parameters are zero, previously specified 
 * parameters are used (state re-initialization).
 *
 * require:
 *  input_id != 0 || in_channels != 0
 *  output_id != 0 || out_channels != 0
 *  audioslot_repp != 0 || sbuf != 0
 *
 * ensure:
 *  is_initialized() == true
 */
void CHAIN::init(SAMPLE_BUFFER* sbuf, int in_channels, int out_channels)
{
  // --------
  DBC_REQUIRE(in_channels != 0 || in_channels_rep != 0);
  DBC_REQUIRE(out_channels != 0 || out_channels_rep != 0);
  DBC_REQUIRE(sbuf != 0 || audioslot_repp != 0);
  // --------

  DBC_CHECK(samples_per_second() > 0);

  if (sbuf != 0) audioslot_repp = sbuf;
  if (in_channels != 0) in_channels_rep = in_channels;
  if (out_channels != 0) out_channels_rep = out_channels;

  int channels_next = in_channels_rep;
  for(size_t p = 0; p != chainops_rep.size(); p++) {
    /* note: buffer must have room to store both input and 
     *       output channels (processing in-place) */
    int out_ch = chainops_rep[p]->output_channels(channels_next);
    if (out_ch > channels_next)
      channels_next = out_ch;
    audioslot_repp->number_of_channels(channels_next);

    chainops_rep[p]->init(audioslot_repp);

    /* note: for the next plugin, only 'out_ch' channels contain 
     *        valid audio */
    channels_next = out_ch;
  }

  for(size_t p = 0; p != gcontrollers_rep.size(); p++) {
    gcontrollers_rep[p]->init();
  }

  refresh_parameters();
  initialized_rep = true;

  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      "Initialized chain " +
	      name() + 
	      " with " +
	      kvu_numtostr(chainops_rep.size()) +
	      " chainops and " +
	      kvu_numtostr(gcontrollers_rep.size()) +
	      " gcontrollers. Sbuf points to " +
	      kvu_numtostr(reinterpret_cast<long int>(audioslot_repp)) + ".");
  
  // --------
  DBC_ENSURE(is_initialized() == true);
  // --------
}

/** 
 * Releases all buffers assigned to chain operators.
 */
void CHAIN::release(void)
{
  for(size_t p = 0; p != chainops_rep.size(); p++) {
    chainops_rep[p]->release();
  }
  initialized_rep = false;

  // ---------
  DBC_ENSURE(is_initialized() != true);
  // ---------
}

/**
 * Processes chain data with all chain operators.
 *
 * require:
 *  is_initialized() == true
 */
void CHAIN::process(void)
{
  // --------
  DBC_REQUIRE(is_initialized() == true);
  // --------

  /* step: update operator parameters */
  controller_update();

  /* step: run processing components */
  if (muted_rep != true) {
    /* note: if muted, don't bother running the chainops */
    if (sfx_rep == true) {
      /* note: processing enabled (no bypass) */
      for(int p = 0; p != static_cast<int>(chainops_rep.size()); p++) {

	/* note: increase channel count if chainop needs the space */
	int out_ch = chainops_rep[p]->output_channels(audioslot_repp->number_of_channels());
	if (out_ch > audioslot_repp->number_of_channels())
	  audioslot_repp->number_of_channels(out_ch);
	
	chainops_rep[p]->process();
      }
    }
  }
  else {
    audioslot_repp->make_silent();
  }

  /* step: update chain position */
  change_position_in_samples(audioslot_repp->length_in_samples());
}

/**
 * Calculates/fetches new values for all controllers.
 */
void CHAIN::controller_update(void)
{
  for(size_t n = 0; n < gcontrollers_rep.size(); n++) {
    DEBUG_CTRL_STATEMENT(GENERIC_CONTROLLER* ptr = gcontrollers_rep[n]);

    gcontrollers_rep[n]->value(position_in_seconds_exact());

    DEBUG_CTRL_STATEMENT(std::cerr << "trace: " << ptr->name());
    DEBUG_CTRL_STATEMENT(std::cerr << "; value " << ptr->source_pointer()->value() << "." << std::endl);
  }
}

/**
 * Re-initializes all effect parameters.
 */
void CHAIN::refresh_parameters(void)
{
  for(int chainop_sizet = 0; chainop_sizet != static_cast<int>(chainops_rep.size()); chainop_sizet++) {
    for(int n = 0; n < chainops_rep[chainop_sizet]->number_of_params(); n++) {
      chainops_rep[chainop_sizet]->set_parameter(n + 1, 
						 chainops_rep[chainop_sizet]->get_parameter(n + 1));
    }
  }
}

/**
 * Converts chain to a formatted string.
 */
string CHAIN::to_string(void) const
{
  MESSAGE_ITEM t; 

  FILE_PRESET* fpreset;
  GLOBAL_PRESET* gpreset;

  int q = 0;
  while (q < static_cast<int>(chainops_rep.size())) {
#ifndef ECA_DISABLE_EFFECTS
    fpreset = 0;
    fpreset = dynamic_cast<FILE_PRESET*>(chainops_rep[q]);
    if (fpreset != 0) {
      t << "-pf:" << fpreset->filename();
      if (fpreset->number_of_params() > 0) t << ",";
      t << ECA_OBJECT_FACTORY::operator_parameters_to_eos(fpreset);
      t << " ";
    }
    else {
      gpreset = 0;
      gpreset = dynamic_cast<GLOBAL_PRESET*>(chainops_rep[q]);
      if (gpreset != 0) {
	t << "-pn:" << gpreset->name();
	if (gpreset->number_of_params() > 0) t << ",";
	t << ECA_OBJECT_FACTORY::operator_parameters_to_eos(gpreset);
	t << " ";
      }
      else {
        t << ECA_OBJECT_FACTORY::chain_operator_to_eos(chainops_rep[q]) << " ";
      }
    }
    
    /* check if the chainop is controlled by a gcontroller */
    std::vector<GENERIC_CONTROLLER*>::size_type p = 0;
    while (p < gcontrollers_rep.size()) {
      if (chainops_rep[q] == gcontrollers_rep[p]->target_pointer()) {
	t << " " << ECA_OBJECT_FACTORY::controller_to_eos(gcontrollers_rep[p]);
	t << " ";
	/* check if the gcontroller is controlled by another gcontroller */
	std::vector<GENERIC_CONTROLLER*>::size_type r = 0;
	while (r < gcontrollers_rep.size()) {
	  if (p != r && 
	      gcontrollers_rep[p] == gcontrollers_rep[r]->target_pointer()) {
	    t << " -kx " << ECA_OBJECT_FACTORY::controller_to_eos(gcontrollers_rep[r]);
	  }
	  ++r;
	} 
      }
      ++p;
    }
#endif
    ++q;
  }

  return t.to_string();
}

/**
 * Reimplemented from ECA_SAMPLERATE_AWARE
 */
void CHAIN::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  for(size_t p = 0; p != chainops_rep.size(); p++) {
    CHAIN_OPERATOR* temp = chainops_rep[p];
    ECA_SAMPLERATE_AWARE* srateobj = dynamic_cast<ECA_SAMPLERATE_AWARE*>(temp);
    if (srateobj != 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		    "sample rate change, chain '" +
		    name() + "' object '" +
		    temp->name() + "' rate " +
		    kvu_numtostr(v) + ".");
      srateobj->set_samples_per_second(v);
    }
  }

  ECA_SAMPLERATE_AWARE::set_samples_per_second(v);
}

/**
 * Reimplemented from ECA_AUDIO_POSITION.
 */
SAMPLE_SPECS::sample_pos_t CHAIN::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,
		"seek position, to pos " +
		kvu_numtostr(pos) + ".");

  return pos;
}
