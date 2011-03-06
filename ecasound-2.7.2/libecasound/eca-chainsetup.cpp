// ------------------------------------------------------------------------
// eca-chainsetup.cpp: Class representing an ecasound chainsetup object.
// Copyright (C) 1999-2006,2008,2009 Kai Vehmanen
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

#include <string>
#include <cstring>
#include <algorithm>        /* find() */
#include <fstream>
#include <vector>
#include <list>
#include <iostream>

#include <sys/types.h>      /* POSIX: getpid() */
#include <unistd.h>         /* POSIX: getpid() */
#ifdef HAVE_SYS_MMAN_H
#include <sys/mman.h>       /* POSIX: for mlockall() */
#endif

#ifdef ECA_COMPILE_JACK
#include <jack/jack.h>
#endif

#include <kvu_dbc.h>
#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_rtcaps.h>
#include <kvu_utils.h>

#include "eca-resources.h"
#include "eca-session.h"

#include "generic-controller.h"
#include "eca-chainop.h"
#include "eca-chain.h"

#include "audioio.h"
#include "audioio-manager.h"
#include "audioio-device.h"
#include "audioio-buffered.h"
#include "audioio-loop.h"
#include "audioio-null.h"
#include "audioio-resample.h"

#include "eca-engine-driver.h"
#include "eca-object-factory.h"
#include "eca-object-map.h"

#include "midiio.h"
#include "midi-client.h"

#include "eca-object-factory.h"
#include "eca-chainsetup-position.h"
#include "sample-specs.h"

#include "eca-error.h"
#include "eca-logger.h"

#include "eca-chainsetup.h"
#include "eca-chainsetup_impl.h"

using std::cerr;
using std::endl;
using namespace ECA;

const string ECA_CHAINSETUP::default_audio_format_const = "s16_le,2,44100,i";
const string ECA_CHAINSETUP::default_bmode_nonrt_const = "1024,false,50,false,100000,true";
const string ECA_CHAINSETUP::default_bmode_rt_const = "1024,true,50,true,100000,true";
const string ECA_CHAINSETUP::default_bmode_rtlowlatency_const = "256,true,50,true,100000,false";

static void priv_erase_object(std::vector<AUDIO_IO*>* vec, const AUDIO_IO* obj);

/**
 * Construct from a vector of options.
 * 
 * If any invalid options are passed us argument, 
 * interpret_result() will be 'false', and 
 * interpret_result_verbose() contains more detailed 
 * error description.
 */
ECA_CHAINSETUP::ECA_CHAINSETUP(const vector<string>& opts) 
  : cparser_rep(this),
    is_enabled_rep(false)
{
  impl_repp = new ECA_CHAINSETUP_impl;

  // FIXME: set default audio format here!

  setup_name_rep = "untitled-chainsetup";
  setup_filename_rep = "";

  set_defaults();

  vector<string> options (opts);
  cparser_rep.preprocess_options(options);
  interpret_options(options);
  if (interpret_result() == true) {
    /* do not add default if there were parsing errors as
     * it might hide real problems */
    add_default_output();
    add_default_midi_device();
  }

  ECA_LOG_MSG(ECA_LOGGER::info, "Chainsetup \"" + setup_name_rep + "\"");
}

/**
 * Constructs an empty chainsetup.
 *
 * @post buffersize != 0
 */
ECA_CHAINSETUP::ECA_CHAINSETUP(void) 
  : cparser_rep(this),
    is_enabled_rep(false)
{
  impl_repp = new ECA_CHAINSETUP_impl;

  setup_name_rep = "";
  set_defaults();

  ECA_LOG_MSG(ECA_LOGGER::info, "Chainsetup created (empty)");
}

/**
 * Construct from a chainsetup file.
 * 
 * If any invalid options are passed us argument, 
 * interpret_result() will be 'false', and 
 * interpret_result_verbose() contains more detailed 
 * error description.
 *
 * @post buffersize != 0
 */
ECA_CHAINSETUP::ECA_CHAINSETUP(const string& setup_file) 
  : cparser_rep(this),
    is_enabled_rep(false)
{
  impl_repp = new ECA_CHAINSETUP_impl;

  setup_name_rep = "";
  set_defaults();
  vector<string> options;
  load_from_file(setup_file, options);
  set_filename(setup_file);
  if (name() == "") set_name(setup_file);
  cparser_rep.preprocess_options(options);
  interpret_options(options);
  if (interpret_result() == true) {
    /* do not add default if there were parsing errors as
     * it might hide real problems */
    add_default_output();
  }

  ECA_LOG_MSG(ECA_LOGGER::info, 
	      "Chainsetup \"" 
	      + name() + "\" created (file: "
	      + setup_file + ")"); 
}

/**
 * Destructor
 */
ECA_CHAINSETUP::~ECA_CHAINSETUP(void)
{ 
  ECA_LOG_MSG(ECA_LOGGER::system_objects,"ECA_CHAINSETUP destructor-in");

  DBC_CHECK(is_locked() != true);
  if (is_enabled() == true) {
    disable();
  }
  DBC_CHECK(is_enabled() != true);

  /* delete chain objects */
  for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting chain \"" + (*q)->name() + "\".");
    delete *q;
    *q = 0;
  }

  /* delete input db objects; reset all pointers to null */
  for(vector<AUDIO_IO*>::iterator q = inputs.begin(); q != inputs.end(); q++) {
    if (dynamic_cast<AUDIO_IO_DB_CLIENT*>(*q) != 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting audio db-client \"" + (*q)->label() + "\".");
      delete *q;
    }
    *q = 0;
  }

  /* delete all actual audio input objects except loop devices; reset all pointers to null */
  for(vector<AUDIO_IO*>::iterator q = inputs_direct_rep.begin(); q != inputs_direct_rep.end(); q++) {
    if (dynamic_cast<LOOP_DEVICE*>(*q) == 0) { 
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting audio object \"" + (*q)->label() + "\".");
      delete *q;
    }
    *q = 0;
  }

  /* delete output db objects; reset all pointers to null */
  for(vector<AUDIO_IO*>::iterator q = outputs.begin(); q != outputs.end(); q++) {
    if (dynamic_cast<AUDIO_IO_DB_CLIENT*>(*q) != 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting audio db-client \"" + (*q)->label() + "\".");
      delete *q;
    }
    *q = 0;
  }

  /* delete all actual audio output objects except loop devices; reset all pointers to null */
  for(vector<AUDIO_IO*>::iterator q = outputs_direct_rep.begin(); q != outputs_direct_rep.end(); q++) {
    // trouble with dynamic_cast with libecasoundc apps like ecalength?
    if (dynamic_cast<LOOP_DEVICE*>(*q) == 0) { 
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting audio object \"" + (*q)->label() + "\".");
      delete *q;
      *q = 0;
    }
  }

  /* delete loop objects */
  for(map<string,LOOP_DEVICE*>::iterator q = loop_map.begin(); q != loop_map.end(); q++) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting loop device \"" + q->second->label() + "\".");
    delete q->second;
    q->second = 0;
  }

  /* delete aio manager objects */
  for(vector<AUDIO_IO_MANAGER*>::iterator q = aio_managers_rep.begin(); q != aio_managers_rep.end(); q++) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Deleting audio manager \"" + (*q)->name() + "\".");
    delete *q;
    *q = 0;
  }

  delete impl_repp;

  ECA_LOG_MSG(ECA_LOGGER::system_objects,"ECA_CHAINSETUP destructor-out");
}

/**
 * Sets default values.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::set_defaults(void)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  /* note: defaults are set as specified in ecasoundrc(5) */

  precise_sample_rates_rep = false;
  ignore_xruns_rep = true;

  pserver_repp = &impl_repp->pserver_rep;
  midi_server_repp = &impl_repp->midi_server_rep;
  engine_driver_repp = 0;

  if (kvu_check_for_sched_fifo() == true) {
    rtcaps_rep = true;
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "Rtcaps detected.");
  }
  else 
    rtcaps_rep = false;

  db_clients_rep = 0;
  multitrack_mode_rep = false;
  multitrack_mode_override_rep = false;
  memory_locked_rep = false;
  midi_server_needed_rep = false;
  is_locked_rep = false;
  selected_chain_index_rep = 0;
  selected_ctrl_index_rep = 0;
  selected_ctrl_param_index_rep = 0;
  multitrack_mode_offset_rep = -1;

  buffering_mode_rep = cs_bmode_auto;
  active_buffering_mode_rep = cs_bmode_none;

  set_output_openmode(AUDIO_IO::io_readwrite);

  ECA_RESOURCES ecaresources;
  if (ecaresources.has_any() != true) {
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"WARNING: Unable to read global resources. May result in incorrect behaviour.");
  }
  
  set_default_midi_device(ecaresources.resource("midi-device"));
  string rc_temp = set_resource_helper(ecaresources,
				       "default-audio-format", 
				       ECA_CHAINSETUP::default_audio_format_const);
  cparser_rep.interpret_object_option("-f:" + rc_temp);
  set_samples_per_second(default_audio_format().samples_per_second());
  toggle_precise_sample_rates(ecaresources.boolean_resource("default-to-precise-sample-rates"));
  rc_temp = set_resource_helper(ecaresources, 
				"default-mix-mode", 
				"avg");
  cparser_rep.interpret_object_option("-z:mixmode," + rc_temp);

  impl_repp->bmode_nonrt_rep.set_all(set_resource_helper(ecaresources,
							 "bmode-defaults-nonrt",
							 ECA_CHAINSETUP::default_bmode_nonrt_const));
  impl_repp->bmode_rt_rep.set_all(set_resource_helper(ecaresources,
						      "bmode-defaults-rt",
						      ECA_CHAINSETUP::default_bmode_rt_const));
  impl_repp->bmode_rtlowlatency_rep.set_all(set_resource_helper(ecaresources,
								"bmode-defaults-rtlowlatency",
								ECA_CHAINSETUP::default_bmode_rtlowlatency_const));

  impl_repp->bmode_active_rep = impl_repp->bmode_nonrt_rep;
}

