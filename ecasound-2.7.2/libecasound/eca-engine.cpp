// ------------------------------------------------------------------------
// eca-engine.cpp: Main processing engine
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
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#include <iostream>
#include <string>
#include <vector>
#include <ctime>
#include <cmath>
#include <utility>

#include <assert.h>
#include <unistd.h>
#include <pthread.h>
#include <errno.h>
#include <sys/time.h> /* gettimeofday() */

#include <kvu_dbc.h>
#include <kvu_numtostr.h>
#include <kvu_procedure_timer.h>
#include <kvu_rtcaps.h>
#include <kvu_threads.h>

#include "samplebuffer.h"
#include "audioio.h"
#include "audioio-buffered.h"
#include "audioio-device.h"
#include "audioio-db-client.h"
#include "audioio-loop.h"
#include "audioio-barrier.h"
#include "audioio-mp3.h"
#include "midi-server.h"
#include "eca-chain.h"
#include "eca-chainop.h"
#include "eca-error.h"
#include "eca-logger.h"
#include "eca-chainsetup-edit.h"
#include "eca-engine.h"
#include "eca-engine_impl.h"

using std::cerr;
using std::endl;
using std::vector;

/**
 * Enable and disable features
 */

/* Profile callback execution */
// #define PROFILE_ENGINE

/**
 * Local macro definitions
 */

#ifdef PROFILE_ENGINE_EXECUTION
#define PROFILE_ENGINE_STATEMENT(x) (x)
#else
#define PROFILE_ENGINE_STATEMENT(x) ((void)0)
#endif

/**
 * Prototypes of static functions
 */

static void mix_to_outputs_divide_helper(const SAMPLE_BUFFER *from, SAMPLE_BUFFER *to, int divide_by, bool first_time);
static void mix_to_outputs_sum_helper(const SAMPLE_BUFFER *from, SAMPLE_BUFFER *to, bool first_time);

/**
 * Implementations of non-static functions
 */

/**********************************************************************
 * Driver implementation
 **********************************************************************/

int ECA_ENGINE_DEFAULT_DRIVER::exec(ECA_ENGINE* engine, ECA_CHAINSETUP* csetup)
{
  engine_repp = engine;

  exit_request_rep = false;
  engine->init_engine_state();

  while(true) {

    engine_repp->check_command_queue();

    /* case 1: external exit request */
    if (exit_request_rep == true) break;

    /* case 2: engine running, execute one loop iteration */
    if (engine_repp->status() == ECA_ENGINE::engine_status_running) {
      engine_repp->engine_iteration();
    }
    else {
      /* case 3a-i: engine finished and in batch mode -> exit */
      if (engine_repp->status() == ECA_ENGINE::engine_status_finished &&
	  engine_repp->batch_mode() == true) {
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "batch finished in exec, exit");
	break;
      }

      /* case 3a-ii: engine error occured -> exit */
      else if (engine_repp->status() == ECA_ENGINE::engine_status_error) {
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "engine error, exit");
	break;
      }

      /* case 3b: engine not running, wait for commands */
      engine_repp->wait_for_commands();
    }

    engine_repp->update_engine_state();
  }

  if (engine_repp->is_prepared() == true) engine_repp->stop_operation();

  return 0;
}

void ECA_ENGINE_DEFAULT_DRIVER::start(void)
{
  if (engine_repp->is_prepared() != true) engine_repp->prepare_operation();
  engine_repp->start_operation();
}

void ECA_ENGINE_DEFAULT_DRIVER::stop(void)
{
  if (engine_repp->is_prepared() == true) engine_repp->stop_operation();
}

void ECA_ENGINE_DEFAULT_DRIVER::exit(void)
{
  if (engine_repp->is_prepared() == true) engine_repp->stop_operation();
  exit_request_rep = true;

  /* no need to block here as the next 
   * function will be exec()
   */
}

/**********************************************************************
 * Engine implementation - Public functions
 **********************************************************************/

/**
 * Context help:
 *  J = originates from driver callback
 *  E = ----- " ------- engine thread (exec())
 *  C = ----- " ------- control thread (external)
 *
 *  X-level-Y -> Y = steps from originating functions
 */

/**
 * Class constructor. A pointer to an enabled 
 * ECA_CHAINSETUP object must be given as argument. 
 *
 * @pre csetup != 0
 * @pre csetup->is_enabled() == true
 * @post status() == ECA_ENGINE::engine_status_stopped
 */
ECA_ENGINE::ECA_ENGINE(ECA_CHAINSETUP* csetup) 
  : prepared_rep(false),
    running_rep(false),
    edit_lock_rep(false),
    finished_rep(false),
    outputs_finished_rep(0),
    driver_errors_rep(0),
    csetup_repp(csetup),
    mixslot_repp(0)
{
  // --
  DBC_REQUIRE(csetup != 0);
  DBC_REQUIRE(csetup->is_enabled() == true);
  // --

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "ECA_ENGINE constructor");

  csetup_repp->toggle_locked_state(true);

  impl_repp = new ECA_ENGINE_impl;
  mixslot_repp = new SAMPLE_BUFFER(buffersize(), 0);

  init_variables();
  init_connection_to_chainsetup();

  PROFILE_ENGINE_STATEMENT(init_profiling());

  csetup_repp->toggle_locked_state(false);

  // --
  DBC_ENSURE(status() == ECA_ENGINE::engine_status_stopped);
  // --
}

/**
 * Class destructor.
 */
ECA_ENGINE::~ECA_ENGINE(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "ECA_ENGINE destructor");

  if (csetup_repp != 0) {
    command(ECA_ENGINE::ep_exit, 0.0f);
    wait_for_exit(5);
    if (csetup_repp != 0) {
      cleanup();
    }
  }
  
  PROFILE_ENGINE_STATEMENT(dump_profile_info());

  if (driver_local == true) {
    delete driver_repp;
    driver_repp = 0;
  }

  for(size_t n = 0; n < cslots_rep.size(); n++) {
    delete cslots_rep[n];
  }

  delete mixslot_repp;
  delete impl_repp;

  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Engine exiting");
}

/**
 * Launches the engine. This function will block 
 * until processing is finished.
 *
 * Note that a exec() is a one-shot function.
 * It's not possible to call it multiple times.
 *
 * @param batch_mode if true, once engine is started 
 *                   it will continue processing until 
 *                   'status() == engine_status_finished' 
 *                   condition is reached and then exit;
 *                   if false, engine will react to 
 *                   commands until explicit 'exit' is
 *                   given
 *
 * @see command()
 * @see status()
 *
 * @pre is_valid() == true
 * @post status() == ECA_ENGINE::engine_status_notready
 * @post is_valid() != true
 */
