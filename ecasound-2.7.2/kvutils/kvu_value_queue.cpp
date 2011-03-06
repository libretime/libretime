// ------------------------------------------------------------------------
// kvu_value_queue.cpp: A thread-safe way to transmit int-double pairs.
// Copyright (C) 1999,2004 Kai Vehmanen
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

#include <cstdio>
#include <deque>
#include <string>

#include <errno.h>
#include <unistd.h>
#include <pthread.h>
#include <sys/time.h>

#include "kvu_dbc.h"
#include "kvu_value_queue.h"

/* --------------------------------------------------------------------- 
 * Options
 */

// #define VERBOSE

/* --------------------------------------------------------------------- 
 * Test util macros
 */

#ifdef VERBOSE
#define KVU_NOTE_S(x)       do { printf("%s:%d - %s\n", __FILE__, __LINE__, x); fflush(stdout); } while(0)
#define KVU_NOTE_SD(x, y)   do { printf("%s:%d - %s=%d\n", __FILE__, __LINE__, x, y); fflush(stdout); } while(0)
#else
#define KVU_NOTE_S(x)       ((void) 0)
#define KVU_NOTE_SD(x, y)   ((void) 0)
#endif

/* --------------------------------------------------------------------- 
 * Definitions
 */

using namespace std;

VALUE_QUEUE::VALUE_QUEUE(void)
{
  pthread_mutex_init(&lock_rep, NULL);
  pthread_cond_init(&cond_rep, NULL);
  empty_rep = pair<int,double>(0, 0.0f);
}

void VALUE_QUEUE::push_back(int key, double value)
{
  pthread_mutex_lock(&lock_rep);
  cmds_rep.push_back(pair<int,double>(key, value));
  pthread_cond_broadcast(&cond_rep);
  pthread_mutex_unlock(&lock_rep);
}

void VALUE_QUEUE::pop_front(void)
{
  // --------
  DBC_REQUIRE(is_empty() == false);
  // --------
  pthread_mutex_lock(&lock_rep);
  cmds_rep.pop_front();
  pthread_mutex_unlock(&lock_rep);
}    

const pair<int,double>& VALUE_QUEUE::front(void)
{
  // --------
  DBC_REQUIRE(is_empty() == false);
  // --------
  pthread_mutex_lock(&lock_rep);
  const pair<int,double>& s = cmds_rep.front();
  pthread_mutex_unlock(&lock_rep);
  return s;
}

void VALUE_QUEUE::poll(int timeout_sec,
		       long int timeout_usec)
{
  struct timeval now;
  struct timespec timeout;
  int retcode;

  pthread_mutex_lock(&lock_rep);
  gettimeofday(&now, 0);
  timeout.tv_sec = now.tv_sec + timeout_sec;
  timeout.tv_nsec = now.tv_usec * 1000 + timeout_usec * 1000;
  retcode = 0;
  while (cmds_rep.empty() == true && retcode != ETIMEDOUT) {
    retcode = pthread_cond_timedwait(&cond_rep, &lock_rep, &timeout);
  }
  pthread_mutex_unlock(&lock_rep);
  return;
}

bool VALUE_QUEUE::is_empty(void) const
{
  pthread_mutex_lock(&lock_rep);
  bool result = cmds_rep.empty(); 
  pthread_mutex_unlock(&lock_rep);
  return result;
}

/************************************************************************/

/**
 * Default for maximum size of the queue for operation in 
 * bounded execution time mode.
 */
static const size_t kvqr_bound_exec_max_size_const = 1024;

/**
 * Class constructor.
 *
 * @param 
 *
 * Execution note: not bounded (may block, may allocate memory)
 */
VALUE_QUEUE_RT_C::VALUE_QUEUE_RT_C(int bounded_exec_max_size)
  : pending_pops_rep(0)
{
  pthread_mutex_init(&lock_rep, NULL);
  pthread_cond_init(&cond_rep, NULL);
  if (bounded_exec_max_size == -1)
    bounded_exec_max_size_rep = kvqr_bound_exec_max_size_const;
  else 
    bounded_exec_max_size_rep = static_cast<size_t>(bounded_exec_max_size);
}

/**
 * Adds a new item to the end of the queue.
 *
 * Execution note: non-realtime (may block, may allocate memory)
 */
