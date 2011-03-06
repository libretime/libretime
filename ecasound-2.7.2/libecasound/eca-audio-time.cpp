// ------------------------------------------------------------------------
// eca-audio-time.cpp: Generic class for representing time in audio 
//                     environment.
// Copyright (C) 2000,2007,2008 Kai Vehmanen
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

#include <cstdlib>
#include <cstdio>
#include <cmath>

#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "eca-audio-time.h"

/**
 * FIXME notes  (last update 2008-03-09)
 *
 *  - Add variant that allows specifying both sample position and
 *    sampling rate to set_time_string(). E.g. "1234@44100sa" or
 *    something similar.
 */

using SAMPLE_SPECS::sample_pos_t;
using SAMPLE_SPECS::sample_rate_t;

ECA_AUDIO_TIME::ECA_AUDIO_TIME(sample_pos_t samples, 
			       sample_rate_t sample_rate)
{
  set_samples_per_second(sample_rate);
  set_samples(samples);
  rate_set_rep = true;
}

ECA_AUDIO_TIME::ECA_AUDIO_TIME(double time_in_seconds)
  : samples_rep(0),
    sample_rate_rep(ECA_AUDIO_TIME::invalid_srate),
    rate_set_rep(false)
{
  set_seconds(time_in_seconds);
  rate_set_rep = true;
}


ECA_AUDIO_TIME::ECA_AUDIO_TIME(format_type type, 
			       const std::string& time)
  : samples_rep(0),
    sample_rate_rep(ECA_AUDIO_TIME::invalid_srate),
    rate_set_rep(false)
{
  set(type, time);
}

ECA_AUDIO_TIME::ECA_AUDIO_TIME(const std::string& time) 
  : samples_rep(0),
    sample_rate_rep(ECA_AUDIO_TIME::invalid_srate),
    rate_set_rep(false)
{
  set_time_string(time);
}

ECA_AUDIO_TIME::ECA_AUDIO_TIME(void) 
  : samples_rep(0),
    sample_rate_rep(ECA_AUDIO_TIME::invalid_srate),
    rate_set_rep(false)
{
}

/**
 * Sets time based on 'type', 'time' and 'srate'.
 *
 * @param sample_rate a value of zero will be ignored.
 */
void ECA_AUDIO_TIME::set(format_type type, const std::string& time)
{
  switch(type) 
    {
      /* FIXME: not implemented! */
    case format_hour_min_sec: { DBC_CHECK(false); break; }
      /* FIXME: not implemented! */
    case format_min_sec: { DBC_CHECK(false); break; }
    case format_seconds:
      {
	double seconds = atof(time.c_str());
	set_seconds(seconds);
	break;
      }
    case format_samples:
      {
	samples_rep = atol(time.c_str());
	break;
      }
    default: { }
    }
}

/**
 * Sets time expressed in seconds. Additionally sample_rate is given
 * to express the timing accuracy.
 *
 * @param sample_rate a value of zero will be ignored.
 */
void ECA_AUDIO_TIME::set_seconds(double seconds)
{
  if (sample_rate_rep ==
      ECA_AUDIO_TIME::invalid_srate) {
    sample_rate_rep = ECA_AUDIO_TIME::default_srate;
    rate_set_rep = true;
  }

  samples_rep = static_cast<SAMPLE_SPECS::sample_pos_t>(seconds * sample_rate_rep);
}

/**
 * Sets time based on string 'time'. Additionally sample_rate is given
 * to express the timing accuracy.
 *
 * The time string is by default interpreted as seconds (need not 
 * be an integer but can be given as a decimal number, e.g. "1.05"). 
 * However, if the string contains an integer number and has a postfix 
 * of "sa" (e.g. "44100sa"), it is interpreted as time expressed as 
 * samples (in case of a multichannel stream, time in sample frames).
 *
 * @param sample_rate a value of zero will be ignored.
 */
void ECA_AUDIO_TIME::set_time_string(const std::string& time)
{
  if (time.size() > 2 &&
      time.find("sa") != std::string::npos)
    ECA_AUDIO_TIME::set(format_samples, std::string(time, 0, time.size() - 2));
  else
    ECA_AUDIO_TIME::set(format_seconds, time);
}

/**
 * Sets the sample count.
 *
 * Note, this can change the value of seconds().
 */
void ECA_AUDIO_TIME::set_samples(SAMPLE_SPECS::sample_pos_t samples)
{
  samples_rep = samples;
}

/**
 * Sets samples per second. Additionally sample_rate is given
 * to express the timing accuracy.
 *
 * Note, this can change the value of seconds().
 */
void ECA_AUDIO_TIME::set_samples_per_second(SAMPLE_SPECS::sample_rate_t srate)
{
  if (srate > 0) {
    sample_rate_rep = srate;
    rate_set_rep = true;
  }
  else {
    rate_set_rep = false;
    sample_rate_rep = ECA_AUDIO_TIME::invalid_srate;
  }
}

/**
 * Sets samples per second.
 *
 * Note, if sampling rate has been set earlier, this function 
 * does NOT change the value of seconds() like 
 * set_samples_per_second() potentially does.
 */
void ECA_AUDIO_TIME::set_samples_per_second_keeptime(sample_rate_t srate)
{
  if (srate > 0 &&
      sample_rate_rep != srate) {
    if (rate_set_rep == true) {
      /* only needed if sampling rate has been set */
      double time_secs = seconds();
      set_samples_per_second(srate);
      set_seconds(time_secs);
    }
    else {
      set_samples_per_second(srate);
    }
  }
}

void ECA_AUDIO_TIME::mark_as_invalid(void)
{
  set_samples_per_second(ECA_AUDIO_TIME::invalid_srate);
}

std::string ECA_AUDIO_TIME::to_string(format_type type) const
{
  /* FIXME: not implemented */

  switch(type) 
    {
    case format_hour_min_sec: 
      { 
	return "";
      }
    case format_min_sec: 
      {
	return "";
      }
    case format_seconds: { return kvu_numtostr(seconds(), 6); }
    case format_samples: { return kvu_numtostr(samples_rep); }

    default: { }
    }

  return "";
}

double ECA_AUDIO_TIME::seconds(void) const
{
  if (rate_set_rep != true) {
    sample_rate_rep = ECA_AUDIO_TIME::default_srate;
    rate_set_rep = true;
  }

  return static_cast<double>(samples_rep) / sample_rate_rep;
}

SAMPLE_SPECS::sample_rate_t ECA_AUDIO_TIME::samples_per_second(void) const
{
  return sample_rate_rep;
}

SAMPLE_SPECS::sample_pos_t ECA_AUDIO_TIME::samples(void) const
{
  return samples_rep;
}

bool ECA_AUDIO_TIME::valid(void) const
{
  return rate_set_rep;
}