/**
 * Sets a resource value.
 *
 * Only used by ECA_CHAINSETUP::set_defaults.
 */
string ECA_CHAINSETUP::set_resource_helper(const ECA_RESOURCES& ecaresources, const string& tag, const string& alternative)
{
  if (ecaresources.has(tag) == true) {
    return ecaresources.resource(tag);
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::system_objects,
		"Using hardcoded defaults for \"" +
		tag + "\".");
    return alternative;
  }
}

/**
 * Tests whether chainsetup is in a valid state.
 */
bool ECA_CHAINSETUP::is_valid(void) const
{
  return is_valid_for_connection(false);
}

/**
 * Checks whether chainsetup is valid for enabling/connecting. 
 * If chainsetup is not valid and 'verbose' is true, detected
 * errors are reported via the logging subsystem.
 */
bool ECA_CHAINSETUP::is_valid_for_connection(bool verbose) const 
{
  bool result = true;

  if (inputs.size() == 0) {
    if (verbose) ECA_LOG_MSG(ECA_LOGGER::info, 
			     "Unable to connect: No inputs in the current chainsetup. (1.1-NO-INPUTS)");
    result = false;
  }
  else if (outputs.size() == 0) {
    if (verbose) ECA_LOG_MSG(ECA_LOGGER::info, 
			     "Unable to connect: No outputs in the current chainsetup. (1.2-NO-OUTPUTS)");
    result = false;
  }
  else if (chains.size() == 0) {
    if (verbose) ECA_LOG_MSG(ECA_LOGGER::info, 
			     "Unable to connect: No chains in the current chainsetup. (1.3-NO-CHAINS)");
    result = false;
  }
  else {
    list<int> conn_inputs, conn_outputs;

    for(vector<CHAIN*>::const_iterator q = chains.begin(); q != chains.end(); q++) {
      /* log messages printed in CHAIN::is_valid() */

      int id = (*q)->connected_input();
      if (id > -1) 
	conn_inputs.push_back(id);
      
      if ((*q)->is_valid() == false) {
	result = false;
	if (verbose) ECA_LOG_MSG(ECA_LOGGER::info, 
				 "Unable to connect: Chain \"" + (*q)->name() + 
				 "\" is not valid. Following errors were detected:");
	if (verbose && id == -1) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "Chain \"" + (*q)->name() + "\" is not connected to any input. "
		      "All chains must have exactly one valid input. (2.1-NO-CHAIN-INPUT)");
	}
      }

      id = (*q)->connected_output();
      if (id > -1) 
	conn_outputs.push_back(id);

      if (verbose && (*q)->is_valid() == false) {
	if (id == -1) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "Chain \"" + (*q)->name() + "\" is not connected to any output. "
		      "All chains must have exactly one valid output. (2.2-NO-CHAIN-OUTPUT)");
	}
      }
    }

    // FIXME: doesn't work yet

    if (verbose) {
      for(int n = 0; n < static_cast<int>(inputs.size()); n++) {
	if (std::find(conn_inputs.begin(), conn_inputs.end(), n) == conn_inputs.end()) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "WARNING: Input \"" + inputs[n]->label() + "\" is not connected to any chain. (3.1-DISCON-INPUT)");
	}
      }
      
      for(int n = 0; n < static_cast<int>(outputs.size()); n++) {
	if (std::find(conn_outputs.begin(), conn_outputs.end(), n) == conn_outputs.end()) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "WARNING: Output \"" + outputs[n]->label() + "\" is not connected to any chain. (3.2-DISCON-OUTPUT)");
	}
      }
    }
  } /* (verbose == true) */

  return result;
}

void ECA_CHAINSETUP::set_buffering_mode(Buffering_mode_t value)
{
  if (value == ECA_CHAINSETUP::cs_bmode_none)
    buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_auto;
  else
    buffering_mode_rep = value;
}

/**
 * Sets audio i/o manager option for manager
 * object type 'mgrname' to be 'optionstr'.
 * Previously set option string is overwritten.
 */
void ECA_CHAINSETUP::set_audio_io_manager_option(const string& mgrname, const string& optionstr)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      "Set manager \"" +
	      mgrname + "\" option string to \"" +
	      optionstr + "\".");

  aio_manager_option_map_rep[mgrname] = optionstr;
  propagate_audio_io_manager_options();
}

/**
 * Determinates the active buffering parameters based on
 * defaults, user overrides and analyzing the current 
 * chainsetup configuration. If the resulting parameters 
 * are different from current ones, a state change is
 * performed.
 */ 
void ECA_CHAINSETUP::select_active_buffering_mode(void)
{
  if (buffering_mode() == ECA_CHAINSETUP::cs_bmode_none) {
    active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_auto;
  }
  
  if (!(multitrack_mode_override_rep == true && 
	multitrack_mode_rep != true) && 
      ((multitrack_mode_override_rep == true && 
	multitrack_mode_rep == true) ||
       (number_of_realtime_inputs() > 0 && 
	number_of_realtime_outputs() > 0 &&
	number_of_non_realtime_inputs() > 0 && 
	number_of_non_realtime_outputs() > 0 &&
	chains.size() > 1))) {
    ECA_LOG_MSG(ECA_LOGGER::info, "Multitrack-mode enabled.");
    multitrack_mode_rep = true;
  }
  else
    multitrack_mode_rep = false;
  
  if (buffering_mode() == ECA_CHAINSETUP::cs_bmode_auto) {

    /* initialize to 'nonrt', mt-disabled */
    active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_nonrt;

    if (has_realtime_objects() == true) {
      /* case 1: a multitrack setup */
      if (multitrack_mode_rep == true) {
	active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_rt;
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection case-1");
      }

      /* case 2: rt-objects without priviledges for rt-scheduling */
      else if (rtcaps_rep != true) {
	ECA_LOG_MSG(ECA_LOGGER::info,
		    "NOTE: Real-time configuration, but insufficient privileges to utilize real-time scheduling (SCHED_FIFO). With small buffersizes, this may cause audible glitches during processing.");
	toggle_raised_priority(false);
	active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_rt;
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection case-2");
      }

      /* case 3: no chain operators and "one-way rt-operation" */
      else if (number_of_chain_operators() == 0 &&
	       (number_of_realtime_inputs() == 0 || 
		number_of_realtime_outputs() == 0)) {
	active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_rt;
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection case-3");
      }

      /* case 4: default for rt-setups */
      else {
	active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_rtlowlatency;
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection case-4");
      }
    }
    else { 
      /* case 5: no rt-objects */
      active_buffering_mode_rep = ECA_CHAINSETUP::cs_bmode_nonrt;
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection case-5");
    }
  }
  else {
    /* user has explicitly selected the buffering mode */
    active_buffering_mode_rep = buffering_mode();
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "bmode-selection explicit");
  }
  
  switch(active_buffering_mode_rep) 
    {
    case ECA_CHAINSETUP::cs_bmode_nonrt: { 
      impl_repp->bmode_active_rep = impl_repp->bmode_nonrt_rep;
      ECA_LOG_MSG(ECA_LOGGER::info, 
		    "\"nonrt\" buffering mode selected.");
      break; 
    }
    case ECA_CHAINSETUP::cs_bmode_rt: { 
      impl_repp->bmode_active_rep = impl_repp->bmode_rt_rep;
      ECA_LOG_MSG(ECA_LOGGER::info, 
		    "\"rt\" buffering mode selected.");
      break; 
    }
    case ECA_CHAINSETUP::cs_bmode_rtlowlatency: { 
      impl_repp->bmode_active_rep = impl_repp->bmode_rtlowlatency_rep;
      ECA_LOG_MSG(ECA_LOGGER::info, 
		    "\"rtlowlatency\" buffering mode selected.");
      break;
    }
    default: { /* error! */ }
    }

  ECA_LOG_MSG(ECA_LOGGER::system_objects,
		"Set buffering parameters to: \n--cut--" +
		impl_repp->bmode_active_rep.to_string() +"\n--cut--");
}

/**
 * Enable chosen active buffering mode.
 * 
 * Called only from enable().
 */
void ECA_CHAINSETUP::enable_active_buffering_mode(void)
{
  /* 1. if requested, lock all memory */
  if (raised_priority() == true) {
    lock_all_memory();
  }
  else {
    unlock_all_memory();
  }

  /* 2. if necessary, switch between different db and direct modes */
  if (double_buffering() == true) {
    if (has_realtime_objects() != true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,
		    "No realtime objects; switching to direct mode.");
      switch_to_direct_mode();
      impl_repp->bmode_active_rep.toggle_double_buffering(false);
    }
    else if (has_nonrealtime_objects() != true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,
		    "Only realtime objects; switching to direct mode.");
      switch_to_direct_mode();
      impl_repp->bmode_active_rep.toggle_double_buffering(false);
    }
    else if (db_clients_rep == 0) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,
		    "Switching to db mode.");
      switch_to_db_mode();
    }

    if (buffersize() != 0) {
      impl_repp->pserver_rep.set_buffer_defaults(double_buffer_size() / buffersize(), 
						 buffersize());
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::info,
		    "WARNING: Buffersize set to 0.");
      impl_repp->pserver_rep.set_buffer_defaults(0, 0);
    }
  }
  else {
    /* double_buffering() != true */
    if (db_clients_rep > 0) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,
		    "Switching to direct mode.");
      switch_to_direct_mode();
    }
  }

  /* 3. propagate buffersize value to all dependent objects */
  /* FIXME: create a system for tracking buffesize aware objs */
}

