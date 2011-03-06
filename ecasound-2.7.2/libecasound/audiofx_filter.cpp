// ------------------------------------------------------------------------
// audiofx_filter.cpp: Routines for filter effects.
// Copyright (C) 1999,2004 Kai Vehmanen
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

#include <cmath>

#include <kvu_utils.h>

#include "samplebuffer_iterators.h"
#include "sample-ops_impl.h"
#include "eca-logger.h"
#include "audiofx_filter.h"


EFFECT_FILTER::~EFFECT_FILTER(void)
{
}

EFFECT_BANDPASS::EFFECT_BANDPASS (CHAIN_OPERATOR::parameter_t centerf, CHAIN_OPERATOR::parameter_t w)
{
  /* to avoid accessing uninitialized data */
  width = 1;
  center = 1;
  C = 1;

  set_parameter(1, centerf);
  set_parameter(2, w);
}

void EFFECT_BANDPASS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    center = value;
    D = 2 * cos(2 * M_PI * center / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    b[0] = -C * D * a[0];
    break;
  case 2: 
    if (value != 0) width = value;
    else width = center / 2;
    C = 1.0 / tan(M_PI * width / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    D = 2 * cos(2 * M_PI * center / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    a[0] = 1.0 / (1.0 + C);
    a[1] = 0.0;
    a[2] = -a[0];
    b[0] = -C * D * a[0];
    b[1] = (C - 1.0) * a[0];
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_BANDPASS::get_parameter(int param) const
{ 
  switch (param) {
  case 1: 
    return center;
  case 2: 
    return width;
  }
  return 0.0;
}

EFFECT_BANDREJECT::EFFECT_BANDREJECT (CHAIN_OPERATOR::parameter_t centerf, CHAIN_OPERATOR::parameter_t w)
{
  set_parameter(1, centerf);
  set_parameter(2, w);
}

void EFFECT_BANDREJECT::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    center = value;
    D = 2 * cos(2 * M_PI * center / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    a[1] = -D * a[0];
    b[0] = a[1];
    break;
  case 2: 
    if (value != 0) width = value;
    else width = center / 2;
    C = tan(M_PI * width / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    a[0] = 1.0 / (1.0 + C);
    a[1] = -D * a[0];
    a[2] = a[0];
    b[0] =  a[1];
    b[1] = (1.0 - C) * a[0];
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_BANDREJECT::get_parameter(int param) const
{ 
  switch (param) {
  case 1: 
    return center;
  case 2: 
    return width;
  }
  return 0.0;
}

void EFFECT_BW_FILTER::init(SAMPLE_BUFFER *insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  sin.resize(insample->number_of_channels(), std::vector<SAMPLE_SPECS::sample_t> (2));
  sout.resize(insample->number_of_channels(), std::vector<SAMPLE_SPECS::sample_t> (2));
}

void EFFECT_BW_FILTER::process(void)
{
  i.begin();
  while(!i.end()) {
    outputSample = ecaops_flush_to_zero(a[0] * (*i.current()) + 
					a[1] * sin[i.channel()][0] + 
					a[2] * sin[i.channel()][1] - 
					b[0] * sout[i.channel()][0] - 
					b[1] * sout[i.channel()][1]);
    sin[i.channel()][1] = sin[i.channel()][0];
    sin[i.channel()][0] = *i.current();

    sout[i.channel()][1] = sout[i.channel()][0];
    sout[i.channel()][0] = outputSample;

    *i.current() = outputSample;
    i.next();
  }
}

void EFFECT_BW_FILTER::process_notused(SAMPLE_BUFFER* sbuf)
{
//    sbuf->first();
//    while(sbuf->is_readable()) {
//      outputSample = *sbuf->current_sample() * a[0]
//                     + sin[0] * a[1]
//                     + sin[1] * a[2]  
//                     - sout[0] * b[0]
//                     - sout[1] * b[1];

//      sin[1] = sin[0];
//      sin[0] = *(sbuf->current_sample());

//      sout[1] = sout[0];
//      sout[0] = outputSample;
  
//      sbuf->current_sample()->operator=(outputSample);
//      sbuf->next();
//    }
}

void EFFECT_BW_FILTER::init_values(void)
{
//    for(int j = 0; j < 2;j++) {
//      sin[j].sample[SAMPLE_BUFFER::ch_left] = 0.0;
//      sin[j].sample[SAMPLE_BUFFER::ch_right] = 0.0;
//      sout[j].sample[SAMPLE_BUFFER::ch_left] = 0.0;
//      sout[j].sample[SAMPLE_BUFFER::ch_right] = 0.0;
//    }
}

EFFECT_HIGHPASS::EFFECT_HIGHPASS (CHAIN_OPERATOR::parameter_t cutoff)
{
  set_parameter(1, cutoff);
}

void EFFECT_HIGHPASS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    cutOffFreq = value;
    C = tan(M_PI * cutOffFreq / (CHAIN_OPERATOR::parameter_t)samples_per_second());
    a[0] = 1.0 / (1.0 + sqrt(2.0) * C + C * C);
    a[1] = -2.0 * a[0];
    a[2] = a[0];
    b[0] = 2 * (C * C - 1.0) * a[0];
    b[1] = (1.0 - sqrt(2.0) * C + C * C) * a[0];
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_HIGHPASS::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return cutOffFreq;
  }
  return 0.0;
}

EFFECT_ALLPASS_FILTER::EFFECT_ALLPASS_FILTER (void)
  : feedback_gain(0.0),
    D(0.0)
{

}

void EFFECT_ALLPASS_FILTER::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    D = value;
//    assert(inbuf.size() == outbuf.size());
    for(int n = 0; n < static_cast<int>(inbuf.size()); n++) {
      if (inbuf[n].size() > D) inbuf[n].resize(static_cast<unsigned int>(D));
//      if (outbuf[n].size() > D) inbuf[n].resize(D);
    }
    break;
  case 2: 
    feedback_gain = value / 100.0;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_ALLPASS_FILTER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return D;
  case 2: 
    return feedback_gain * 100.0;
  }
  return 0.0;
}

void EFFECT_ALLPASS_FILTER::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  inbuf.resize(insample->number_of_channels());
  //  outbuf.resize(insample->number_of_channels());
}

