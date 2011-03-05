// ------------------------------------------------------------------------
// midi-cc.cpp: Interface to MIDI continuous controllers
// Copyright (C) 1999,2001-2002,2005,2008 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// This program is fre software; you can redistribute it and/or modify
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

#include <string>
#include <iostream>

#include <kvu_dbc.h>
#include <kvu_message_item.h>

//  #include "eca-midi.h"
#include "midi-client.h"
#include "midi-server.h"
#include "midi-cc.h"

#include "eca-logger.h"

CONTROLLER_SOURCE::parameter_t MIDI_CONTROLLER::value(double pos)
{
  DBC_CHECK(server() != 0);
  parameter_t value_rep = init_value_rep;
  if (server() != 0) {
    if (trace_request_rep == true) {
      server()->add_controller_trace(channel_rep, 
				     controller_rep,
				     static_cast<int>(value_rep * 127.0));
      trace_request_rep = false;
    }

    value_rep =
      static_cast<double>(server()->last_controller_value(channel_rep, controller_rep));
    value_rep /= 127.0;
  }

  return value_rep;
}

void MIDI_CONTROLLER::set_initial_value(parameter_t arg)
{
  init_value_rep = arg;
  if (server() != 0) {
    server()->add_controller_trace(channel_rep, 
				   controller_rep,
				   static_cast<int>(init_value_rep * 127.0));
  }
  else {
    /* add controller trace when server is available */
    trace_request_rep = true;
  }
}

MIDI_CONTROLLER::MIDI_CONTROLLER(int controller_number, 
				 int midi_channel) 
  : controller_rep(controller_number), 
    channel_rep(midi_channel),
    init_value_rep(0.0),
    trace_request_rep(false) 
{
}

void MIDI_CONTROLLER::init(void)
{
    MESSAGE_ITEM otemp;
    otemp << "MIDI-controller initialized using controller ";
    otemp.setprecision(0);
    otemp << controller_rep << " and channel " << channel_rep << ".";
    ECA_LOG_MSG(ECA_LOGGER::user_objects, otemp.to_string());
}

void MIDI_CONTROLLER::set_parameter(int param, CONTROLLER_SOURCE::parameter_t value)
{
  /* FIXME: we should really remove unused ctrl+channel traces */
  switch (param) {
  case 1: 
    controller_rep = static_cast<int>(value);
    if (controller_rep < 0 ||
	controller_rep > 127) {
      controller_rep = 1;
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "(midi-cc) Controller number must be a number between 0 and 127. Defaulting to controller 0");
    }
    break;
  case 2: 
    channel_rep = static_cast<int>(value);
    if (channel_rep < 1 ||
	channel_rep > 16) {
      channel_rep = 1;
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "(midi-cc) MIDI-channel must be a number between 1 and 16. Defaulting to channel 1.");
    }
    --channel_rep; /* map from 1...16 -> 0...15 */
    break;
  }
  trace_request_rep = true;
}

CONTROLLER_SOURCE::parameter_t MIDI_CONTROLLER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return static_cast<parameter_t>(controller_rep);
  case 2: 
    /* map from 0...15 -> 1...16 */
    return static_cast<parameter_t>(channel_rep + 1);
  }
  return 0.0;
}