int ECA_ENGINE::exec(bool batch_mode)
{
  // --
  DBC_REQUIRE(csetup_repp != 0);
  DBC_REQUIRE(csetup_repp->is_enabled() == true);
  // --

  int result = 0;

  csetup_repp->toggle_locked_state(true);

  batchmode_enabled_rep = batch_mode;

  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Engine - Driver start");

  int res = driver_repp->exec(this, csetup_repp);
  if (res < 0) {
    ++driver_errors_rep;
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"WARNING: Engine has raised an error! "
		"Possible causes: connection lost to system services, unable to adapt "
		"to changes in operating environment, etc.");
  }

  csetup_repp->toggle_locked_state(false);

  signal_exit();

  if (outputs_finished_rep > 0) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: An output object has raised an error! "
		"Possible causes: Out of disk space, permission denied, unable to launch external "
		"applications needed in processing, etc.");
  }

  DBC_CHECK(status() == ECA_ENGINE::engine_status_stopped ||
	    status() == ECA_ENGINE::engine_status_finished ||
	    status() == ECA_ENGINE::engine_status_error);

  if (status() == ECA_ENGINE::engine_status_error) {
    result = -1;
  }

  cleanup();

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"Engine state when finishing: " + 
		kvu_numtostr(static_cast<int>(status())));

  // --
  DBC_ENSURE(status() == ECA_ENGINE::engine_status_notready);
  // --

  return result;
}

/**
 * Sends 'cmd' to engines command queue. Commands are 
 * processed in the server's main loop. 
 *
 * context: C-level-0
 *          must no be called from exec() context
 */
void ECA_ENGINE::command(Engine_command_t cmd, double arg)
{
  ECA_ENGINE::complex_command_t item;
  item.type = cmd;
  item.m.legacy.value = arg;
  impl_repp->command_queue_rep.push_back(item);
}

/**
 * Sends 'ccmd' to engines command queue. Commands are 
 * processed in the server's main loop. Passing a complex
 * command allows to address objects regardless of
 * the state of ECA_CHAINSETUP iterators (i.e. currently
 * selected objects).
 *
 * context: C-level-0
 *          must no be called from exec() context
 */
void ECA_ENGINE::command(complex_command_t ccmd)
{
  impl_repp->command_queue_rep.push_back(ccmd);
}

/**
 * Wait for a stop signal. Functions blocks until 
 * the signal is received or 'timeout' seconds
 * has elapsed.
 * 
 * context: C-level-0
 *          must not be run from the engine 
 *          driver context
 *
 * @see signal_stop()
 */
void ECA_ENGINE::wait_for_stop(int timeout)
{
  int ret = kvu_pthread_timed_wait(&impl_repp->ecasound_stop_mutex_repp, 
				   &impl_repp->ecasound_stop_cond_repp, 
				   timeout);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      kvu_pthread_timed_wait_result(ret, "wait_for_stop"));
}

/**
 * Wait for an exit signal. Function blocks until 
 * the signal is received or 'timeout' seconds
 * has elapsed.
 * 
 * context: C-level-0
 *
 * @see signal_exit()
 */
void ECA_ENGINE::wait_for_exit(int timeout)
{

  int ret = kvu_pthread_timed_wait(&impl_repp->ecasound_exit_mutex_repp, 
				   &impl_repp->ecasound_exit_cond_repp, 
				   timeout);
  ECA_LOG_MSG(ECA_LOGGER::info, 
	      kvu_pthread_timed_wait_result(ret, "(eca_main) wait_for_exit"));
}

/**********************************************************************
 * Engine implementation - Public functions for observing engine 
 *                         status information
 **********************************************************************/

/**
 * Returns true engine's internal 
 * state is valid for processing.
 *
 * context: C-level-0
 *          no limitations
 */
bool ECA_ENGINE::is_valid(void) const
{
  if (csetup_repp == 0 ||
      csetup_repp->is_enabled() != true ||
      csetup_repp->is_valid() != true ||
      chains_repp == 0 ||
      chains_repp->size() == 0 ||
      inputs_repp == 0 ||
      inputs_repp->size() == 0 ||
      outputs_repp == 0 ||
      outputs_repp->size() == 0) {

    return false;
  }
  
  return true;
}

/**
 * Whether current setup has finite length.
 *
 * context: C-level-0
 */
bool ECA_ENGINE::is_finite_length(void) const
{
  DBC_CHECK(csetup_repp != 0);

  if (csetup_repp->max_length_set() == true ||
      csetup_repp->number_of_realtime_inputs() == 0) {
    return true;
  }

  return false;
}

/**
 * Returns engine's current status.
 *
 * context: C-level-0
 *          no limitations
 */
ECA_ENGINE::Engine_status_t ECA_ENGINE::status(void) const
{ 
  if (csetup_repp == 0) 
    return ECA_ENGINE::engine_status_notready;

  /* calculated in update_engine_status() */
  if (finished_rep == true)
    return ECA_ENGINE::engine_status_finished;
  
  if (outputs_finished_rep > 0 ||
      driver_errors_rep > 0) 
    return ECA_ENGINE::engine_status_error;

  if (is_running() == true) 
    return ECA_ENGINE::engine_status_running;

  if (is_prepared() == true) 
    return ECA_ENGINE::engine_status_stopped;

  return ECA_ENGINE::engine_status_stopped;
}

/**********************************************************************
 * Engine implementation - API for engine driver objects
 **********************************************************************/

/**
 * Processes available new commands. If no
 * messages are available, function will return
 * immediately without blocking.
 *
 * context: E-level-1
 *          can be run at the same time as engine_iteration(); 
 *          note! this is called with the engine lock held
 *          so no long operations allowed!
 */
void ECA_ENGINE::check_command_queue(void)
{
  while(impl_repp->command_queue_rep.is_empty() != true) {
    ECA_ENGINE::complex_command_t item;
    int popres = impl_repp->command_queue_rep.pop_front(&item);

    if (popres <= 0) {
      /* queue is empty or temporarily unavailable, unable to continue 
       * processing messages without blocking */
      break;
    }

    switch(item.type) 
      {
	// ---
	// Basic commands.
	// ---            
      case ep_exit:
	{
	  edit_lock_rep = true;
	  if (status() == engine_status_running || 
	      status() == engine_status_finished) request_stop();
	  impl_repp->command_queue_rep.clear();
	  ECA_LOG_MSG(ECA_LOGGER::system_objects,"ecasound_queue: exit!");
	  driver_repp->exit();
	  return;
	}

	// ---
	// Chain operators (stateless addressing)
	// ---
      case ep_exec_edit: {
	csetup_repp->execute_edit(item.m.cs);
	break;
      }

      case ep_prepare: { if (is_prepared() != true) prepare_operation(); break; }
      case ep_start: { if (status() != engine_status_running) request_start(); break; }
      case ep_stop: { if (status() == engine_status_running || 
			  status() == engine_status_finished) request_stop(); break; }
	
	// ---
	// Edit locks
	// ---            
      case ep_edit_lock: { edit_lock_rep = true; break; }
      case ep_edit_unlock: { edit_lock_rep = false; break; }
	
	// ---
	// Section/chain (en/dis)abling commands.
	// ---
      case ep_c_select: { csetup_repp->selected_chain_index_rep = static_cast<size_t>(item.m.legacy.value); break; }
      case ep_c_muting: { chain_muting(); break; }
      case ep_c_bypass: { chain_processing(); break; }

	// ---
	// Global position
	// ---
      case ep_rewind: { change_position(- item.m.legacy.value); break; }
      case ep_forward: { change_position(item.m.legacy.value); break; }
      case ep_setpos: { set_position(item.m.legacy.value); break; }
      case ep_setpos_samples: { set_position_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(item.m.legacy.value)); break; }
      case ep_setpos_live_samples: { set_position_samples_live(static_cast<SAMPLE_SPECS::sample_pos_t>(item.m.legacy.value)); break; }

      case ep_debug: break;

      } /* switch */
    
  }
}