void EFFECT_ALLPASS_FILTER::process(void)
{
  i.begin();
  while(!i.end()) {
    if (inbuf[i.channel()].size() >= D) {
      inbuf[i.channel()].push_back(*i.current());

      //      *i.current() = -feedback_gain * (*i.current()) +
      //	             inbuf[i.channel()].front() +
      //	             feedback_gain * outbuf[i.channel()].front();

      *i.current() = ecaops_flush_to_zero(-feedback_gain * (*i.current()) +
					  (feedback_gain * inbuf[i.channel()].front() +
					   *i.current()) * 
					  (1.0 - feedback_gain * feedback_gain));

      //      feedback_gain * outbuf[i.channel()].front();
      //      outbuf[i.channel()].push_back(*i.current());

      inbuf[i.channel()].pop_front();
      // outbuf[i.channel()].pop_front();
    } 
    else {
      inbuf[i.channel()].push_back(*i.current());
      *i.current() = ecaops_flush_to_zero(*i.current() * (1.0 - feedback_gain));
      // outbuf[i.channel()].push_back(*i.current());
    }
    i.next();
  }
}

EFFECT_COMB_FILTER::EFFECT_COMB_FILTER (int delay_in_samples, CHAIN_OPERATOR::parameter_t radius)
{
  set_parameter(1, (CHAIN_OPERATOR::parameter_t)delay_in_samples);
  set_parameter(2, radius);
}

void EFFECT_COMB_FILTER::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    {
      C = value;
      std::vector<std::deque<SAMPLE_SPECS::sample_t> >::iterator p = buffer.begin();
      while(p != buffer.end()) {
	if (p->size() > C) {
	  p->resize(static_cast<unsigned int>(C));
	}
	++p;
      }
      break;
    }

  case 2: 
    D = value;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_COMB_FILTER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return C;
  case 2: 
    return D;
  }
  return 0.0;
}

void EFFECT_COMB_FILTER::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  buffer.resize(insample->number_of_channels());
}

