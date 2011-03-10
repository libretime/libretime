// ------------------------------------------------------------------------
// audioio-proxy.cpp: Generic interface for objects that act as
//                    proxies for other objects of type AUDIO_IO.
// Copyright (C) 2002,2005,2008 Kai Vehmanen
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

#include <kvu_dbc.h>

#include "samplebuffer.h"
#include "audioio-null.h"
#include "audioio-proxy.h"

AUDIO_IO_PROXY::AUDIO_IO_PROXY(void) 
  : buffersize_rep(0),
    child_initialized_rep(false)
{
  child_repp = new NULLFILE("uninitialized proxy child");
}

AUDIO_IO_PROXY::~AUDIO_IO_PROXY(void)
{ 
  delete child_repp; // either null or the actual child object
}

/**
 * Sets a new proxy target object. The old 
 * target (if any) is deleted.
 */
void AUDIO_IO_PROXY::set_child(AUDIO_IO* v)
{ 
  if (child_repp != 0) 
    delete child_repp;
  
  child_repp = v;

  child_initialized_rep = true;
}

/**
 * Releases the current child without deleting Itx.
 */
void AUDIO_IO_PROXY::release_child_no_delete(void)
{
  child_repp = new NULLFILE("uninitialized proxy child");
  child_initialized_rep = false;
}

/**
 * Prepares child object for opening.
 * 
 * Sets the audio objects parameters, which have an effect
 * at open time, to those of the parent object.
 */
void AUDIO_IO_PROXY::pre_child_open(void)
{
  child()->set_buffersize(buffersize());
  child()->set_io_mode(io_mode());
  child()->set_audio_format(audio_format());
  child()->set_samples_per_second(samples_per_second());
}

/**
 * Checks if any audio parameters were changed
 * during child's open(), fetches any changes and
 * sets the object length.
 */
void AUDIO_IO_PROXY::post_child_open(void)
{
  if (child()->locked_audio_format() == true) {
    set_audio_format(child()->audio_format());
  }
  set_length_in_samples(child()->length_in_samples());
}

void AUDIO_IO_PROXY::set_buffersize(long int samples)
{
  buffersize_rep = samples;
  child_repp->set_buffersize(samples);
}

void AUDIO_IO_PROXY::set_channels(SAMPLE_SPECS::channel_t v)
{
  AUDIO_IO::set_channels(v);
  child_repp->set_channels(v);
}

void AUDIO_IO_PROXY::set_sample_format(Sample_format v) throw(ECA_ERROR&)
{
  AUDIO_IO::set_sample_format(v);
  child_repp->set_sample_format(v);
}

void AUDIO_IO_PROXY::set_audio_format(const ECA_AUDIO_FORMAT& f_str)
{
  AUDIO_IO::set_audio_format(f_str);
  child_repp->set_audio_format(f_str);
}

void AUDIO_IO_PROXY::toggle_interleaved_channels(bool v)
{
  AUDIO_IO::toggle_interleaved_channels(v);
  child_repp->toggle_interleaved_channels(v);
}

void AUDIO_IO_PROXY::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  AUDIO_IO::set_samples_per_second(v);
  child_repp->set_samples_per_second(v);
}

std::string AUDIO_IO_PROXY::parameter_names(void) const
{
  return child_repp->parameter_names();
}

void AUDIO_IO_PROXY::set_parameter(int param, std::string value)
{
  AUDIO_IO::set_parameter(param, value);
  child_repp->set_parameter(param, value);
}

std::string AUDIO_IO_PROXY::get_parameter(int param) const
{
  return child_repp->get_parameter(param);
}

void AUDIO_IO_PROXY::start_io(void)
{
  AUDIO_IO_BARRIER *barrier
    = dynamic_cast<AUDIO_IO_BARRIER*>(child_repp);

  if (barrier)
    barrier->start_io();

}

void AUDIO_IO_PROXY::stop_io(void)
{
  AUDIO_IO_BARRIER *barrier
    = dynamic_cast<AUDIO_IO_BARRIER*>(child_repp);

  if (barrier)
    barrier->stop_io();
}

std::string AUDIO_IO_PROXY::child_params_as_string(int first, std::vector<std::string>* params)
{
  int last = params->size();
  string res;
  for (int n = first; n <= last; n++) {
    /* FIXME: should quote the commas */
    res += get_parameter(n);
    if (n != last)
      res += ",";
  }
  return res;
}