/**
 * Waits for new commands to arrive. Function
 * will block until at least one new message
 * is available or until a timeout occurs.
 *
 * context: E-level-1
 *          can be run at the same time as 
 *          engine_iteration()
 */
void ECA_ENGINE::wait_for_commands(void)
{
  impl_repp->command_queue_rep.poll(5, 0);
}

/**
 * Intializes internal state variables.
 *
 * context: E-level-1
 *          must not be run at the same 
 *          time as engine_iteration()
 *
 * @see update_engine_state()
 */
void ECA_ENGINE::init_engine_state(void)
{
  finished_rep = false;
  inputs_not_finished_rep = 1; // for the 1st iteration
  outputs_finished_rep = 0;
  mixslot_repp->event_tag_set(SAMPLE_BUFFER::tag_end_of_stream, false);
  for(size_t n = 0; n < cslots_rep.size(); n++) {
    cslots_rep[n]->event_tag_set(SAMPLE_BUFFER::tag_end_of_stream, false);
  }
}

/**
 * Updates engine state to match current 
 * situation.
 *
 * context: J-level-0
 *          must not be run at the same 
 *          time as engine_iteration()
 *
 * @see init_engine_state()
 */
void ECA_ENGINE::update_engine_state(void)
{
  // --
  // Check whether all inputs have finished
  // (note: as update_engine_state() is 
  // guaranteed to not to be called at the same 
  // time as engine_iteration(), this is the 
  // only safe place to calculate finished state
  
  if (inputs_not_finished_rep == 0 && 
      outputs_finished_rep == 0 && 
      finished_rep != true) {
    if (is_running() == true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,"all inputs finished - stop");
      // FIXME: this is still wrong, command() is not fully rt-safe
      // we are not allowed to call request_stop here
      command(ECA_ENGINE::ep_stop, 0.0f);
    }

    state_change_to_finished();
  }
  
  // --
  // Check whether some output has raised an error

  if (status() == ECA_ENGINE::engine_status_error) {
    if (is_running() == true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,"output error - stop");
      // FIXME: this is still wrong, command() is not fully rt-safe
      // we are not allowed to call request_stop here
      command(ECA_ENGINE::ep_stop, 0.0f);
    }
  }
}

/**
 * Executes one engine loop iteration. It is critical
 * that this function is only called when engine is 
 * running.
 *
 * @pre is_running() == true
 *
 * context: J-level-0
 */
void ECA_ENGINE::engine_iteration(void)
{
  DBC_CHECK(is_running() == true);
  
  PROFILE_ENGINE_STATEMENT(impl_repp->looptimer_rep.start(); impl_repp->looptimer_range_rep.start());
  
  inputs_not_finished_rep = 0;
  prehandle_control_position();
  inputs_to_chains();
  process_chains();
  // FIXME: add support for sub-buffersize offsets
  if (preroll_samples_rep >= recording_offset_rep) {
    /* record material to non-real-time outputs */
    mix_to_outputs(false);
  }
  else {
    /* skip slave targets */
    mix_to_outputs(true);
    preroll_samples_rep += buffersize();
  }
  posthandle_control_position();
  
  PROFILE_ENGINE_STATEMENT(impl_repp->looptimer_rep.stop(); impl_repp->looptimer_range_rep.stop());
}

/**
 * Prepares engine for operation. Prepares all 
 * realtime devices and starts servers.
 * 
 * This function should be called by the
 * driver before it starts iterating the 
 * engine's main loop.
 *
 * context: E-level-1/3
 *          must not be run at the same time
 *          as engine_iteration()
 *
 * @pre is_running() != true
 * @pre is_prepared() != true
 * @post is_prepared() == true
 * @post status() == ECA_ENGINE::engine_status_running
 */
void ECA_ENGINE::prepare_operation(void)
{
  // ---
  DBC_REQUIRE(is_running() != true);
  DBC_REQUIRE(is_prepared() != true);
  // ---

  /* 1. acquire rt-lock for chainsetup and samplebuffers */
  csetup_repp->toggle_locked_state(true);

  for(size_t n = 0; n < cslots_rep.size(); n++) {
    cslots_rep[n]->set_rt_lock(true);
  }
  mixslot_repp->set_rt_lock(true);

  /* 2. reinitialize chains if necessary */
  for(size_t i = 0; i != chains_repp->size(); i++) {
    if ((*chains_repp)[i]->is_initialized() != true) (*chains_repp)[i]->init(0, 0, 0);
  }

  /* 3. start subsystem servers and forked audio objects */
  start_forked_objects();
  start_servers();

  /* 4. prepare rt objects */
  prepare_realtime_objects();

  /* ... initial offset is needed because preroll is 
   * incremented only after checking whether we are 
   * still in preroll mode */
  preroll_samples_rep = buffersize(); 

  /* 6. enable rt-scheduling */
  if (csetup_repp->raised_priority() == true) {
    if (kvu_set_thread_scheduling(SCHED_FIFO, csetup_repp->get_sched_priority()) != 0)
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Unable to change scheduling policy!");
    else
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  std::string("Using realtime-scheduling (SCHED_FIFO:")
		  + kvu_numtostr(csetup_repp->get_sched_priority()) + ").");
  }

  /* 7. change engine to active and running */
  prepared_rep = true;
  init_engine_state();

  // ---
  DBC_ENSURE(is_prepared() == true);
  DBC_ENSURE(status() == ECA_ENGINE::engine_status_stopped);
  // ---
}

/**
 * Starts engine operation.
 * 
 * This function should be called by the
 * driver just before it starts iterating the 
 * engine's main loop.
 *
 * context: E-level-1/3
 *          must not be run at the same time
 *          as engine_iteration()
 *
 * @pre is_prepared() == true
 * @pre is_running() != true
 * @post is_running() == true
 * @post status() == ECA_ENGINE::engine_status_running
 */
