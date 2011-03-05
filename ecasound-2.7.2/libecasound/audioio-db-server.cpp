// ------------------------------------------------------------------------
// audioio-db-server.cpp: Audio i/o engine serving db clients.
// Copyright (C) 2000-2005,2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cstdlib>
#include <iostream>
#include <string>

#include <errno.h> /* ETIMEDOUT */
#include <signal.h>
#include <pthread.h>
#include <unistd.h>
#include <sys/time.h> /* gettimeofday() */

#include <kvu_dbc.h>
#include <kvu_utils.h>
#include <kvu_numtostr.h>

#include "sample-specs.h"
#include "samplebuffer.h"
#include "eca-logger.h"
#include "audioio-db-server.h"
#include "audioio-db-server_impl.h"

// --
// Select features

// #define DB_PROFILING

// --
// Macro definitions

#ifdef DB_PROFILING
#define DB_PROFILING_INC(x)          ++(x)
#define DB_PROFILING_STATEMENT(x)    x
#else
#define DB_PROFILING_INC(x)          (void)0
#define DB_PROFILING_STATEMENT(x)    (void)0
#endif

// --
// Initialization of static member functions

const int AUDIO_IO_DB_SERVER::buffercount_default = 32;
const long int AUDIO_IO_DB_SERVER::buffersize_default = 1024;

// --
// Initialization of static, global functions

static int timed_wait(pthread_mutex_t* mutex, pthread_cond_t* cond, long int usecs);
static void timed_wait_print_result(int result, const char* tag, bool verbose);

/**
 * Helper function for starting the slave thread.
 */
void* start_db_server_io_thread(void *ptr)
{
  sigset_t sigset;
  sigemptyset(&sigset);
  sigaddset(&sigset, SIGINT);
  sigprocmask(SIG_BLOCK, &sigset, 0);

  AUDIO_IO_DB_SERVER* pserver =
    static_cast<AUDIO_IO_DB_SERVER*>(ptr);
  pserver->io_thread();

  return 0;
}

/**
 * Constructor.
 */
AUDIO_IO_DB_SERVER::AUDIO_IO_DB_SERVER (void)
{ 
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "constructor");
  buffercount_rep = buffercount_default;
  buffersize_rep = buffersize_default;

  impl_repp = new AUDIO_IO_DB_SERVER_impl;

  thread_running_rep = false;

  pthread_cond_init(&impl_repp->client_cond_rep, NULL);
  pthread_mutex_init(&impl_repp->client_mutex_rep, NULL);
  pthread_cond_init(&impl_repp->data_cond_rep, NULL);
  pthread_mutex_init(&impl_repp->data_mutex_rep, NULL);
  pthread_cond_init(&impl_repp->full_cond_rep, NULL);
  pthread_mutex_init(&impl_repp->full_mutex_rep, NULL);
  pthread_cond_init(&impl_repp->stop_cond_rep, NULL);
  pthread_mutex_init(&impl_repp->stop_mutex_rep, NULL);
  pthread_cond_init(&impl_repp->flush_cond_rep, NULL);
  pthread_mutex_init(&impl_repp->flush_mutex_rep, NULL);

  running_rep.set(0);
  full_rep.set(0);
  stop_request_rep.set(0);
  exit_request_rep.set(0);
  exit_ok_rep.set(0);

  impl_repp->profile_full_rep = 0;
  impl_repp->profile_no_processing_rep = 0;
  impl_repp->profile_not_full_anymore_rep = 0;
  impl_repp->profile_processing_rep = 0;
  impl_repp->profile_read_xrun_danger_rep = 0;
  impl_repp->profile_write_xrun_danger_rep = 0;
  impl_repp->profile_rounds_total_rep = 0;
}

/**
 * Destructor. Doesn't delete any client objects.
 */
AUDIO_IO_DB_SERVER::~AUDIO_IO_DB_SERVER(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "destructor");
  stop_request_rep.set(1);
  exit_request_rep.set(1);
  exit_ok_rep.set(0);
  if (thread_running_rep == true) {
    pthread_join(impl_repp->io_thread_rep, 0);
  }
  for(unsigned int p = 0; p < buffers_rep.size(); p++) {
    delete buffers_rep[p];
  }

  delete impl_repp;

  DB_PROFILING_STATEMENT(dump_profile_counters());

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "destructor-out");
}