void ECA_CHAINSETUP::switch_to_direct_mode(void)
{
  switch_to_direct_mode_helper(&inputs, inputs_direct_rep);
  switch_to_direct_mode_helper(&outputs, outputs_direct_rep);
  // --
  DBC_ENSURE(db_clients_rep == 0);
  // --
}

void ECA_CHAINSETUP::switch_to_direct_mode_helper(vector<AUDIO_IO*>* objs, 
						  const vector<AUDIO_IO*>& directobjs)
{
  // --
  DBC_CHECK(objs->size() == directobjs.size());
  // --

  for(size_t n = 0; n < objs->size(); n++) {
    AUDIO_IO_DB_CLIENT* pobj = dynamic_cast<AUDIO_IO_DB_CLIENT*>((*objs)[n]);
    if (pobj != 0) {
      delete (*objs)[n];
      (*objs)[n] = directobjs[n];
      --db_clients_rep;
    }
  } 
}

void ECA_CHAINSETUP::switch_to_db_mode(void)
{
  switch_to_db_mode_helper(&inputs, inputs_direct_rep);
  switch_to_db_mode_helper(&outputs, outputs_direct_rep);
}

void ECA_CHAINSETUP::switch_to_db_mode_helper(vector<AUDIO_IO*>* objs, 
						 const vector<AUDIO_IO*>& directobjs)
{
  // --
  DBC_REQUIRE(db_clients_rep == 0);
  DBC_CHECK(objs->size() == directobjs.size());
  // --

  for(size_t n = 0; n < directobjs.size(); n++) {
    (*objs)[n] = add_audio_object_helper(directobjs[n]);
  } 
}

/**
 * Locks all memory with mlockall().
 */
void ECA_CHAINSETUP::lock_all_memory(void)
{
#ifdef HAVE_MLOCKALL
  if (::mlockall (MCL_CURRENT|MCL_FUTURE)) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: Couldn't lock all memory!");
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "Memory locked!");
    memory_locked_rep = true;
  }
#else
  ECA_LOG_MSG(ECA_LOGGER::info, "Memory locking not available.");
#endif
}

/**
 * Unlocks all memory with munlockall().
 */
void ECA_CHAINSETUP::unlock_all_memory(void)
{
#ifdef HAVE_MUNLOCKALL
  if (memory_locked_rep == true) {
    if (::munlockall()) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "WARNING: Couldn't unlock all memory!");
    }
    else 
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Memory unlocked!");
    memory_locked_rep = false;
  }
#else
  memory_locked_rep = false;
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Memory unlocking not available.");
#endif
}

/**
 * Adds a "default" chain to this chainsetup.
 *
 * @pre buffersize >= 0 && chains.size() == 0
 * @pre is_locked() != true
 *
 * @post chains.back()->name() == "default" && 
 * @post active_chainids.back() == "default"
 */
void ECA_CHAINSETUP::add_default_chain(void)
{
  // --------
  DBC_REQUIRE(buffersize() >= 0);
  DBC_REQUIRE(chains.size() == 0);
  DBC_REQUIRE(is_locked() != true);
  // --------

  add_chain_helper("default");
  selected_chainids.push_back("default");

  // --------
  DBC_ENSURE(chains.back()->name() == "default");
  DBC_ENSURE(selected_chainids.back() == "default");
  // --------  
}

/**
 * Adds new chains to this chainsetup.
 * 
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::add_new_chains(const vector<string>& newchains)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  for(vector<string>::const_iterator p = newchains.begin(); p != newchains.end(); p++) {
    bool exists = false;
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*p == (*q)->name()) exists = true;
    }
    if (exists == false) {
      add_chain_helper(*p);
    }
  }
}

void ECA_CHAINSETUP::add_chain_helper(const string& name)
{
  chains.push_back(new CHAIN());
  chains.back()->name(name);
  chains.back()->set_samples_per_second(samples_per_second());
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Chain \"" + name + "\" created.");
}

/**
 * Removes all selected chains from this chainsetup.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::remove_chains(void)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  DBC_DECLARE(size_t old_chains_size = chains.size());
  DBC_DECLARE(size_t sel_chains_size = selected_chainids.size());
  // --------

  for(vector<string>::const_iterator a = selected_chainids.begin(); a != selected_chainids.end(); a++) {
    vector<CHAIN*>::iterator q = chains.begin();
    while(q != chains.end()) {
      if (*a == (*q)->name()) {
	delete *q;
	chains.erase(q);
	break;
      }
      ++q;
    }
  }
  selected_chainids.resize(0);

  // --
  DBC_ENSURE(chains.size() == old_chains_size - sel_chains_size);
  // --
}

/**
 * Clears all selected chains. Removes all chain operators
 * and controllers.
 *
 * @pre is_locked() != true
 */
void ECA_CHAINSETUP::clear_chains(void)
{
  // --------
  DBC_REQUIRE(is_locked() != true);
  // --------

  for(vector<string>::const_iterator a = selected_chainids.begin(); a != selected_chainids.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	(*q)->clear();
      }
    }
  }
}

/**
 * Renames the first selected chain.
 */
void ECA_CHAINSETUP::rename_chain(const string& name)
{
  for(vector<string>::const_iterator a = selected_chainids.begin(); a != selected_chainids.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	(*q)->name(name);
	return;
      }
    }
  }
}

/**
 * Selects all chains present in this chainsetup.
 */
void ECA_CHAINSETUP::select_all_chains(void)
{
  vector<CHAIN*>::const_iterator p = chains.begin();
  selected_chainids.resize(0);
  while(p != chains.end()) {
    selected_chainids.push_back((*p)->name());
    ++p;
  }
}

/**
 * Returns the index number of first selected chains. If no chains 
 * are selected, returns 'last_index + 1' (==chains.size()).
 */
unsigned int ECA_CHAINSETUP::first_selected_chain(void) const
{
  const vector<string>& schains = selected_chains();
  vector<string>::const_iterator o = schains.begin();
  unsigned int p = chains.size();
  while(o != schains.end()) {
    for(p = 0; p != chains.size(); p++) {
      if (chains[p]->name() == *o)
	return p;
    }
    ++o;
  }
  return p;
}

/**
 * Toggles chain muting of all selected chains.
 *
 * @pre is_locked() != true
 */
void ECA_CHAINSETUP::toggle_chain_muting(void)
{
  // ---
  DBC_REQUIRE(is_locked() != true);
  // ---

  for(vector<string>::const_iterator a = selected_chainids.begin(); a != selected_chainids.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	if ((*q)->is_muted()) 
	  (*q)->toggle_muting(false);
	else 
	  (*q)->toggle_muting(true);
      }
    }
  }
}

/**
 * Toggles chain bypass of all selected chains.
 *
 * @pre is_locked() != true
 */
void ECA_CHAINSETUP::toggle_chain_bypass(void)
{
  // ---
  DBC_REQUIRE(is_locked() != true);
  // ---

  for(vector<string>::const_iterator a = selected_chainids.begin(); a != selected_chainids.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	if ((*q)->is_processing()) 
	  (*q)->toggle_processing(false);
	else 
	  (*q)->toggle_processing(true);
      }
    }
  }
}

const ECA_CHAINSETUP_BUFPARAMS& ECA_CHAINSETUP::active_buffering_parameters(void) const 
{
  return impl_repp->bmode_active_rep;
}

const ECA_CHAINSETUP_BUFPARAMS& ECA_CHAINSETUP::override_buffering_parameters(void) const 
{
  return impl_repp->bmode_override_rep;
}

vector<string> ECA_CHAINSETUP::chain_names(void) const
{
  vector<string> result;
  vector<CHAIN*>::const_iterator p = chains.begin();
  while(p != chains.end()) {
    result.push_back((*p)->name());
    ++p;
  }
  return result;
}

vector<string> ECA_CHAINSETUP::audio_input_names(void) const
{
  vector<string> result;
  vector<AUDIO_IO*>::const_iterator p = inputs.begin();
  while(p != inputs.end()) {
    result.push_back((*p)->label());
    ++p;
  }
  return result;
}

vector<string> ECA_CHAINSETUP::audio_output_names(void) const
{
  vector<string> result;
  vector<AUDIO_IO*>::const_iterator p = outputs.begin();
  while(p != outputs.end()) {
    result.push_back((*p)->label());
    ++p;
  }
  return result;
}

vector<string> ECA_CHAINSETUP::get_attached_chains_to_input(AUDIO_IO* aiod) const 
{ 
  vector<string> res;
  
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    if (aiod == inputs[(*q)->connected_input()]) {
      res.push_back((*q)->name());
    }
    ++q;
  }
  
  return res; 
}

vector<string> ECA_CHAINSETUP::get_attached_chains_to_output(AUDIO_IO* aiod) const
{ 
  vector<string> res;
  
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    if (aiod == outputs[(*q)->connected_output()]) {
      res.push_back((*q)->name());
    }
    ++q;
  }

  return(res); 
}

int ECA_CHAINSETUP::number_of_attached_chains_to_input(AUDIO_IO* aiod) const
{
  int count = 0;
  
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    if (aiod == inputs[(*q)->connected_input()]) {
      ++count;
    }
    ++q;
  }

  return count; 
}

