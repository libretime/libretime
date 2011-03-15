// ------------------------------------------------------------------------
// audiofx.cpp: General effect processing routines.
// Copyright (C) 1999-2002,2004,2006,2008 Kai Vehmanen
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

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "sample-specs.h"
#include "samplebuffer.h"
#include "audiofx.h"
#include "eca-logger.h"

EFFECT_BASE::EFFECT_BASE(void)
  : channels_rep(0) 
{
}

EFFECT_BASE::~EFFECT_BASE(void)
{
}

void EFFECT_BASE::init(SAMPLE_BUFFER* sbuf)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,
	      "Init w/ samplerate " +
	      kvu_numtostr(samples_per_second()) + " for object " +
	      name() + ".");

  set_channels(sbuf->number_of_channels());

  DBC_CHECK(channels() > 0);
  DBC_CHECK(samples_per_second() > 0);
}

int EFFECT_BASE::channels(void) const
{ 
  return channels_rep;
}

void EFFECT_BASE::set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_rate)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,
	      "Setting samplerate to " +
	      kvu_numtostr(new_rate) + " for object " +
	      name() + ". Old value " +
	      kvu_numtostr(samples_per_second()) + ".");

  /* note: changing the sample rate might change values of
   *       of parameters, so we want to preserve the values */

  if (samples_per_second() != new_rate) {
    std::vector<parameter_t> old_values (number_of_params());
    for(int n = 0; n < number_of_params(); n++) {
      old_values[n] = get_parameter(n + 1);
    }
    
    ECA_SAMPLERATE_AWARE::set_samples_per_second(new_rate);

    for(int n = 0; n < number_of_params(); n++) {
      set_parameter(n + 1, old_values[n]);
    }
  }
}

void EFFECT_BASE::set_channels(int v)
{
  channels_rep = v; 
}
