// ------------------------------------------------------------------------
// audioio-jack.cpp: Interface to JACK audio framework
// Copyright (C) 2001-2003,2008,2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <iostream>

#include <jack/jack.h>
#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "audioio.h"
#include "eca-version.h"
#include "eca-logger.h"
#include "samplebuffer.h"

#include "audioio_jack.h"
#include "audioio_jack_manager.h"

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
/* see eca-static-object-maps.cpp */
static const char* audio_io_keyword_const = "jack";
static const char* audio_io_keyword_regex_const = "(^jack$)|(^jack_alsa$)|(^jack_auto$)|(^jack_generic$)";

AUDIO_IO* audio_io_descriptor(void) { return new AUDIO_IO_JACK(); }
const char* audio_io_keyword(void) {return audio_io_keyword_const; }
const char* audio_io_keyword_regex(void){return audio_io_keyword_regex_const; }
int audio_io_interface_version(void) { return ecasound_library_version_current; }
#endif

AUDIO_IO_JACK::AUDIO_IO_JACK (void)
  : jackmgr_rep(0),
    myid_rep(0),
    error_flag_rep(false)
{
  ECA_LOG_MSG(ECA_LOGGER::functions, "constructor");
  
}

AUDIO_IO_JACK::~AUDIO_IO_JACK(void)
{ 
  if (is_open() == true && is_running()) stop();
  if (is_open() == true) {
    close();
  }
}

AUDIO_IO_MANAGER* AUDIO_IO_JACK::create_object_manager(void) const
{
  return new AUDIO_IO_JACK_MANAGER();
}

void AUDIO_IO_JACK::set_manager(AUDIO_IO_JACK_MANAGER* mgr, int id)
{
  string mgrname = (mgr != 0 ? mgr->name() : "null");
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"setting manager to " + mgrname);
  jackmgr_rep = mgr;
  myid_rep = id;
}

void AUDIO_IO_JACK::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "open");

#ifdef WORDS_BIGENDIAN
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_f32_be);
#else
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_f32_le);
#endif
  toggle_interleaved_channels(false);

  if (jackmgr_rep != 0) {
    string my_in_portname ("in"), my_out_portname ("out");

    if (label() == "jack" &&
	params_rep.size() > 2 && 
	params_rep[2].size() > 0) {
      my_in_portname = my_out_portname = params_rep[2];
    }
    /* note: deprecated interface */
    else if (label() == "jack_generic" &&
	     params_rep.size() > 1) {
      my_in_portname = my_out_portname = params_rep[1];
    }

    jackmgr_rep->open(myid_rep);

    if (jackmgr_rep->is_open() != true) {
      /* unable to open connection to jackd, exit */
      throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-JACK: Unable to open JACK-client"));
    }

    if (samples_per_second() != jackmgr_rep->samples_per_second()) {
      set_samples_per_second(jackmgr_rep->samples_per_second());
      ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		  "Note! Locking to jackd samplerate " +
		  kvu_numtostr(samples_per_second()));
    }
    
    if (buffersize() != jackmgr_rep->buffersize()) {
      long int jackd_bsize = jackmgr_rep->buffersize();
      jackmgr_rep->close(myid_rep);
      throw(SETUP_ERROR(SETUP_ERROR::unexpected, 
			"AUDIOIO-JACK: Cannot connect open connection! Buffersize " +
			kvu_numtostr(buffersize()) + " differs from JACK server's buffersize of " + 
			kvu_numtostr(jackd_bsize) + "."));
    }

    if (io_mode() == AUDIO_IO::io_read) {
      jackmgr_rep->register_jack_ports(myid_rep, channels(), my_in_portname);
    }
    else {
      jackmgr_rep->register_jack_ports(myid_rep, channels(), my_out_portname);
    }

    /* - make automatic connections */

    if (label() == "jack" &&
	params_rep.size() > 1 &&
	params_rep[1].size() > 0) {
      /* note: if 2nd param given, use it as the client to autoconnect to */
      jackmgr_rep->auto_connect_jack_port_client(myid_rep, params_rep[1], channels());
    }
    else if (label() == "jack_multi") {
      int i;
      for(i = 0; i < channels(); i++) {
	if (static_cast<int>(params_rep.size()) > i + 1 &&
	    params_rep[i + 1].size() > 0) {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		      "adding auto connection from " +
		      my_out_portname + "_" + kvu_numtostr(i + 1) + 
		      " to " + 
		      params_rep[i + 1]);
	  jackmgr_rep->auto_connect_jack_port(myid_rep, i + 1, params_rep[i + 1]);
	}


      }
    }
    else if (label() == "jack_alsa") {
      /* note: deprecated feature: 'alsa_pcm' is hidden in the port
	 list returned by jack_get_ports(), but as you can still
	 connect with the direct backend names, we have to keep this
	 code around to be backward compatible */
      string in_aconn_portprefix, out_aconn_portprefix;

      in_aconn_portprefix = "alsa_pcm:capture_";
      out_aconn_portprefix = "alsa_pcm:playback_";
      
      for(int n = 0; n < channels(); n++) {
	if (io_mode() == AUDIO_IO::io_read) {
	  jackmgr_rep->auto_connect_jack_port(myid_rep, n + 1, in_aconn_portprefix + kvu_numtostr(n + 1));
	}
	else {
	  jackmgr_rep->auto_connect_jack_port(myid_rep, n + 1, out_aconn_portprefix + kvu_numtostr(n + 1));
	}
      }
    }
    /* note: deprecated interface, plain "jack" should be used now */
    else if (label() == "jack_auto" &&
	     params_rep.size() > 1 &&
	     params_rep[1].size() > 0) {
      jackmgr_rep->auto_connect_jack_port_client(myid_rep, params_rep[1], channels());
    }
  }

  AUDIO_IO_DEVICE::open();
}