void EFFECT_COMB_FILTER::process(void)
{
  i.begin();
  while(!i.end()) {
    if (buffer[i.channel()].size() >= C) {
      *i.current() = (*i.current())  + (pow(D, C) *
					buffer[i.channel()].front());
      buffer[i.channel()].push_back(*i.current());
      buffer[i.channel()].pop_front();
    } 
    else {
      buffer[i.channel()].push_back(*i.current());
    }
    i.next();
  }
}

EFFECT_INVERSE_COMB_FILTER::EFFECT_INVERSE_COMB_FILTER (int delay_in_samples, CHAIN_OPERATOR::parameter_t radius)
{
  // 
  // delay in number of samples
  // circle radius
  //
  set_parameter(1, (CHAIN_OPERATOR::parameter_t)delay_in_samples);
  set_parameter(2, radius);
}

void EFFECT_INVERSE_COMB_FILTER::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    C = value;
    break;
  case 2: 
    D = value;
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_INVERSE_COMB_FILTER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return C;
  case 2: 
    return D;
  }
  return 0.0;
}

void EFFECT_INVERSE_COMB_FILTER::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  buffer.resize(insample->number_of_channels());
  laskuri.resize(insample->number_of_channels(), parameter_t(0.0));
}

void EFFECT_INVERSE_COMB_FILTER::process(void)
{
  i.begin();
  while(!i.end()) {
    buffer[i.channel()].push_back(*i.current());
    
    if (laskuri[i.channel()] >= C) {
      *i.current() = (*i.current())  - (pow(D, C) *
					buffer[i.channel()].front());
      buffer[i.channel()].pop_front();
    } 
    else {
      laskuri[i.channel()]++;
    }
    i.next();
  }
}

EFFECT_LOWPASS::EFFECT_LOWPASS (CHAIN_OPERATOR::parameter_t cutoff)
{
  set_parameter(1, cutoff);
}

void EFFECT_LOWPASS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value) {
  switch (param) {
  case 1: 
    set_cutoff(value, samples_per_second());
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_LOWPASS::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return cutOffFreq;
  }
  return 0.0;
}

void EFFECT_LOWPASS::set_cutoff(CHAIN_OPERATOR::parameter_t value, long int srate)
{
  cutOffFreq = value;
  C = 1.0 / tan(M_PI * cutOffFreq / (CHAIN_OPERATOR::parameter_t)srate);
  a[0] = 1.0 / (1.0 + sqrt(2.0) * C + C * C);
  a[1] = 2.0 * a[0];
  a[2] = a[0];
  b[0] = 2 * (1.0 - C * C) * a[0];
  b[1] = (1.0 - sqrt(2.0) * C + C * C) * a[0];
}

EFFECT_LOWPASS_SIMPLE::EFFECT_LOWPASS_SIMPLE (CHAIN_OPERATOR::parameter_t cutoff)
{
  set_parameter(1, cutoff);
}

void EFFECT_LOWPASS_SIMPLE::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    cutOffFreq = value;
    A = 2.0 * M_PI * cutOffFreq / samples_per_second();
    B = exp(-A / samples_per_second());
    break;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_LOWPASS_SIMPLE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return cutOffFreq;
  }
  return 0.0;
}

void EFFECT_LOWPASS_SIMPLE::init(SAMPLE_BUFFER *insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  outhist.resize(insample->number_of_channels());
  tempin.resize(insample->number_of_channels());
  temphist.resize(insample->number_of_channels());
}

void EFFECT_LOWPASS_SIMPLE::process(void)
{
  i.begin();
  while(!i.end()) {
    tempin[i.channel()] = *i.current();
    temphist[i.channel()] = outhist[i.channel()];
    outhist[i.channel()] = tempin[i.channel()];
    
    tempin[i.channel()] *= A * 0.5;
    temphist[i.channel()] *= B * 0.5;

    *i.current() = ecaops_flush_to_zero(tempin[i.channel()] + temphist[i.channel()]);

    i.next();
  }
}

