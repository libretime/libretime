// ------------------------------------------------------------------------
// audiofx_analysis.cpp: Classes for signal analysis
// Copyright (C) 1999-2002,2008 Kai Vehmanen
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

#include <string>
#include <cmath>

#include <kvu_dbc.h>
#include <kvu_message_item.h>
#include <kvu_numtostr.h>

#include "samplebuffer_iterators.h"
#include "audiofx_analysis.h"
#include "audiofx_amplitude.h"

#include "eca-logger.h"
#include "eca-error.h"

using namespace std;

static string priv_align_right(const string txt, int width, char padchar)
{
  string res;
  int pad = width - static_cast<int>(txt.size());
  if (pad > 0) {
    res.resize(pad, padchar);
  }
  return res + txt;
}

struct bucket {
  const char *name;
  SAMPLE_SPECS::sample_t threshold;
};

#define BUCKET_ENTRY_DB(x) \
 { #x, EFFECT_AMPLITUDE::db_to_linear(x) }

static struct bucket bucket_table[] = 
  { BUCKET_ENTRY_DB(3),
    BUCKET_ENTRY_DB(0),
    BUCKET_ENTRY_DB(-0.1),
    BUCKET_ENTRY_DB(-3),
    BUCKET_ENTRY_DB(-6),
    BUCKET_ENTRY_DB(-10),
    BUCKET_ENTRY_DB(-20),
    BUCKET_ENTRY_DB(-30),
    BUCKET_ENTRY_DB(-60),
    { "-inf", -1 }
  };

EFFECT_ANALYSIS::~EFFECT_ANALYSIS(void)
{
}


EFFECT_VOLUME_BUCKETS::EFFECT_VOLUME_BUCKETS (void)
{
  reset_all_stats();
  int res = pthread_mutex_init(&lock_rep, NULL);
  DBC_CHECK(res == 0);
}

EFFECT_VOLUME_BUCKETS::~EFFECT_VOLUME_BUCKETS (void)
{
}

void EFFECT_VOLUME_BUCKETS::status_entry(const std::vector<unsigned long int>& buckets, std::string& otemp) const
{
  /* note: is called with 'lock_rep' taken */

  for(unsigned int n = 0; n < buckets.size(); n++) {
    string samples = kvu_numtostr(buckets[n]);

    otemp += priv_align_right(samples, 8, '_');

#if NEVER_USED_PRINT_PERCENTAGE
    otemp += " (";
    otemp += priv_align_right(kvu_numtostr(100.0f *  
					   buckets[n] / 
					   num_of_samples[n], 2),
			      6, '_');
    otemp += "%)";
#endif
    if (n != buckets.size())
      otemp += " ";
  }
} 


void EFFECT_VOLUME_BUCKETS::reset_all_stats(void)
{
  reset_period_stats();
  max_pos = max_neg = 0.0f;
}

void EFFECT_VOLUME_BUCKETS::reset_period_stats(void)
{
  for(unsigned int nm = 0; nm < pos_samples_db.size(); nm++)
    for(unsigned int ch = 0; ch < pos_samples_db[nm].size(); ch++)
      pos_samples_db[nm][ch] = 0;

  for(unsigned int nm = 0; nm < neg_samples_db.size(); nm++)
    for(unsigned int ch = 0; ch < neg_samples_db[nm].size(); ch++)
      neg_samples_db[nm][ch] = 0;

  for(unsigned int nm = 0; nm < num_of_samples.size(); nm++)
    num_of_samples[nm] = 0;
}

string EFFECT_VOLUME_BUCKETS::status(void) const
{
  int res = pthread_mutex_lock(&lock_rep);
  DBC_CHECK(res == 0);

  std::string status_str;

  status_str = "-- Amplitude statistics --\n";
  status_str += "Pos/neg, count,(%), ch1...n";

  for(unsigned j = 0; j < pos_samples_db.size(); j++) {
    status_str += std::string("\nPos ")
      + priv_align_right(bucket_table[j].name, 4, ' ')
      + "dB: ";
    status_entry(pos_samples_db[j], status_str);
  }

  for(unsigned int j = neg_samples_db.size(); j > 0; j--) {
    DBC_CHECK(j >= 0);
    status_str += std::string("\nNeg ")
      + priv_align_right(bucket_table[j-1].name, 4, ' ')
      + "dB: ";
    status_entry(neg_samples_db[j-1], status_str);
  }

  status_str += std::string("\nTotal.....: ");
  status_entry(num_of_samples, status_str);
  status_str += "\n";

  status_str += "(audiofx) Peak amplitude: pos=" + kvu_numtostr(max_pos,5) + " neg=" + kvu_numtostr(max_neg,5) + ".\n";
  status_str += "(audiofx) Max gain without clipping: " + kvu_numtostr(max_multiplier(),5) + ".\n";

  status_str += "(audiofx) -- End of statistics --\n";

  res = pthread_mutex_unlock(&lock_rep);
  DBC_CHECK(res == 0);

  return status_str;
}

void EFFECT_VOLUME_BUCKETS::parameter_description(int param, 
						  struct PARAM_DESCRIPTION *pd) const
{
  switch(param) {
  case 1: 
    pd->default_value = 0;
    pd->description = get_parameter_name(param);
    pd->bounded_above = true;
    pd->upper_bound = 1.0;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = true;
    pd->integer = true;
    pd->logarithmic = false;
    pd->output = false;
    break;

  case 2: 
    pd->default_value = 1.0f;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    pd->upper_bound = 0.0f;
    pd->bounded_below = false;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = true;
    break;
  }
}

void EFFECT_VOLUME_BUCKETS::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  return;
}