int ECA_CHAINSETUP::number_of_attached_chains_to_output(AUDIO_IO* aiod) const
{
  int count = 0;
  
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    if (aiod == outputs[(*q)->connected_output()]) {
      ++count;
    }
    ++q;
  }

  return count; 
}

/**
 * Output object is realtime target if it is not 
 * connected to any chains with non-realtime inputs.
 * In other words all data coming to a rt target
 * output comes from realtime devices.
 */
bool ECA_CHAINSETUP::is_realtime_target_output(int output_id) const
{
  bool result = true;
  bool output_found = false;
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    if ((*q)->connected_output() == output_id) {
      output_found = true;
      AUDIO_IO_DEVICE* p = dynamic_cast<AUDIO_IO_DEVICE*>(inputs[(*q)->connected_input()]);
      if (p == 0) {
	result = false;
      }
    }
    ++q;
  }
  if (output_found == true && result == true) 
    ECA_LOG_MSG(ECA_LOGGER::system_objects,"slave output detected: " + outputs[output_id]->label());
  else
    result = false;

  return result;
}

vector<string> ECA_CHAINSETUP::get_attached_chains_to_iodev(const string& filename) const
{
  vector<AUDIO_IO*>::size_type p;

  p = 0;
  while (p < inputs.size()) {
    if (inputs[p]->label() == filename)
      return get_attached_chains_to_input(inputs[p]);
    ++p;
  }

  p = 0;
  while (p < outputs.size()) {
    if (outputs[p]->label() == filename)
      return get_attached_chains_to_output(outputs[p]);
    ++p;
  }
  return vector<string> (0);
}

/**
 * Returns number of realtime audio input objects.
 */
int ECA_CHAINSETUP::number_of_chain_operators(void) const
{
  int cops = 0;
  vector<CHAIN*>::const_iterator q = chains.begin();
  while(q != chains.end()) {
    cops += (*q)->number_of_chain_operators();
    ++q;
  }
  return cops;
}

/**
 * Returns true if the connected chainsetup contains at least
 * one realtime audio input or output.
 */
bool ECA_CHAINSETUP::has_realtime_objects(void) const
{
  if (number_of_realtime_inputs() > 0 ||
      number_of_realtime_outputs() > 0) 
    return true;

  return false;
}

/**
 * Returns true if the connected chainsetup contains at least
 * one nonrealtime audio input or output.
 */
bool ECA_CHAINSETUP::has_nonrealtime_objects(void) const
{
  if (static_cast<int>(inputs_direct_rep.size() + outputs_direct_rep.size()) >
      number_of_realtime_inputs() + number_of_realtime_outputs())
    return true;
  
  return false;
}

/**
 * Returns a string containing currently active chainsetup
 * options and settings. Syntax is the same as used for
 * saved chainsetup files.
 */
string ECA_CHAINSETUP::options_to_string(void) const
{
  return cparser_rep.general_options_to_string();
}

/**
 * Returns number of realtime audio input objects.
 */
int ECA_CHAINSETUP::number_of_realtime_inputs(void) const
{
  int res = 0;
  for(size_t n = 0; n < inputs_direct_rep.size(); n++) {
    AUDIO_IO_DEVICE* p = dynamic_cast<AUDIO_IO_DEVICE*>(inputs_direct_rep[n]);
    if (p != 0) res++;
  }
  return res;
}

/**
 * Returns number of realtime audio output objects.
 */
int ECA_CHAINSETUP::number_of_realtime_outputs(void) const
{
  int res = 0;
  for(size_t n = 0; n < outputs_direct_rep.size(); n++) {
    AUDIO_IO_DEVICE* p = dynamic_cast<AUDIO_IO_DEVICE*>(outputs_direct_rep[n]);
    if (p != 0) res++;
  }
  return res;
}

/**
 * Returns number of non-realtime audio input objects.
 */
int ECA_CHAINSETUP::number_of_non_realtime_inputs(void) const
{
  return inputs.size() - number_of_realtime_inputs();
}

/**
 * Returns number of non-realtime audio input objects.
 */
int ECA_CHAINSETUP::number_of_non_realtime_outputs(void) const
{
  return outputs.size() - number_of_realtime_outputs();
}

/**
 * Returns a pointer to the manager handling audio object 'aobj'.
 *
 * @return 0 if 'aobj' is not handled by any manager
 */
AUDIO_IO_MANAGER* ECA_CHAINSETUP::get_audio_object_manager(AUDIO_IO* aio) const
{
  for(vector<AUDIO_IO_MANAGER*>::const_iterator q = aio_managers_rep.begin(); q != aio_managers_rep.end(); q++) {
    if ((*q)->is_managed_type(aio) && 
	(*q)->get_object_id(aio) != -1) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		    "Found object manager \"" +
		    (*q)->name() + 
		    "\" for aio \"" +
		    aio->label() + "\".");
      
      return *q;
    }
  }
  return 0;
}

/**
 * Returns a pointer to the manager handling audio 
 * objects of type 'aobj'.
 *
 * @return 0 if 'aobj' type is not handled by any manager
 */
AUDIO_IO_MANAGER* ECA_CHAINSETUP::get_audio_object_type_manager(AUDIO_IO* aio) const
{
  for(vector<AUDIO_IO_MANAGER*>::const_iterator q = aio_managers_rep.begin(); q != aio_managers_rep.end(); q++) {
    if ((*q)->is_managed_type(aio) == true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		    "Found object manager \"" +
		    (*q)->name() + 
		    "\" for aio type \"" +
		    aio->name() + "\".");
      
      return *q;
    }
  }
  return 0;
}

/**
 * If 'amgr' implements the ECA_ENGINE_DRIVER interface, 
 * it is registered as the active driver.
 */
void ECA_CHAINSETUP::register_engine_driver(AUDIO_IO_MANAGER* amgr)
{
  ECA_ENGINE_DRIVER* driver = dynamic_cast<ECA_ENGINE_DRIVER*>(amgr);

  if (driver != 0) {
    engine_driver_repp = driver;
    ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		  "Registered audio i/o manager \"" +
		  amgr->name() +
		  "\" as the current engine driver.");
  }
}

/**
 * Registers audio object to a manager. If no managers are
 * available for object's type, and it can create one,
 * a new manager is created.
 */
void ECA_CHAINSETUP::register_audio_object_to_manager(AUDIO_IO* aio)
{
  AUDIO_IO_MANAGER* mgr = get_audio_object_type_manager(aio);
  if (mgr == 0) {
    mgr = aio->create_object_manager();
    if (mgr != 0) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		    "Creating object manager \"" +
		    mgr->name() + 
		    "\" for aio \"" +
		    aio->name() + "\".");
      aio_managers_rep.push_back(mgr);
      propagate_audio_io_manager_options();
      mgr->register_object(aio);

      /* in case manager is also a driver */
      register_engine_driver(mgr);
    }
  }
  else {
    mgr->register_object(aio);
  }
}

/**
 * Unregisters audio object from manager.
 */
void ECA_CHAINSETUP::unregister_audio_object_from_manager(AUDIO_IO* aio)
{
  AUDIO_IO_MANAGER* mgr = get_audio_object_manager(aio);
  if (mgr != 0) {
    int id = mgr->get_object_id(aio);
    if (id != -1) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		    "Unregistering object \"" +
		    aio->name() + 
		    "\" from manager \"" +
		    mgr->name() + "\".");
      mgr->unregister_object(id);
    }
  }
}

/**
 * Propagates to set manager options to all existing 
 * audio i/o manager objects.
 */
void ECA_CHAINSETUP::propagate_audio_io_manager_options(void)
{
  for(vector<AUDIO_IO_MANAGER*>::const_iterator q = aio_managers_rep.begin(); q != aio_managers_rep.end(); q++) {
    if (aio_manager_option_map_rep.find((*q)->name()) != 
	aio_manager_option_map_rep.end()) {
      
      const string& optstring = aio_manager_option_map_rep[(*q)->name()];
      int numparams = (*q)->number_of_params();
      for(int n = 0; n < numparams; n++) {
	(*q)->set_parameter(n + 1, kvu_get_argument_number(n + 1, optstring));
	ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		    "Manager \"" +
		    (*q)->name() + "\", " + 
		    kvu_numtostr(n + 1) + ". parameter set to \"" +
		    (*q)->get_parameter(n + 1) + "\".");
      }
    }      
  }
}

/** 
 * Helper function used by add_input() and add_output().
 * 
 * All audio object creates go through this function,
 * so this is good place to do global operations that
 * apply to both inputs and outputs.
 */
AUDIO_IO* ECA_CHAINSETUP::add_audio_object_helper(AUDIO_IO* aio)
{
  AUDIO_IO* retobj = aio;
  
  AUDIO_IO_DEVICE* p = dynamic_cast<AUDIO_IO_DEVICE*>(aio);
  LOOP_DEVICE* q = dynamic_cast<LOOP_DEVICE*>(aio);
  if (p == 0 && q == 0) {
    /* not a realtime or loop device */
    retobj = new AUDIO_IO_DB_CLIENT(&impl_repp->pserver_rep, aio, false);
    ++db_clients_rep;
  }
  return retobj;
}

/** 
 * Helper function used by remove_audio_object().
 */
void ECA_CHAINSETUP::remove_audio_object_proxy(AUDIO_IO* aio)
{
  AUDIO_IO_DB_CLIENT* p = dynamic_cast<AUDIO_IO_DB_CLIENT*>(aio);
  if (p != 0) {
    /* a proxied object */
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Delete proxy object " + aio->label() + ".");
    delete aio;
    --db_clients_rep;
  }
}

