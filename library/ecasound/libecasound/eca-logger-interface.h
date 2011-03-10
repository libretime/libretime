// ------------------------------------------------------------------------
// eca-logger-interface.h: Logging subsystem interface
// Copyright (C) 2002-2004,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
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

#ifndef INCLUDE_ECA_LOGGER_INTERFACE_H
#define INCLUDE_ECA_LOGGER_INTERFACE_H

#include <iostream> /* remove me */
#include <list>
#include <string>
#include "eca-logger.h"

/**
 * Virtual base class for logging subsystem implementations.
 *
 * @author Kai Vehmanen
 */
class ECA_LOGGER_INTERFACE {

  public:

  ECA_LOGGER_INTERFACE(void);
  virtual ~ECA_LOGGER_INTERFACE(void);

  void msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message);
  void flush(void);
  void disable(void);
  void set_log_history_length(int len);
  void set_log_level(ECA_LOGGER::Msg_level_t level, bool enabled);
  const std::list<std::string>& log_history(void) const { return log_history_rep; }

  /**
   * Gets current log level bitmask.
   */
  int get_log_level_bitmask(void) const { return debug_value_rep; }
  
  /**
   * Sets state of all logging types according to 'bitmask'.
   */
  void set_log_level_bitmask(int level_bitmask) { debug_value_rep = static_cast<ECA_LOGGER::Msg_level_t>(level_bitmask); }
 
  /**
   * Whether 'level' is set or not?
   */
  bool is_log_level_set(ECA_LOGGER::Msg_level_t level) const { return (level & debug_value_rep) > 0 ? true : false; }

  protected:

  virtual void do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message) = 0;
  virtual void do_flush(void) = 0;
  virtual void do_log_level_changed(void) = 0;

  static std::string filter_module_name(const std::string& rawmodule);
  static void format_log_msg(std::string *logmsg, ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message);

  private:

  int debug_value_rep;
  int log_history_len_rep;
  std::list<std::string> log_history_rep;

  int extlog_debug_level_rep;
  FILE *extlog_file_repp;
};

#endif
