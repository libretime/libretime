// ------------------------------------------------------------------------
// audiofx_timebased.cpp: Routines for time-based effects.
// Copyright (C) 1999-2005 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
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

#include <assert.h>
#include <string>

#include <kvu_dbc.h>
#include <kvu_utils.h>

#include "eca-logger.h"

#include "samplebuffer_iterators.h"
#include "sample-ops_impl.h"
#include "audiofx_timebased.h"


EFFECT_DELAY::EFFECT_DELAY (CHAIN_OPERATOR::parameter_t delay_time, int surround_mode, 
			    int num_of_delays, CHAIN_OPERATOR::parameter_t mix_percent,
			    CHAIN_OPERATOR::parameter_t feedback_percent) 
{
  laskuri = 0.0;

  set_parameter(1, delay_time);
  set_parameter(2, surround_mode);
  set_parameter(3, num_of_delays);
  set_parameter(4, mix_percent);
  set_parameter(5, feedback_percent);
}

void EFFECT_DELAY::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 100.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 2:
    pd->default_value = 0.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = true;
    pd->upper_bound = 1.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = true;
    pd->integer = true;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 3:
    pd->default_value = 1.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    // pd->upper_bound = 0.0f;
    pd->bounded_below = true;
    pd->lower_bound = 1.0f;
    pd->toggled = false;
    pd->integer = true;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 4:
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
  case 5:
    pd->default_value = 100.0f;
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

CHAIN_OPERATOR::parameter_t EFFECT_DELAY::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return dtime_msec;
  case 2:
    return surround;
  case 3:
    return dnum;
  case 4:
    return mix * 100.0;
  case 5:
    return feedback * 100.0;
  }
  return 0.0;
}

void EFFECT_DELAY::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1:
    {
      dtime_msec = value;
      dtime = dtime_msec * (CHAIN_OPERATOR::parameter_t)samples_per_second() / 1000;
      std::vector<std::vector<SINGLE_BUFFER> >::iterator p = buffer.begin();
      while(p != buffer.end()) {
	std::vector<SINGLE_BUFFER>::iterator q = p->begin();
	while(q != p->end()) {
	  if (q->size() > dtime) {
	    q->resize(static_cast<unsigned int>(dtime));
	    laskuri = dtime;
	  }
	  ++q;
	}
	++p;
      }
      break;
    }

  case 2: 
    surround = value;
    break;

  case 3: 
    {
      if (value != 0.0) dnum = static_cast<long int>(value);
      else dnum = 1.0;
      std::vector<std::vector<SINGLE_BUFFER> >::iterator p = buffer.begin();
      while(p != buffer.end()) {
	p->resize(static_cast<unsigned int>(dnum));
	++p;
      }
      laskuri = 0;
      break;
    }

  case 4:
    mix = value / 100.0;
    break;

  case 5:
    if (value == 0 || value > 100) {
      feedback = 1.0;
    } else {
    feedback = value / 100.0;
    }
    break;
  }
}

void EFFECT_DELAY::init(SAMPLE_BUFFER* insample)
{
  l.init(insample);
  r.init(insample);

  EFFECT_BASE::init(insample);

  set_parameter(1, dtime_msec);

  buffer.resize(2, std::vector<SINGLE_BUFFER> (static_cast<unsigned int>(dnum)));
}