CHAIN_OPERATOR::parameter_t EFFECT_VOLUME_BUCKETS::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    /* note: always enabled since 2.7.0, but keeping the parameter 
     *       still for backwards compatibility */
    return 1.0f;
    
  case 2:
    return max_multiplier();
  }
  return 0.0;
}

CHAIN_OPERATOR::parameter_t EFFECT_VOLUME_BUCKETS::max_multiplier(void) const
{
  parameter_t k;
  SAMPLE_SPECS::sample_t max_peak = max_pos;

  if (max_neg > max_pos) 
    max_peak = max_neg;
  if (max_peak != 0.0f) 
    k = SAMPLE_SPECS::max_amplitude / max_peak;
  else 
    k = 0.0f;

  return k;
}

void EFFECT_VOLUME_BUCKETS::init(SAMPLE_BUFFER* insample)
{
  int res = pthread_mutex_lock(&lock_rep);
  DBC_CHECK(res == 0);
  
  i.init(insample);
  set_channels(insample->number_of_channels());
  DBC_CHECK(channels() == insample->number_of_channels());
  num_of_samples.resize(insample->number_of_channels(), 0);

  int entries = sizeof(bucket_table) / sizeof(struct bucket);

  pos_samples_db.resize(entries, std::vector<unsigned long int> (channels()));
  neg_samples_db.resize(entries, std::vector<unsigned long int> (channels()));

  reset_all_stats();
  
  res = pthread_mutex_unlock(&lock_rep);
  DBC_CHECK(res == 0);

  EFFECT_ANALYSIS::init(insample);
}

void EFFECT_VOLUME_BUCKETS::process(void)
{
  DBC_CHECK(static_cast<int>(num_of_samples.size()) == channels());

  int res = pthread_mutex_trylock(&lock_rep);
  if (res == 0) {
    i.begin();
    while(!i.end()) {

      DBC_CHECK(num_of_samples.size() > static_cast<unsigned>(i.channel()));
      num_of_samples[i.channel()]++;

      if (*i.current() >= 0) {
	if (*i.current() > max_pos) max_pos = *i.current();

	for(unsigned j = 0; j < pos_samples_db.size(); j++) {
	  if (*i.current() > bucket_table[j].threshold) {
	    pos_samples_db[j][i.channel()]++;
	    break;
	  }
	}
      }
      else {
	if (-(*i.current()) > max_neg) max_neg = -(*i.current());

	for(unsigned j = 0; j < neg_samples_db.size(); j++) {
	  if (*i.current() < -bucket_table[j].threshold) {
	    neg_samples_db[j][i.channel()]++;
	    break;
	  }
	}
      }
      i.next();
    }

    res = pthread_mutex_unlock(&lock_rep);
    DBC_CHECK(res == 0);
  }
  // else { std::cerr << "(audiofx_analysis) lock taken, skipping process().\n"; }
}

EFFECT_VOLUME_PEAK::EFFECT_VOLUME_PEAK (void)
{
  max_amplitude_repp = 0;
}

EFFECT_VOLUME_PEAK::~EFFECT_VOLUME_PEAK (void)
{
  if (max_amplitude_repp != 0) {
    delete[] max_amplitude_repp;
    max_amplitude_repp = 0;
  }
}

void EFFECT_VOLUME_PEAK::parameter_description(int param, 
					       struct PARAM_DESCRIPTION *pd) const
{
  if (param > 0 && param <= channels()) {
    pd->default_value = 0;
    pd->description = get_parameter_name(param);
    pd->bounded_above = false;
    pd->bounded_below = true;
    pd->lower_bound = 0.0f;
    pd->toggled = false;
    pd->integer = false;
    pd->logarithmic = false;
    pd->output = true;
  }
}