EFFECT_RESONANT_BANDPASS::EFFECT_RESONANT_BANDPASS (CHAIN_OPERATOR::parameter_t centerf,
						    CHAIN_OPERATOR::parameter_t w) 
{
  /* to avoid accessing uninitialized data */
  width = 1;
  center = 1;

  set_parameter(1, centerf);
  set_parameter(2, w);
}

void EFFECT_RESONANT_BANDPASS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    center = value;
    break;
  case 2: 
    if (value != 0) width = value;
    else width = center / 2.0;
    break;
  }
  //  R = 1.0 - M_PI * width / (CHAIN_OPERATOR::parameter_t)samples_per_second();
  //  R = 1.0 - ((width / (CHAIN_OPERATOR::parameter_t)samples_per_second()) / 2.0);
  R = 1.0 - M_PI * (width / (CHAIN_OPERATOR::parameter_t)samples_per_second());
  c = R * R;
  pole_angle = (((2.0 * R) / (1.0 + c)) * cos((center / 
					       (CHAIN_OPERATOR::parameter_t)samples_per_second() * 2.0 * M_PI)));
  pole_angle = acos(pole_angle);
  a = (1.0 - c) * sin(pole_angle);
  b = 2.0 * R * cos(pole_angle);
}

CHAIN_OPERATOR::parameter_t EFFECT_RESONANT_BANDPASS::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return center;
  case 2: 
    return width;
  }
  return 0.0;
}

void EFFECT_RESONANT_BANDPASS::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  outhist1.resize(insample->number_of_channels());
  outhist2.resize(insample->number_of_channels());
}

void EFFECT_RESONANT_BANDPASS::process(void)
{
  i.begin();
  while(!i.end()) {
    *i.current() = ecaops_flush_to_zero(a * (*i.current()) +
					b * outhist1[i.channel()] -
					c * outhist2[i.channel()]);
  
    outhist2[i.channel()] = outhist1[i.channel()];
    outhist1[i.channel()] = *i.current();

    i.next();
  }
}

EFFECT_RESONANT_LOWPASS::EFFECT_RESONANT_LOWPASS (CHAIN_OPERATOR::parameter_t co, CHAIN_OPERATOR::parameter_t
						  res, CHAIN_OPERATOR::parameter_t g) 
  : ProtoCoef(2), Coef(2)
{
  cutoff = co;
  Q = res;

  gain_orig = gain = g;

  laskuri = 0.0;
    
  pi = 4.0 * atan(1.0);
    
  // ---
  // Setup filter s-domain coefficients
  // ---

  ProtoCoef[0].a0 = 1.0;
  ProtoCoef[0].a1 = 0;
  ProtoCoef[0].a2 = 0;
  ProtoCoef[0].b0 = 1.0;
  ProtoCoef[0].b1 = 0.765367 / Q;      // Divide by resonance or Q
  ProtoCoef[0].b2 = 1.0;

  ProtoCoef[1].a0 = 1.0;
  ProtoCoef[1].a1 = 0;
  ProtoCoef[1].a2 = 0;
  ProtoCoef[1].b0 = 1.0;
  ProtoCoef[1].b1 = 1.847759 / Q;      // Divide by resonance or Q
  ProtoCoef[1].b2 = 1.0;

  szxform(0);
  szxform(1);
}

void EFFECT_RESONANT_LOWPASS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    cutoff = value;
    break;
  case 2: 
    Q = value;
    break;
  case 3: 
    gain_orig = value;
    break;
  }
  refresh_values();
}

CHAIN_OPERATOR::parameter_t EFFECT_RESONANT_LOWPASS::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return cutoff;
  case 2: 
    return Q;
  case 3: 
    return gain_orig;
  }
  return 0.0;
}

void EFFECT_RESONANT_LOWPASS::refresh_values(void)
{
  if (cutoff == 0.0) cutoff = 0.1;
    
  gain = gain_orig;
    
  //    ProtoCoef[0].a0 = 1.0;
  ProtoCoef[0].a1 = 0;
  ProtoCoef[0].a2 = 0;
  //    ProtoCoef[0].b0 = 1.0;
  ProtoCoef[0].b1 = 0.765367 / Q;      // Divide by resonance or Q
  ProtoCoef[0].b2 = 1.0;

  //    ProtoCoef[1].a0 = 1.0;
  ProtoCoef[1].a1 = 0;
  ProtoCoef[1].a2 = 0;
  //    ProtoCoef[1].b0 = 1.0;
  ProtoCoef[1].b1 = 1.847759 / Q;      // Divide by resonance or Q
  ProtoCoef[1].b2 = 1.0;

  szxform(0);
  szxform(1);
}