void EFFECT_DELAY::process(void)
{
  l.begin(SAMPLE_SPECS::ch_left);
  r.begin(SAMPLE_SPECS::ch_right);

  while(!l.end() && !r.end()) {
    SAMPLE_SPECS::sample_t temp2_left = 0.0;
    SAMPLE_SPECS::sample_t temp2_right = 0.0;

    // Initializing the feedback factor to one. (x*1 = x)
    SAMPLE_SPECS::sample_t feedfact = 1;

    for(int nm2 = 0; nm2 < dnum; nm2++) {
      SAMPLE_SPECS::sample_t temp_left = 0.0;
      SAMPLE_SPECS::sample_t temp_right = 0.0;

      // Preparing the factor...
      feedfact *= feedback;

      if (laskuri >= dtime * (nm2 + 1)) {
 
	switch ((int)surround) {
	case 0: 
	  {
	    // ---
	    // surround
	    temp_left = buffer[SAMPLE_SPECS::ch_left][nm2].front();
	    temp_right = buffer[SAMPLE_SPECS::ch_right][nm2].front();
	    break;
	  }

	case 1: 
	  {
	    // ---
	    // surround
	    temp_left = buffer[SAMPLE_SPECS::ch_right][nm2].front();
	    temp_right = buffer[SAMPLE_SPECS::ch_left][nm2].front();
	    break;
	  }
	case 2: 
	  {
	    if (nm2 % 2 == 0) {
	      temp_left = (buffer[SAMPLE_SPECS::ch_left][nm2].front()
			   + 
			   buffer[SAMPLE_SPECS::ch_right][nm2].front()) / 2.0;
	      temp_right = 0.0;
	    }
	    else {
	      temp_right = (buffer[SAMPLE_SPECS::ch_left][nm2].front()
			   + 
			   buffer[SAMPLE_SPECS::ch_right][nm2].front()) / 2.0;
	      temp_left = 0.0;
	    }
	    break;
	}
	} // switch

	// Applying the reduction.
	temp_left *= feedfact;
	temp_right *= feedfact;

	buffer[SAMPLE_SPECS::ch_left][nm2].pop_front();
	buffer[SAMPLE_SPECS::ch_right][nm2].pop_front();
      }
      buffer[SAMPLE_SPECS::ch_left][nm2].push_back(*l.current());
      buffer[SAMPLE_SPECS::ch_right][nm2].push_back(*r.current());

      temp2_left += temp_left / dnum;
      temp2_right += temp_right / dnum;

    }
    *l.current() = (*l.current() * (1.0 - mix)) + (temp2_left * mix);
    *r.current() = (*r.current() * (1.0 - mix)) + (temp2_right * mix);

    l.next();
    r.next();

    if (laskuri < dtime * dnum) laskuri++;
  }
}

EFFECT_MULTITAP_DELAY::EFFECT_MULTITAP_DELAY (CHAIN_OPERATOR::parameter_t delay_time, 
					      int num_of_delays, 
					      CHAIN_OPERATOR::parameter_t mix_percent)
  : 
  delay_index(0),
  filled (0),
  buffer (0)
{
  set_parameter(1, delay_time);
  set_parameter(2, num_of_delays);
  set_parameter(3, mix_percent);
}

void EFFECT_MULTITAP_DELAY::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 100.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 2:
    pd->default_value = 1.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    // pd->upper_bound = 0.0f;
    pd->bounded_below = true;
    pd->lower_bound = 1.0f;
    pd->toggled = false;
    pd->integer = true;
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

CHAIN_OPERATOR::parameter_t EFFECT_MULTITAP_DELAY::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return dtime_msec;
  case 2:
    return dnum;
  case 3:
    return mix * 100.0;
  }
  return 0.0;
}

void EFFECT_MULTITAP_DELAY::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1:
    {
      dtime_msec = value;
      dtime = static_cast<long int>(dtime_msec * (CHAIN_OPERATOR::parameter_t)samples_per_second() / 1000);
      DBC_CHECK(buffer.size() == filled.size());
      for(int n = 0; n < static_cast<int>(buffer.size()); n++) {
	if ((dtime * dnum) > static_cast<int>(buffer[n].size())) {
	  buffer[n].resize(dtime * dnum);
	}
	delay_index[n] = dtime * dnum - 1;
	for(int m = 0; m < static_cast<int>(filled[n].size()); m++) {
	  filled[n][m] = false;
	}
      }
      break;
    }

  case 2: 
    {
      if (value != 0.0) dnum = static_cast<long int>(value);
      else dnum = 1;
      DBC_CHECK(buffer.size() == filled.size());
      for(int n = 0; n < static_cast<int>(buffer.size()); n++) {
	if ((dtime * dnum) > static_cast<int>(buffer[n].size())) {
	  buffer[n].resize(dtime * dnum);
	}
	for(int m = 0; m < static_cast<int>(filled[n].size()); m++) {
	  filled[n][m] = false;
	}
	delay_index[n] = dtime * dnum - 1;
      }
      break;
    }

  case 3:
    mix = value / 100.0;
    break;
  }
}

