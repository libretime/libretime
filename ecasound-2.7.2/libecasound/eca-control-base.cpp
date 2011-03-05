// ------------------------------------------------------------------------
// eca-control-base.cpp: Base class providing basic functionality
//                       for controlling the ecasound library
// Copyright (C) 1999-2004,2006,2008,2009 Kai Vehmanen
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

#include <string>
#include <vector>

#include <pthread.h>
#include <signal.h>
#include <unistd.h>

#include <kvu_dbc.h>
#include <kvu_utils.h>
#include <kvu_numtostr.h>
#include <kvu_message_item.h>
#include <kvu_value_queue.h>

#include "eca-engine.h"
#include "eca-session.h"
#include "eca-chainsetup.h"
#include "eca-resources.h"
#include "eca-control.h"

#include "eca-error.h"
#include "eca-logger.h"

/**
 * Import namespaces
 */
using std::list;
using std::string;
using std::vector;

/**
 * Definitions for member functions
 */

/**
 * Helper function for starting the slave thread.
 */
void* ECA_CONTROL::start_normal_thread(void *ptr)
{
  ECA_CONTROL* ctrl_base = static_cast<ECA_CONTROL*>(ptr);
  ctrl_base->engine_pid_rep = getpid();
  DBC_CHECK(ctrl_base->engine_pid_rep >= 0);

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Engine thread started with pid: " + kvu_numtostr(ctrl_base->engine_pid_rep));

  ctrl_base->run_engine();

  ECA_LOG_MSG(ECA_LOGGER::system_objects,  
	      "Engine thread " + kvu_numtostr(ctrl_base->engine_pid_rep) + " will exit.\n");
  ctrl_base->engine_pid_rep = -1;

  return 0;
}

/**
 * Initializes the engine
 *
 * @pre is_connected() == true
 * @pre is_engine_created() != true
 */
void ECA_CONTROL::engine_start(void)
{
  // --------
  DBC_REQUIRE(is_connected() == true);
  DBC_REQUIRE(is_engine_created() != true);
  // --------

  start_engine_sub(false);
}

/**
 * Start the processing engine
 *
 * @pre is_connected() == true
 * @pre is_running() != true
 * @post is_engine_created() == true
 *
 * @return negative on error, zero on success 
 */
int ECA_CONTROL::start(void)
{
  // --------
  DBC_REQUIRE(is_connected() == true);
  DBC_REQUIRE(is_running() != true);
  // --------
  
  int result = 0;

  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Controller/Processing started");

  if (is_engine_created() != true) {
    /* request_batchmode=false */
    start_engine_sub(false);
  }

  if (is_engine_created() != true) {
    ECA_LOG_MSG(ECA_LOGGER::info, "Can't start processing: couldn't start engine.");
    result = -1;
  }  
  else {
    engine_repp->command(ECA_ENGINE::ep_start, 0.0);
  }

  // --------
  DBC_ENSURE(result != 0 || is_engine_created() == true);
  // --------

  return result;
}

/**
 * Starts the processing engine and blocks until 
 * processing is finished.
 *
 * @param batchmode if true, runs until finished/stopped state is reached, and
 *        then returns; if false, will run infinitely
 *
 * @pre is_connected() == true
 * @pre is_running() != true
 * @post is_finished() == true || 
 *       processing_started == true && is_running() != true ||
 *       processing_started != true &&
 *       (is_engine_created() != true ||
 *        is_engine_created() == true &&
 * 	  engine_repp->status() != ECA_ENGINE::engine_status_stopped))
 *
 * @return negative on error, zero on success 
 */