void ECA_ENGINE::start_operation(void)
{
  // ---
  DBC_REQUIRE(is_prepared() == true);
  DBC_REQUIRE(is_running() != true);
  // ---

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "starting engine operation!");

  start_realtime_objects();
  running_rep = true;

  // ---
  DBC_ENSURE(is_running() == true);
  DBC_ENSURE(status() == ECA_ENGINE::engine_status_running);
  // ---
}

/**
 * Stops all realtime devices and servers.
 * 
 * This function should be called by the
 * driver when it stops iterating the 
 * engine's main loop.
 *
 * context: E-level-1/3
 *          must not be run at the same time
 *          as engine_iteration()
 *
 * @pre is_running() == true
 * @post is_running() != true
 * @post is_prepared() != true
 */
void ECA_ENGINE::stop_operation(void)
{
  // ---
  DBC_REQUIRE(is_prepared() == true);
  // ---

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "stopping engine operation!");

  running_rep = false;

  /* stop realtime devices */
  for (unsigned int adev_sizet = 0; adev_sizet != realtime_objects_rep.size(); adev_sizet++) {
    if (realtime_objects_rep[adev_sizet]->is_running() == true) realtime_objects_rep[adev_sizet]->stop();
  }

  prepared_rep = false;

  /* release samplebuffer rt-locks */
  for(size_t n = 0; n < cslots_rep.size(); n++) {
    cslots_rep[n]->set_rt_lock(false);
  }
  mixslot_repp->set_rt_lock(false);

  stop_servers();
  stop_forked_objects();

  /* lower priority back to normal */
  if (csetup_repp->raised_priority() == true) {
    if (kvu_set_thread_scheduling(SCHED_OTHER, 0) != 0)
      ECA_LOG_MSG(ECA_LOGGER::info, "Unable to change scheduling back to SCHED_OTHER!");
    else
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Changed back to non-realtime scheduling SCHED_OTHER.");
  }

  /* release chainsetup lock */
  csetup_repp->toggle_locked_state(false);

  /* signals wait_for_stop() that engine operation has stopped */
  signal_stop();

  // ---
  DBC_ENSURE(is_running() != true);
  DBC_ENSURE(is_prepared() != true);
  // ---
}

/**
 * Whether engine has been actived 
 * with prepare_operation().
 *
 * context: no limitations
 */
bool ECA_ENGINE::is_prepared(void) const
{
  return prepared_rep;
}

/**
 * Whether engine has been started
 * with start_operation().
 *
 * context: no limitations
 */
bool ECA_ENGINE::is_running(void) const
{
  return running_rep;
}

/**********************************************************************
 * Engine implementation - Attribute functions
 **********************************************************************/

long int ECA_ENGINE::buffersize(void) const
{
  DBC_CHECK(csetup_repp != 0);
  return csetup_repp->buffersize();
}

int ECA_ENGINE::max_channels(void) const
{
  int result = 0;
  for(unsigned int n = 0; n < csetup_repp->inputs.size(); n++) {
    if (csetup_repp->inputs[n]->channels() > result)
      result = csetup_repp->inputs[n]->channels();
  }
  for(unsigned int n = 0; n < csetup_repp->outputs.size(); n++) {
    if (csetup_repp->outputs[n]->channels() > result)
      result = csetup_repp->outputs[n]->channels();
  }
  return result;
}

/**********************************************************************
 * Engine implementation - Private functions for transport control
 **********************************************************************/

/**
 * Requests the engine driver to start operation.
 *
 * This function should only be called from 
 * check_command_queue().
 * 
 * @pre status() != engine_status_running
 *
 * context: E-level-2
 */
void ECA_ENGINE::request_start(void)
{
  // ---
  DBC_REQUIRE(status() != engine_status_running);
  // ---

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Request start");

  // --
  // start the driver
  driver_repp->start();
}

/**
 * Requests the engine driver to stop operation.
 *
 * This function should only be called from 
 * check_command_queue().
 *
 * @pre status() == ECA_ENGINE::engine_status_running ||
 *      status() == ECA_ENGINE::engine_status_finished
 *
 * context: E-level-2
 */
void ECA_ENGINE::request_stop(void)
{ 
  // ---
  DBC_REQUIRE(status() == engine_status_running ||
	      status() == engine_status_finished);
  // ---

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Request stop");

  driver_repp->stop();
}

/**
 * Sends a stop signal indicating that engine
 * state has changed to stopped.
 *
 * context: E-level-1/4 
 *
 * @see wait_for_stop()
 */
void ECA_ENGINE::signal_stop(void)
{
  pthread_mutex_lock(&impl_repp->ecasound_stop_mutex_repp);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Signaling stop");
  pthread_cond_broadcast(&impl_repp->ecasound_stop_cond_repp);
  pthread_mutex_unlock(&impl_repp->ecasound_stop_mutex_repp);
}

/**
 * Sends an exit signal indicating that engine
 * driver has exited.
 *
 * context: E-level-1/4 
 * 
 * @see wait_for_exit()
 */
void ECA_ENGINE::signal_exit(void)
{
  pthread_mutex_lock(&impl_repp->ecasound_exit_mutex_repp);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Signaling exit");
  pthread_cond_broadcast(&impl_repp->ecasound_exit_cond_repp);
  pthread_mutex_unlock(&impl_repp->ecasound_exit_mutex_repp);
}

/**
 * Processing is start If and only if processing 
 * previously stopped with conditional_stop().
 *
 * context: E-level-3
 */
void ECA_ENGINE::conditional_start(void)
{
  if (was_running_rep == true) {
    // don't call request_start(), as it would signal that we are 
    // starting from completely halted state
    if (is_prepared() != true) prepare_operation();
    start_operation();
  }
}

/**
 * Processing is stopped.
 *
 * context: E-level-3
 *
 * @see conditional_stop()
 * @see request_stop()
 */
void ECA_ENGINE::conditional_stop(void)
{
  if (status() == ECA_ENGINE::engine_status_running) {
    ECA_LOG_MSG(ECA_LOGGER::system_objects,"conditional stop");
    was_running_rep = true;
    // don't call request_stop(), as it would signal that we are 
    // stopping completely (JACK transport stop will be sent  to all)
    if (is_prepared() == true) stop_operation();
  }
  else was_running_rep = false;
}

void ECA_ENGINE::start_servers(void)
{
  if (csetup_repp->double_buffering() == true) {
    csetup_repp->pserver_repp->start();
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "prefilling i/o buffers.");
    csetup_repp->pserver_repp->wait_for_full();
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "i/o buffers prefilled.");
  }
  
  if (use_midi_rep == true) {
    csetup_repp->midi_server_repp->start();
  }
}

void ECA_ENGINE::stop_servers(void)
{ 
  if (csetup_repp->double_buffering() == true) {
    csetup_repp->pserver_repp->stop();
    csetup_repp->pserver_repp->wait_for_stop();
  }

  if (use_midi_rep == true) {
    csetup_repp->midi_server_repp->stop();
  }
}