void EFFECT_MULTITAP_DELAY::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  EFFECT_BASE::init(insample);

  set_parameter(1, dtime_msec);

  delay_index.resize(channels(), dtime * dnum - 1);
  filled.resize(channels(), std::vector<bool> (dnum, false));
  buffer.resize(channels(), std::vector<SAMPLE_SPECS::sample_t> (dtime *
							       dnum));
}

void EFFECT_MULTITAP_DELAY::process(void)
{
  long int len = dtime * dnum;

  i.begin();
  while(!i.end()) {
    for(int n = 0; n < channels(); n++) {
      SAMPLE_SPECS::sample_t temp1 = 0.0;
      for(int nm2 = 0; nm2 < dnum; nm2++) {
	if (filled[n][nm2] == true) {
	  DBC_CHECK((delay_index[n] + nm2 * dtime) % len >= 0);
	  DBC_CHECK((delay_index[n] + nm2 * dtime) % len < len);
	  temp1 += buffer[n][(delay_index[n] + nm2 * dtime) % len];
	}
      }
      buffer[n][delay_index[n]] = *i.current(n);
      *i.current(n) = (*i.current(n) * (1.0 - mix)) + (temp1 * mix / dnum);
    
      --(delay_index[n]);
      for(int nm2 = 0; nm2 < dnum; nm2++) {
	if (delay_index[n] < len - dtime * nm2) filled[n][nm2] = true;
      }
      if (delay_index[n] == -1) delay_index[n] = len - 1;
    }
    i.next();
  }
}

EFFECT_FAKE_STEREO::EFFECT_FAKE_STEREO (CHAIN_OPERATOR::parameter_t delay_time)
{
   set_parameter(1, delay_time);
}

void EFFECT_FAKE_STEREO::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 20.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
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

CHAIN_OPERATOR::parameter_t EFFECT_FAKE_STEREO::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return dtime_msec;
  }
  return 0.0;
}

void EFFECT_FAKE_STEREO::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1:
    dtime_msec = value;
    dtime = dtime_msec * (CHAIN_OPERATOR::parameter_t)samples_per_second() / 1000;
    std::vector<std::deque<SAMPLE_SPECS::sample_t> >::iterator p = buffer.begin();
    while(p != buffer.end()) {
      if (p->size() > dtime) {
	p->resize(static_cast<unsigned int>(dtime));
      }
      ++p;
    }
    break;
  }
}

void EFFECT_FAKE_STEREO::init(SAMPLE_BUFFER* insample)
{
  l.init(insample);
  r.init(insample);

  EFFECT_BASE::init(insample);

  set_parameter(1, dtime_msec);
  buffer.resize(2);
}

void EFFECT_FAKE_STEREO::process(void)
{
  l.begin(SAMPLE_SPECS::ch_left);
  r.begin(SAMPLE_SPECS::ch_right);
  while(!l.end() && !r.end()) {
    SAMPLE_SPECS::sample_t temp_left = 0;
    SAMPLE_SPECS::sample_t temp_right = 0;
    if (buffer[SAMPLE_SPECS::ch_left].size() >= dtime && dtime > 0) {
      temp_left = buffer[SAMPLE_SPECS::ch_left].front();
      temp_right = buffer[SAMPLE_SPECS::ch_right].front();

      temp_right = (temp_left + temp_right) / 2.0;
      temp_left = (*l.current() + *r.current()) / 2.0;

      buffer[SAMPLE_SPECS::ch_left].pop_front();
      buffer[SAMPLE_SPECS::ch_right].pop_front();
    }
    else {
      temp_left = (*l.current() + *r.current()) / 2.0;
      temp_right = 0.0;
    }
    buffer[SAMPLE_SPECS::ch_left].push_back(*l.current());
    buffer[SAMPLE_SPECS::ch_right].push_back(*r.current());

    *l.current() = temp_left;
    *r.current() = temp_right;

    l.next();
    r.next();
  }
}

EFFECT_REVERB::EFFECT_REVERB (CHAIN_OPERATOR::parameter_t delay_time, int surround_mode, 
			      CHAIN_OPERATOR::parameter_t feedback_percent) 
{
  set_parameter(1, delay_time);
  set_parameter(2, surround_mode);
  set_parameter(3, feedback_percent);
}