/** 
 * Helper function used bu remove_audio_object() to remove input 
 * and output loop devices.
 */
void ECA_CHAINSETUP::remove_audio_object_loop(const string& label, AUDIO_IO* aio, int dir)
{
  int rdir = (dir == cs_dir_input ? cs_dir_output : cs_dir_input);

  /* loop devices are registered simultaneously to both input
   * and output object vectors, so they have to be removed
   * from both, but deleted only once */

  remove_audio_object_impl(label, rdir, false);

  /* we also need to remove the loop device from 
   * the loop_map table */

  map<string,LOOP_DEVICE*>::iterator iter = loop_map.begin();
  while(iter != loop_map.end()) {
    if (iter->second == aio) {
      loop_map.erase(iter);
      break;
    }
    ++iter;
  }
}

/**
 * Adds a new input object and attaches it to selected chains.
 * 
 * If double-buffering is enabled (double_buffering() == true),
 * and the object in question is not a realtime object, it
 * is wrapped in a AUDIO_IO_DB_CLIENT object before 
 * inserted to the chainsetup. Otherwise object is added
 * as is. 
 * 
 * Ownership of the insert object is transfered to 
 * ECA_CHAINSETUP.
 *
 * @pre aiod != 0
 * @pre is_enabled() != true
 * @post inputs.size() == old(inputs.size() + 1
 */
void ECA_CHAINSETUP::add_input(AUDIO_IO* aio)
{
  // --------
  DBC_REQUIRE(aio != 0);
  DBC_REQUIRE(is_enabled() != true);
  DBC_DECLARE(size_t old_inputs_size = inputs.size());
  // --------

  aio->set_io_mode(AUDIO_IO::io_read);
  aio->set_audio_format(default_audio_format());
  aio->set_buffersize(buffersize());
  
  register_audio_object_to_manager(aio);
  AUDIO_IO* layerobj = add_audio_object_helper(aio);
  inputs.push_back(layerobj);
  inputs_direct_rep.push_back(aio);
  input_start_pos.push_back(0);
  attach_input_to_selected_chains(layerobj);

  // --------
  DBC_ENSURE(inputs.size() == old_inputs_size + 1);
  DBC_ENSURE(inputs.size() == inputs_direct_rep.size());
  // --------
}

/**
 * Add a new output object and attach it to selected chains.
 * 
 * If double-buffering is enabled (double_buffering() == true),
 * and the object in question is not a realtime object, it
 * is wrapped in a AUDIO_IO_DB_CLIENT object before 
 * inserted to the chainsetup. Otherwise object is added
 * as is. 
 * 
 * Ownership of the insert object is transfered to 
 * ECA_CHAINSETUP.
 *
 * @pre aiod != 0
 * @pre is_enabled() != true
 * @post outputs.size() == outputs_direct_rep.size()
 */
void ECA_CHAINSETUP::add_output(AUDIO_IO* aio, bool truncate)
{
  // --------
  DBC_REQUIRE(aio != 0);
  DBC_REQUIRE(is_enabled() != true);
  DBC_DECLARE(size_t old_outputs_size = outputs.size());
  // --------

  aio->set_audio_format(default_audio_format());
  aio->set_buffersize(buffersize());
  if (truncate == true) 
    aio->set_io_mode(AUDIO_IO::io_write);
  else
    aio->set_io_mode(AUDIO_IO::io_readwrite);

  register_audio_object_to_manager(aio);
  AUDIO_IO* layerobj = add_audio_object_helper(aio);
  outputs.push_back(layerobj);
  outputs_direct_rep.push_back(aio);
  output_start_pos.push_back(0);
  attach_output_to_selected_chains(layerobj);

  // ---
  DBC_ENSURE(outputs.size() == old_outputs_size + 1);
  DBC_ENSURE(outputs.size() == outputs_direct_rep.size());
  // ---
}
/**
 * Erases an element matching 'obj' from 'vec'. At most one element
 * is removed. The function does not delete the referred object, just
 * removes it from the vector.
 */ 
static void priv_erase_object(std::vector<AUDIO_IO*>* vec, const AUDIO_IO* obj)
{
  vector<AUDIO_IO*>::iterator p = vec->begin();
  while(p != vec->end()) {
    if (*p == obj) {
      vec->erase(p);
      break;
    }
    ++p;
  }
}

/**
 * Removes the labeled audio object from this chainsetup.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::remove_audio_object_impl(const string& label, int dir, bool destroy)
{
  // ---
  DBC_REQUIRE(is_enabled() != true);
  // ---

  vector<AUDIO_IO*> *objs = (dir == cs_dir_input ? &inputs : &outputs);
  vector<AUDIO_IO*> *objs_dir = (dir == cs_dir_input ? &inputs_direct_rep : &outputs_direct_rep);
  DBC_DECLARE(size_t oldsize = objs->size());
  AUDIO_IO *obj_to_remove = 0, *obj_dir_to_remove = NULL;
  int remove_index = -1;

  /* Notes
   *  - objs and objs_dir vectors are always of the same size
   *  - for non-proxied objects 'objs[n] == objs_dir[n]' for all n
   */

  for(size_t n = 0; n < objs->size(); n++) {
    if ((*objs)[n]->label() == label) {
      obj_to_remove = (*objs)[n];
      obj_dir_to_remove = (*objs_dir)[n];
      remove_index = static_cast<int>(n);
    }
  }

  if (obj_to_remove) {
    DBC_CHECK(remove_index >= 0);
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Removing object " + obj_to_remove->label() + ".");

    /* disconnect object from chains */
    vector<CHAIN*>::iterator q = chains.begin();
    while(q != chains.end()) {
      if (dir == cs_dir_input) {
	if ((*q)->connected_input() == remove_index) {
	  (*q)->disconnect_input();
	}
      }
      else {
	if ((*q)->connected_output() == remove_index) {
	  (*q)->disconnect_output();
	}
      }
      ++q;
    }

    /* unregister from manager (always the objs_dir object) */
    unregister_audio_object_from_manager((*objs_dir)[remove_index]);

    /* delete proxy object if any */ 
    if (obj_to_remove != obj_dir_to_remove) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Audio object proxied: " + obj_to_remove->label());
      remove_audio_object_proxy(obj_to_remove);
    }
   
    priv_erase_object(objs, obj_to_remove);
    priv_erase_object(objs_dir, obj_dir_to_remove);

    LOOP_DEVICE* loop_dev = dynamic_cast<LOOP_DEVICE*>(obj_dir_to_remove);
    if (loop_dev != 0 && destroy == true) {
      /* note: destroy must be true to limit recursion */
      remove_audio_object_loop(label, obj_dir_to_remove, dir);
    }

    /* finally actually delete the object */
    if (destroy == true) 
      delete obj_dir_to_remove;
  }

  // ---
  DBC_ENSURE(objs->size() == objs_dir->size());
  DBC_ENSURE(oldsize == objs->size() + 1);
  // ---
}

/**
 * Removes the labeled audio input from this chainsetup.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::remove_audio_input(const string& label)
{
  // ---
  DBC_REQUIRE(is_enabled() != true);
  DBC_DECLARE(size_t oldsize = inputs.size());
  // ---

  remove_audio_object_impl(label, cs_dir_input, true);

  // ---
  DBC_ENSURE(inputs.size() == inputs_direct_rep.size());
  DBC_ENSURE(oldsize == inputs.size() + 1);
  // ---
}

/**
 * Removes the labeled audio output from this chainsetup.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::remove_audio_output(const string& label)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  DBC_DECLARE(size_t oldsize = outputs.size());
  // --------

  remove_audio_object_impl(label, cs_dir_output, true);

  // ---
  DBC_ENSURE(outputs.size() == outputs_direct_rep.size());
  DBC_ENSURE(oldsize == outputs.size() + 1);
  // ---
}

/**
 * Print trace messages when opening audio file 'aio'.
 *
 * @pre aio != 0
 */
void ECA_CHAINSETUP::audio_object_open_info(const AUDIO_IO* aio)
{
  // --------
  DBC_REQUIRE(aio != 0);
  // --------

  string temp = "Opened ";
  
  temp += (aio->io_mode() == AUDIO_IO::io_read) ? "input" : "output";
  temp += " \"" + aio->label();
  temp += "\", mode \"";
  if (aio->io_mode() == AUDIO_IO::io_read) temp += "read";
  if (aio->io_mode() == AUDIO_IO::io_write) temp += "write";
  if (aio->io_mode() == AUDIO_IO::io_readwrite) temp += "read/write (update)";
  temp += "\". ";
  temp += aio->format_info();

  ECA_LOG_MSG(ECA_LOGGER::info, temp);
}


/**
 * Adds a new MIDI-device object.
 *
 * @pre mididev != 0
 * @pre is_enabled() != true
 * @post midi_devices.size() > 0
 */
void ECA_CHAINSETUP::add_midi_device(MIDI_IO* mididev)
{
  // --------
  DBC_REQUIRE(mididev != 0);
  DBC_REQUIRE(is_enabled() != true);
  // --------

  midi_devices.push_back(mididev);
  impl_repp->midi_server_rep.register_client(mididev);

  // --------
  DBC_ENSURE(midi_devices.size() > 0);
  // --------
}

