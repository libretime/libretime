// ------------------------------------------------------------------------
// eca-logger-wellformed.cpp: Logging implementation that outputs 
//                            messages in a well-formed format.
// Copyright (C) 2002-2004 Kai Vehmanen
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

#include <iostream>
#include <string>

#include <kvu_numtostr.h>

#include "eca-logger-wellformed.h"

using namespace std;

ECA_LOGGER_WELLFORMED::ECA_LOGGER_WELLFORMED(void)
{
}

ECA_LOGGER_WELLFORMED::~ECA_LOGGER_WELLFORMED(void)
{
}

/**
 * Prints the given log message in well-formed
 * format. 
 * 
 * See section "Ecasound Interactive Mode - 
 * Well-Formed Output Mode" in the Ecasound 
 * Programmer's Guide for more detailed documentation.
 */
void ECA_LOGGER_WELLFORMED::do_msg(ECA_LOGGER::Msg_level_t level, const string& module_name, const string& log_message)
{
  if (is_log_level_set(level) == true) {
    cout << ECA_LOGGER_WELLFORMED::create_wellformed_message(level,
							     log_message);
  }
}

void ECA_LOGGER_WELLFORMED::do_flush(void) 
{
}

void ECA_LOGGER_WELLFORMED::do_log_level_changed(void)
{
}

string ECA_LOGGER_WELLFORMED::create_wellformed_message(ECA_LOGGER::Msg_level_t level, const string& message)
{
  string result, rettype;
  string::const_iterator p = message.begin();
  size_t msglen = message.size();

  /* 1. loglevel */
  result += kvu_numtostr(static_cast<int>(level));
    
  /* 2. space */
  result += " ";
  
  if (level == ECA_LOGGER::eiam_return_values) {
    while(p != message.end()) {
      msglen--;
      if (isspace(*p) != 0) {
	rettype = string(message.begin(), p);
	p++; /* skip space to reach start of actual msg */
	break;
      }
      ++p;
    }
  }
  
  /* 3. message size */
  result += kvu_numtostr(msglen);
  
  if (level == ECA_LOGGER::eiam_return_values) {
    /* 4. space */
    result += " ";
    
    /* 5. return type */
    result += rettype;
  }
  
  /* 6. contentblock */
  result += "\r\n";
  result += string(p,message.end()); 
  result += "\r\n\r\n";

  return result;
}
