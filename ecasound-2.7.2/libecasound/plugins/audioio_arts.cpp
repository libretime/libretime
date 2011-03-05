// ------------------------------------------------------------------------
// audioio-arts.cpp: Interface for communicating with aRts/MCOP.
// Copyright (C) 2000,2002 Kai Vehmanen
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

#include "audioio-device.h"
#include "eca-logger.h"
#include "eca-version.h"

extern "C" {
#include <artsc.h>
}

#include "audioio_arts.h"

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
static const char* audio_io_keyword_const = "arts";
static const char* audio_io_keyword_regex_const = "^arts$";

const char* audio_io_keyword(void){return(audio_io_keyword_const); }
const char* audio_io_keyword_regex(void){return(audio_io_keyword_regex_const); }
int audio_io_interface_version(void) { return(ecasound_library_version_current); }
#endif

int ARTS_INTERFACE::ref_rep = 0;

ARTS_INTERFACE::ARTS_INTERFACE(const string& name)
{
  set_label(name);
}

ARTS_INTERFACE::~ARTS_INTERFACE(void)
{
  if (is_open() == true && is_running()) stop();

  if (is_open() == true) {
    close();
  }

  --ref_rep;
  if (ref_rep == 0) ::arts_free();
}

ARTS_INTERFACE* ARTS_INTERFACE::clone(void) const
{
  ARTS_INTERFACE* target = new ARTS_INTERFACE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return(target);
}

void ARTS_INTERFACE::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  if (ref_rep == 0) {
    int err = ::arts_init();
    if (err < 0) {
      throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ARTS: unable to connect to aRts server: " + string(arts_error_text(err))));
    }
  }
  ++ref_rep;

  if (io_mode() == io_read) {
    stream_rep = ::arts_record_stream(samples_per_second(), bits(), channels(), "ecasound-input");
  }
  else if (io_mode() == io_write) {
    stream_rep = ::arts_play_stream(samples_per_second(), bits(), channels(), "ecasound-output");
  }
  else {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-ARTS: Simultanious input/output not supported."));
  }

  ::arts_stream_set(stream_rep, ARTS_P_BUFFER_SIZE, buffersize() * frame_size());
  ::arts_stream_set(stream_rep, ARTS_P_BLOCKING, 1);
  samples_rep = 0;

  double total_latency = ::arts_stream_get(stream_rep, ARTS_P_TOTAL_LATENCY);
  latency_rep = static_cast<long int>(total_latency * samples_per_second() / 1000.0f);
  
  AUDIO_IO::open();
}

void ARTS_INTERFACE::stop(void)
{
  AUDIO_IO_DEVICE::stop();
}

void ARTS_INTERFACE::close(void)
{
  ::arts_close_stream(stream_rep);

  AUDIO_IO::close();
}

void ARTS_INTERFACE::start(void)
{ 
  AUDIO_IO_DEVICE::start();
}

long int ARTS_INTERFACE::read_samples(void* target_buffer, 
				      long int samples) 
{
  long int res = ::arts_read(stream_rep, target_buffer, frame_size() * samples);
  if (res >= 0) {
    return(res / frame_size());
  }
  else {
    return(0);
  }
}

void ARTS_INTERFACE::write_samples(void* target_buffer, 
				   long int samples) 
{
  ::arts_write(stream_rep, target_buffer, frame_size() * samples);
}
