// ------------------------------------------------------------------------
// eca-logger-interface.cpp: Logging subsystem interface
// Copyright (C) 2002-2004,2009 Kai Vehmanen
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

#include <algorithm> /* find() */
#include <list>
#include <string>

#include <sys/types.h> /* for getpid() */
#include <unistd.h>    /* --"-- */

#include <cstdio>
#include <cstdlib>
#include <ctime>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "eca-version.h"
#include "eca-logger-interface.h"

using namespace std;

const static int eca_l_i_default_log_history_len = 0;
const static int eca_l_i_default_extlog_level = ECA_LOGGER::errors | ECA_LOGGER::info | ECA_LOGGER::subsystems | ECA_LOGGER::module_names | ECA_LOGGER::user_objects | ECA_LOGGER::system_objects | ECA_LOGGER::eiam_return_values;

/**
 * Class constructor. Initializes log level to 'disabled'.
 */
ECA_LOGGER_INTERFACE::ECA_LOGGER_INTERFACE(void) 
  : debug_value_rep(0),
    log_history_len_rep(eca_l_i_default_log_history_len),
    extlog_debug_level_rep(eca_l_i_default_extlog_level),
    extlog_file_repp(0)
{
  char *extlog_dest = getenv("ECASOUND_LOGFILE");
  char *extlog_loglevel = getenv("ECASOUND_LOGLEVEL");
  
  if (extlog_dest) {
    if (extlog_loglevel) {
      extlog_debug_level_rep = atoi(extlog_loglevel);
    }

    extlog_file_repp = fopen(extlog_dest, "a");
    if (extlog_file_repp) {
      time_t curtime;
      time(&curtime);
      fprintf(extlog_file_repp, 
	      "---------------------------------------------------------------------\n"
	      "%sOpening logfile \"%s\" for ecasound-%s (logger=%p, pid=%d):\n", 
	      ctime(&curtime), extlog_dest, ecasound_library_version, this, getpid());
    }
    else {
      std::cerr << "*** ERROR: Error in opening \"" 
		<< extlog_dest << "\". Check ECASOUND_LOGFILE and file permissions. ***\n";
    }
  }
}

/**
 * Class destructor.
 */
ECA_LOGGER_INTERFACE::~ECA_LOGGER_INTERFACE(void)
{
  if (extlog_file_repp != 0) {
    fprintf(extlog_file_repp, 
	    "Closing logfile (logger=%p, pid=%d).\n", 
	    this, getpid());
    fclose(extlog_file_repp);
  }
}

/**
 * Issues a generic log message.
 */
void ECA_LOGGER_INTERFACE::msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message)
{
  std::string logmsg;

  /* step: output message with subclass implementation */
  do_msg(level, module_name, log_message);

  /* step: store item to history 
   *       (note that we cannot archive EIAM return values as this 
   *        could create a loop when the backlog itself is printed) */
  if (log_history_len_rep > 0 &&
      level != ECA_LOGGER::eiam_return_values) {
    format_log_msg(&logmsg, level, module_name, log_message);
    log_history_rep.push_back(string("[") + ECA_LOGGER::level_to_string(level) + "] ("
			      + ECA_LOGGER_INTERFACE::filter_module_name(module_name)
			      + ") " + log_message);
    if (static_cast<int>(log_history_rep.size()) > log_history_len_rep) {
      log_history_rep.pop_front();
      DBC_CHECK(static_cast<int>(log_history_rep.size()) == log_history_len_rep);
    }
  }

  /* step: conditionally output to external logfile */
  if (extlog_file_repp != 0) {
    ECA_LOGGER::Msg_level_t extlevel = 
      static_cast<ECA_LOGGER::Msg_level_t>(extlog_debug_level_rep > 0 ? extlog_debug_level_rep : debug_value_rep);

    /* if message matches the request level mask, write it to log */
    if (extlevel & level) {
      static unsigned long counter = 0;

      if (logmsg.size() == 0) 
	format_log_msg(&logmsg, level, module_name, log_message);

      logmsg += " <" + kvu_numtostr(counter++) + ">\n";
      
      fwrite(logmsg.c_str(), logmsg.size(), 1, extlog_file_repp);
      if (ferror(extlog_file_repp)) {
	std::cerr << "*** ERROR: Error in writing to ECASOUND_LOGFILE. Check free disk space. ***\n";
	fclose(extlog_file_repp);
	extlog_file_repp = 0;
      }
      fflush(extlog_file_repp);
    }
  }
}

/**
 * Sets logging level to 'level' state to 'enabled'.
 */
void ECA_LOGGER_INTERFACE::set_log_level(ECA_LOGGER::Msg_level_t level, bool enabled)
{
  if (enabled == true) {
    debug_value_rep |= level;
  }
  else {
    debug_value_rep &= ~level;
  }
}

/**
 * Flush all log messages.
 */
void ECA_LOGGER_INTERFACE::flush(void)
{
  do_flush();
}

/**
 * Disables logging.
 * 
 * Note! Is equivalent to 
 * 'set_log_level(ECA_LOGGER_INTERFACE::disabled)'.
 */
void ECA_LOGGER_INTERFACE::disable(void)
{
  debug_value_rep = 0;
}

/**
 * Sets the log message history length.
 */
void ECA_LOGGER_INTERFACE::set_log_history_length(int len)
{
  log_history_len_rep = len;
}

/**
 * Formats module name for logging purposes.
 *
 * Both "foobar.cpp" and "../src/foobar.cpp" return 
 * the same formatted string "foobar".
 */
string ECA_LOGGER_INTERFACE::filter_module_name(const string& rawmodule)
{
  string retval;
  
  size_t begin =
    rawmodule.rfind("/");
  if (begin == string::npos)
    begin = 0;
  else
    /* skip the initial "/" */
    ++begin;

  size_t end =
    rawmodule.rfind(".");

  if (end > begin)
    return string(rawmodule, begin, end - begin);

  return rawmodule;
}

void ECA_LOGGER_INTERFACE::format_log_msg(std::string *logmsg, ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message)
{
  *logmsg = 
    string("[") + ECA_LOGGER::level_to_string(level)  + "] ("
    + ECA_LOGGER_INTERFACE::filter_module_name(module_name) + ") " 
    + log_message;
}