/**
 * Goes through all input/outputs in 'vec', checks whether they 
 * implement the AUDIO_IO_BARRIER interface, and if yes, 
 * issues either 'start_io()' or 'stop_io()' on them.
 */
static void priv_toggle_forked_objects(bool start, std::vector<AUDIO_IO*>* vec)
{
  unsigned int n;
  for(n = 0; n < vec->size(); n++) {
    AUDIO_IO_BARRIER *barrier
      = dynamic_cast<AUDIO_IO_BARRIER*>((*vec)[n]);

    if (barrier) {
      if (start)
	barrier->start_io();
      else
	barrier->stop_io();
    }
  }
}

void ECA_ENGINE::start_forked_objects(void)
{
  priv_toggle_forked_objects(true, inputs_repp);
  priv_toggle_forked_objects(true, outputs_repp);
}

void ECA_ENGINE::stop_forked_objects(void)
{
  priv_toggle_forked_objects(false, inputs_repp);
  priv_toggle_forked_objects(false, outputs_repp);
}

void ECA_ENGINE::state_change_to_finished(void)
{
  if (finished_rep != true) {
    ECA_LOG_MSG_NOPREFIX(ECA_LOGGER::info, "");
    ECA_LOG_MSG(ECA_LOGGER::subsystems, "Engine - Processing finished");
  }
  finished_rep = true;
}

void ECA_ENGINE::prepare_realtime_objects(void)
{
  /* 1. prepare objects */
  for (unsigned int n = 0; n < realtime_objects_rep.size(); n++) {
    realtime_objects_rep[n]->prepare();
  }

  /* 2. prefill rt output objects with silence */
  mixslot_repp->make_silent();
  for (unsigned int n = 0; n < realtime_outputs_rep.size(); n++) {
    if (realtime_outputs_rep[n]->prefill_space() > 0) {
      if (realtime_outputs_rep[n]->prefill_space() < 
	  prefill_threshold_rep * buffersize()) {

	ECA_LOG_MSG(ECA_LOGGER::user_objects,
		    "audio output '" + 
		    realtime_outputs_rep[n]->name() + 
		    "' only offers " +
		    kvu_numtostr(realtime_outputs_rep[n]->prefill_space()) +
		    " frames of prefill space. Decreasing amount of prefill.");
	
	prefill_threshold_rep = realtime_outputs_rep[n]->prefill_space() / buffersize();
      }
      
      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "prefilling rt-outputs with " +
		  kvu_numtostr(prefill_threshold_rep) +
		  " blocks.");

      for (int m = 0; m < prefill_threshold_rep; m++) {
	realtime_outputs_rep[n]->write_buffer(mixslot_repp);
      }
    }
  }
}

void ECA_ENGINE::start_realtime_objects(void)
{
  /* 1. start all realtime devices */
  for (unsigned int n = 0; n < realtime_objects_rep.size(); n++)
    realtime_objects_rep[n]->start();
}

/**
 * Performs a close-open cycle for all realtime 
 * devices.
 */
void ECA_ENGINE::reset_realtime_devices(void)
{
  for (size_t n = 0; n < realtime_objects_rep.size(); n++) {
    if (realtime_objects_rep[n]->is_open() == true) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		    "Reseting rt-object " + 
		    realtime_objects_rep[n]->label());
      realtime_objects_rep[n]->close();
    }
  }
  for (size_t n = 0; n < realtime_objects_rep.size(); n++) {
    realtime_objects_rep[n]->open();
  }  
}

/**********************************************************************
 * Engine implementation - Private functions for observing and
 *                         modifying position
 **********************************************************************/

SAMPLE_SPECS::sample_pos_t ECA_ENGINE::current_position_in_samples(void) const
{
  return csetup_repp->position_in_samples();
}

double ECA_ENGINE::current_position_in_seconds_exact(void) const
{
  return csetup_repp->position_in_seconds_exact();
}

// FIXME: remove
#if 0
double ECA_ENGINE::current_position_chain(void) const
{
  AUDIO_IO* ptr = (*inputs_repp)[(*chains_repp)[csetup_repp->active_chain_index_rep]->connected_input()]; 
    return ptr->position_in_seconds_exact();
  return 0.0f;
}
#endif

/**
 * Seeks to position 'seconds'. Affects all input and 
 * outputs objects, and the chainsetup object position.
 *
 * context: E-level-2
 */
void ECA_ENGINE::set_position(double seconds)
{
  conditional_stop();
  csetup_repp->seek_position_in_seconds(seconds);
  // FIXME: calling init_engine_state() may lead to races
  init_engine_state();
  conditional_start();
}

/**
 * Seeks to position 'samples'. Affects all input and 
 * outputs objects, and the chainsetup object position.
 *
 * context: E-level-2
 */
void ECA_ENGINE::set_position_samples(SAMPLE_SPECS::sample_pos_t samples)
{
  conditional_stop();
  csetup_repp->seek_position_in_samples(samples);
  // FIXME: calling init_engine_state() may lead to races
  init_engine_state();
  conditional_start();
}

/**
 * Seeks to position 'samples' without stopping the engine.
 * Affects all input and outputs objects, and the chainsetup 
 * object position.
 *
 * context: E-level-2
 */
void ECA_ENGINE::set_position_samples_live(SAMPLE_SPECS::sample_pos_t samples)
{
  csetup_repp->seek_position_in_samples(samples);
  // FIXME: calling init_engine_state() may lead to races
  init_engine_state();
}

/**
 * Seeks to position 'current+seconds'. Affects all input and 
 * outputs objects, and the chainsetup object position.
 *
 * context: E-level-2
 */
void ECA_ENGINE::change_position(double seconds)
{
  double curpos = csetup_repp->position_in_seconds_exact();
  conditional_stop();
  csetup_repp->seek_position_in_seconds(curpos + seconds);
  conditional_start();
}

/**
 * Calculates how much data we need to process and sets the
 * buffersize accordingly for all non-real-time inputs.
 *
 * context: J-level-1
 */
void ECA_ENGINE::prehandle_control_position(void)
{
  csetup_repp->change_position_in_samples(buffersize());
  if (csetup_repp->max_length_set() == true &&
      csetup_repp->is_over_max_length() == true) {
    int buffer_remain = csetup_repp->position_in_samples() -
                        csetup_repp->max_length_in_samples();
    for(unsigned int adev_sizet = 0; adev_sizet < non_realtime_inputs_rep.size(); adev_sizet++) {
      non_realtime_inputs_rep[adev_sizet]->set_buffersize(buffer_remain);
    }
  }
}

/**
 * If we've processed all the data that was requested, stop or rewind. 
 * Also resets buffersize to its default value. If finished
 * state is reached, engine status is set to 
 * 'ECA_ENGINE::engine_status_finished'.
 */
