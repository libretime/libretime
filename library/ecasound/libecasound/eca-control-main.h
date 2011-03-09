// ------------------------------------------------------------------------
// eca-control-main.h: ECA_CONTROL_MAIN class
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

#ifndef INCLUDED_ECA_CONTROL_MAIN_H
#define INCLUDED_ECA_CONTROL_MAIN_H

#include <string>
#include <vector>

#include "eca-chainsetup-edit.h"

struct eci_return_value {
  enum { 
    retval_none = 0,
    retval_string_list,
    retval_string,
    retval_float,
    retval_integer,
    retval_long_integer,
    retval_error
  } type;
  std::vector<std::string> string_list_val;
  std::string string_val;
  union {
    double float_val;
    int int_val;
    long int long_int_val;
  } m;
};
  
/**
 * High-level interface for using libecasound functionality
 *
 * This class is the abstract interface. Actual implementations
 * are done in subclasses - e.g. ECA_CONTROL and 
 * ECA_CONTROL_MT (multi-threaded).
 *
 * Related design patters: Facade (GoF185)
 */
class ECA_CONTROL_MAIN {
  
public:

  /** @name Constructors and dtors */
  /*@{*/

  virtual ~ECA_CONTROL_MAIN(void);

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Runtime control */
  /*@{*/

  virtual void engine_start(void) = 0;
  virtual int start(void) = 0;
  virtual void stop(void) = 0;
  virtual void stop_on_condition(void) = 0;
  virtual int run(bool batchmode = true) = 0;
  virtual void quit(void) = 0;
  virtual void quit_async(void) = 0;

  virtual bool is_running(void) const = 0;
  virtual bool is_connected(void) const = 0;
  virtual bool is_selected(void) const = 0;
  virtual bool is_finished(void) const = 0;
  virtual bool is_valid(void) const = 0;
  virtual bool is_engine_created(void) const = 0;
  virtual bool is_engine_running(void) const = 0;

  virtual const ECA_CHAINSETUP* get_connected_chainsetup(void) const = 0;

  virtual void connect_chainsetup(struct eci_return_value *retval) = 0;
  virtual void disconnect_chainsetup(void) = 0;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Execute edit objects */
  /*@{*/

  virtual bool execute_edit_on_connected(const ECA::chainsetup_edit_t& edit) = 0;
  virtual bool execute_edit_on_selected(const ECA::chainsetup_edit_t& edit, int index = -1) = 0;

  /*@}*/

  /** @name Building blocks for ECI -Ecasound Control Interface */
  /*@{*/

  /**
   * Parses and executes a string containing a single Ecasound 
   * Interactive Mode (EIAM) command and its arguments.
   *
   * Result of the command is stored to 'retval'.
   */
  virtual void command(const std::string& cmd_and_args, struct eci_return_value *retval) = 0;

  /**
   * A special version of 'command()' which parses a command taking 
   * a single double parameter.
   *
   * Result of the command is stored to 'retval'.
   */
  virtual void command_float_arg(const std::string& cmd, double arg, struct eci_return_value *retval) = 0;

  virtual void print_last_value(struct eci_return_value *retval) const = 0;

  /*@}*/

  // -------------------------------------------------------------------

  /** @name Static helper functions  */
  /*@{*/

  static std::string return_value_to_string(const struct eci_return_value *retval, int float_precision = 9);
  static const char* return_value_type_to_string(const struct eci_return_value *retval);
  static void clear_return_value(struct eci_return_value *retval);

  /*@}*/

  // -------------------------------------------------------------------

};

#endif /*  INCLUDED_ECA_CONTROL_MAIN_H */