/**
 * Starts the db server.
 *
 * @pre is_running() != true
 */
void AUDIO_IO_DB_SERVER::start(void)
{
  // --
  DBC_REQUIRE(is_running() != true);
  // --

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "start");
  if (thread_running_rep != true) {
    int ret = pthread_create(&impl_repp->io_thread_rep,
			     0,
			     start_db_server_io_thread,
			     static_cast<void *>(this));
    if (ret != 0) {
      ECA_LOG_MSG(ECA_LOGGER::info, "pthread_create failed, exiting");
      exit(1);
    }
    
    thread_running_rep = true;
  }

  stop_request_rep.set(0);
  running_rep.set(1);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "starting processing");
}

/**
 * Stops the db server.
 */
void AUDIO_IO_DB_SERVER::stop(void)
{ 
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "stop");
  stop_request_rep.set(1);
}

/**
 * Whether the db server has been started?
 */
bool AUDIO_IO_DB_SERVER::is_running(void) const
{ 
  if (running_rep.get() == 0) return false; 
  return true;
}

/**
 * Whether the db server buffers are full?
 */
bool AUDIO_IO_DB_SERVER::is_full(void) const
{
  if (full_rep.get() == 0) return false; 
  return true;
}

/**
 * Waits for condition to occur.
 *
 * @return 0 on success, 
 *         -ETIMEDOUT if timeout occured, 
 *	   other nonzero value on other errors
 */
static int timed_wait(pthread_mutex_t* mutex, 
		      pthread_cond_t* cond, 
		      long int msecs)
{
   struct timeval now;
   gettimeofday(&now, 0);

   struct timespec sleepcount;
   sleepcount.tv_nsec = now.tv_usec * 1000 + (msecs % 1000) * 1000000;
   sleepcount.tv_sec = now.tv_sec + msecs / 1000;
   if (sleepcount.tv_nsec > 1000000000) {
     sleepcount.tv_sec++; 
     sleepcount.tv_nsec -= 1000000000;
   }

   int ret = 0;
    
   pthread_mutex_lock(mutex);
   ret = pthread_cond_timedwait(cond, 
				mutex,
				&sleepcount);
   pthread_mutex_unlock(mutex);

   return ret;
}

/**
 * Prints debug information based on the result 
 * of timed_wait() call.
 */
static void timed_wait_print_result(int result, const char* tag, bool verbose)
{
  ECA_LOGGER::Msg_level_t level = ECA_LOGGER::info;
  if (verbose != true)
    level = ECA_LOGGER::continuous;

  if (result != 0) {
    if (result == -ETIMEDOUT)
      ECA_LOG_MSG(level, std::string(tag) + " failed; timeout");
    else
      ECA_LOG_MSG(level, std::string(tag) + " failed");
  }
}

/**
 * Signals the server that one of its client has
 * processed data from the db buffers. This 
 * function helps server to keep its buffers 
 * full without resorting to polling.
 *
 * Called by both db clients and the db server.
 */
void AUDIO_IO_DB_SERVER::signal_client_activity(void)
{
  pthread_mutex_lock(&impl_repp->client_mutex_rep);
  pthread_cond_broadcast(&impl_repp->client_cond_rep);
  pthread_mutex_unlock(&impl_repp->client_mutex_rep);
}

/**
 * Function that blocks until some client
 * activity occurs.
 *
 * Only called by db server.
 *
 * @see signal_client_activity()
 *
 * @pre is_running() != true
 */
void AUDIO_IO_DB_SERVER::wait_for_client_activity(void)
{
  // --
  DBC_REQUIRE(is_running() == true);
  // --

  /* note! we only wait for 100msec in case no clients
   *       clients signal activity but there's still
   *       room for new data 
   */
  int res = timed_wait(&impl_repp->client_mutex_rep, &impl_repp->client_cond_rep, 100);
  timed_wait_print_result(res, "wait_for_client_activity", false);
}

/**
 * Function that blocks until the server signals 
 * that all its buffers are full.
 */