void ECA_ENGINE::posthandle_control_position(void)
{
  if (csetup_repp->max_length_set() == true &&
      csetup_repp->is_over_max_length() == true) {
    if (csetup_repp->looping_enabled() == true) {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,"loop point reached");
      inputs_not_finished_rep = 1;
      csetup_repp->seek_position_in_samples(0);
      for(unsigned int adev_sizet = 0; adev_sizet < non_realtime_inputs_rep.size(); adev_sizet++) {
	non_realtime_inputs_rep[adev_sizet]->set_buffersize(buffersize());
      }
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::system_objects,"posthandle_c_p over_max - stop");
      if (status() == ECA_ENGINE::engine_status_running ||
	  status() == ECA_ENGINE::engine_status_finished) {
	command(ECA_ENGINE::ep_stop, 0.0f);
      }
      state_change_to_finished();
    }
  }
}

/**********************************************************************
 * Engine implementation - Private functions for setup and cleanup
 **********************************************************************/

/**
 * Called only from class constructor.
 */
void ECA_ENGINE::init_variables(void)
{
  use_midi_rep = false;
  batchmode_enabled_rep = false;
  driver_local = false;

  pthread_cond_init(&impl_repp->ecasound_stop_cond_repp, NULL);
  pthread_mutex_init(&impl_repp->ecasound_stop_mutex_repp, NULL);
  pthread_cond_init(&impl_repp->ecasound_exit_cond_repp, NULL);
  pthread_mutex_init(&impl_repp->ecasound_exit_mutex_repp, NULL);
}

/**
 * Called only from class constructor.
 */
void ECA_ENGINE::init_connection_to_chainsetup(void)
{
  inputs_repp = &(csetup_repp->inputs);
  outputs_repp = &(csetup_repp->outputs);
  chains_repp = &(csetup_repp->chains);

  init_engine_state();
  init_driver();
  init_prefill();
  init_servers();
  init_chains();
  create_cache_object_lists();
  update_cache_chain_connections();
  update_cache_latency_values();
}

/**
 * Initializes the engine driver object.
 */
void ECA_ENGINE::init_driver(void)
{
  if (csetup_repp->engine_driver_repp != 0) {
    driver_repp = csetup_repp->engine_driver_repp;
    driver_local = false;
  }
  else {
    driver_repp = new ECA_ENGINE_DEFAULT_DRIVER();
    driver_local = true;
  }
}

/** 
 * Initializes prefill variables.
 */
void ECA_ENGINE::init_prefill(void)
{
  int channels = (max_channels() > 0 ? max_channels() : 1);
  prefill_threshold_rep = 0;

  if (csetup_repp->max_buffers() == true) 
    prefill_threshold_rep = ECA_ENGINE::prefill_threshold_constant / buffersize() / channels;

  if (prefill_threshold_rep < ECA_ENGINE::prefill_blocks_constant)
    prefill_threshold_rep = ECA_ENGINE::prefill_blocks_constant;
  
  ECA_LOG_MSG(ECA_LOGGER::system_objects,
		"Prefill loops: " +
		kvu_numtostr(prefill_threshold_rep) +
		" (blocksize " + 
		kvu_numtostr(buffersize()) + ").");
}

/**
 * 
 * Called only from init_connection_to_chainsetup().
 */
void ECA_ENGINE::init_servers(void)
{
  if (csetup_repp->midi_devices.size() > 0) {
    use_midi_rep = true;
    ECA_LOG_MSG(ECA_LOGGER::info, "Initializing MIDI-server.");
    csetup_repp->midi_server_repp->init();
  }
}

/**
 * Called only from init_connection_to_chainsetup().
 */
void ECA_ENGINE::init_chains(void)
{
  mixslot_repp->number_of_channels(max_channels());
  mixslot_repp->event_tag_set(SAMPLE_BUFFER::tag_mixed_content);

  cslots_rep.resize(chains_repp->size());
  for(size_t n = 0; n < cslots_rep.size(); n++) {
    cslots_rep[n] = new SAMPLE_BUFFER(buffersize(), max_channels());
  }

  for (unsigned int c = 0; c != chains_repp->size(); c++) {
    int inch = (*inputs_repp)[(*chains_repp)[c]->connected_input()]->channels();
    int outch = (*outputs_repp)[(*chains_repp)[c]->connected_output()]->channels();
    (*chains_repp)[c]->init(cslots_rep[c], inch, outch);
  }
}

/**
 * Frees all reserved resources.
 *
 * @post status() == ECA_ENGINE::engine_status_notready
 * @post is_valid() != true
 */
void ECA_ENGINE::cleanup(void)
{
  if (csetup_repp != 0) {
    csetup_repp->toggle_locked_state(true);
    vector<CHAIN*>::iterator q = csetup_repp->chains.begin();
    while(q != csetup_repp->chains.end()) {
      if (*q != 0) {
	(*q)->disconnect_buffer();
      }
      ++q;
    }
    csetup_repp->toggle_locked_state(false);
  }

  csetup_repp = 0;

  // --
  DBC_ENSURE(status() == ECA_ENGINE::engine_status_notready);
  DBC_ENSURE(is_valid() != true);
  // --
}

/**
 * Updates 'input_chain_count_rep' and
 * 'output_chain_count_rep'.
 */
void ECA_ENGINE::update_cache_chain_connections(void)
{
  input_chain_count_rep.resize(inputs_repp->size());
  for(unsigned int n = 0; n < inputs_repp->size(); n++) {
    input_chain_count_rep[n] =
      csetup_repp->number_of_attached_chains_to_input(csetup_repp->inputs[n]);
  }
  
  output_chain_count_rep.resize(outputs_repp->size());
  for(unsigned int n = 0; n < outputs_repp->size(); n++) {
    output_chain_count_rep[n] =
      csetup_repp->number_of_attached_chains_to_output(csetup_repp->outputs[n]);
  }
}

/**
 * Update system latency values for multitrack
 * recording.
 */
