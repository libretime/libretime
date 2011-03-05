// ------------------------------------------------------------------------
// audiofx_mixing.cpp: Effects for channel mixing and routing
// Copyright (C) 1999-2002,2006,2008,2009 Kai Vehmanen
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

#include "samplebuffer_iterators.h"
#include "audiofx_mixing.h"

static EFFECT_MIXING::ch_type priv_from_make_sane(EFFECT_MIXING::ch_type channel, SAMPLE_BUFFER *insample)
{
  EFFECT_MIXING::ch_type channels =
    static_cast<EFFECT_MIXING::ch_type>(insample->number_of_channels());

  if (channel < 0)
    return 0;

  if (channel >= channels)
    return channels - 1;

  return channel;
}

static EFFECT_MIXING::ch_type priv_to_make_sane(EFFECT_MIXING::ch_type channel, SAMPLE_BUFFER *insample)
{
  if (channel < 0)
    return 0;

  return channel;
}

EFFECT_MIXING::~EFFECT_MIXING(void)
{
}

EFFECT_CHANNEL_COPY::EFFECT_CHANNEL_COPY (parameter_t from, 
					  parameter_t to)
{
  set_parameter(1, from);
  set_parameter(2, to);
}

int EFFECT_CHANNEL_COPY::output_channels(int i_channels) const
{
  int c = static_cast<int>(to_channel > from_channel ? to_channel : from_channel);
  ++c;
  return c > i_channels ? c : i_channels;
}

void EFFECT_CHANNEL_COPY::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  pd->default_value = 1;
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = true;
  pd->lower_bound = 1.0f;
  pd->toggled = false;
  pd->integer = true;
  pd->logarithmic = false;
  pd->output = false;
}

void EFFECT_CHANNEL_COPY::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    from_channel = static_cast<ch_type>(value);
    DBC_CHECK(from_channel > 0);
    from_channel--;
    break;
  case 2: 
    to_channel = static_cast<ch_type>(value);
    DBC_CHECK(to_channel > 0);
    to_channel--;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_CHANNEL_COPY::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return from_channel + 1;
  case 2: 
    return to_channel + 1;
  }
  return 0.0f;
}

void EFFECT_CHANNEL_COPY::init(SAMPLE_BUFFER *insample)
{
  f_iter.init(insample);
  t_iter.init(insample);
  from_channel = priv_from_make_sane(from_channel, insample);
  to_channel = priv_to_make_sane(to_channel, insample);
}

void EFFECT_CHANNEL_COPY::process(void)
{
  f_iter.begin(from_channel);
  t_iter.begin(to_channel);
  while(!f_iter.end() && !t_iter.end()) {
    *t_iter.current() = *f_iter.current();
    f_iter.next();
    t_iter.next();
  }
}

EFFECT_CHANNEL_MOVE::EFFECT_CHANNEL_MOVE (parameter_t from, 
					  parameter_t to)
{
  set_parameter(1, from);
  set_parameter(2, to);
}

int EFFECT_CHANNEL_MOVE::output_channels(int i_channels) const
{
  int c = static_cast<int>(to_channel > from_channel ? to_channel : from_channel);
  ++c;
  return c > i_channels ? c : i_channels;
}

void EFFECT_CHANNEL_MOVE::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  pd->default_value = 1;
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = true;
  pd->lower_bound = 1.0f;
  pd->toggled = false;
  pd->integer = true;
  pd->logarithmic = false;
  pd->output = false;
}

void EFFECT_CHANNEL_MOVE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    from_channel = static_cast<ch_type>(value);
    DBC_CHECK(from_channel > 0);
    from_channel--;
    break;
  case 2: 
    to_channel = static_cast<ch_type>(value);
    DBC_CHECK(to_channel > 0);
    to_channel--;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_CHANNEL_MOVE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return from_channel + 1;
  case 2: 
    return to_channel + 1;
  }
  return 0.0;
}


void EFFECT_CHANNEL_MOVE::init(SAMPLE_BUFFER *insample)
{
  f_iter.init(insample);
  t_iter.init(insample);
  from_channel = priv_from_make_sane(from_channel, insample);
  to_channel = priv_to_make_sane(to_channel, insample);
}

void EFFECT_CHANNEL_MOVE::process(void)
{
  f_iter.begin(from_channel);
  t_iter.begin(to_channel);
  while(!f_iter.end() && !t_iter.end()) {
    *t_iter.current() = *f_iter.current();
    if (from_channel != to_channel)
      *f_iter.current() = SAMPLE_SPECS::silent_value;
    f_iter.next();
    t_iter.next();
  }
}

EFFECT_CHANNEL_MUTE::EFFECT_CHANNEL_MUTE (parameter_t channel)
  : EFFECT_AMPLIFY_CHANNEL(0, static_cast<int>(channel))
{
  set_parameter(1, channel);
}

void EFFECT_CHANNEL_MUTE::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  pd->default_value = 1;
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = true;
  pd->lower_bound = 1.0f;
  pd->toggled = false;
  pd->integer = true;
  pd->logarithmic = false;
  pd->output = false;
}

void EFFECT_CHANNEL_MUTE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  EFFECT_MIXING::ch_type from_channel;

  switch (param) {
  case 1: 
    from_channel = static_cast<EFFECT_MIXING::ch_type>(value);
    DBC_CHECK(from_channel > 0);
    EFFECT_AMPLIFY_CHANNEL::set_parameter(2, from_channel);
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_CHANNEL_MUTE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return EFFECT_AMPLIFY_CHANNEL::get_parameter(2);
  }
  return 0.0;
}

EFFECT_MIX_TO_CHANNEL::EFFECT_MIX_TO_CHANNEL (parameter_t to)
{
  set_parameter(1, to);
}