void AUDIO_IO_DB_SERVER::wait_for_full(void)
{
  if (is_running() == true &&
      clients_rep.size() > 0) {

    /* note! we wait until we get a signal_full() even though 
     *       full_rep could already be set */

    int res = timed_wait(&impl_repp->full_mutex_rep, &impl_repp->full_cond_rep, 5000);
    timed_wait_print_result(res, "wait_for_full", true);
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "wait_for_full failed; not running");
  }
}

/**
 * Function that blocks until the server signals 
 * that it has stopped.
 */
void AUDIO_IO_DB_SERVER::wait_for_stop(void)
{
  if (is_running() == true) {
    int res = timed_wait(&impl_repp->stop_mutex_rep, &impl_repp->stop_cond_rep, 5000);
    timed_wait_print_result(res, "wait_for_stop", true);
  }
}

/**
 * Function that blocks until the server signals 
 * that it has flushed all buffers (after 
 * exit request).
 */
void AUDIO_IO_DB_SERVER::wait_for_flush(void)
{
  if (is_running() == true) {
    if (exit_ok_rep.get() == 0) {
      signal_client_activity();
      int res = timed_wait(&impl_repp->flush_mutex_rep, &impl_repp->flush_cond_rep, 5000);
      timed_wait_print_result(res, "wait_for_flush", true);
    }
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "wait_for_flush failed; not running");
  }
}

/**
 * Sends a signal notifying that server buffers
 * are fulls.
 *
 * Called by db server.
 */
void AUDIO_IO_DB_SERVER::signal_full(void)
{
  pthread_mutex_lock(&impl_repp->full_mutex_rep);
  pthread_cond_broadcast(&impl_repp->full_cond_rep);
  pthread_mutex_unlock(&impl_repp->full_mutex_rep);
}

/**
 * Sends a signal notifying that server has
 * stopped.
 *
 * Called by db server.
 */
void AUDIO_IO_DB_SERVER::signal_stop(void)
{
  pthread_mutex_lock(&impl_repp->stop_mutex_rep);
  pthread_cond_broadcast(&impl_repp->stop_cond_rep);
  pthread_mutex_unlock(&impl_repp->stop_mutex_rep);
}

/**
 * Sends a signal notifying that server has
 * flushed all buffers (after an exit request).
 *
 * Called by db server.
 */
void AUDIO_IO_DB_SERVER::signal_flush(void)
{
  pthread_mutex_lock(&impl_repp->flush_mutex_rep);
  pthread_cond_broadcast(&impl_repp->flush_cond_rep);
  pthread_mutex_unlock(&impl_repp->flush_mutex_rep);
}


/**
 * Sets new default values for sample buffers.
 * 
 * @pre is_running() != true
 */
void AUDIO_IO_DB_SERVER::set_buffer_defaults(int buffers, 
						long int buffersize)
{ 
  // --
  DBC_REQUIRE(is_running() != true);
  // --

  buffercount_rep = buffers;
  buffersize_rep = buffersize;
}

/**
 * Registers a new client object.
 *
 * @pre aobject != 0
 * @pre is_running() != true
 */
void AUDIO_IO_DB_SERVER::register_client(AUDIO_IO* aobject)
{
  // --
  DBC_REQUIRE(aobject != 0);
  DBC_REQUIRE(is_running() != true);
  // --
  
  clients_rep.push_back(aobject);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"Registering client " +
		kvu_numtostr(clients_rep.size() - 1) +
		". Buffer count " +
		kvu_numtostr(buffercount_rep) + ".");
  buffers_rep.push_back(new AUDIO_IO_DB_BUFFER(buffercount_rep,
					       buffersize_rep,
					       aobject->channels()));
  client_map_rep[aobject] = clients_rep.size() - 1;
}

/**
 * Unregisters the client object given as the argument. No
 * resources are freed during this call.
 *
 */
void AUDIO_IO_DB_SERVER::unregister_client(AUDIO_IO* aobject)
{ 
  // --
  DBC_REQUIRE(is_running() != true);
  // --

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "unregister_client " + aobject->name() + ".");
  if (client_map_rep.find(aobject) != client_map_rep.end()) {
    size_t index = client_map_rep[aobject];
    if (index >= 0 && index < clients_rep.size()) {
      clients_rep[index] = 0;
      delete buffers_rep[index];
      buffers_rep[index] = 0;
    }
    else 
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "unregister_client failed (1)");
  }
  else 
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "unregister_client failed (2)");
      
}