int ECA_CONTROL::run(bool batchmode)
{
  // --------
  DBC_REQUIRE(is_connected() == true);
  DBC_REQUIRE(is_running() != true);
  // --------

  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Controller/Starting batch processing");

  bool processing_started = false;
  int result = -1;

  if (is_engine_created() != true) {
    /* request_batchmode=true */
    start_engine_sub(batchmode);
  }

  if (is_engine_created() != true) {
    ECA_LOG_MSG(ECA_LOGGER::info, "Can't start processing: couldn't start the engine. (2)");
  } 
  else { 
    engine_repp->command(ECA_ENGINE::ep_start, 0.0);

    DBC_CHECK(is_finished() != true);
    
    result = 0;

    /* run until processing is finished; in batchmode run forever (or
     * until error occurs) */
    while(is_finished() != true || batchmode != true) {
      
      /* sleep for 250ms */
      kvu_sleep(0, 250000000);

      if (processing_started != true) {
	if (is_running() == true ||
	    is_finished() == true ||
	    engine_exited_rep.get() == 1) {
	  /* make a note that engine state changed to 'running' */
	  processing_started = true;
	}
	else if (is_engine_created() == true) {
	  if (engine_repp->status() == ECA_ENGINE::engine_status_error) {
	    /* not running, so status() is either 'not_ready' or 'error' */
	    ECA_LOG_MSG(ECA_LOGGER::info, "Can't start processing: engine startup failed. (3)");
	    result = -2;
	    break;
	  }
	  /* other valid state alternatives: */
	  DBC_CHECK(engine_repp->status() == ECA_ENGINE::engine_status_stopped ||
		    engine_repp->status() == ECA_ENGINE::engine_status_notready);
	}
	else {
	  /* ECA_CONTROL_BASE destructor has been run and 
	   * engine_repp is now 0 (--> is_engine_created() != true) */
	  break;
	}
      }
      else {
	/* engine was started succesfully (processing_started == true) */
	if (is_running() != true) {
	  /* operation succesfully completed, exit from run() unless
	   * infinite operation is requested (batchmode) */
	  if (batchmode == true) break;
	}
      }
    }
  }    

  if (last_exec_res_rep < 0) {
    /* error occured during processing */
    result = -3;
  }

  ECA_LOG_MSG(ECA_LOGGER::subsystems, 
	      std::string("Controller/Batch processing finished (")
	      + kvu_numtostr(result) + ")");

  // --------
  DBC_ENSURE(is_finished() == true ||
	     (processing_started == true && is_running()) != true ||
	     (processing_started != true &&
	      (is_engine_created() != true ||
	       (is_engine_created() == true &&
		engine_repp->status() != ECA_ENGINE::engine_status_stopped))));
  // --------

  return result;
}

/**
 * Stops the processing engine.
 *
 * @see stop_on_condition()
 *
 * @pre is_engine_created() == true
 * @pre is_running() == true
 * @post is_running() == false
 */
void ECA_CONTROL::stop(void)
{
  // --------
  DBC_REQUIRE(is_engine_created() == true);
  DBC_REQUIRE(is_running() == true);
  // --------

  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Controller/Processing stopped");
  engine_repp->command(ECA_ENGINE::ep_stop, 0.0);
  
  // --------
  // ensure:
  // assert(is_running() == false); 
  // -- there's a small timeout so assertion cannot be checked
  // --------
}

/**
 * Stop the processing engine using thread-to-thread condition
 * signaling.
 *
 * @pre is_engine_created() == true
 * @post is_running() == false
 */
void ECA_CONTROL::stop_on_condition(void)
{
  // --------
  DBC_REQUIRE(is_engine_created() == true);
  // --------

  if (engine_repp->status() != ECA_ENGINE::engine_status_running) return;
  ECA_LOG_MSG(ECA_LOGGER::subsystems, "Controller/Processing stopped (cond)");
  engine_repp->command(ECA_ENGINE::ep_stop, 0.0);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Received stop-cond");

  // --
  // blocks until engine has stopped (or 5 sec has passed);
  engine_repp->wait_for_stop(5);

  // --------
  DBC_ENSURE(is_running() == false); 
  // --------
}

/**
 * Stops the processing engine.
 * Call will block until engine has terminated.
 */
void ECA_CONTROL::quit(void) { close_engine(); }

/**
 * Stops the processing engine. A thread-safe variant of
 * quit(). Call will not block.
 *
 */
void ECA_CONTROL::quit_async(void)
{
  if (is_engine_running() != true) 
    return;

  engine_repp->command(ECA_ENGINE::ep_exit, 0.0);
}

/**
 * Starts the processing engine.
 *
 * @pre is_connected() == true
 * @pre is_engine_running() != true
 */
void ECA_CONTROL::start_engine_sub(bool batchmode)
{
  // --------
  DBC_REQUIRE(is_connected() == true);
  DBC_REQUIRE(is_engine_running() != true);
  // --------

  DBC_CHECK(engine_exited_rep.get() != 1);

  unsigned int p = session_repp->connected_chainsetup_repp->first_selected_chain();
  if (p < session_repp->connected_chainsetup_repp->chains.size())
    session_repp->connected_chainsetup_repp->selected_chain_index_rep = p;
  
  if (engine_repp)
    close_engine();

  DBC_CHECK(is_engine_created() != true);
  engine_repp = new ECA_ENGINE (session_repp->connected_chainsetup_repp);
  DBC_CHECK(is_engine_created() == true);

  /* to relay the batchmode parameter to created new thread */
  req_batchmode_rep = batchmode;

  pthread_attr_t th_attr;
  pthread_attr_init(&th_attr);
  int retcode_rep = pthread_create(&th_cqueue_rep,
				   &th_attr,
				   start_normal_thread, 
				   static_cast<void *>(this));
  if (retcode_rep != 0) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: Unable to create a new thread for engine.");
    ECA_ENGINE *engine_tmp = engine_repp;
    engine_repp = 0;
    delete engine_tmp;
  }

  DBC_ENSURE(is_engine_created() == true);
}

