// ------------------------------------------------------------------------
// audiogate.cpp: Signal gates.
// Copyright (C) 1999-2002,2005-2008,2010 Kai Vehmanen
// Copyrtigh (C) 2008 Andrew Lees
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

#include <kvu_numtostr.h>

#include "samplebuffer.h"
#include "samplebuffer_functions.h"
#include "audiogate.h"
#include "eca-logger.h"

GATE_BASE::~GATE_BASE(void)
{
}

void GATE_BASE::process(void)
{
  analyze(target);
  if (is_open() == false) {
    target->length_in_samples(0);
  }
}

void GATE_BASE::init(SAMPLE_BUFFER* sbuf)
{ 
  gate_open = false;
  target = sbuf;
}

void TIME_CROP_GATE::analyze(SAMPLE_BUFFER* sbuf)
{
  parameter_t etime = begtime_rep + durtime_rep;
  parameter_t curtime = static_cast<parameter_t>(position_in_samples_rep) / samples_per_second();

  if (curtime >= begtime_rep) {
    /* note: handle the special case where a zero open time
     *       has been requested */
    if (begtime_rep == etime) 
      open_gate();
    else if (curtime < etime)
      open_gate();
    else
      close_gate();
  }
  else
    close_gate();

  position_in_samples_rep += sbuf->length_in_samples();
}

TIME_CROP_GATE::TIME_CROP_GATE (CHAIN_OPERATOR::parameter_t open_at, CHAIN_OPERATOR::parameter_t duration)
{
  begtime_rep = open_at;
  durtime_rep = duration;
  position_in_samples_rep = 0;
  
  ECA_LOG_MSG(ECA_LOGGER::info, "Time crop gate created; opens at " +
	      kvu_numtostr(begtime_rep) + " seconds and stays open for " +
	      kvu_numtostr(durtime_rep) + " seconds.\n");
}

CHAIN_OPERATOR::parameter_t TIME_CROP_GATE::get_parameter(int param) const 
{ 
  switch (param) {
  case 1: 
    return begtime_rep;
  case 2: 
    return durtime_rep;
  }
  return 0.0;
}

void TIME_CROP_GATE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) 
{
  switch (param) {
  case 1: 
    begtime_rep = value;
    position_in_samples_rep = 0;
    break;
  case 2: 
    durtime_rep = value;
    break;
  }
}

void TIME_CROP_GATE::set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value)
{
  double ratio (new_value);
  ratio /= samples_per_second();
  /* note: as we store position as samples, changes in sampling rate
   *       require recalculation of position */
  position_in_samples_rep = static_cast<SAMPLE_SPECS::sample_pos_t>(position_in_samples_rep * ratio);
  ECA_SAMPLERATE_AWARE::set_samples_per_second(new_value);
}

THRESHOLD_GATE::THRESHOLD_GATE (CHAIN_OPERATOR::parameter_t threshold_openlevel, 
				CHAIN_OPERATOR::parameter_t threshold_closelevel,
				bool use_rms) 
{
  openlevel_rep = threshold_openlevel / 100.0;
  closelevel_rep = threshold_closelevel / 100.0;
  rms_rep = use_rms;
  reopen_count_param_rep = 0;

  is_opened_rep = is_closed_rep = false;

  if (rms_rep) {
    ECA_LOG_MSG(ECA_LOGGER::info, "Threshold gate created; open threshold " +
		kvu_numtostr(openlevel_rep * 100) + "%, close threshold " +
		kvu_numtostr(closelevel_rep * 100) + "%, using RMS volume.");
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::info, "Threshold gate created; open threshold " +
		kvu_numtostr(openlevel_rep * 100) + "%, close threshold " +
		kvu_numtostr(closelevel_rep * 100) + "%, using peak volume.");
  }
}

void THRESHOLD_GATE::init(SAMPLE_BUFFER* sbuf)
{
  reopens_left_rep = reopen_count_param_rep;
  is_opened_rep = false;
  is_closed_rep = false;
  GATE_BASE::init(sbuf);
}

void THRESHOLD_GATE::analyze(SAMPLE_BUFFER* sbuf)
{
  if (rms_rep == true)
    avolume_rep = SAMPLE_BUFFER_FUNCTIONS::RMS_volume(*sbuf) / SAMPLE_SPECS::max_amplitude;
  else 
    avolume_rep = SAMPLE_BUFFER_FUNCTIONS::average_amplitude(*sbuf) / SAMPLE_SPECS::max_amplitude;

  if (is_opened_rep == false) {
    if (avolume_rep > openlevel_rep) { 
      open_gate();
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Threshold gate opened (reopen count = " + kvu_numtostr(reopens_left_rep) + ")");
      is_opened_rep = true;
      is_closed_rep = false;
    }
  }
  else if (is_closed_rep == false) {
    if (avolume_rep < closelevel_rep) { 
      close_gate();
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Threshold gate closed (reopens left = " + kvu_numtostr(reopens_left_rep) + ")");
      is_closed_rep = true;
      if (reopens_left_rep != 0) {
        is_opened_rep = false;
        if (reopens_left_rep > 0)
	  --reopens_left_rep;
      } else {
        // - Could we stop the engine and exit here, maybe? -AL/2008-Jul
	// - Not from a chain operator, but the audio object
	//   that writes the stream to a file could in
	//   theory react in a special way to the 0-length 
	//   samplebuffers we generate when the gate is closed... -KV/2008-Jul
      }
    }
  }
}

CHAIN_OPERATOR::parameter_t THRESHOLD_GATE::get_parameter(int param) const
{ 
  switch (param) {
  case 1: 
    return openlevel_rep * 100.0;
  case 2: 
    return closelevel_rep * 100.0;
  case 3: 
    if (rms_rep) 
      return 1.0;
    else 
      return 0.0;
  case 4:
    return reopen_count_param_rep;
  }
  return 0.0;
}

void THRESHOLD_GATE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) 
{
  switch (param) {
  case 1: 
    openlevel_rep = value / 100.0;
    break;
  case 2: 
    closelevel_rep = value / 100.0;
    break;
  case 3: 
    rms_rep = (value != 0);
    break;
  case 4:
    reopen_count_param_rep = static_cast<int>(value);
    break;
  }
}

void MANUAL_GATE::analyze(SAMPLE_BUFFER* sbuf)
{
  if (is_open() == true &&
      open_rep != true) {
    close_gate();
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Manual gate closed");
  }
  else if (is_open() != true &&
	   open_rep == true) {
    open_gate();
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Manual gate opened");
  }
}

CHAIN_OPERATOR::parameter_t MANUAL_GATE::get_parameter(int param) const 
{ 
  switch (param) {
  case 1: 
    return open_rep == true ? 1 : 0;
  }
  return 0.0;
}

void MANUAL_GATE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) 
{
  switch (param) {
  case 1: 
    if (value > 0)
      open_rep = true;
    else
      open_rep = false;
    break;
  }
}