/**
 * Gets a pointer to the client buffer belonging to 
 * the audio object given as parameter. If no
 * buffers are found (client not registered, etc), 
 * null is returned.
 */
AUDIO_IO_DB_BUFFER* AUDIO_IO_DB_SERVER::get_client_buffer(AUDIO_IO* aobject)
{
  if (client_map_rep.find(aobject) == client_map_rep.end() ||
      clients_rep[client_map_rep[aobject]] == 0)
    return 0;

  return buffers_rep[client_map_rep[aobject]];
}

/**
 * Slave thread.
 */
void AUDIO_IO_DB_SERVER::io_thread(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Hey, in the I/O loop!");

  int processed = 0;
  int passive_rounds = 0;
  DB_PROFILING_STATEMENT(bool one_time_full = false);

  /* set idle timeout to ~10% of total buffersize (using 44.1Hz as a reference) */
  long int sleeplen = buffersize_rep * buffercount_rep * 1000 / 44100 / 10 * 1000000;

  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"Using idle timeout of " +
		kvu_numtostr(sleeplen) + 
		" nsecs.");

  while(true) {
    if (running_rep.get() == 0) {
      kvu_sleep(0, sleeplen);
      if (exit_request_rep.get() == 1) break;
      continue;
    }

    DB_PROFILING_INC(impl_repp->profile_rounds_total_rep);

    processed = 0;

    int min_free_space = buffercount_rep;

    DB_PROFILING_STATEMENT(impl_repp->looptimer_rep.start());

    for(unsigned int p = 0; p < clients_rep.size(); p++) {

      if (clients_rep[p] == 0 ||
	  buffers_rep[p]->finished_rep.get()) {
	continue;
      }
      else if (clients_rep[p]->finished() == true) {
	buffers_rep[p]->finished_rep.set(1);
      }

      int free_space = 0;

      if (buffers_rep[p]->io_mode_rep == AUDIO_IO::io_read) {
	free_space = buffers_rep[p]->write_space();
	if (free_space > 0) {
	  /* room available, so we can read at least one buffer of data */

	  if (clients_rep[p]->finished() != true) {
	    clients_rep[p]->read_buffer(buffers_rep[p]->sbufs_rep[buffers_rep[p]->writeptr_rep.get()]);
	    if (clients_rep[p]->finished() == true) buffers_rep[p]->finished_rep.set(1);
	    buffers_rep[p]->advance_write_pointer();
	    ++processed;
	  }

#ifdef DB_PROFILING
	  if (buffers_rep[p]->write_space() > 16 && one_time_full == true) {
	    DB_PROFILING_INC(impl_repp->profile_read_xrun_danger_rep);
	  }
#endif

	}
      }
      else {
	free_space = buffers_rep[p]->read_space();
	if (free_space > 0) {
	  /* room available, so we can write at least one buffer of data */

	  if (clients_rep[p]->finished() != true) {
	    clients_rep[p]->write_buffer(buffers_rep[p]->sbufs_rep[buffers_rep[p]->readptr_rep.get()]);
	    if (clients_rep[p]->finished() == true) buffers_rep[p]->finished_rep.set(1);
	    buffers_rep[p]->advance_read_pointer();
	    ++processed;
	  }

#ifdef DB_PROFILING
	  if (buffers_rep[p]->read_space() < 16  && one_time_full == true) {
	    DB_PROFILING_INC(impl_repp->profile_write_xrun_danger_rep);
	  }
#endif
	}
      }

      if (free_space < min_free_space) min_free_space = free_space;
    }

    DB_PROFILING_STATEMENT(impl_repp->looptimer_rep.stop());

    if (stop_request_rep.get() == 1) {
      stop_request_rep.set(0);
      running_rep.set(0);
      full_rep.set(0);
      signal_stop();
    }
    else {
      if (processed == 0) passive_rounds++;
      else passive_rounds = 0;

      if (processed == 0) {
	if (passive_rounds > 1) {
	  /* case 1: nothing processed during the last two rounds ==> signal_full, wait_for_client_activity */
	  DB_PROFILING_INC(impl_repp->profile_full_rep);
	  full_rep.set(1);
	  DB_PROFILING_STATEMENT(if (one_time_full != true) one_time_full = true);
	  signal_full();
	  DBC_CHECK(running_rep.get() == 1);
	}
	else {
	  /* case 2: nothing processed during the last round ==> wait_for_client_activity */
	  DB_PROFILING_INC(impl_repp->profile_no_processing_rep);
	}
	
	wait_for_client_activity();
      }
      else {
	/* case 3: something processed; business as usual */
	DB_PROFILING_INC(impl_repp->profile_processing_rep);
      }

      /* case X: something processed; room in the buffers ==> data available */
      // DB_PROFILING_INC(impl_repp->profile_not_full_anymore_rep);
    }
  }
  flush();
  exit_ok_rep.set(1);
  // std::cerr << "Exiting db server thread." << std::endl;
}

