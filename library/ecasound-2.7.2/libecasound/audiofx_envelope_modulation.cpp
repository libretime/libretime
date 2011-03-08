// ------------------------------------------------------------------------
// audiofx_envelope-modulation.cpp: Effects which modify/modulate signal
//                                  envelope
// Copyright (C) 2000 Rob Coker 
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
//
// ------------------------------------------------------------------------
// 
// History: 
//
// 2003-08-21 Kai Vehmanen
//     - Fixed a timing resolution bug in the envelope modulation code.
//       This involved changing from float to fixed point presentation for 
//       the position counters to avoid loss of precision (led to unexpected
//       drift in pulse timing).
// 2000-11-01 Rob Coker
//     - Initial version.
//
// ------------------------------------------------------------------------

#include <cmath>

#include <kvu_message_item.h>

#include "samplebuffer_iterators.h"
#include "audiofx_envelope_modulation.h"

#include "eca-logger.h"
#include "eca-error.h"

EFFECT_ENV_MOD::~EFFECT_ENV_MOD(void)
{
}

EFFECT_PULSE_GATE::EFFECT_PULSE_GATE (parameter_t freq_Hz,
				      parameter_t onTime_percent)
{
  set_parameter(1, freq_Hz);
  set_parameter(2, onTime_percent);

  freq_rep = 1.0;
  on_time_rep = 0;
  current_rep = 0;
  period_rep = 0;
  on_from_rep = 0;
}

EFFECT_PULSE_GATE::~EFFECT_PULSE_GATE(void)
{
}

void EFFECT_PULSE_GATE::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  /* NOP, see audiofx.cpp:set_samples_per_second(); */
  EFFECT_ENV_MOD::set_samples_per_second(v);
}

void EFFECT_PULSE_GATE::set_parameter(int param, parameter_t value)
{
  switch (param) {
  case 1: 
    if (value > 0)
      {
	freq_rep = value;
	period_rep = static_cast<long int>(1.0f / freq_rep * samples_per_second() + 0.5f); // samples
      }
    else
      {
	MESSAGE_ITEM otemp;
	otemp << "(audiofx_envelope_modulation) WARNING! Frequency must be greater than 0! ";
	ECA_LOG_MSG(ECA_LOGGER::user_objects, otemp.to_string());
      }
    break;

  case 2:
    if ((value > 0) && (value < 100))
      {
	on_time_rep = value;
	on_from_rep = static_cast<long int>((on_time_rep / 100.0) * period_rep + 0.5f);
      }
    else
      {
	MESSAGE_ITEM otemp;
	otemp << "(audiofx_envelope_modulation) WARNING! on time must be between 0 and 100 inclusive! ";
	ECA_LOG_MSG(ECA_LOGGER::user_objects, otemp.to_string());
      }
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_PULSE_GATE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return(freq_rep);

  case 2:
    return (on_time_rep);
  }
  return(0.0);
}

void EFFECT_PULSE_GATE::init(SAMPLE_BUFFER* sbuf)
{ 
  i.init(sbuf); 
  set_channels(sbuf->number_of_channels());
  EFFECT_ENV_MOD::init(sbuf);
}

void EFFECT_PULSE_GATE::process(void)
{
  i.begin(); // iterate through all samples, one sample-frame at a
             // time (interleaved)
  while(!i.end()) {
    ++current_rep;
    if (current_rep >= period_rep) {
	current_rep = 0;
    }
    if (current_rep > on_from_rep) {
      for(int n = 0; n < channels(); n++) {
	*i.current(n) = 0.0;
      }
    }
    i.next();
  }
}

EFFECT_PULSE_GATE_BPM::EFFECT_PULSE_GATE_BPM (parameter_t bpm,
					      parameter_t ontime_percent)
{
  set_parameter(1, bpm);
  set_parameter(2, ontime_percent);
}

EFFECT_PULSE_GATE_BPM::~EFFECT_PULSE_GATE_BPM(void)
{
}

void EFFECT_PULSE_GATE_BPM::set_parameter(int param, parameter_t value)
{
  switch (param) {
  case 1: 
    pulsegate_rep.set_parameter(1, value / 60.0f);
    break;
  case 2:
    pulsegate_rep.set_parameter(2, value);
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_PULSE_GATE_BPM::get_parameter(int param) const {
  switch (param) {
  case 1: 
    return (pulsegate_rep.get_parameter(1) * 60.0f);
    break;
  case 2:
    return (pulsegate_rep.get_parameter(2));
    break;
  }
  return(0.0);
}

void EFFECT_PULSE_GATE_BPM::init(SAMPLE_BUFFER* sbuf)
{ 
  pulsegate_rep.init(sbuf); 
  EFFECT_ENV_MOD::init(sbuf);
}

void EFFECT_PULSE_GATE_BPM::process(void) { pulsegate_rep.process(); }

void EFFECT_PULSE_GATE_BPM::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  pulsegate_rep.set_samples_per_second(v);
  EFFECT_ENV_MOD::set_samples_per_second(v);
}

EFFECT_TREMOLO::EFFECT_TREMOLO (parameter_t freq_bpm,
				parameter_t depth_percent)
{
  set_parameter(1, freq_bpm);
  set_parameter(2, depth_percent);
  currentTime = 0.0;
}

EFFECT_TREMOLO::~EFFECT_TREMOLO(void)
{
}

void EFFECT_TREMOLO::set_parameter(int param, parameter_t value)
{
  switch (param) {
  case 1:
    if (value > 0)
      {
	freq = value/(2*60); // convert from bpm to Hz
      }
    else
      {
	MESSAGE_ITEM otemp;
	otemp << "(audiofx_envelope_modulation) WARNING! bpm must be greater than 0! ";
	ECA_LOG_MSG(ECA_LOGGER::info, otemp.to_string());
      }
    break;
  case 2:
	depth = (value/100.0); // from percent to fraction
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_TREMOLO::get_parameter(int param) const
{
  switch (param) {
  case 1:
    return freq*120;
    break;
  case 2:
    return depth*100.0;
    break;
  }
  return(0.0);
}

void EFFECT_TREMOLO::init(SAMPLE_BUFFER* sbuf)
{
  i.init(sbuf);
  set_channels(sbuf->number_of_channels());
  incrTime = 1.0/samples_per_second();
  EFFECT_ENV_MOD::init(sbuf);
}

void EFFECT_TREMOLO::process(void)
{
  i.begin(); // iterate through all samples, one sample-frame at a
             // time (interleaved)
  while(!i.end())
  {
    currentTime += incrTime;
    double envelope = (1-depth)+depth*fabs(sin(2*3.1416*currentTime*freq));
    if (envelope < 0)
    {
       envelope = 0;
    }
  	for(int n = 0; n < channels(); n++)
  	{
	    *i.current(n) *= envelope;
    }
    i.next();
  }
}
