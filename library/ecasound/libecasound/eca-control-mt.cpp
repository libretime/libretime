// ------------------------------------------------------------------------
// eca-control-mt.h: ECA_CONTROL_MT class implementation
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

#include "eca-control-mt.h"
#include "eca-control.h"

ECA_CONTROL_MT::ECA_CONTROL_MT(ECA_SESSION* psession)
{
  pthread_mutexattr_t mutex_attr;
  pthread_mutexattr_init(&mutex_attr);
  pthread_mutexattr_settype(&mutex_attr, PTHREAD_MUTEX_RECURSIVE);
  pthread_mutex_init(&mutex_rep, &mutex_attr);
  ec_repp = new ECA_CONTROL(psession);
}

ECA_CONTROL_MT::~ECA_CONTROL_MT(void)
{
  pthread_mutex_destroy(&mutex_rep);
  delete ec_repp;
}

void ECA_CONTROL_MT::lock_control(void)
{
  pthread_mutex_lock(&mutex_rep);
}

void ECA_CONTROL_MT::unlock_control(void)
{
  pthread_mutex_unlock(&mutex_rep);
}

void ECA_CONTROL_MT::engine_start(void)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->engine_start();
  pthread_mutex_unlock(&mutex_rep);
}

int ECA_CONTROL_MT::start(void)
{
  int res;
  pthread_mutex_lock(&mutex_rep);
  res = ec_repp->start();
  pthread_mutex_unlock(&mutex_rep);
  return res;
}

void ECA_CONTROL_MT::stop(void)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->stop();
  pthread_mutex_unlock(&mutex_rep);
}

void ECA_CONTROL_MT::stop_on_condition(void)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->stop_on_condition();
  pthread_mutex_unlock(&mutex_rep);
}

int ECA_CONTROL_MT::run(bool batchmode)
{
  int res;
  pthread_mutex_lock(&mutex_rep);
  res = ec_repp->run(batchmode);
  pthread_mutex_unlock(&mutex_rep);
  return res;
}

void ECA_CONTROL_MT::quit(void)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->quit();
  pthread_mutex_unlock(&mutex_rep);
}

void ECA_CONTROL_MT::quit_async(void)
{
  /* note: does not need locking! */
  ec_repp->quit_async();
}

bool ECA_CONTROL_MT::is_running(void) const
{
  /* note: thread-safe as the member function is thread-safe,
   *       but ordering across CPUs is not guaranteed (one
   *       might get stale data */
  return ec_repp->is_running();
}

bool ECA_CONTROL_MT::is_connected(void) const
{
  /* see note for is_running() */
  return ec_repp->is_connected();
}
 
bool ECA_CONTROL_MT::is_selected(void) const
{
  /* see note for is_running() */
  return ec_repp->is_selected();
}

bool ECA_CONTROL_MT::is_finished(void) const
{
  /* see note for is_running() */
  return ec_repp->is_finished();
}

bool ECA_CONTROL_MT::is_valid(void) const
{
  /* see note for is_running() */
  return ec_repp->is_valid();
}

bool ECA_CONTROL_MT::is_engine_created(void) const
{
  /* see note for is_running() */
  return ec_repp->is_engine_created();
}

bool ECA_CONTROL_MT::is_engine_running(void) const
{
  /* see note for is_running() */
  return ec_repp->is_engine_running();
}

const ECA_CHAINSETUP* ECA_CONTROL_MT::get_connected_chainsetup(void) const
{
  /* note: locking even though this is const */
  const ECA_CHAINSETUP* res;
  pthread_mutex_lock(&mutex_rep);
  res = ec_repp->get_connected_chainsetup();
  pthread_mutex_unlock(&mutex_rep);
  return res;
}


void ECA_CONTROL_MT::connect_chainsetup(struct eci_return_value *retval)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->connect_chainsetup(retval);
  pthread_mutex_unlock(&mutex_rep);
}

void ECA_CONTROL_MT::disconnect_chainsetup(void)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->disconnect_chainsetup();
  pthread_mutex_unlock(&mutex_rep);
}

bool ECA_CONTROL_MT::execute_edit_on_connected(const ECA::chainsetup_edit_t& edit)
{
  bool res;
  pthread_mutex_lock(&mutex_rep);
  res = ec_repp->execute_edit_on_connected(edit);
  pthread_mutex_unlock(&mutex_rep);
  return res;
}
 
bool ECA_CONTROL_MT::execute_edit_on_selected(const ECA::chainsetup_edit_t& edit, int index)
{
  bool res;
  pthread_mutex_lock(&mutex_rep);
  res = ec_repp->execute_edit_on_selected(edit);
  pthread_mutex_unlock(&mutex_rep);
  return res;
}

void ECA_CONTROL_MT::command(const std::string& cmd_and_args, struct eci_return_value *retval)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->command(cmd_and_args, retval);
  pthread_mutex_unlock(&mutex_rep);
}


void ECA_CONTROL_MT::command_float_arg(const std::string& cmd, double arg, struct eci_return_value *retval)
{
  pthread_mutex_lock(&mutex_rep);
  ec_repp->command_float_arg(cmd, arg, retval);
  pthread_mutex_unlock(&mutex_rep);
}

void ECA_CONTROL_MT::print_last_value(struct eci_return_value *retval) const
{
  /* note: a const function that only depends on 'retval', so 
   *       this is safe */
  ec_repp->print_last_value(retval);
}