void AUDIO_IO_DB_SERVER::dump_profile_counters(void)
{
  std::cerr << "(audioio-db-server) *** profile begin ***" << std::endl;
  std::cerr << "Profile_full_rep: " << impl_repp->profile_full_rep << std::endl;
  std::cerr << "Profile_no_processing_rep: " << impl_repp->profile_no_processing_rep << std::endl;
  std::cerr << "Profile_not_full_anymore_rep: " << impl_repp->profile_not_full_anymore_rep << std::endl;
  std::cerr << "Profile_processing_rep: " << impl_repp->profile_processing_rep << std::endl;
  std::cerr << "Profile_read_xrun_danger_rep: " << impl_repp->profile_read_xrun_danger_rep << std::endl;
  std::cerr << "Profile_write_xrun_danger_rep: " << impl_repp->profile_write_xrun_danger_rep << std::endl;
  std::cerr << "Profile_rounds_total_rep: " << impl_repp->profile_rounds_total_rep << std::endl;
  std::cerr << "Fastest/slowest/average loop time: ";
  std::cerr << kvu_numtostr(impl_repp->looptimer_rep.min_duration_seconds() * 1000, 1);
  std::cerr << "/";
  std::cerr << kvu_numtostr(impl_repp->looptimer_rep.max_duration_seconds() * 1000, 1);
  std::cerr << "/";
  std::cerr << kvu_numtostr(impl_repp->looptimer_rep.average_duration_seconds() * 1000, 1);
  std::cerr << " msec." << std::endl;
  std::cerr << "(audioio-db-server) *** profile end   ***" << std::endl;
}

/**
 * Flushes all data in the buffers to disk.
 */
void AUDIO_IO_DB_SERVER::flush(void)
{
  int not_finished = 1;
  while(not_finished != 0) {
    not_finished = 0;
    for(unsigned int p = 0; p < clients_rep.size(); p++) {
      if (clients_rep[p] == 0 ||
	  buffers_rep[p]->finished_rep.get()) continue;
      if (buffers_rep[p]->io_mode_rep != AUDIO_IO::io_read) {
	if (buffers_rep[p]->read_space() > 0) {
	  ++not_finished;

	  ECA_LOG_MSG(ECA_LOGGER::info, 
		      "Flushing buffer " + 
		      kvu_numtostr(buffers_rep[p]->readptr_rep.get()) +
		      " of client " +
		      kvu_numtostr(p) +
		      " read_space: " +
		      kvu_numtostr(buffers_rep[p]->read_space()) +
		      ".");
	  
	  clients_rep[p]->write_buffer(buffers_rep[p]->sbufs_rep[buffers_rep[p]->readptr_rep.get()]);
	  if (clients_rep[p]->finished() == true) buffers_rep[p]->finished_rep.set(1);
	  buffers_rep[p]->advance_read_pointer();
	}
      }
    }
  }
  for(unsigned int p = 0; p < buffers_rep.size(); p++) {
    if (buffers_rep[p] != 0) {
      buffers_rep[p]->reset();
    }
  }
  signal_flush();
}
