// ------------------------------------------------------------------------
// eca-chainsetup-position.cpp: Global chainsetup position
// Copyright (C) 1999-2003 Kai Vehmanen
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
#include <math.h> /* ceil() */

#include "eca-chainsetup-position.h"

ECA_CHAINSETUP_POSITION::ECA_CHAINSETUP_POSITION(void)
{
  looping_rep = false;
  max_length_set_rep = false;
}

ECA_CHAINSETUP_POSITION::~ECA_CHAINSETUP_POSITION(void)
{
}

SAMPLE_SPECS::sample_pos_t ECA_CHAINSETUP_POSITION::max_length_in_samples(void) const
{
  return(max_length_in_samples_rep); 
}

double ECA_CHAINSETUP_POSITION::max_length_in_seconds_exact(void) const
{
  return((double)max_length_in_samples_rep / (double)samples_per_second());
}

/**
 * Explicitly sets the chainsetup length (in seconds).
 *
 * A special-case value of '-1 * srate' can be used to override 
 * a previously set chainsetup length. 
 *
 * @post ((pos == -1 * samples_per_second()) && (max_length_set() != true)) ||
 *       (max_length_set() == true)
 */
void ECA_CHAINSETUP_POSITION::set_max_length_in_samples(SAMPLE_SPECS::sample_pos_t pos)
{
  max_length_in_samples_rep = pos;
  if (pos != -1 * samples_per_second()) {
    max_length_set_rep = true;
  }
  else {
    max_length_set_rep = false;
  }
}

void ECA_CHAINSETUP_POSITION::set_max_length_in_seconds(double pos_in_seconds)
{
  set_max_length_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(pos_in_seconds * samples_per_second()));
}

void ECA_CHAINSETUP_POSITION::set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value)
{
  if (max_length_set() == true) {
    double ratio (new_value);
    ratio /= samples_per_second();
    set_max_length_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(max_length_in_samples() * ratio));
  }
   
  ECA_AUDIO_POSITION::set_samples_per_second(new_value);
}