void EFFECT_REVERB::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 20.0f;
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
    pd->default_value = 0.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = true;
    pd->upper_bound = 1.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = true;
    pd->integer = true;
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

CHAIN_OPERATOR::parameter_t EFFECT_REVERB::get_parameter(int param) const 
{
  switch (param) {
  case 1: 
    return dtime_msec;
  case 2:
    return surround;
  case 3:
    return feedback * 100.0;
  }
  return 0.0;
}

void EFFECT_REVERB::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    {
      dtime_msec = value;
      dtime = dtime_msec * (CHAIN_OPERATOR::parameter_t)samples_per_second() / 1000;
      std::vector<std::deque<SAMPLE_SPECS::sample_t> >::iterator p = buffer.begin();
      while(p != buffer.end()) {
	if (p->size() > dtime) {
	  p->resize(static_cast<unsigned int>(dtime));
	}
	++p;
      }
      break;
    }

  case 2: 
    surround = value;
    break;

  case 3: 
    feedback = value / 100.0;
    break;
  }
}

void EFFECT_REVERB::init(SAMPLE_BUFFER* insample)
{
  l.init(insample);
  r.init(insample);

  EFFECT_BASE::init(insample);

  set_parameter(1, dtime_msec);

  buffer.resize(2);
}

void EFFECT_REVERB::process(void)
{
  l.begin(SAMPLE_SPECS::ch_left);
  r.begin(SAMPLE_SPECS::ch_right);
  while(!l.end() && !r.end()) {
    SAMPLE_SPECS::sample_t temp_left = 0.0;
    SAMPLE_SPECS::sample_t temp_right = 0.0;
    if (buffer[SAMPLE_SPECS::ch_left].size() >= dtime) {
      temp_left = buffer[SAMPLE_SPECS::ch_left].front();
      temp_right = buffer[SAMPLE_SPECS::ch_right].front();
      
      if (surround == 0) {
	*l.current() = (*l.current() * (1 - feedback)) + (temp_left *  feedback);
	*r.current() = (*r.current() * (1 - feedback)) + (temp_right * feedback);
      }
      else {
	*l.current() = (*l.current() * (1 - feedback)) + (temp_right *  feedback);
	*r.current() = (*r.current() * (1 - feedback)) + (temp_left * feedback);
      }
      buffer[SAMPLE_SPECS::ch_left].pop_front();
      buffer[SAMPLE_SPECS::ch_right].pop_front();
    }
    else {
	*l.current() = (*l.current() * (1 - feedback));
	*r.current() = (*r.current() * (1 - feedback));
    }
    *l.current() = ecaops_flush_to_zero(*l.current());
    *r.current() = ecaops_flush_to_zero(*r.current());
    buffer[SAMPLE_SPECS::ch_left].push_back(*l.current());
    buffer[SAMPLE_SPECS::ch_right].push_back(*r.current());
    l.next();
    r.next();
  }
}

EFFECT_MODULATING_DELAY::EFFECT_MODULATING_DELAY(CHAIN_OPERATOR::parameter_t delay_time, 
						 long int vartime_in_samples,
						 CHAIN_OPERATOR::parameter_t feedback_percent,
						 CHAIN_OPERATOR::parameter_t lfo_freq)
{
  set_parameter(1, delay_time);
  set_parameter(2, vartime_in_samples);
  set_parameter(3, feedback_percent);
  set_parameter(4, lfo_freq);
}

void EFFECT_MODULATING_DELAY::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  switch (param) {
  case 1:
    pd->default_value = 2.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = false;
    break;
  case 2:
    pd->default_value = 20.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    // pd->upper_bound = 0.0f;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = true;
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
  case 4:
    pd->default_value = 0.4f;
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
  default: {}
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_MODULATING_DELAY::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return dtime_msec;

  case 2: 
    return vartime;

  case 3: 
    return feedback * 100.0;

  case 4: 
    return lfo.get_parameter(1);
  }
  return 0.0;
}

