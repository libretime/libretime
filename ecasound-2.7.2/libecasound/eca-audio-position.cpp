// ------------------------------------------------------------------------
// eca-audio-position.cpp: Base class for representing position and length
//                         of a audio stream.
// Copyright (C) 1999-2002,2007,2008 Kai Vehmanen
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

#include <cmath>
#include <math.h> /* ceil() */

#include <kvu_dbc.h>

#include "eca-audio-position.h"

ECA_AUDIO_POSITION::ECA_AUDIO_POSITION(void)
{
  length_set_rep = false;
  position_in_samples_rep = 0;
  length_in_samples_rep = 0;
}

ECA_AUDIO_POSITION::~ECA_AUDIO_POSITION(void)
{
}

SAMPLE_SPECS::sample_pos_t ECA_AUDIO_POSITION::length_in_samples(void) const
{
  return length_in_samples_rep; 
}

int ECA_AUDIO_POSITION::length_in_seconds(void) const
{
  DBC_CHECK(samples_per_second() != 0);
  return (int)ceil((double)length_in_samples() /
		   (double)samples_per_second());
}

double ECA_AUDIO_POSITION::length_in_seconds_exact(void) const
{ 
  DBC_CHECK(samples_per_second() != 0);
  return (double)length_in_samples() / (double)samples_per_second();
}

void ECA_AUDIO_POSITION::set_length_in_samples(SAMPLE_SPECS::sample_pos_t pos)
{
  length_in_samples_rep = pos;
  length_set_rep = true;
}

void ECA_AUDIO_POSITION::set_length_in_seconds(int pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  set_length_in_seconds((double)pos_in_seconds); 
}

void ECA_AUDIO_POSITION::set_length_in_seconds(double pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  set_length_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(pos_in_seconds * samples_per_second()));
}

SAMPLE_SPECS::sample_pos_t ECA_AUDIO_POSITION::position_in_samples(void) const
{
  return position_in_samples_rep;
}

int ECA_AUDIO_POSITION::position_in_seconds(void) const
{
  DBC_CHECK(samples_per_second() != 0);
  return (int)ceil((double)position_in_samples() /
		   (double)samples_per_second());
}

double ECA_AUDIO_POSITION::position_in_seconds_exact(void) const
{
  DBC_CHECK(samples_per_second() != 0);
  return (double)position_in_samples() / (double)samples_per_second();
}

void ECA_AUDIO_POSITION::set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos)
{
  position_in_samples_rep = pos;
  if (position_in_samples_rep < 0) {
    position_in_samples_rep = 0;
  }
}

void ECA_AUDIO_POSITION::change_position_in_samples(SAMPLE_SPECS::sample_pos_t pos)
{
  position_in_samples_rep += pos;
}

void ECA_AUDIO_POSITION::change_position_in_seconds(double pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  change_position_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(pos_in_seconds * samples_per_second()));
}

void ECA_AUDIO_POSITION::set_position_in_seconds(int pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  set_position_in_seconds((double)pos_in_seconds); 
}

void ECA_AUDIO_POSITION::set_position_in_seconds(double pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  set_position_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(pos_in_seconds * samples_per_second()));
}

void ECA_AUDIO_POSITION::seek_first(void)
{
  seek_position_in_samples(0);
}

void ECA_AUDIO_POSITION::seek_last(void)
{
  seek_position_in_samples(length_in_samples());
}

void ECA_AUDIO_POSITION::seek_position_in_samples(SAMPLE_SPECS::sample_pos_t pos_in_samples)
{
  SAMPLE_SPECS::sample_pos_t res =
    seek_position(pos_in_samples);
  set_position_in_samples(res);
}

void ECA_AUDIO_POSITION::seek_position_in_samples_advance(SAMPLE_SPECS::sample_pos_t pos)
{
  seek_position_in_samples(position_in_samples() + pos);
}

void ECA_AUDIO_POSITION::seek_position_in_seconds(double pos_in_seconds)
{
  DBC_CHECK(samples_per_second() != 0);
  seek_position_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(pos_in_seconds * samples_per_second()));
}

void ECA_AUDIO_POSITION::set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value)
{
  double ratio (new_value);
  ratio /= samples_per_second();
  set_position_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(position_in_samples() * ratio));
  if (length_set() == true) {
    set_length_in_samples(static_cast<SAMPLE_SPECS::sample_pos_t>(length_in_samples() * ratio));
  }
  ECA_SAMPLERATE_AWARE::set_samples_per_second(new_value);
}