void ECA_ENGINE::update_cache_latency_values(void)
{
  if (csetup_repp->multitrack_mode() == true &&
      csetup_repp->multitrack_mode_offset() == -1) {
    long int in_latency = -1;
    for(unsigned int n = 0; n < realtime_inputs_rep.size(); n++) {
      if (in_latency == -1) {
	in_latency = realtime_inputs_rep[n]->latency();
      }
      else {
	if (in_latency != realtime_inputs_rep[n]->latency()) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
			"WARNING: Latency mismatch between input objects!");
	}
      }

      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "Input latency for '" +
		  realtime_inputs_rep[n]->name() + 
		  "' is " + kvu_numtostr(in_latency) + ".");

    }
    
    long int out_latency = -1;
    for(unsigned int n = 0; n < realtime_outputs_rep.size(); n++) {
      if (out_latency == -1) {
	if (realtime_outputs_rep[n]->prefill_space() > 0) {
	  long int max_prefill = prefill_threshold_rep * buffersize();
	  if (max_prefill > realtime_outputs_rep[n]->prefill_space()) {
	    max_prefill = realtime_outputs_rep[n]->prefill_space();
	  }
	  out_latency = max_prefill + realtime_outputs_rep[n]->latency();
	}
	else
	  out_latency = realtime_outputs_rep[n]->latency();
      }
      else {
	if ((realtime_outputs_rep[n]->prefill_space() > 0 && 
	    out_latency != (prefill_threshold_rep * buffersize()) + realtime_outputs_rep[n]->latency()) || 
	    (realtime_outputs_rep[n]->prefill_space() == 0 &&
	    out_latency != realtime_outputs_rep[n]->latency())) {
	  ECA_LOG_MSG(ECA_LOGGER::info, 
			"WARNING: Latency mismatch between output objects!");
	}
      }

      ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "Output latency for '" +
		  realtime_outputs_rep[n]->name() + 
		  "' is " + kvu_numtostr(out_latency) + ".");
    }

    recording_offset_rep = (out_latency > in_latency ? 
			    out_latency : in_latency);
    
    if (recording_offset_rep % buffersize()) {
      ECA_LOG_MSG(ECA_LOGGER::info, 
		    "WARNING: Recording offset not divisible with chainsetup buffersize.");
    }
    
    ECA_LOG_MSG(ECA_LOGGER::user_objects,
		  "recording offset is " +
		  kvu_numtostr(recording_offset_rep) +
		  	" samples.");
  }
  else if (csetup_repp->multitrack_mode() == true) {
    /* multitrack_mode_offset() explicitly given (not -1) */
    recording_offset_rep = csetup_repp->multitrack_mode_offset();
  }
  else {
    recording_offset_rep = 0;
  }
}

/**
 * Assigns input and output objects in lists of realtime
 * and nonrealtime objects.
 */
void ECA_ENGINE::create_cache_object_lists(void)
{
  for(unsigned int n = 0; n < inputs_repp->size(); n++) {
    if (AUDIO_IO_DEVICE::is_realtime_object((*inputs_repp)[n]) == true) {
      realtime_inputs_rep.push_back(static_cast<AUDIO_IO_DEVICE*>((*inputs_repp)[n]));
      realtime_objects_rep.push_back(static_cast<AUDIO_IO_DEVICE*>((*inputs_repp)[n]));
    }
    else {
      non_realtime_inputs_rep.push_back((*inputs_repp)[n]);
      non_realtime_objects_rep.push_back((*inputs_repp)[n]);
    }
  }
  DBC_CHECK(static_cast<int>(realtime_inputs_rep.size()) == csetup_repp->number_of_realtime_inputs());

  for(unsigned int n = 0; n < outputs_repp->size(); n++) {
    if (AUDIO_IO_DEVICE::is_realtime_object((*outputs_repp)[n]) == true) {
      realtime_outputs_rep.push_back(static_cast<AUDIO_IO_DEVICE*>((*outputs_repp)[n]));
      realtime_objects_rep.push_back(static_cast<AUDIO_IO_DEVICE*>((*outputs_repp)[n]));
    }
    else {
      non_realtime_outputs_rep.push_back((*outputs_repp)[n]);
      non_realtime_objects_rep.push_back((*outputs_repp)[n]);
    }
  }
  DBC_CHECK(static_cast<int>(realtime_outputs_rep.size()) == csetup_repp->number_of_realtime_outputs());
}

/**
 * Called only from class constructor.
 */
void ECA_ENGINE::init_profiling(void)
{
  impl_repp->looptimer_low_rep = static_cast<double>(buffersize()) / csetup_repp->samples_per_second();
  impl_repp->looptimer_mid_rep = static_cast<double>(buffersize() * 2) / csetup_repp->samples_per_second();
  impl_repp->looptimer_high_rep = static_cast<double>(buffersize()) * prefill_threshold_rep / csetup_repp->samples_per_second();

  impl_repp->looptimer_rep.set_lower_bound_seconds(impl_repp->looptimer_low_rep);
  impl_repp->looptimer_rep.set_upper_bound_seconds(impl_repp->looptimer_high_rep);
  impl_repp->looptimer_range_rep.set_lower_bound_seconds(impl_repp->looptimer_mid_rep);
  impl_repp->looptimer_range_rep.set_upper_bound_seconds(impl_repp->looptimer_mid_rep);
}

/**
 * Prints  profiling information to stderr.
 */
void ECA_ENGINE::dump_profile_info(void)
{
  long int slower_than_rt = impl_repp->looptimer_rep.event_count() -
                            impl_repp->looptimer_rep.events_under_lower_bound() -
                            impl_repp->looptimer_rep.events_over_upper_bound();

  cerr << "*** profile begin ***" << endl;
  cerr << "Loops faster than realtime: "  << kvu_numtostr(impl_repp->looptimer_rep.events_under_lower_bound());
  cerr << " (<" << kvu_numtostr(impl_repp->looptimer_low_rep * 1000, 1) << " msec)" << endl;
  cerr << "Loops slower than realtime: "  << kvu_numtostr(slower_than_rt);
  cerr << " (>=" << kvu_numtostr(impl_repp->looptimer_low_rep * 1000, 1) << " msec)" << endl;
  cerr << "Loops slower than realtime: "  << kvu_numtostr(impl_repp->looptimer_range_rep.events_over_upper_bound());
  cerr << " (>" << kvu_numtostr(impl_repp->looptimer_mid_rep * 1000, 1) << " msec)" << endl;
  cerr << "Loops exceeding all buffering: " << kvu_numtostr(impl_repp->looptimer_rep.events_over_upper_bound());
  cerr << " (>" << kvu_numtostr(impl_repp->looptimer_high_rep * 1000, 1) << " msec)" << endl;
  cerr << "Total loops: " << kvu_numtostr(impl_repp->looptimer_rep.event_count()) << endl;
  cerr << "Fastest/slowest/average loop time: ";
  cerr << kvu_numtostr(impl_repp->looptimer_rep.min_duration_seconds() * 1000, 1);
  cerr << "/";
  cerr << kvu_numtostr(impl_repp->looptimer_rep.max_duration_seconds() * 1000, 1);
  cerr << "/";
  cerr << kvu_numtostr(impl_repp->looptimer_rep.average_duration_seconds() * 1000, 1);
  cerr << " msec." << endl;
  cerr << "*** profile end   ***" << endl;
}

/**********************************************************************
 * Engine implementation - Private functions for signal routing
 **********************************************************************/

/**
 * Reads audio data from input objects.
 *
 * context: J-level-1 (see 
 */
