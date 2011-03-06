// ------------------------------------------------------------------------
// audioio-loop.cpp: Audio object that routes data between reads and writes
// Copyright (C) 2000-2001,2004,2007,2008 Kai Vehmanen
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
#include <string>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "samplebuffer.h"
#include "sample-specs.h"
#include "audioio-buffered.h"
#include "audioio-loop.h"

#include "eca-error.h"
#include "eca-logger.h"

using std::string;

LOOP_DEVICE::LOOP_DEVICE(string tag) 
  :  AUDIO_IO("loop", io_readwrite),
     tag_rep(tag),
     sbuf(buffersize(), 0)
{ 
  writes_rep = 0;
  registered_inputs_rep = 0;
  registered_outputs_rep = 0;
  filled_rep = false;
  finished_rep = false;
  empty_rounds_rep = 0;
}

LOOP_DEVICE::~LOOP_DEVICE(void)
{
  if (is_open() == true) {
    close();
  }
}

LOOP_DEVICE* LOOP_DEVICE::clone(void) const
{
  LOOP_DEVICE* target = new LOOP_DEVICE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

bool LOOP_DEVICE::finished(void) const
{
  return finished_rep;
}

SAMPLE_SPECS::sample_pos_t LOOP_DEVICE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  writes_rep = 0;
  empty_rounds_rep = 0;
  filled_rep = false;
  finished_rep = false;
  return pos;
} 

void LOOP_DEVICE::read_buffer(SAMPLE_BUFFER* buffer)
{
  if (empty_rounds_rep == 0) {
    if (filled_rep == true) {
      buffer->copy_all_content(sbuf);
      /* note: read_buffer() should never be called in the middle
       *       of the 'X * write_buffer()' sequence (where X is the number
       *       of chain outputs connected to this loop device */
      DBC_CHECK(writes_rep == 0);
    }
    else {
      buffer->number_of_channels(channels());
      buffer->make_silent();
    }
  }
  else {
    finished_rep = true;
    buffer->number_of_channels(channels());
    buffer->make_silent();
  }

  DBC_ENSURE(buffer->number_of_channels() == channels());
}

void LOOP_DEVICE::write_buffer(SAMPLE_BUFFER* buffer)
{
  ++writes_rep;

  /* first write of a new engine iteration (and after a read) */
  if (writes_rep == 1) {
    change_position_in_samples(buffer->length_in_samples());
    extend_position();
    sbuf.number_of_channels(channels());
    sbuf.make_silent();
  }

  /* check if this is the last write for this engine iteration */
  if (writes_rep == registered_outputs_rep)
    writes_rep = 0;

  /* store data from 'buffer' */
  if (buffer->is_empty() != true) {
    empty_rounds_rep = 0;
    sbuf.add_with_weight(*buffer, registered_outputs_rep);
    filled_rep = true;
  }
  /* empty 'buffer' */
  else {
    ++empty_rounds_rep;
  }

  DBC_CHECK(sbuf.number_of_channels() == channels());
}

void LOOP_DEVICE::set_parameter(int param, 
				string value)
{
  switch (param) {
  case 1: 
    AUDIO_IO::set_parameter(param, value);
    break;

  case 2: 
    tag_rep = value;
    break;
  }
}

string LOOP_DEVICE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return AUDIO_IO::get_parameter(param);

  case 2: 
    return tag_rep;
  }
  return "";
}
