// ------------------------------------------------------------------------
// audioio_alsa_named.cpp: ALSA 0.9.x named PCM-device input/output.
// Copyright (C) 2001-2002 Kai Vehmanen
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
#include <unistd.h>

#include <kvu_message_item.h>
#include <kvu_numtostr.h>

#include <alsa/asoundlib.h>

#include "audioio_alsa_named.h"
#include "eca-error.h"
#include "eca-logger.h"
#include "eca-version.h"

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
static const char* audio_io_keyword_const = "alsa_09";
static const char* audio_io_keyword_regex_const = "^alsa_09$";

const char* audio_io_keyword(void){return(audio_io_keyword_const); }
const char* audio_io_keyword_regex(void){return(audio_io_keyword_regex_const); }
AUDIO_IO* audio_io_descriptor(void) { return(new AUDIO_IO_ALSA_PCM_NAMED()); }
int audio_io_interface_version(void) { return(ecasound_library_version_current); }
#endif

AUDIO_IO_ALSA_PCM_NAMED::AUDIO_IO_ALSA_PCM_NAMED (void)
{
  set_pcm_device_name("");
}

AUDIO_IO_ALSA_PCM_NAMED::~AUDIO_IO_ALSA_PCM_NAMED(void)
{
}

AUDIO_IO_ALSA_PCM_NAMED* AUDIO_IO_ALSA_PCM_NAMED::clone(void) const
{
  AUDIO_IO_ALSA_PCM_NAMED* target = new AUDIO_IO_ALSA_PCM_NAMED();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return(target);
}

void AUDIO_IO_ALSA_PCM_NAMED::set_parameter(int param, 
					    string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;

  case 2: 
    set_pcm_device_name(value);
    break;
  }
}

string AUDIO_IO_ALSA_PCM_NAMED::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return(label());

  case 2: 
    return(pcm_device_name());
  }
  return("");
}