std::string EFFECT_VOLUME_PEAK::parameter_names(void) const
{
  string params;
  for(int n = 0; n < channels(); n++) {
    params += "peak-amplitude-ch" + kvu_numtostr(n + 1);
    if (n != channels()) params += ",";
  }
  return params;
}

void EFFECT_VOLUME_PEAK::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
}

CHAIN_OPERATOR::parameter_t EFFECT_VOLUME_PEAK::get_parameter(int param) const
{
  if (param > 0 && param <= channels()) {
    parameter_t temp = max_amplitude_repp[param - 1];
    max_amplitude_repp[param - 1] = 0.0f;
    return temp;
  }
  return 0.0f;
}

void EFFECT_VOLUME_PEAK::init(SAMPLE_BUFFER* insample)
{
  i.init(insample);
  if (max_amplitude_repp != 0) {
    delete[] max_amplitude_repp;
    max_amplitude_repp = 0;
  }
  max_amplitude_repp = new parameter_t [insample->number_of_channels()];
  set_channels(insample->number_of_channels());
}

void EFFECT_VOLUME_PEAK::process(void)
{
  i.begin();
  while(!i.end()) {
    SAMPLE_SPECS::sample_t abscurrent = std::fabs(*i.current());
    DBC_CHECK(i.channel() >= 0);
    DBC_CHECK(i.channel() < channels());
    if (abscurrent > max_amplitude_repp[i.channel()]) {
      max_amplitude_repp[i.channel()] = std::fabs(*i.current());
    }
    i.next();
  }
}

EFFECT_DCFIND::EFFECT_DCFIND (void)
{
}

string EFFECT_DCFIND::status(void) const
{
  MESSAGE_ITEM mitem;
  mitem.setprecision(5);
  mitem << "(audiofx) Optimal value for DC-adjust: ";
  mitem << get_deltafix(SAMPLE_SPECS::ch_left) << " (left), ";
  mitem << get_deltafix(SAMPLE_SPECS::ch_right) << " (right).";
  return mitem.to_string();
}

string EFFECT_DCFIND::parameter_names(void) const
{
  std::vector<std::string> t;
  for(int n = 0; n < channels(); n++) {
    t.push_back("result-offset-ch" + kvu_numtostr(n + 1));
  }
  return kvu_vector_to_string(t, ",");
}

CHAIN_OPERATOR::parameter_t EFFECT_DCFIND::get_deltafix(int channel) const
{
  SAMPLE_SPECS::sample_t deltafix;

  if (channel < 0 || 
      channel >= static_cast<int>(pos_sum.size()) ||
      channel >= static_cast<int>(neg_sum.size())) return 0.0;

  if (pos_sum[channel] > neg_sum[channel]) deltafix = -(pos_sum[channel] - neg_sum[channel]) / num_of_samples[channel];
  else deltafix = (neg_sum[channel] - pos_sum[channel]) / num_of_samples[channel];

  return (CHAIN_OPERATOR::parameter_t)deltafix; 
}

void EFFECT_DCFIND::parameter_description(int param, 
					  struct PARAM_DESCRIPTION *pd) const
{
  pd->default_value = 0.0f;
  pd->description = get_parameter_name(param);
  pd->bounded_above = false;
  pd->upper_bound = 0.0f;
  pd->bounded_below = false;
  pd->lower_bound = 0.0f;
  pd->toggled = false;
  pd->integer = false;
  pd->logarithmic = false;
  pd->output = true;
}

void EFFECT_DCFIND::set_parameter(int param,
				  CHAIN_OPERATOR::parameter_t value)
{
}

CHAIN_OPERATOR::parameter_t EFFECT_DCFIND::get_parameter(int param) const
{
  return get_deltafix(param-1);
}

void EFFECT_DCFIND::init(SAMPLE_BUFFER *insample)
{
  i.init(insample);
  set_channels(insample->number_of_channels());
  pos_sum.resize(channels());
  neg_sum.resize(channels());
  num_of_samples.resize(channels());
}

void EFFECT_DCFIND::process(void)
{
  i.begin();
  while(!i.end()) {
    tempval = *i.current();
    if (tempval > SAMPLE_SPECS::silent_value)
      pos_sum[i.channel()] += tempval;
    else
      neg_sum[i.channel()] += fabs(tempval);
    num_of_samples[i.channel()]++;
    i.next();
  }
}