/**
 * Remove an MIDI-device by the name 'mdev_name'.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::remove_midi_device(const string& mdev_name)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  for(vector<MIDI_IO*>::iterator q = midi_devices.begin(); q != midi_devices.end(); q++) {
    if (mdev_name == (*q)->label()) {
      delete *q;
      midi_devices.erase(q);
      break;
    }
  }
}

const CHAIN* ECA_CHAINSETUP::get_chain_with_name(const string& name) const
{
  vector<CHAIN*>::const_iterator p = chains.begin();
  while(p != chains.end()) {
    if ((*p)->name() == name) return(*p);
    ++p;
  }
  return 0;
}

/**
 * Returns a non-zero index for chain 'name'. If the chain
 * does not exist, -1 is returned.
 * 
 * The chain index can be used with ECA::chainsetup_edit_t 
 * items passed to ECA_CHAINSETUP::execute_edit().
 *
 * Note: Mapping of chain names to indices can change if any
 *       chains are either added or removed. If that happens,
 *       the indices need to be recalculated. 
 */
int ECA_CHAINSETUP::get_chain_index(const string& name) const
{
  int retval = -1;
  vector<CHAIN*>::const_iterator p = chains.begin();
  for(int n = 1; p != chains.end(); n++) {
    if ((*p)->name() == name) {
      retval = n;
      break;
    }
    ++p;
  }
  return retval;
}

/**
 * Attaches input 'obj' to all selected chains.
 *
 * @pre is_locked() != true
 */
void ECA_CHAINSETUP::attach_input_to_selected_chains(const AUDIO_IO* obj)
{
  // --------
  DBC_REQUIRE(obj != 0);
  DBC_REQUIRE(is_locked() != true);
  // --------

  string temp;
  vector<AUDIO_IO*>::size_type c = 0;

  while (c < inputs.size()) {
    if (inputs[c] == obj) {
      for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
	if ((*q)->connected_input() == static_cast<int>(c)) {
	  (*q)->disconnect_input();
	}
      }
      temp += "Assigning file to chains:";
      for(vector<string>::const_iterator p = selected_chainids.begin(); p!= selected_chainids.end(); p++) {
	for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
	  if (*p == (*q)->name()) {
	    (*q)->connect_input(c);
	    temp += " " + *p;
	  }
	}
      }
    }
    ++c;
  }
  ECA_LOG_MSG(ECA_LOGGER::system_objects, temp);
}

/**
 * Attaches output 'obj' to all selected chains.
 *
 * @pre is_locked() != true
 */
void ECA_CHAINSETUP::attach_output_to_selected_chains(const AUDIO_IO* obj)
{
  // --------
  DBC_REQUIRE(obj != 0);
  DBC_REQUIRE(is_locked() != true);
  // --------

  string temp;
  vector<AUDIO_IO*>::size_type c = 0;
  while (c < outputs.size()) {
    if (outputs[c] == obj) {
      for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
	if ((*q)->connected_output() == static_cast<int>(c)) {
	  (*q)->disconnect_output();
	}
      }
      temp += "Assigning file to chains:";
      for(vector<string>::const_iterator p = selected_chainids.begin(); p!= selected_chainids.end(); p++) {
	for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
	  if (*p == (*q)->name()) {
	    (*q)->connect_output(static_cast<int>(c));
	    temp += " " + *p;
	  }
	}
      }
    }
    ++c;
  }
  ECA_LOG_MSG(ECA_LOGGER::system_objects, temp);
}

/**
 * Returns true if 'aobj' is a pointer to some input
 * or output object.
 */
bool ECA_CHAINSETUP::ok_audio_object(const AUDIO_IO* aobj) const
{
  if (ok_audio_object_helper(aobj, inputs) == true ||
      ok_audio_object_helper(aobj, outputs) == true ) return(true);

  return false;
  
}

bool ECA_CHAINSETUP::ok_audio_object_helper(const AUDIO_IO* aobj,
					    const vector<AUDIO_IO*>& aobjs)
{
  for(size_t n = 0; n < aobjs.size(); n++) {
    if (aobjs[n] == aobj) return(true);
  }
  return false;
}

void ECA_CHAINSETUP::check_object_samplerate(const AUDIO_IO* obj,
					     SAMPLE_SPECS::sample_rate_t srate) throw(ECA_ERROR&)
{
  if (obj->samples_per_second() != srate) {
    throw(ECA_ERROR("ECA-CHAINSETUP", 
		    string("All audio objects must have a common") +
		    " sampling rate; sampling rate of audio object \"" +
 		    obj->label() +
		    "\" differs from engine rate (" +
		    kvu_numtostr(obj->samples_per_second()) +
		    " <-> " + 
		    kvu_numtostr(srate) + 
		    "); unable to continue."));
  }
}

void ECA_CHAINSETUP::enable_audio_object_helper(AUDIO_IO* aobj) const 
{
  aobj->set_buffersize(buffersize());
  AUDIO_IO_DEVICE* dev = dynamic_cast<AUDIO_IO_DEVICE*>(aobj);
  if (dev != 0) {
    dev->toggle_max_buffers(max_buffers());
    dev->toggle_ignore_xruns(ignore_xruns());
  }
  if (aobj->is_open() == false) {
    const std::string req_format = ECA_OBJECT_FACTORY::audio_object_format_to_eos(aobj);
    aobj->open();
    const std::string act_format =
      ECA_OBJECT_FACTORY::audio_object_format_to_eos(aobj);
    if (act_format != req_format) {
      DBC_CHECK(aobj->locked_audio_format() == true);
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "NOTE: using existing audio parameters " + act_format +
		  " for object '" + aobj->label() + " (tried to open with " +
		  req_format + ").");
    }
  }
  if (aobj->is_open() == true) {
    aobj->seek_position_in_samples(aobj->position_in_samples());
    audio_object_open_info(aobj);
  }
}

/**
 * Enable chainsetup. Opens all devices and reinitializes all 
 * chain operators if necessary.
 *
 * This action is performed before connecting the chainsetup
 * to a engine object (for instance ECA_ENGINE). 
 * 
 * @pre is_locked() != true
 * @post is_enabled() == true
 */
void ECA_CHAINSETUP::enable(void) throw(ECA_ERROR&)
{
  // --------
  DBC_REQUIRE(is_locked() != true);
  // --------

  try {
    if (is_enabled_rep != true) {

      /* 1. check that current buffersize is supported by all devices */
      long int locked_bsize = check_for_locked_buffersize();
      if (locked_bsize != -1) {
	set_buffersize(locked_bsize);
      }

      /* 2. select and enable buffering parameters */
      select_active_buffering_mode();
      enable_active_buffering_mode();

      /* 3.1 open input devices */
      for(vector<AUDIO_IO*>::iterator q = inputs.begin(); q != inputs.end(); q++) {
	enable_audio_object_helper(*q);
	if ((*q)->is_open() != true) { 
	  throw(ECA_ERROR("ECA-CHAINSETUP", "Open failed without explicit exception!"));
	}
      }

      /* 3.2. make sure that all input devices have a common 
       *      sampling rate */
      SAMPLE_SPECS::sample_rate_t first_locked_srate = 0;
      for(vector<AUDIO_IO*>::iterator q = inputs.begin(); q != inputs.end(); q++) {
	if (first_locked_srate == 0) {
	  if ((*q)->locked_audio_format() == true) {
	    first_locked_srate = (*q)->samples_per_second();

	    /* set chainsetup sampling rate to 'first_srate'. */
	    set_samples_per_second(first_locked_srate);
	  }
	}
	else {
	  check_object_samplerate(*q, first_locked_srate);
	}
      }

      /* 4. open output devices */
      for(vector<AUDIO_IO*>::iterator q = outputs.begin(); q != outputs.end(); q++) {
	enable_audio_object_helper(*q);
	if ((*q)->is_open() != true) { 
	  throw(ECA_ERROR("ECA-CHAINSETUP", "Open failed without explicit exception!"));
	}
	if (first_locked_srate == 0) {
	  if ((*q)->locked_audio_format() == true) {
	    first_locked_srate = (*q)->samples_per_second();

	    /* set chainsetup sampling rate to 'first_srate'. */
	    set_samples_per_second(first_locked_srate);
	  }
	}
	else {
	  check_object_samplerate(*q, first_locked_srate);
	}
      }

      /* 5. in case there were no objects with locked srates */
      if (first_locked_srate == 0) {
	if (inputs.size() > 0) {
	  /* set chainsetup srate to that of the first input */
	  set_samples_per_second(inputs[0]->samples_per_second());
	}
      }

      /* 6. enable the MIDI server */
      if (impl_repp->midi_server_rep.is_enabled() != true &&
	  midi_devices.size() > 0) {
	impl_repp->midi_server_rep.set_schedrealtime(raised_priority());
	impl_repp->midi_server_rep.set_schedpriority(get_sched_priority());
	impl_repp->midi_server_rep.enable();
      }

      /* 7. enable all MIDI-devices */
      for(vector<MIDI_IO*>::iterator q = midi_devices.begin(); q != midi_devices.end(); q++) {
	(*q)->toggle_nonblocking_mode(true);
	if ((*q)->is_open() != true) {
	  (*q)->open();
	  if ((*q)->is_open() != true) {
	    throw(ECA_ERROR("ECA-CHAINSETUP", 
			    string("Unable to open MIDI-device: ") +
			    (*q)->label() +
			    "."));
	  }
	}
      }

      /* 8. calculate chainsetup length */
      calculate_processing_length();

    }
    is_enabled_rep = true;
  }
  catch(AUDIO_IO::SETUP_ERROR& e) {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"Connecting chainsetup failed, throwing an SETUP_ERROR exception.");
    throw(ECA_ERROR("ECA-CHAINSETUP", 
		    string("Enabling chainsetup: ")
		    + e.message()));
  }
  catch(...) { 
    ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"Connecting chainsetup failed, throwing a generic exception.");
    throw; 
  }

  // --------
  DBC_ENSURE(is_enabled() == true);
  // --------
}



