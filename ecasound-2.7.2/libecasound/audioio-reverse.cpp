// ------------------------------------------------------------------------
// audioio-reverse.cpp: A proxy class that reverts the child 
//                      object's data.
// Copyright (C) 2002,2005,2008,2009 Kai Vehmanen
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

#include <iostream>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "audioio-reverse.h"
#include "audioio-resample.h"
#include "eca-logger.h"
#include "eca-object-factory.h"
#include "samplebuffer.h"

/**
 * Constructor.
 */
AUDIO_IO_REVERSE::AUDIO_IO_REVERSE (void)
{
  
  tempbuf_repp = new SAMPLE_BUFFER();
  init_rep = false;
  finished_rep = false;
}

/**
 * Destructor.
 */
AUDIO_IO_REVERSE::~AUDIO_IO_REVERSE (void)
{
}

AUDIO_IO_REVERSE* AUDIO_IO_REVERSE::clone(void) const
{
  AUDIO_IO_REVERSE* target = new AUDIO_IO_REVERSE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void AUDIO_IO_REVERSE::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "open " + label() + ".");  

  if (io_mode() != AUDIO_IO::io_read) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-REVERSE: Reversed writing not supported!"));
  }
  
  if (init_rep != true) {
    AUDIO_IO* tmp = 0;

    const string& objname = 
      child_params_as_string(1 + AUDIO_IO_REVERSE::child_parameter_offset, &params_rep);
    
    if (objname.size() > 0)
      tmp = ECA_OBJECT_FACTORY::create_audio_object(objname);

    if (tmp == 0) 
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-REVERSE: unable to open child object '" + objname + "'"));

    set_child(tmp);

    int numparams = child()->number_of_params();
    for(int n = 0; n < numparams; n++) {
      child()->set_parameter(n + 1, get_parameter(n + 1 + AUDIO_IO_REVERSE::child_parameter_offset));
      if (child()->variable_params())
	numparams = child()->number_of_params();
    }

    init_rep = true; /* must be set after dyn. parameters */
  }

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"checking whether child is a finite object");  
    
  pre_child_open();
  child()->open();
  post_child_open();

  if (child()->finite_length_stream() != true) {
    child()->close();
    throw(SETUP_ERROR(SETUP_ERROR::dynamic_params, "AUDIOIO-REVERSE: Unable to reverse an infinite length audio object " + child()->label() + "."));
  }

  if (dynamic_cast<AUDIO_IO_RESAMPLE*>(child()) != 0) {
    child()->close();
    throw(SETUP_ERROR(SETUP_ERROR::dynamic_params, "AUDIOIO-REVERSE: 'resample' objects not supported"));
  }

  if (child()->supports_seeking() != true) {
    child()->close();
    throw(SETUP_ERROR(SETUP_ERROR::dynamic_params, "AUDIOIO-REVERSE: Unable to reverse audio object types that don't support seek (" + child()->label() + ")."));
  }

  AUDIO_IO::open();
}

void AUDIO_IO_REVERSE::close(void)
{
  if (child()->is_open() == true) child()->close();

  AUDIO_IO::close();
}

bool AUDIO_IO_REVERSE::finished(void) const
{
  return finished_rep;
}

string AUDIO_IO_REVERSE::parameter_names(void) const
{
  return string("reverse,") + child()->parameter_names(); 
}

void AUDIO_IO_REVERSE::set_parameter(int param, string value)
{

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"set_parameter " + label() + ".");  

  /* total of n+1 params, where n is number of childobj params */
  if (param > static_cast<int>(params_rep.size())) params_rep.resize(param);

  if (param > 0) {
    params_rep[param - 1] = value;
  }
  
  if (param > AUDIO_IO_REVERSE::child_parameter_offset && init_rep == true) {
    child()->set_parameter(param - AUDIO_IO_REVERSE::child_parameter_offset, value);
  }
}

string AUDIO_IO_REVERSE::get_parameter(int param) const
{

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"get_parameter " + label() + ".");

  if (param > 0 && param < static_cast<int>(params_rep.size()) + 1) {
    if (param > AUDIO_IO_REVERSE::child_parameter_offset 
	&& init_rep == true) {
      params_rep[param - 1] = 
	child()->get_parameter(param - AUDIO_IO_REVERSE::child_parameter_offset);
    }
    return params_rep[param - 1];
  }

  return ""; 
}

SAMPLE_SPECS::sample_pos_t AUDIO_IO_REVERSE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  finished_rep = false;
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"seek_position " + kvu_numtostr(pos) + ".");
  return AUDIO_IO_PROXY::seek_position(pos);
}

void AUDIO_IO_REVERSE::read_buffer(SAMPLE_BUFFER* sbuf)
{
  tempbuf_repp->number_of_channels(channels());
  sbuf->number_of_channels(channels());

  SAMPLE_BUFFER::buf_size_t read_count = buffersize();

  /* phase 1: Seek to correct position and read one buffer */
  SAMPLE_SPECS::sample_pos_t curpos = position_in_samples();
  SAMPLE_SPECS::sample_pos_t newpos = child()->length_in_samples() - curpos - buffersize();
  if (newpos <= 0) {
    child()->seek_position_in_samples(0);
    read_count = -newpos;
    finished_rep = true;
  }
  else {
    child()->seek_position_in_samples(newpos);
  }

  /* phase 2: Copy the data in reversed order from tempbuf
   *          to sbuf. As we cannot have any gaps between 
   *          the blocks before reversing, we try to read
   *          multiple times until we get the full block 
   *          of data (at least buffersize() worth of samples).
   */

  const int max_loops = 3;
  SAMPLE_BUFFER::buf_size_t read_sofar = 0;

  /* this is how much reverse samples we will produce */
  sbuf->length_in_samples(read_count);

  for(int i = 0; i < max_loops; i++) {
    child()->read_buffer(tempbuf_repp);

    if (tempbuf_repp->length_in_samples() + read_sofar > read_count)
      tempbuf_repp->length_in_samples(read_count - read_sofar);

    for(int c = 0; c < sbuf->number_of_channels(); c++) {

      SAMPLE_BUFFER::buf_size_t src = 0;
      SAMPLE_BUFFER::buf_size_t dst = 
	(read_count - read_sofar - tempbuf_repp->length_in_samples());

      for(; src < tempbuf_repp->length_in_samples(); src++, dst++) {

	sbuf->buffer[c][dst] = 
	  tempbuf_repp->buffer[c][tempbuf_repp->length_in_samples() - src - 1];

      }
    }

    read_sofar += tempbuf_repp->length_in_samples();

    if (read_sofar >= read_count)
      break;
  }

  DBC_CHECK(read_sofar <= buffersize());
  DBC_CHECK(sbuf->length_in_samples() == read_count);

  curpos += read_sofar;
  set_position_in_samples(curpos);

  DBC_ENSURE(sbuf->number_of_channels() == channels());
}