/**
 * Routine used for launching the engine.
 */
void ECA_CONTROL::run_engine(void)
{
  last_exec_res_rep = 0;
  last_exec_res_rep = engine_repp->exec(req_batchmode_rep);
  engine_exited_rep.set(1); 
}

/**
 * Closes the processing engine.
 *
 * ensure:
 *  is_engine_created() != true
 *  is_engine_running() != true
 */
void ECA_CONTROL::close_engine(void)
{
  if (is_engine_created() != true) return;

  engine_repp->command(ECA_ENGINE::ep_exit, 0.0);

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Waiting for engine thread to exit.");
  if (joining_rep != true) {
    joining_rep = true;
    int res = pthread_join(th_cqueue_rep,NULL);
    joining_rep = false;
    ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"pthread_join returned: " 
		+ kvu_numtostr(res));
  }
  else {
    DBC_CHECK(engine_pid_rep >= 0);
    int i;
    for (i = 0; i < 30; i++) { 

      if (engine_exited_rep.get() ==1)
	break;

      /* 100ms sleep */
      kvu_sleep(0, 100000000);
    }
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: engine is stuck, sending SIGKILL.");
    DBC_CHECK(engine_pid_rep >= 0);
    /* note: we use SIGKILL as SIGTERM, SIGINT et al are blocked and
     *       handled by the watchdog thread */
    pthread_kill(th_cqueue_rep, SIGKILL);
  }

  if (engine_exited_rep.get() == 1) {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "Engine thread has exited succesfully.");
    delete engine_repp;
    engine_repp = 0;
    engine_exited_rep.set(0);
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: Problems while shutting down the engine!");
  }

  // ---
  DBC_ENSURE(is_engine_created() != true);
  DBC_ENSURE(is_engine_running() != true);
  // ---
}

/**
 * Is currently selected chainsetup valid?
 *
 * @pre is_selected()
 */
bool ECA_CONTROL::is_valid(void) const
{
  // --------
  DBC_REQUIRE(is_selected());
  // --------

  /* use is_valid_for_connection() instead of is_valid() to 
   * report any detected errors via the logging subsystem */
  return selected_chainsetup_repp->is_valid_for_connection(true);
}

/**
 * Returns true if active chainsetup exists and is connected.
 */
bool ECA_CONTROL::is_connected(void) const
{
  if (session_repp->connected_chainsetup_repp == 0) {
    return false;
  }

  return (session_repp->connected_chainsetup_repp->is_valid() &&
	  session_repp->connected_chainsetup_repp->is_enabled());
}

/**
 * Returns true if some chainsetup is selected.
 */
bool ECA_CONTROL::is_selected(void) const { return selected_chainsetup_repp != 0; } 

/**
 * Returns true if processing engine is running.
 */
bool ECA_CONTROL::is_running(void) const { return (is_engine_created() == true && engine_repp->status() == ECA_ENGINE::engine_status_running); } 

/**
 * Returns true if engine has finished processing. Engine state is 
 * either "finished" or "error".
 */
bool ECA_CONTROL::is_finished(void) const
{
  return (is_engine_created() == true && 
	  (engine_repp->status() == ECA_ENGINE::engine_status_finished ||
	   engine_repp->status() == ECA_ENGINE::engine_status_error)); 
} 

string ECA_CONTROL::resource_value(const string& key) const
{ 
  ECA_RESOURCES ecarc;
  return ecarc.resource(key); 
}

/**
 * Returns the length of the selected chainsetup (in samples).
 *
 * @pre is_selected() == true
 */
SAMPLE_SPECS::sample_pos_t ECA_CONTROL::length_in_samples(void) const
{
  // --------
  DBC_REQUIRE(is_selected());
  // --------

  SAMPLE_SPECS::sample_pos_t cslen = 0;
  if (selected_chainsetup_repp->length_set() == true) {
    cslen = selected_chainsetup_repp->length_in_samples();
  }
  if (selected_chainsetup_repp->max_length_set() == true) {
    cslen = selected_chainsetup_repp->max_length_in_samples();
  }

  return cslen;
}

/**
 * Returns the length of the selected chainsetup (in seconds).
 *
 * @pre is_selected() == true
 */
