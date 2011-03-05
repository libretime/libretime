// ------------------------------------------------------------------------
// eca-control-mt.h: Multithreaded implementation of ECA_CONTROL_INTERFACE
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

#ifndef INCLUDED_ECA_CONTROL_MT_H
#define INCLUDED_ECA_CONTROL_MT_H

#include <pthread.h>

#include "eca-control-main.h"

class ECA_CONTROL;
class ECA_SESSION;

/**
 * High-level interface to libecasound functionality
 *
 * @see ECA_CONTROL_MAIN, ECA_CONTROL
 *
 * Related design patters: Facade (GoF185)
 *
 * @author Kai Vehmanen
 */
class ECA_CONTROL_MT : public ECA_CONTROL_MAIN {
  
public:

  /** @name Constructors and dtors */
  /*@{*/

  ECA_CONTROL_MT(ECA_SESSION* psession);
  virtual ~ECA_CONTROL_MT (void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Lock object exclusively to perform transactions */
  /*@{*/

  void lock_control(void);
  void unlock_control(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Runtime control */
  /*@{*/

  virtual void engine_start(void);
  virtual int start(void);
  virtual void stop(void);
  virtual void stop_on_condition(void);
  virtual int run(bool batchmode = true);
  virtual void quit(void);
  virtual void quit_async(void);

  virtual bool is_running(void) const;
  virtual bool is_connected(void) const;
  virtual bool is_selected(void) const;
  virtual bool is_finished(void) const;
  virtual bool is_valid(void) const;
  virtual bool is_engine_created(void) const;
  virtual bool is_engine_running(void) const;

  virtual const ECA_CHAINSETUP* get_connected_chainsetup(void) const;

  virtual void connect_chainsetup(struct eci_return_value *retval);
  virtual void disconnect_chainsetup(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Execute edit objects */
  /*@{*/

  virtual bool execute_edit_on_connected(const ECA::chainsetup_edit_t& edit);
  virtual bool execute_edit_on_selected(const ECA::chainsetup_edit_t& edit, int index = -1);

  /*@}*/

  /** @name Building blocks for ECI -Ecasound Control Interface */
  /*@{*/

  /**
   * Parses and executes a string containing a single Ecasound 
   * Interactive Mode (EIAM) command and its arguments.
   *
   * Result of the command can be queried with last_value_to_string().
   */
  virtual void command(const std::string& cmd_and_args, struct eci_return_value *retval);

  /**
   * A special version of 'command()' which parses a command taking 
   * a single double parameter.
   *
   * Result of the command can be queried with last_value_to_string().
   */
  virtual void command_float_arg(const std::string& cmd, double arg, struct eci_return_value *retval);

  virtual void print_last_value(struct eci_return_value *retval) const;

  // -------------------------------------------------------------------

private:

  mutable pthread_mutex_t mutex_rep;

  ECA_CONTROL *ec_repp;

  ECA_CONTROL_MT& operator=(const ECA_CONTROL_MT& v) { return *this; }
  ECA_CONTROL_MT(const ECA_CONTROL_MT* v) {}
};

#endif /*  INCLUDED_ECA_CONTROL_IF_H */