void EFFECT_RESONANT_LOWPASS::szxform(int section)
{
  wp = 2.0 * (CHAIN_OPERATOR::parameter_t)samples_per_second() * tan(pi * cutoff / (CHAIN_OPERATOR::parameter_t)samples_per_second());

  // ---
  // a0 and b0 are presumed to be 1, so...

  ProtoCoef[section].a2 = ProtoCoef[section].a2 / (wp * wp);
  ProtoCoef[section].a1 = ProtoCoef[section].a1 / wp;

  ProtoCoef[section].b2 = ProtoCoef[section].b2 / (wp * wp);
  ProtoCoef[section].b1 = ProtoCoef[section].b1 / wp;

  // ---
  // alpha (Numerator in s-domain)
  ad = 4.0 * ProtoCoef[section].a2 * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second() + 2.0 * ProtoCoef[section].a1
    * (CHAIN_OPERATOR::parameter_t)samples_per_second() + ProtoCoef[section].a0;
  // ---
  // beta (Denominator in s-domain)
  bd = 4.0 * ProtoCoef[section].b2 * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second() + 2.0 * ProtoCoef[section].b1
    * (CHAIN_OPERATOR::parameter_t)samples_per_second() + ProtoCoef[section].b0;

  // ---
  /* update gain constant for this section */
  gain *= ad/bd;

  // ---
  // Denominator
  Coef[section].A = (2.0 * ProtoCoef[section].b0 - 8.0 * ProtoCoef[section].b2
		     * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second()) / bd;
  // ---
  // beta1
  Coef[section].B = (4.0 * ProtoCoef[section].b2 * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second() - 2.0 * ProtoCoef[section].b1
		     * (CHAIN_OPERATOR::parameter_t)samples_per_second() + ProtoCoef[section].b0) / bd;
  // ---
  // beta2

  // ---
  // Nominator
  Coef[section].C = (2.0 * ProtoCoef[section].a0 - 8.0 * ProtoCoef[section].a2
		     * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second()) / ad;
  // ---
  // alpha1
  Coef[section].D = (4.0 * ProtoCoef[section].a2 * (CHAIN_OPERATOR::parameter_t)samples_per_second() * (CHAIN_OPERATOR::parameter_t)samples_per_second() - 2.0
		     * ProtoCoef[section].a1 * (CHAIN_OPERATOR::parameter_t)samples_per_second() + ProtoCoef[section].a0) / ad;
  // ---
  // alpha2
}

void EFFECT_RESONANT_LOWPASS::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);

  set_channels(insample->number_of_channels());

  outhist0.resize(insample->number_of_channels());
  outhist1.resize(insample->number_of_channels());
  outhist2.resize(insample->number_of_channels());
  outhist3.resize(insample->number_of_channels());

  newhist0.resize(insample->number_of_channels());
  newhist1.resize(insample->number_of_channels());
}

void EFFECT_RESONANT_LOWPASS::process(void)
{
  i.begin();
  while(!i.end()) {
    *i.current() = (*i.current()) * gain;

    // first section:
    // --------------
    
    // poles:
    *i.current() =  (*i.current()) - outhist0[i.channel()] * Coef[0].A;
    newhist0[i.channel()] = ecaops_flush_to_zero((*i.current()) - outhist1[i.channel()] * Coef[0].B);
        
    // zeros:
    *i.current() = newhist0[i.channel()] + outhist0[i.channel()] * Coef[0].C;
    *i.current() = (*i.current()) +  outhist1[i.channel()] * Coef[0].D;
    
    outhist1[i.channel()] = outhist0[i.channel()];
    outhist0[i.channel()] = newhist0[i.channel()];
        
    // second section:
    // --------------
    
    // poles:
    *i.current() =  (*i.current()) - outhist2[i.channel()] * Coef[1].A;
    newhist1[i.channel()] = ecaops_flush_to_zero((*i.current()) - outhist3[i.channel()] * Coef[1].B);
       
    // zeros:
    *i.current() = newhist1[i.channel()] + outhist2[i.channel()] * Coef[1].C;
    *i.current() = (*i.current()) +  outhist3[i.channel()] * Coef[1].D;
    
    outhist3[i.channel()] = outhist2[i.channel()];
    outhist2[i.channel()] = newhist1[i.channel()];

    i.next();
  }
}