void EFFECT_MODULATING_DELAY::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1:
    {
      dtime_msec = value;
      dtime = static_cast<long int>(dtime_msec * (CHAIN_OPERATOR::parameter_t)samples_per_second() / 1000);
      if (dtime < 1) { 
	dtime = 1;
      }
      DBC_CHECK(buffer.size() == delay_index.size());
      DBC_CHECK(buffer.size() == filled.size());
      for(int n = 0; n < static_cast<int>(buffer.size()); n++) {
	if (dtime * 2 > static_cast<long int>(buffer[n].size())) {
	  buffer[n].resize(dtime * 2);
	}
	delay_index[n] = 0;
	filled[n] = false;
      }
      break;
    }

  case 2: 
    vartime = value;
    break;

  case 3: 
    feedback = value / 100.0;
    break;

  case 4:
    lfo.set_parameter(1, value);
    break;
  }
}

void EFFECT_MODULATING_DELAY::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);
  lfo.init();

  if (samples_per_second() > 0)
    advance_len_secs_rep = 
      ((double)insample->length_in_samples()) / 
      samples_per_second();
  else
    advance_len_secs_rep = 0;
    
  set_parameter(1, dtime_msec);

  EFFECT_BASE::init(insample);

  filled.resize(channels(), false);
  delay_index.resize(channels(), 2 * dtime);
  buffer.resize(channels(), std::vector<SAMPLE_SPECS::sample_t> (2 * dtime));
}

void EFFECT_MODULATING_DELAY::process(void)
{
  lfo_pos_secs_rep += advance_len_secs_rep;
}

void EFFECT_FLANGER::process(void)
{
  EFFECT_MODULATING_DELAY::process();

  i.begin();
  while(!i.end()) {
    SAMPLE_SPECS::sample_t temp1 = 0.0;
    parameter_t p = vartime * lfo.value(lfo_pos_secs_rep);
    if (filled[i.channel()] == true) {
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) >= 0);
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) < static_cast<long int>(buffer[i.channel()].size()));
      temp1 = buffer[i.channel()][(dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2)];
    }
    *i.current() = ecaops_flush_to_zero((*i.current() * (1.0 - feedback)) + (temp1 * feedback));
    buffer[i.channel()][delay_index[i.channel()]] = *i.current();

    ++(delay_index[i.channel()]);
    if (delay_index[i.channel()] == 2 * dtime) {
      delay_index[i.channel()] = 0;
      filled[i.channel()] = true;
    }
    i.next();
  }
}

void EFFECT_CHORUS::process(void)
{
  EFFECT_MODULATING_DELAY::process();

  i.begin();
  while(!i.end()) {
    SAMPLE_SPECS::sample_t temp1 = 0.0;
    parameter_t p = vartime * lfo.value(lfo_pos_secs_rep);
    if (filled[i.channel()] == true) {
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) >= 0);
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) < static_cast<long int>(buffer[i.channel()].size()));
      temp1 = buffer[i.channel()][(dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2)];
    }
    buffer[i.channel()][delay_index[i.channel()]] = *i.current();
    *i.current() = (*i.current() * (1.0 - feedback)) + (temp1 * feedback);

    ++(delay_index[i.channel()]);
    if (delay_index[i.channel()] == 2 * dtime) {
      delay_index[i.channel()] = 0;
      filled[i.channel()] = true;
    }
    i.next();
  }
}

void EFFECT_PHASER::process(void)
{
  EFFECT_MODULATING_DELAY::process();

  i.begin();
  while(!i.end()) {
    SAMPLE_SPECS::sample_t temp1 = 0.0;
    parameter_t p = vartime * lfo.value(lfo_pos_secs_rep);
    if (filled[i.channel()] == true) {
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) >= 0);
      DBC_CHECK((dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2) < static_cast<long int>(buffer[i.channel()].size()));
      temp1 = buffer[i.channel()][(dtime + delay_index[i.channel()] + static_cast<long int>(p)) % (dtime * 2)];
      //          cerr << "b: "
      //    	   << (delay_index[i.channel()] + static_cast<long int>(p)) % dtime
      //    	   << "," << p << ".\n";
    }
    *i.current() = ecaops_flush_to_zero(*i.current() * (1.0 - feedback) + (-1.0 * temp1 * feedback));
    buffer[i.channel()][delay_index[i.channel()]] = *i.current();

    ++(delay_index[i.channel()]);
    if (delay_index[i.channel()] == 2 * dtime) {
      delay_index[i.channel()] = 0;
      filled[i.channel()] = true;
    }
    i.next();
  }
}
