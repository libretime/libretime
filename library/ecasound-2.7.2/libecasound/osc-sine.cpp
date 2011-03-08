// ------------------------------------------------------------------------
// osc-sine.cpp: Sine oscillator
// Copyright (C) 1999,2001,2008 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
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

#include <cmath>

#include <kvu_numtostr.h>
#include <kvu_message_item.h>

#include "oscillator.h"
#include "osc-sine.h"
#include "eca-logger.h"

CONTROLLER_SOURCE::parameter_t SINE_OSCILLATOR::value(double pos_secs)
{
  parameter_t retval, phase;

  phase = 2.0 * M_PI * (pos_secs / period_len_rep);
  retval = sin(phase + phase_offset());

  /* note: scale return value to proper [0,1] range */
  retval += 1.0;
  retval /= 2.0;

  return retval;
}

SINE_OSCILLATOR::SINE_OSCILLATOR (double freq, double initial_phase) :
  OSCILLATOR(freq, initial_phase)
{
  set_parameter(1, get_parameter(1));
  set_parameter(2, get_parameter(2));
}

void SINE_OSCILLATOR::init(void)
{
  MESSAGE_ITEM otemp;
  otemp << "Sine oscillator created; frequency ";
  otemp.setprecision(3);
  otemp << frequency();
  otemp << " and initial phase of "; 
  otemp << phase_offset() << ".";
  ECA_LOG_MSG(ECA_LOGGER::user_objects, otemp.to_string());
}

void SINE_OSCILLATOR::set_parameter(int param, CONTROLLER_SOURCE::parameter_t v)
{
  switch (param) {
  case 1: 
    frequency(v);
    /* note: cache length of one period in seconds */
    if (v > 0)
      period_len_rep = 1.0 / frequency();
    else
      period_len_rep = 0;
    break;

  case 2: 
    phase_offset(static_cast<double>(v * M_PI));
    break;
  }
}

CONTROLLER_SOURCE::parameter_t SINE_OSCILLATOR::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return frequency();
  case 2: 
    return phase_offset() / M_PI;
  }
  return(0.0);
}