/**
 * Disable chainsetup. Closes all devices. 
 * 
 * This action is performed before disconnecting the 
 * chainsetup from a engine object (for instance 
 * ECA_ENGINE). 
 * 
 * @pre is_locked() != true
 * @post is_enabled() != true
 */
void ECA_CHAINSETUP::disable(void)
{
  // --------
  DBC_REQUIRE(is_locked() != true);
  // --------

  /* calculate chainsetup length in case it has changed during processing */
  calculate_processing_length();

  if (is_enabled_rep == true) {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "Closing chainsetup \"" + name() + "\"");
    for(vector<AUDIO_IO*>::iterator q = inputs.begin(); q != inputs.end(); q++) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Closing audio device/file \"" + (*q)->label() + "\".");
      if ((*q)->is_open() == true) (*q)->close();
    }
    
    for(vector<AUDIO_IO*>::iterator q = outputs.begin(); q != outputs.end(); q++) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Closing audio device/file \"" + (*q)->label() + "\".");
      if ((*q)->is_open() == true) (*q)->close();
    }

    if (impl_repp->midi_server_rep.is_enabled() == true) impl_repp->midi_server_rep.disable();
    for(vector<MIDI_IO*>::iterator q = midi_devices.begin(); q != midi_devices.end(); q++) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Closing midi device \"" + (*q)->label() + "\".");
      if ((*q)->is_open() == true) (*q)->close();
    }

    is_enabled_rep = false;
  }

  // --------
  DBC_ENSURE(is_enabled() != true);
  // --------
}

/**
 * Executes chainsetup edit 'edit'.
 *
 * @return true if succesful, false if edit cannot
 *         be performed
 */
bool ECA_CHAINSETUP::execute_edit(const chainsetup_edit_t& edit)
{
  bool retval = true;

  ECA_LOG_MSG(ECA_LOGGER::user_objects,
	      "Executing edit type of " +
	      kvu_numtostr(static_cast<int>(edit.type)));

  if (edit.cs_ptr != this) {
    ECA_LOG_MSG(ECA_LOGGER::errors, 
		"ERROR: chainsetup edit executed on wrong object");
    return false;
  }

  switch(edit.type)
    {
    case edit_cop_set_param:
      {
	if (edit.m.cop_set_param.chain < 1 ||
	    edit.m.cop_set_param.chain > static_cast<int>(chains.size())) {
	  retval = false;
	  break;
	}
	CHAIN *ch = chains[edit.m.cop_set_param.chain - 1];
	ch->set_parameter(edit.m.cop_set_param.op, 
			  edit.m.cop_set_param.param,
			  edit.m.cop_set_param.value);
	break;
      }
    case edit_ctrl_set_param:
      {
	if (edit.m.ctrl_set_param.chain < 1 ||
	    edit.m.ctrl_set_param.chain > static_cast<int>(chains.size())) {
	  retval = false;
	  break;
	}
	CHAIN *ch = chains[edit.m.ctrl_set_param.chain - 1];
	ch->set_controller_parameter(edit.m.ctrl_set_param.op,
				     edit.m.ctrl_set_param.param,
				     edit.m.ctrl_set_param.value);
	break;
      }
    default:
      {
	DBC_NEVER_REACHED();
	retval = false;
	ECA_LOG_MSG(ECA_LOGGER::info,
		    "Unknown edit of type " +
		    kvu_numtostr(static_cast<int>(edit.type)));
	break;
      }
    }

  return retval;
}

/**
 * Updates the chainsetup processing length based on 
 * 1) requested length, 2) lengths of individual 
 * input objects, and 3) looping settings.
 */
void ECA_CHAINSETUP::calculate_processing_length(void)
{
  long int max_input_length = 0;
  for(unsigned int n = 0; n < inputs.size(); n++) {
    if (inputs[n]->length_in_samples() > max_input_length)
      max_input_length = inputs[n]->length_in_samples();
  }
  
  /* note! here we set the _actual_ length of the 
   *       chainsetup */
  set_length_in_samples(max_input_length);

  if (looping_enabled() == true) {
    if (max_length_set() != true &&
	max_input_length > 0) {
      /* looping but length not set */
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "Setting loop point to "
		   + kvu_numtostr(length_in_seconds_exact()) + ".");
      set_max_length_in_samples(max_input_length);
    }
  }
}

/**
 * Check whether the buffersize is locked to some 
 * specific value.
 *
 * @return -1 if not locked, otherwise the locked
 *         value
 */
long int ECA_CHAINSETUP::check_for_locked_buffersize(void) const
{
  long int result = -1;
#ifdef ECA_COMPILE_JACK
  int pid = getpid();
  string cname = "ecasound-ctrl-" + kvu_numtostr(pid);
  int jackobjs = 0;

  for(size_t n = 0; n < inputs_direct_rep.size(); n++) {
    if (inputs_direct_rep[n]->name() == "JACK interface") ++jackobjs;
  }
  for(size_t n = 0; n < outputs_direct_rep.size(); n++) {
    if (outputs_direct_rep[n]->name() == "JACK interface") ++jackobjs;
  }

  /* contact jackd only if there is at least one jack audio object 
   * present */
  if (jackobjs > 0) {
    jack_client_t *client = jack_client_new (cname.c_str());
    if (client != 0) {
      // xxx = static_cast<long int>(jack_get_sample_rate(client);
      result = static_cast<long int>(jack_get_buffer_size(client));
      
      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		"jackd buffersize check returned " +
		  kvu_numtostr(result) + ".");
      
      jack_client_close(client);
      
      client = 0;
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "unable to perform jackd buffersize check.");
    }
    
    DBC_CHECK(client == 0);
  }
#endif
  return result;
}

/**
 * Reimplemented from ECA_CHAINSETUP_POSITION.
 */
void ECA_CHAINSETUP::set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value)
{
  /* not necessarily a problem */
  DBC_CHECK(is_locked() != true);

  ECA_LOG_MSG(ECA_LOGGER::user_objects,
		"sample rate change, chainsetup " +
		name() +
		" to rate " + 
		kvu_numtostr(new_value) + ".");

  for(vector<AUDIO_IO*>::iterator q = inputs.begin(); q != inputs.end(); q++) {
    (*q)->set_samples_per_second(new_value);
  }
  
  for(vector<AUDIO_IO*>::iterator q = outputs.begin(); q != outputs.end(); q++) {
    (*q)->set_samples_per_second(new_value);
  }

  for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
    (*q)->set_samples_per_second(new_value);
  }
  
  ECA_CHAINSETUP_POSITION::set_samples_per_second(new_value);
}

static void priv_seek_position_helper(std::vector<AUDIO_IO*>* objs, SAMPLE_SPECS::sample_pos_t pos, const std::string& tag)
{
  for(vector<AUDIO_IO*>::iterator q = objs->begin(); q != objs->end(); q++) {
    /* note: don't try to seek real-time devices (only
     *       allowed exception, try seeking all other
     *       objects */
    if (dynamic_cast<AUDIO_IO_DEVICE*>(*q) == 0) { 
      (*q)->seek_position_in_samples(pos);
      /* note: report if object claims it supports seeking, but
       *       in fact the seek failed */
      if ((*q)->supports_seeking() == true) {
	if (pos <= (*q)->length_in_samples() &&
	    (*q)->position_in_samples() != pos)
	  ECA_LOG_MSG(ECA_LOGGER::info,
		      "WARNING: sample accurate seek failed with " +
		      tag + " \"" + (*q)->name() + "\"");
      }
    }
  }
}

/**
 * Reimplemented from ECA_AUDIO_POSITION.
 */
SAMPLE_SPECS::sample_pos_t ECA_CHAINSETUP::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,
	      "seek position, chainsetup \"" +
	      name() +
	      "\" to pos in sec " + 
	      kvu_numtostr(pos) + ".");

  if (is_enabled() == true) {
    if (double_buffering() == true) pserver_repp->flush();
  }

  priv_seek_position_helper(&inputs, pos, "input");
  priv_seek_position_helper(&outputs, pos, "output");

  for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
    (*q)->seek_position_in_samples(pos);
    if ((*q)->position_in_samples() != pos)
      ECA_LOG_MSG(ECA_LOGGER::info,
		  "WARNING: sample accurate seek failed with chainop \"" +
		  (*q)->name() + "\"");
    
  }

  return pos;
}

/**
 * Interprets one option. This is the most generic variant of
 * the interpretation routines; both global and object specific
 * options are handled.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre is_enabled() != true
 * 
 * @post (option succesfully interpreted && interpret_result() ==  true) ||
 *       (unknown or invalid option && interpret_result() != true)
 */
void ECA_CHAINSETUP::interpret_option (const string& arg)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  cparser_rep.interpret_option(arg);
}

/**
 * Interprets one option. All non-global options are ignored. Global
 * options can be interpreted multiple times and in any order.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre is_enabled() != true
 * @post (option succesfully interpreted && interpretation_result() ==  true) ||
 *       (unknown or invalid option && interpretation_result() == false)
 */
void ECA_CHAINSETUP::interpret_global_option (const string& arg)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  cparser_rep.interpret_global_option(arg);
}

/**
 * Interprets one option. All options not directly related to 
 * ecasound objects are ignored.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre is_enabled() != true
 * 
 * @post (option succesfully interpreted && interpretation_result() ==  true) ||
 *       (unknown or invalid option && interpretation_result() == false)
 */
