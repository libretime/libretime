// ------------------------------------------------------------------------
// audiofx_rcfilter.cpp: Simulation of an 3rd-order 36dB active RC-lowpass
// Copyright (C) 2000 Stefan Fendt, Kai Vehmanen (C++ version)
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

#include <cmath>
#include <kvu_utils.h>

#include "samplebuffer_iterators.h"
#include "eca-logger.h"
#include "audiofx_rcfilter.h"

EFFECT_RC_LOWPASS_FILTER::EFFECT_RC_LOWPASS_FILTER (CHAIN_OPERATOR::parameter_t cutoff, 
						    CHAIN_OPERATOR::parameter_t resonance) {
  set_parameter(1, cutoff);
  set_parameter(2, resonance);
}

void EFFECT_RC_LOWPASS_FILTER::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) {
  switch (param) {
  case 1: 
    cutoff_rep = value;
    break;
  case 2: 
    resonance_rep = value;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_RC_LOWPASS_FILTER::get_parameter(int param) const { 
  switch (param) {
  case 1: 
    return(cutoff_rep);
  case 2: 
    return(resonance_rep);
  }
  return(0.0);
}

void EFFECT_RC_LOWPASS_FILTER::init(SAMPLE_BUFFER *insample) {
  i.init(insample);

  lp1_old.resize(insample->number_of_channels(), 0.0015);
  lp2_old.resize(insample->number_of_channels(), -0.00067);
  lp3_old.resize(insample->number_of_channels(), 0.0);
  hp1_old.resize(insample->number_of_channels(), 0.0);
  feedback.resize(insample->number_of_channels(), 0.0);
}

void EFFECT_RC_LOWPASS_FILTER::process(void) {
  i.begin();
  while(!i.end()) {
    output_temp = *i.current();
    output_temp += (feedback[i.channel()] * resonance_rep);

    // --
    // The two lines above prevent the filter from clipping if it is 
    // self-oscillating. This is necessary for a good simulation because 
    // real analouge RC-filters can't clip when oscillating, too. Clipping
    // used to simulate saturation of an amp (as many TB-303-emulators do)
    // is dissatisfying ! We should use an Amp-simulation instead to avoid
    // digital clipping ...

    if (output_temp > SAMPLE_SPECS::impl_max_value)
      output_temp = SAMPLE_SPECS::impl_max_value;
    else if (output_temp < SAMPLE_SPECS::impl_min_value) 
      output_temp = SAMPLE_SPECS::impl_min_value;

    // --
    // Ok, this is the first step of the filter. We simulate an simple
    // non-active RC-lowpass ... 

    lp1_old[i.channel()] = output_temp * cutoff_rep + lp1_old[i.channel()] * (1.0 - cutoff_rep);
    lp2_old[i.channel()] = lp1_old[i.channel()] * cutoff_rep + lp2_old[i.channel()] * (1.0 - cutoff_rep);
    lp3_old[i.channel()] = lp2_old[i.channel()] * cutoff_rep + lp3_old[i.channel()] * (1.0 - cutoff_rep);

    // --
    // A simple non-active highpass

    hp1_old[i.channel()] = output_temp - lp3_old[i.channel()];

    // --
    // Now we add a feedback of this bandpass-filtered signal to the
    // input of the filter again. (Look some lines above for that!)

    feedback[i.channel()] = hp1_old[i.channel()];

    // --
    // We catch the out-value of the filter after the second lp-filter
    // this provides 24-db filtering even with moderate resonance.

    *i.current() = lp3_old[i.channel()];
    i.next();
  }
}