void AUDIO_IO_JACK::close(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "close");

  if (jackmgr_rep != 0) {
    jackmgr_rep->unregister_jack_ports(myid_rep);
    jackmgr_rep->close(myid_rep);
  }
  
  AUDIO_IO_DEVICE::close();
}

bool AUDIO_IO_JACK::finished(void) const 
{
  if (is_open() != true ||
      jackmgr_rep == 0 ||
      jackmgr_rep->is_open() != true ||
      error_flag_rep == true)
    return true;

  return false;
}

long int AUDIO_IO_JACK::read_samples(void* target_buffer, long int samples)
{
  if (jackmgr_rep != 0) {
    DBC_CHECK(samples == jackmgr_rep->buffersize());
    long int res = jackmgr_rep->read_samples(myid_rep, target_buffer, samples);
    return res;
  }
  
  return 0;
}

void AUDIO_IO_JACK::write_buffer(SAMPLE_BUFFER* sbuf)
{
  /* note: this is reimplemented only to catch errors with unsupported
   *       input streams (e.g. one produces by 'resample' object' */

  if (sbuf->length_in_samples() > 0 &&
      sbuf->length_in_samples() != jackmgr_rep->buffersize() &&
      sbuf->event_tag_test(SAMPLE_BUFFER::tag_end_of_stream) != true) {
    error_flag_rep = true;
    ECA_LOG_MSG(ECA_LOGGER::errors, 
		"ERROR: Variable size input buffers detected at JACK output, stopping processing. " 
		"This can happen e.g. with a 'resample' input object.");
  }

  AUDIO_IO_DEVICE::write_buffer(sbuf);
}

void AUDIO_IO_JACK::write_samples(void* target_buffer, long int samples)
{
  DBC_CHECK(samples <= jackmgr_rep->buffersize());
  if (jackmgr_rep != 0) {
    jackmgr_rep->write_samples(myid_rep, target_buffer, samples);
  }
}

void AUDIO_IO_JACK::prepare(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "prepare / " + label());
  error_flag_rep = false;
  AUDIO_IO_DEVICE::prepare();
}

void AUDIO_IO_JACK::start(void)
{ 
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "start / " + label());
  AUDIO_IO_DEVICE::start();
}

void AUDIO_IO_JACK::stop(void)
{ 
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "stop / " + label());
  AUDIO_IO_DEVICE::stop();
}

long int AUDIO_IO_JACK::latency(void) const
{
  return jackmgr_rep == 0 ? 0 : jackmgr_rep->client_latency(myid_rep);
}

std::string AUDIO_IO_JACK::parameter_names(void) const
{ 
  if (label() == "jack_generic")
    return "label,portname";

  else if (label() == "jack_auto")
    return "label,client";

  else if (label() == "jack_multi") {
    string paramlist = "label,";
    int i;
    for(i = 0; i < channels(); i++) {
      paramlist += ",dstport" + kvu_numtostr(i + 1);
    }
    return paramlist;
  }

  /* jack */
  return "label,client,portprefix";
}

void AUDIO_IO_JACK::set_parameter(int param, std::string value)
{
  if (param > static_cast<int>(params_rep.size()))
    params_rep.resize(param);

  params_rep[param - 1] = value;

  if (param == 1) {
    set_label(value);
  }
}

std::string AUDIO_IO_JACK::get_parameter(int param) const
{
  if (param > 0 && param <= static_cast<int>(params_rep.size()))
    return params_rep[param - 1];

  return AUDIO_IO::get_parameter(param);
}