void ECA_ENGINE::inputs_to_chains(void)
{
  /**
   * - go through all inputs
   * - depending on connectivity, read either to a mixdown slot, or 
   *   directly to a per-chain slot
   */

  for(size_t inputnum = 0; inputnum < inputs_repp->size(); inputnum++) {

    if (input_chain_count_rep[inputnum] > 1) {
      /* case-1a: read buffer from input 'inputnum' to 'mixslot';
       *          later (1b) the data is copied to each per-chain slow
       *          to which input is connected to */

      mixslot_repp->length_in_samples(buffersize());

      if ((*inputs_repp)[inputnum]->finished() != true) {
	(*inputs_repp)[inputnum]->read_buffer(mixslot_repp);
	if ((*inputs_repp)[inputnum]->finished() != true) {
	  inputs_not_finished_rep++;
	}
      }
      else {
	/* note: no more input data for this change (N:1 input-chain case) */
	mixslot_repp->make_empty();
      }
    }
    for (size_t c = 0; c != chains_repp->size(); c++) {
      if ((*chains_repp)[c]->connected_input() == static_cast<int>(inputnum)) {
	if (input_chain_count_rep[inputnum] == 1) {
	  /* case-2: read buffer from input 'inputnum' to chain 'c' */
	  cslots_rep[c]->length_in_samples(buffersize());

	  if ((*inputs_repp)[inputnum]->finished() != true) {
	    (*inputs_repp)[inputnum]->read_buffer(cslots_rep[c]);
	    if ((*inputs_repp)[inputnum]->finished() != true) {
	      inputs_not_finished_rep++;
	    }
	  }
	  else {
	    /* note: no more input data for this change (1:1 input-chain case) */
	    cslots_rep[c]->make_empty();
	  }

	  /* note: input connected to only one chain, so no need to
	           iterate through the other chains */
	  break; 
	}
	else {
	  /* case-1b: input connected to chain 'n', copy 'mixslot' to 
	   *          the matching per-chain slot */
	  cslots_rep[c]->copy_all_content(*mixslot_repp);
	}
      }
    }
  }
}

/**
 * context: J-level-1
 */
void ECA_ENGINE::process_chains(void)
{
  vector<CHAIN*>::const_iterator p = chains_repp->begin();
  while(p != chains_repp->end()) {
    (*p)->process();
    ++p;
  }
}

void mix_to_outputs_divide_helper(const SAMPLE_BUFFER *from, SAMPLE_BUFFER *to, int divide_by, bool first_time)
{
  if (first_time == true) {
    // this is the first output connected to this chain
    if (from->number_of_channels() < to->number_of_channels()) {
      to->make_silent();
    }
    to->copy_matching_channels(*from);
    to->divide_by(divide_by);
  }
  else {
    to->add_with_weight(*from, divide_by);
  }
}

void mix_to_outputs_sum_helper(const SAMPLE_BUFFER *from, SAMPLE_BUFFER *to, bool first_time)
{
  if (first_time == true) {
    // this is the first output connected to this chain
    if (from->number_of_channels() < to->number_of_channels()) {
      to->make_silent();
    }
    to->copy_matching_channels(*from);
  }
  else {
    to->add_matching_channels(*from);
  }
}

/**
 * context: J-level-1
 */
void ECA_ENGINE::mix_to_outputs(bool skip_realtime_target_outputs)
{
  for(size_t outputnum = 0; outputnum < outputs_repp->size(); outputnum++) {
    if (skip_realtime_target_outputs == true) {
      if (csetup_repp->is_realtime_target_output(outputnum) == true) {
	ECA_LOG_MSG(ECA_LOGGER::system_objects,
		    "Skipping rt-target output " +
		    (*outputs_repp)[outputnum]->label() + ".");
	continue;
      }
    }

    int count = 0;

    /* FIXME: number_of_channels() may end up allocating memory! */
    mixslot_repp->number_of_channels((*outputs_repp)[outputnum]->channels());
    
    for(size_t n = 0; n != chains_repp->size(); n++) {
      // --
      // if chain is already released, skip
      // --
      if ((*chains_repp)[n]->connected_output() == -1) {
	// --
	// skip, if chain is not connected
	// --
	continue;
      }

      if ((*chains_repp)[n]->connected_output() == static_cast<int>(outputnum)) {
	// --
	// output is connected to this chain
	// --
	if (output_chain_count_rep[outputnum] == 1) {
	  // --
	  // there's only one output connected to this chain,
	  // so we don't need to mix anything
	  // --
	  (*outputs_repp)[outputnum]->write_buffer(cslots_rep[n]);
	  if ((*outputs_repp)[outputnum]->finished() == true) 
	    /* note: loop devices always connected both as inputs as
	     *       outputs, so their finished status must not be
	     *       counted as an error (like for other output types) */
	    if (dynamic_cast<LOOP_DEVICE*>((*outputs_repp)[outputnum]) == 0)
	      outputs_finished_rep++;
	  break;
	}
	else {
	  ++count;

	  if (csetup_repp->mix_mode() == ECA_CHAINSETUP::cs_mmode_avg) 	  
	    mix_to_outputs_divide_helper(cslots_rep[n], mixslot_repp, output_chain_count_rep[outputnum], (count == 1));
 	  else
	    mix_to_outputs_sum_helper(cslots_rep[n], mixslot_repp, (count == 1));

	  mixslot_repp->event_tags_add(*cslots_rep[n]);

	  if (count == output_chain_count_rep[outputnum]) {
	    (*outputs_repp)[outputnum]->write_buffer(mixslot_repp);
	    if ((*outputs_repp)[outputnum]->finished() == true) 
	      /* note: loop devices always connected both as inputs as
	       *       outputs, so their finished status must not be
	       *       counted as an error (like for other output types) */
	      if (dynamic_cast<LOOP_DEVICE*>((*outputs_repp)[outputnum]) == 0)
		outputs_finished_rep++;
	  }
	}
      }
    }
  } 
}

/**********************************************************************
 * Engine implementation - Private functions for toggling features
 **********************************************************************/

/**
 * context: E-level-2
 */
void ECA_ENGINE::chain_muting(void)
{
  if ((*chains_repp)[csetup_repp->selected_chain_index_rep]->is_muted()) 
    (*chains_repp)[csetup_repp->selected_chain_index_rep]->toggle_muting(false);
  else
    (*chains_repp)[csetup_repp->selected_chain_index_rep]->toggle_muting(true);
}

/**
 * context: E-level-2
 */
void ECA_ENGINE::chain_processing(void)
{
  if ((*chains_repp)[csetup_repp->selected_chain_index_rep]->is_processing()) 
    (*chains_repp)[csetup_repp->selected_chain_index_rep]->toggle_processing(false);
  else
    (*chains_repp)[csetup_repp->selected_chain_index_rep]->toggle_processing(true);
}

/**********************************************************************
 * Engine implementation - Obsolete functions
 **********************************************************************/