void VALUE_QUEUE_RT_C::push_back(int key, double value)
{
  pthread_mutex_lock(&lock_rep);
  cmds_rep.push_back(pair<int,double>(key, value));
  KVU_NOTE_SD("pushback-when-size=", cmds_rep.size());
  pthread_cond_broadcast(&cond_rep);
  pthread_mutex_unlock(&lock_rep);
}

/**
 * Removes the first item.
 *
 * Execution note: bounded
 *
 * @pre is_empty() != true
 */
void VALUE_QUEUE_RT_C::pop_front(void)
{
  int ret = pthread_mutex_trylock(&lock_rep);
  if (ret == 0) {
    cmds_rep.pop_front();
    pthread_mutex_unlock(&lock_rep);
  }
  else {
    /* could not remove item, add to pending pops */
    if (pending_pops_rep != cmds_rep.size()) {
      ++pending_pops_rep;
      KVU_NOTE_SD("add-pending-pop=", pending_pops_rep);
    }
  }
}    

/**
 * Returns the first item.
 *
 * Execution note: bounded
 *
 * @pre is_empty() != true
 * @return returns VALUE_QUEUE_RT_C::invalid_item() if temporarily 
 *         unable to access the queue
 */
const pair<int,double>* VALUE_QUEUE_RT_C::front(void)
{
  pair<int,double>* s = &invalid_rep;
  int ret = pthread_mutex_trylock(&lock_rep);

  if (ret != 0 && 
      cmds_rep.size() >= bounded_execution_queue_size_limit()) {
    /* queue has grown beyond the rt-safe maximum size, 
     * change to non-bounded mode to force synchronization
     * between the producer and consumer threads 
     */
    KVU_NOTE_SD("queue-limit-when-size=", cmds_rep.size());
    ret = pthread_mutex_lock(&lock_rep);
  }

  if (ret == 0) {
    /* now that we have the lock, we can safely process
     * any pending pop requests */
    DBC_CHECK(cmds_rep.size() >= pending_pops_rep);
    while(pending_pops_rep > 0 &&
	  cmds_rep.size() > 0) {
      cmds_rep.pop_front();
      --pending_pops_rep;
      KVU_NOTE_SD("dec-pending-pop=", pending_pops_rep);
    }
    KVU_NOTE_SD("front-when-size=", cmds_rep.size());
    s = &cmds_rep.front();
    pthread_mutex_unlock(&lock_rep);
  }

  return s;
}

/**
 * Blocks until 'is_empty() != true'. 'timeout_sec' and
 * 'timeout_usec' specify the upper time limit for blocking.
 *
 * Execution: not bounded (may block, may allocate memory)
 *
 * @pre is_empty() != true
 */
void VALUE_QUEUE_RT_C::poll(int timeout_sec,
			    long int timeout_usec)
{
  struct timeval now;
  struct timespec timeout;
  int retcode;

  pthread_mutex_lock(&lock_rep);
  gettimeofday(&now, 0);
  timeout.tv_sec = now.tv_sec + timeout_sec;
  timeout.tv_nsec = now.tv_usec * 1000 + timeout_usec * 1000;
  retcode = 0;
  while (cmds_rep.empty() == true && retcode != ETIMEDOUT) {
    KVU_NOTE_S("poll-in");
    retcode = pthread_cond_timedwait(&cond_rep, &lock_rep, &timeout);
    KVU_NOTE_S("poll-out");
  }
  pthread_mutex_unlock(&lock_rep);
  return;
}

/**
 * Is queue empty?
 *
 * Execution note: bounded (may block, may allocate memory)
 */
bool VALUE_QUEUE_RT_C::is_empty(void) const
{
  size_t size = 0;
  int ret = pthread_mutex_trylock(&lock_rep);

  if (ret != 0 && 
      cmds_rep.size() >= bounded_execution_queue_size_limit()) {
    /* queue has grown beyond the rt-safe maximum size, 
     * change to non-bounded mode to force synchronization
     * between the producer and consumer threads 
     */
    KVU_NOTE_SD("queue-limit-when-size=", cmds_rep.size());
    ret = pthread_mutex_lock(&lock_rep);
  }

  if (ret == 0) {
    size = cmds_rep.size();
    DBC_CHECK(size - pending_pops_rep >= 0);
    pthread_mutex_unlock(&lock_rep);
  }

  return (size - pending_pops_rep) == 0;
}

size_t VALUE_QUEUE_RT_C::bounded_execution_queue_size_limit(void) const
{
  return bounded_exec_max_size_rep;
}

/************************************************************************/
