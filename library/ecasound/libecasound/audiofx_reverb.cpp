// ------------------------------------------------------------------------
// audiofx_reverb.cpp: Reverb effect
// Copyright (C) 2000 Stefan Fendt
// Copyright (C) 2000,2003,2008 Kai Vehmanen (C++ version)
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
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
// USA
//
// ------------------------------------------------------------------------
// History: 
//
// 2003-01-19 Kai Vehmanen
//     - Added param hint information.
// 2002-12-04 Hans-Georg Fischer
//     - Fixed a bug in initializing the delay line, which cause
//       unwanted audible noise at start of processing. 
// 2000-06-06 Kai Vehmanen
//     - Initial version. Based on Stefan M. Fendt's reverb 
//       code.
// ------------------------------------------------------------------------

#include <cstdlib>

#include "sample-ops_impl.h"
#include "samplebuffer_iterators.h"
#include "sample-specs.h"
#include "audiofx_reverb.h"

ADVANCED_REVERB::ADVANCED_REVERB (parameter_t roomsize,
				  parameter_t feedback_percent, 
				  parameter_t wet_percent)
{
  set_parameter(1, roomsize);
  set_parameter(2, feedback_percent);
  set_parameter(3, wet_percent);
}

void ADVANCED_REVERB::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 10.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    // pd->upper_bound = 0.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 2:
    pd->default_value = 50.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = true;
    pd->upper_bound = 100.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 3:
    pd->default_value = 50.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = true;
    pd->upper_bound = 100.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  default: {}
  }
}

CHAIN_OPERATOR::parameter_t ADVANCED_REVERB::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return roomsize_rep;
  case 2:
    return feedback_rep * 100.0;
  case 3:
    return wet_rep * 100.0;
  }
  return 0.0;
}

void ADVANCED_REVERB::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    roomsize_rep = value;
    break;

  case 2: 
    if (value == 0) 
      feedback_rep = 0.001;
    else
      feedback_rep = value / 100.0;
    break;

  case 3: 
    wet_rep = value / 100.0;
    break;
  }
  if (param == 1 || param == 2) {
    std::vector<CHANNEL_DATA>::iterator p = cdata.begin();
    while(p != cdata.end()) {
      p->oldvalue=0.0;
      p->lpvalue=0.0;
      p->dpos[0] = static_cast<long int>(roomsize_rep * samples_per_second() / 333);
      p->mul[0] = 0.035;
      p->bufferpos_rep = 0;
      for(int i = 1; i < 64; i++) {
	p->dpos[i] = p->dpos[i-1] + (rand() & 511);
	p->mul[i] = p->mul[i-1] * (1 - 1 / feedback_rep / 1000);
      }
      ++p;
    }
  }
}

void ADVANCED_REVERB::init(SAMPLE_BUFFER *insample)
{
  i_channels.init(insample);
  cdata.resize(insample->number_of_channels());
  std::vector<CHANNEL_DATA>::iterator p = cdata.begin();
  while(p != cdata.end()) {
    p->oldvalue=0.0;
    p->lpvalue=0.0;
    p->dpos[0] = static_cast<long int>(roomsize_rep * samples_per_second() / 333);
    p->mul[0] = 0.035;
    p->bufferpos_rep = 0;
    for(int i = 1; i < 64; i++) {
      p->dpos[i] = p->dpos[i-1] + (rand() & 511);
      p->mul[i] = p->mul[i-1] * (1 - 1 / feedback_rep / 1000);
    }
    ++p;
  }
}

void ADVANCED_REVERB::process(void)
{
  i_channels.begin();
  while(!i_channels.end()) {
    int ch = i_channels.channel();

    cdata[ch].bufferpos_rep++;
    cdata[ch].bufferpos_rep &= 65535;

    double old_value = cdata[ch].oldvalue;
    cdata[ch].buffer[cdata[ch].bufferpos_rep] = 
      ecaops_flush_to_zero(*i_channels.current() + old_value);

    old_value = 0.0;
    for(int i = 0; i < 64; i++) {
      old_value +=
	static_cast<float>(cdata[ch].buffer[(cdata[ch].bufferpos_rep - cdata[ch].dpos[i]) & 65535] * cdata[ch].mul[i]);
    }

    /**
     * This is just a very simple high-pass-filter to remove offsets
     * which can accour during calculation of the echos
     */
    cdata[ch].lpvalue = 
      ecaops_flush_to_zero(cdata[ch].lpvalue * 0.99 + old_value * 0.01);
    old_value = old_value - cdata[ch].lpvalue;

    /**
     * This is a simple lowpass to make the apearence of the reverb 
     * more realistic... (Walls do not reflect high frequencies very
     * well at all...) 
     */
    cdata[ch].oldvalue = 
      ecaops_flush_to_zero(cdata[ch].oldvalue * 0.75 + old_value * 0.25);

    *i_channels.current() = cdata[ch].oldvalue * wet_rep + *i_channels.current() * (1 - wet_rep);
    i_channels.next();
  }
}