double ECA_CONTROL::length_in_seconds_exact(void) const
{
  // --------
  DBC_REQUIRE(is_selected());
  // --------

  double cslen = 0.0f;
  if (selected_chainsetup_repp->length_set() == true) {
    cslen = selected_chainsetup_repp->length_in_seconds_exact();
  }
  if (selected_chainsetup_repp->max_length_set() == true) {
    cslen = selected_chainsetup_repp->max_length_in_seconds_exact();
  }

  return cslen;
}

/**
 * Returns the current position of the selected chainsetup (in samples).
 *
 * @pre is_selected() == true
 */
SAMPLE_SPECS::sample_pos_t ECA_CONTROL::position_in_samples(void) const
{
  // --------
  DBC_REQUIRE(is_selected());
  // --------

  return selected_chainsetup_repp->position_in_samples();
}

/**
 * Returns the current position of the selected chainsetup (in seconds).
 *
 * @pre is_selected() == true
 */
double ECA_CONTROL::position_in_seconds_exact(void) const
{
  // --------
  DBC_REQUIRE(is_selected());
  // --------

  return selected_chainsetup_repp->position_in_seconds_exact();
}

/**
 * Returns true if engine object has been created.
 * If true, the engine object is available for use, but 
 * the related engine thread is not necessarily running.
 *
 * @see is_engine_running()
 */
bool ECA_CONTROL::is_engine_created(void) const
{
  return (engine_repp != 0);
}

/**
 * Returns true if engine is running and ready to receive
 * control commands via the message queue.
 * 
 * In practise running means that the engine thread 
 * has been created and it is running the exec() method
 * of the engine.
 * 
 * @see is_engine_created()
 */
bool ECA_CONTROL::is_engine_running(void) const
{
  bool started = is_engine_created();

  if (started != true)
    return false;

  /* note: has been started, but run_engine() has returned */
  if (engine_pid_rep < 0)
    return false;

  DBC_CHECK(engine_repp != 0);
  if (engine_repp->status() ==
      ECA_ENGINE::engine_status_notready)
    return false;

  return true;
}

/**
 * Return info about engine status.
 */
string ECA_CONTROL::engine_status(void) const
{
  if (is_engine_created() == true) {
    switch(engine_repp->status()) {
    case ECA_ENGINE::engine_status_running: 
      {
	return "running"; 
      }
    case ECA_ENGINE::engine_status_stopped: 
      {
	return "stopped"; 
      }
    case ECA_ENGINE::engine_status_finished:
      {
	return "finished"; 
      }
    case ECA_ENGINE::engine_status_error:
      {
	return "error"; 
      }
    case ECA_ENGINE::engine_status_notready: 
      {
	return "not ready"; 
      }
    default: 
      {
	return "unknown status"; 
      }
    }
  }
  return "not started";
}

void ECA_CONTROL::set_last_string(const list<string>& s)
{
  string s_rep;

  DBC_CHECK(s_rep.size() == 0);
  list<string>::const_iterator p = s.begin();
  while(p != s.end()) {
    s_rep += *p;
    ++p;
    if (p != s.end()) s_rep += "\n";
  }
  set_last_string(s_rep);
}

void ECA_CONTROL::set_last_string_list(const vector<string>& s)
{
  last_retval_rep.type = eci_return_value::retval_string_list;
  last_retval_rep.string_list_val = s;
}

void ECA_CONTROL::set_last_string(const string& s)
{
  last_retval_rep.type = eci_return_value::retval_string;
  last_retval_rep.string_val = s;
}

void ECA_CONTROL::set_last_float(double v)
{
  last_retval_rep.type = eci_return_value::retval_float;
  last_retval_rep.m.float_val = v;
}

void ECA_CONTROL::set_last_integer(int v)
{
  last_retval_rep.type = eci_return_value::retval_integer;
  last_retval_rep.m.int_val = v;
}
 
void ECA_CONTROL::set_last_long_integer(long int v)
{
  last_retval_rep.type = eci_return_value::retval_long_integer;
  last_retval_rep.m.long_int_val = v;
}

void ECA_CONTROL::set_last_error(const string& s)
{
  last_retval_rep.type = eci_return_value::retval_error;
  last_retval_rep.string_val = s;
}

string ECA_CONTROL::last_error(void) const
{
  if (last_retval_rep.type == eci_return_value::retval_error)
    return last_retval_rep.string_val;

  return string();
}

void ECA_CONTROL::clear_last_values(void)
{ 
  ECA_CONTROL_MAIN::clear_return_value(&last_retval_rep);
}

void ECA_CONTROL::set_float_to_string_precision(int precision)
{
  float_to_string_precision_rep = precision;
}
std::string ECA_CONTROL::float_to_string(double n) const
{
  return kvu_numtostr(n, float_to_string_precision_rep);
}