int EFFECT_MIX_TO_CHANNEL::output_channels(int i_channels) const
{
  int c = static_cast<int>(to_channel);
  ++c;
  return(c > i_channels ? c : i_channels);
}

void EFFECT_MIX_TO_CHANNEL::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  pd->default_value = 1;
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = true;
  pd->lower_bound = 1.0f;
  pd->toggled = false;
  pd->integer = true;
  pd->logarithmic = false;
  pd->output = false;
}

void EFFECT_MIX_TO_CHANNEL::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) {
  switch (param) {
  case 1: 
    to_channel = static_cast<ch_type>(value);
    DBC_CHECK(to_channel > 0);
    to_channel--;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_MIX_TO_CHANNEL::get_parameter(int param) const
{ 
  switch (param) {
  case 1: 
    return to_channel + 1;
  }
  return 0.0f;
}

void EFFECT_MIX_TO_CHANNEL::init(SAMPLE_BUFFER *insample)
{
  i.init(insample);
  t_iter.init(insample);
  channels = insample->number_of_channels();
  to_channel = priv_to_make_sane(to_channel, insample);
}

void EFFECT_MIX_TO_CHANNEL::process(void)
{
  i.begin();
  t_iter.begin(to_channel);
  while(!t_iter.end() && !i.end()) {
    sum = SAMPLE_SPECS::silent_value;
    for (int n = 0; n < channels; n++) {
      if (i.end()) break;
      sum += (*i.current(n));
    }
    *t_iter.current() = sum / channels;
    i.next();
    t_iter.next();
  }
}

EFFECT_CHANNEL_ORDER::EFFECT_CHANNEL_ORDER (void)
  : sbuf_repp(0), 
    out_channels_rep(0)
{
}

EFFECT_CHANNEL_ORDER* EFFECT_CHANNEL_ORDER::clone(void) const
{
  EFFECT_CHANNEL_ORDER *obj =
    new EFFECT_CHANNEL_ORDER();
  /* note: obj->sbuf_repp is shared but this is ok */
  return obj;
}

int EFFECT_CHANNEL_ORDER::output_channels(int i_channels) const
{
  return out_channels_rep;
}

void EFFECT_CHANNEL_ORDER::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  /* these apply for all params */
  pd->default_value = 1;
  pd->description = "channel";
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = true;
  pd->lower_bound = 1.0f;
  pd->toggled = false;
  pd->integer = true;
  pd->logarithmic = false;
  pd->output = false;
}

void EFFECT_CHANNEL_ORDER::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  int src_ch = static_cast<int>(value);
  int dst_ch = param;

  if (dst_ch > 0) {
    if (dst_ch > static_cast<int>(chsrc_map_rep.size())) {
      chsrc_map_rep.resize(dst_ch);
    }

    chsrc_map_rep[dst_ch - 1] = src_ch - 1;

    /* step: reset highest non-zero channel */
    int n;
    for(n = chsrc_map_rep.size() - 1;
	n >= 0; n--) {
      if (chsrc_map_rep[n] >= 0) 
	break;
    }
    out_channels_rep = n + 1;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_CHANNEL_ORDER::get_parameter(int param) const
{

  /* note: we ignore zero-src channel at the end of
   *       chsrc_map_rep to avoid infinite loops in
   *       e.g. ECA_OBJECT_FACTORY */
  if (param > 0 &&
      param <= out_channels_rep) {
    
    DBC_CHECK(out_channels_rep <= static_cast<int>(chsrc_map_rep.size()));

    /* return 1...N */
    return chsrc_map_rep[param - 1] + 1;
  }

  return 0.0;
}

std::string EFFECT_CHANNEL_ORDER::parameter_names(void) const 
{
  std::string params;
  int ch = 0;
  while(ch < out_channels_rep) {
    params += "src-ch-" + kvu_numtostr(ch + 1);
    ++ch;
    if (ch != out_channels_rep)
      params += ",";
  }
  return params;
  //return param_names_rep;
}

void EFFECT_CHANNEL_ORDER::init(SAMPLE_BUFFER *insample)
{
  sbuf_repp = insample;
  bouncebuf_rep.number_of_channels(sbuf_repp->number_of_channels());
  bouncebuf_rep.length_in_samples(sbuf_repp->length_in_samples());

  f_iter.init(&bouncebuf_rep);
  t_iter.init(insample);
}

void EFFECT_CHANNEL_ORDER::release(void)
{
  sbuf_repp = 0;
}

void EFFECT_CHANNEL_ORDER::process(void)
{
  /* step: copy input buffer to a temporary buffer */
  bouncebuf_rep.copy_all_content(*sbuf_repp);

  /* step: route channels bouncebuf_rep -> sbuf_repp */
  for(int dst_ch = 0; dst_ch < out_channels_rep; dst_ch++) {
    int src_ch = chsrc_map_rep[dst_ch];

    /* for development use only */
#if 0
    std::fprintf(stderr, "%sout#%d <-- in#%d (avail in=%d, out=%d)\n",
		 dst_ch == 0 ? "---\n" : "",
		 dst_ch, src_ch,
		 bouncebuf_rep.number_of_channels(),
		 sbuf_repp->number_of_channels());
#endif

    if (src_ch >= 0 && src_ch < bouncebuf_rep.number_of_channels()) {
      f_iter.begin(src_ch);
      t_iter.begin(dst_ch);
      while(!f_iter.end() && !t_iter.end()) {
	*t_iter.current() = *f_iter.current();
	f_iter.next();
	t_iter.next();
      }
    }
    else {
      sbuf_repp->make_silent(dst_ch);
    }
  }

  /* step: make sure output buf has exactly N channels */
  sbuf_repp->number_of_channels(out_channels_rep);
}