void ECA_CHAINSETUP::interpret_object_option (const string& arg)
{
  // --------
  // FIXME: this requirement is broken by eca-control.h (for 
  //        adding effects on-the-fly, just stopping the engine)
  // DBC_REQUIRE(is_enabled() != true);
  // --------

  cparser_rep.interpret_object_option(arg);
}

/**
 * Interpret a vector of options.
 *
 * If any invalid options are passed us argument, 
 * interpret_result() will be 'false', and 
 * interpret_result_verbose() contains more detailed 
 * error description.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::interpret_options(const vector<string>& opts)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  cparser_rep.interpret_options(opts);
}

void ECA_CHAINSETUP::set_buffersize(long int value)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "overriding buffersize.");
  impl_repp->bmode_override_rep.set_buffersize(value); 
}

void ECA_CHAINSETUP::toggle_raised_priority(bool value) { 
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "overriding raised priority.");
  impl_repp->bmode_override_rep.toggle_raised_priority(value); 
}

void ECA_CHAINSETUP::set_sched_priority(int value)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "sched_priority.");
  impl_repp->bmode_override_rep.set_sched_priority(value); 
}

void ECA_CHAINSETUP::toggle_double_buffering(bool value)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "overriding doublebuffering.");
  impl_repp->bmode_override_rep.toggle_double_buffering(value); 
}

void ECA_CHAINSETUP::set_double_buffer_size(long int v)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "overriding db-size.");
  impl_repp->bmode_override_rep.set_double_buffer_size(v); 
}

void ECA_CHAINSETUP::toggle_max_buffers(bool v)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "overriding max_buffers.");
  impl_repp->bmode_override_rep.toggle_max_buffers(v); 
}

long int ECA_CHAINSETUP::buffersize(void) const
{
  if (impl_repp->bmode_override_rep.is_set_buffersize() == true)
    return impl_repp->bmode_override_rep.buffersize();
  
  return impl_repp->bmode_active_rep.buffersize(); 
}

bool ECA_CHAINSETUP::raised_priority(void) const
{
  if (impl_repp->bmode_override_rep.is_set_raised_priority() == true)
    return impl_repp->bmode_override_rep.raised_priority();

  return impl_repp->bmode_active_rep.raised_priority(); 
}

int ECA_CHAINSETUP::get_sched_priority(void) const
{
  if (impl_repp->bmode_override_rep.is_set_sched_priority() == true)
    return impl_repp->bmode_override_rep.get_sched_priority();

  return impl_repp->bmode_active_rep.get_sched_priority(); 
}

bool ECA_CHAINSETUP::double_buffering(void) const { 
  if (impl_repp->bmode_override_rep.is_set_double_buffering() == true)
    return impl_repp->bmode_override_rep.double_buffering();

  return impl_repp->bmode_active_rep.double_buffering(); 
}

long int ECA_CHAINSETUP::double_buffer_size(void) const { 
  if (impl_repp->bmode_override_rep.is_set_double_buffer_size() == true)
    return impl_repp->bmode_override_rep.double_buffer_size();

  return impl_repp->bmode_active_rep.double_buffer_size(); 
}

bool ECA_CHAINSETUP::max_buffers(void) const { 
  if (impl_repp->bmode_override_rep.is_set_max_buffers() == true)
    return impl_repp->bmode_override_rep.max_buffers();

  return impl_repp->bmode_active_rep.max_buffers(); 
}

void ECA_CHAINSETUP::set_default_audio_format(ECA_AUDIO_FORMAT& value) { 
  impl_repp->default_audio_format_rep = value; 
}

const ECA_AUDIO_FORMAT& ECA_CHAINSETUP::default_audio_format(void) const
{ 
  return impl_repp->default_audio_format_rep; 
}

/**
 * Select controllers as targets for parameter control
 */
void ECA_CHAINSETUP::set_target_to_controller(void) {
  vector<string> schains = selected_chains();
  for(vector<string>::const_iterator a = schains.begin(); a != schains.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	(*q)->selected_controller_as_target();
	return;
      }
    }
  }
}

/**
 * Add general controller to selected chainop.
 *
 * @pre csrc != 0
 * @pre is_locked() != true
 * @pre selected_chains().size() == 1
 */
void ECA_CHAINSETUP::add_controller(GENERIC_CONTROLLER* csrc)
{
  // --------
  DBC_REQUIRE(csrc != 0);
  DBC_REQUIRE(is_locked() != true);
  DBC_REQUIRE(selected_chains().size() == 1);
  // --------

#ifndef ECA_DISABLE_EFFECTS
  AUDIO_STAMP_CLIENT* p = dynamic_cast<AUDIO_STAMP_CLIENT*>(csrc->source_pointer());
  if (p != 0) {
    p->register_server(&impl_repp->stamp_server_rep);
  }
#endif

  DBC_CHECK(buffersize() != 0);
  DBC_CHECK(samples_per_second() != 0);

  vector<string> schains = selected_chains();
  for(vector<string>::const_iterator a = schains.begin(); a != schains.end(); a++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*a == (*q)->name()) {
	if ((*q)->selected_target() == 0) return;
	(*q)->add_controller(csrc);
	return;
      }
    }
  }
}

/**
 * Add chain operator to selected chain.
 *
 * @pre cotmp != 0
 * @pre is_locked() != true
 * @pre selected_chains().size() == 1
 */
void ECA_CHAINSETUP::add_chain_operator(CHAIN_OPERATOR* cotmp)
{
  // --------
  DBC_REQUIRE(cotmp != 0);
  DBC_REQUIRE(is_locked() != true);
  DBC_REQUIRE(selected_chains().size() == 1);
  // --------
  
#ifndef ECA_DISABLE_EFFECTS
  AUDIO_STAMP* p = dynamic_cast<AUDIO_STAMP*>(cotmp);
  if (p != 0) {
    impl_repp->stamp_server_rep.register_stamp(p);
  }
#endif

  vector<string> schains = selected_chains();
  for(vector<string>::const_iterator p = schains.begin(); p != schains.end(); p++) {
    for(vector<CHAIN*>::iterator q = chains.begin(); q != chains.end(); q++) {
      if (*p == (*q)->name()) {
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "Adding chainop to chain " + (*q)->name() + ".");
	(*q)->add_chain_operator(cotmp);
	(*q)->selected_chain_operator_as_target();
	return;
      }
    }
  }
}

/**
 * If chainsetup has inputs, but no outputs, a default output is
 * added.
 * 
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::add_default_output(void)
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  if (inputs.size() > 0 && outputs.size() == 0) {

    // No -o[:] options specified; let's use the default output
    
    select_all_chains();
    interpret_object_option(string("-o:") + ECA_OBJECT_FACTORY::probe_default_output_device());
  }
}

/**
 * If chainsetup has objects that need MIDI services, 
 * but no MIDI-devices defined, a default MIDI-device is
 * added.
 * 
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::add_default_midi_device(void)
{
  if (midi_server_needed_rep == true &&
      midi_devices.size() == 0) {
    cparser_rep.interpret_object_option("-Md:" + default_midi_device());
  }
}

/**
 * Loads chainsetup options from file.
 *
 * @pre is_enabled() != true
 */
void ECA_CHAINSETUP::load_from_file(const string& filename,
				    vector<string>& opts) const throw(ECA_ERROR&) 
{
  // --------
  DBC_REQUIRE(is_enabled() != true);
  // --------

  std::ifstream fin (filename.c_str());
  if (!fin) throw(ECA_ERROR("ECA_CHAINSETUP", "Couldn't open setup read file: \"" + filename + "\".", ECA_ERROR::retry));

  vector<string> options;
  string temp;
  while(getline(fin,temp)) {
    if (temp.size() > 0 && temp[0] == '#') {
      continue;
    }
    // FIXME: we should add quoting when saving the chainsetup or
    // give on quoting altogether...
    vector<string> words = kvu_string_to_tokens_quoted(temp);
    for(unsigned int n = 0; n < words.size(); n++) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Adding \"" + words[n] + "\" to options (loaded from \"" + filename + "\".");
      options.push_back(words[n]);
    }
  }
  fin.close();

  opts = COMMAND_LINE::combine(options);
}

void ECA_CHAINSETUP::save(void) throw(ECA_ERROR&)
{ 
  if (setup_filename_rep.empty() == true)
    setup_filename_rep = setup_name_rep + ".ecs";
  save_to_file(setup_filename_rep);
}

void ECA_CHAINSETUP::save_to_file(const string& filename) throw(ECA_ERROR&)
{
  // make sure that all overrides are processed
  select_active_buffering_mode();

  std::ofstream fout (filename.c_str());
  if (!fout) {
    cerr << "Going to throw an exception...\n";
    throw(ECA_ERROR("ECA_CHAINSETUP", "Couldn't open setup save file: \"" +
  			filename + "\".", ECA_ERROR::retry));
  }
  else {
    fout << "# ecasound chainsetup file" << endl;
    fout << endl;

    fout << "# general " << endl;
    fout << cparser_rep.general_options_to_string() << endl;
    fout << endl;

    string tmpstr = cparser_rep.midi_to_string();
    if (tmpstr.size() > 0) {
      fout << "# MIDI " << endl;
      fout << tmpstr << endl;
      fout << endl;      
    }

    fout << "# audio inputs " << endl;
    fout << cparser_rep.inputs_to_string() << endl;
    fout << endl;

    fout << "# audio outputs " << endl;
    fout << cparser_rep.outputs_to_string() << endl;
    fout << endl;

    tmpstr = cparser_rep.chains_to_string();
    if (tmpstr.size() > 0) {
      fout << "# chain operators and controllers " << endl;
      fout << tmpstr << endl;
      fout << endl;      
    }

    fout.close();
    set_filename(filename);
  }
}
