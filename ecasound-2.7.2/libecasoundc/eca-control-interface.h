#ifndef INCLUDED_ECA_CONTROL_INTERFACE_H
#define INCLUDED_ECA_CONTROL_INTERFACE_H

/** ------------------------------------------------------------------------
 * ecasoundc.h: C++ implementation of the Ecasound Control Interface
 * Copyright (C) 2000-2002 Kai Vehmanen
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * -------------------------------------------------------------------------
 */

#include <string>
#include <vector>

#include <ecasoundc.h>

/**
 * C++ implementation of the Ecasound Control Interface
 *
 * @author Kai Vehmanen
 */
class ECA_CONTROL_INTERFACE {

 public:

  // -------------------------------------------------------------------
  // State
  // -------------------------------------------------------------------

  bool ready(void);

  // -------------------------------------------------------------------
  // Issuing EIAM commands
  // -------------------------------------------------------------------

  void command(const std::string& cmd);
  void command_float_arg(const std::string& cmd, double arg);

  // -------------------------------------------------------------------
  // Getting return values
  // -------------------------------------------------------------------

  const std::vector<std::string>& last_string_list(void) const;
  const std::string& last_string(void) const;
  double last_float(void) const;
  int last_integer(void) const;
  bool last_bool(void) const;
  long int last_long_integer(void) const;
  const std::string& last_error(void) const;
  const std::string& last_type(void) const;
  bool error(void) const;

  /**
   * Returns last_integer() interpreted as a bool.
   */
  bool last_boolean(void) const { return(last_integer() != 0); }
  
  // -------------------------------------------------------------------
  // Events
  // -------------------------------------------------------------------

  bool events_available(void);
  void next_event(void);
  const std::string& current_event(void);

  // -------------------------------------------------------------------
  // Constructors and destructors
  // -------------------------------------------------------------------

  ECA_CONTROL_INTERFACE(void);
  ~ECA_CONTROL_INTERFACE(void);

 private:
  
  eci_handle_t eci_repp;
  mutable std::string str_rep;
  mutable std::vector<std::string> strlist_rep;
};

#endif
