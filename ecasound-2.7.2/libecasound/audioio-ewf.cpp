// ------------------------------------------------------------------------
// audioio-ewf.cpp: Ecasound wave format input/output
// Copyright (C) 1999-2002,2005,2008 Kai Vehmanen
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

#include <algorithm>
#include <string>
#include <vector>
#include <iostream>
#include <fstream>
#include <cmath>

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "eca-object-factory.h"
#include "samplebuffer.h"
#include "audioio-ewf.h"

#include "eca-error.h"
#include "eca-logger.h"

using std::cout;
using std::endl;
using SAMPLE_SPECS::sample_pos_t;

/**
 * FIXME notes  (last update 2008-03-09)
 *
 *  - Add (more) sanity checks for ewf fields.
 */

EWFFILE::EWFFILE (void)
{
}

EWFFILE::~EWFFILE(void)
{
}

EWFFILE* EWFFILE::clone(void) const
{
  EWFFILE* target = new EWFFILE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void EWFFILE::open(void) throw(AUDIO_IO::SETUP_ERROR &)
{
  if (io_mode() != AUDIO_IO::io_read)
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"WARNING: Writing to EWF files is a deprecated feature since 2.4.7 (2008), and it will be disabled in a future release.");

  ewf_rc.resource_file(label());
  ewf_rc.load();
  read_ewf_data();

  AUDIO_SEQUENCER_BASE::open();
}

void EWFFILE::close(void)
{
  if (io_mode() != AUDIO_IO::io_read) 
    write_ewf_data();

  AUDIO_SEQUENCER_BASE::close();
}

void EWFFILE::init_default_child(void) throw(ECA_ERROR&)
{
  string::const_iterator e = std::find(label().begin(), label().end(), '.');
  if (e == label().end()) {
    throw(ECA_ERROR("AUDIOIO-EWF", "Invalid file name; unable to open file.",ECA_ERROR::retry));
  }

  string child_name (label().begin(), e);
  child_name += ".wav";
  set_child_object_string(child_name);

  DBC_CHECK(child_name == child_object_string());

  ewf_rc.resource("source", child_object_string());
}

void EWFFILE::read_ewf_data(void) throw(ECA_ERROR&)
{
  if (ewf_rc.has("source"))
    set_child_object_string(ewf_rc.resource("source"));
  else 
    init_default_child();

  if (ewf_rc.has("offset")) {
    set_child_offset(ECA_AUDIO_TIME(ewf_rc.resource("offset")));
  }
  else
    set_child_offset(ECA_AUDIO_TIME());

  /* FIXME: doesn't work, if child has different srate and start-pos
   *        specified in samples, the result is incorrect! */

  if (ewf_rc.has("start-position")) {
    set_child_start_position(ECA_AUDIO_TIME(ewf_rc.resource("start-position")));
  }
  else
    set_child_start_position(ECA_AUDIO_TIME());

  if (ewf_rc.has("length")) {
    set_child_length(ECA_AUDIO_TIME(ewf_rc.resource("length")));
  }

  toggle_looping(ewf_rc.boolean_resource("looping"));

  const std::vector<std::string> keys = ewf_rc.keywords();
  std::vector<std::string>::const_iterator p = keys.begin();
  while(p != keys.end()) {
    if (*p != "source" &&
	*p != "offset" &&
	*p != "start-position" &&
	*p != "length" &&
	*p != "looping")
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "WARNING: Unknown keyword '" 
		  + *p 
		  + "' in EWF file '" 
		  + label() 
		  + "'.");
    ++p;
  }
}

void EWFFILE::write_ewf_data(void)
{
  ewf_rc.resource("source", child_object_string());
  if (child_offset().samples() > 0)
    ewf_rc.resource("offset", kvu_numtostr(child_offset().seconds(),6));
  if (child_start_position().samples() != 0) 
    ewf_rc.resource("start-position", kvu_numtostr(child_start_position().seconds(), 6));
  if (child_looping() == true) 
    ewf_rc.resource("looping","true");
  if (child_length().valid() == true)
    ewf_rc.resource("length", kvu_numtostr(child_length().seconds(),  6));
  
  ewf_rc.save();
}

std::string EWFFILE::parameter_names(void) const
{
  return AUDIO_IO::parameter_names();
}

void EWFFILE::set_parameter(int param, std::string value)
{
  AUDIO_IO::set_parameter(param, value);
}

std::string EWFFILE::get_parameter(int param) const
{
  return AUDIO_IO::get_parameter(param);
}