//  EFFECT_RESONANT_LOWPASS::EFFECT_RESONANT_LOWPASS (const
//  						  EFFECT_RESONANT_LOWPASS& x) 
//    : outhist(4), newhist(2), ProtoCoef(2), Coef(2)
//  {
//    outhist = x.outhist;
//    newhist = x.newhist;
//    for(vector<BIQUAD>::size_type p = 0; p != x.ProtoCoef.size(); p++) {
//      ProtoCoef[p].a0 = x.ProtoCoef[p].a0;
//      ProtoCoef[p].a1 = x.ProtoCoef[p].a1;
//      ProtoCoef[p].a2 = x.ProtoCoef[p].a2;
//      ProtoCoef[p].b0 = x.ProtoCoef[p].b0;
//      ProtoCoef[p].b1 = x.ProtoCoef[p].b1;
//      ProtoCoef[p].b2 = x.ProtoCoef[p].b2;
//      ++p;
//    }
//    for(vector<BIQUAD>::size_type p = 0; p != x.Coef.size(); p++) {
//      Coef[p].A = x.Coef[p].A;
//      Coef[p].B = x.Coef[p].B;
//      Coef[p].C = x.Coef[p].C;
//      Coef[p].D = x.Coef[p].D;
//      ++p;
//    }
//    cutoff = x.cutoff;
//    Q = x.Q;
//    gain = x.gain;
//    gain_orig = x.gain_orig;
//    pi = x.pi;
//    laskuri = x.laskuri;
//    ad = x.ad;
//    bd = x.bd;
//    wp = x.wp;
//  }

EFFECT_RESONATOR::EFFECT_RESONATOR (CHAIN_OPERATOR::parameter_t centerf, CHAIN_OPERATOR::parameter_t w) 
  : cona(1), conb(2) 
{
  /* to avoid accessing uninitialized data */
  width = 1;
  center = 1;

  set_parameter(1, centerf);
  set_parameter(2, w);
}

void EFFECT_RESONATOR::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  switch (param) {
  case 1: 
    center = value;
    break;
  case 2: 
    if (value != 0) width = value;
    else width = center / 2;
    break;
  }
  conb[1] = exp(-(2 * M_PI) * (width / (CHAIN_OPERATOR::parameter_t)samples_per_second()));
  conb[0] = (-4.0 * conb[1]) / (1.0 + conb[1]) * cos(2 * M_PI * (center / (CHAIN_OPERATOR::parameter_t)samples_per_second()));
  cona[0] = (1.0 - conb[1]) * sqrt(1.0 - (conb[0] * conb[0]) / (4.0 * conb[1]));
}

CHAIN_OPERATOR::parameter_t EFFECT_RESONATOR::get_parameter(int param) const { 
  switch (param) {
  case 1: 
    return center;
  case 2: 
    return width;
  }
  return 0.0;
}

void EFFECT_RESONATOR::init(SAMPLE_BUFFER* insample) {
  i.init(insample);

  set_channels(insample->number_of_channels());

  saout0.resize(insample->number_of_channels());
  saout1.resize(insample->number_of_channels());
}

void EFFECT_RESONATOR::process(void)
{
  i.begin();
  while(!i.end()) {
    *i.current() = cona[0] * (*i.current()) -
                   conb[0] * saout0[i.channel()] -
		   conb[1] * saout1[i.channel()];
    
    saout1[i.channel()] = saout0[i.channel()];
    saout0[i.channel()] = ecaops_flush_to_zero(*i.current());
				 
    i.next();
  }
}
