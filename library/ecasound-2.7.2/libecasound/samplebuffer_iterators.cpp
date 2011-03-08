// ------------------------------------------------------------------------
// audiofx_mixing.cpp: Effects for channel mixing and routing
// Copyright (C) 1999-2005,2008 Kai Vehmanen
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

#include <vector>

#include <kvu_dbc.h>

#include "samplebuffer.h"
#include "samplebuffer_iterators.h"

// ---------------------------------------------------------------------

void SAMPLE_ITERATOR::begin(void)
{
  index = 0;
  channel_index = 0;
  if (target->buffersize_rep == 0)
    channel_index = target->channel_count_rep;
}

void SAMPLE_ITERATOR::next(void)
{
  ++index;
  if (index == target->buffersize_rep) {
    ++channel_index;
    index = 0;
  }
}

// ---------------------------------------------------------------------

void SAMPLE_ITERATOR_CHANNEL::init(SAMPLE_BUFFER* buf, int channel)
{
  target = buf; 
  index = 0; 
  channel_index = channel; 

  if (channel_index < 0 ||
      channel_index >= target->number_of_channels())
    index = target->buffersize_rep;
  DBC_CHECK(index == 0);
}

void SAMPLE_ITERATOR_CHANNEL::begin(int channel)
{
  index = 0;
  channel_index = channel;

  if (channel_index < 0 ||
      channel_index >= target->number_of_channels())
    index = target->buffersize_rep;
  DBC_CHECK(index == 0);
}

// ---------------------------------------------------------------------

void SAMPLE_ITERATOR_CHANNELS::begin(void)
{
  index = 0;
  channel_index = 0;
  if (target->buffersize_rep == 0)
    channel_index = target->channel_count_rep;
}

void SAMPLE_ITERATOR_CHANNELS::next(void)
{
  ++index;
  if (index == target->buffersize_rep) {
    ++channel_index;
    index = 0;
  }
}

// ---------------------------------------------------------------------
