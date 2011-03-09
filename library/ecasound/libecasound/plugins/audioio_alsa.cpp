// ------------------------------------------------------------------------
// audioio-alsa.cpp: ALSA 0.9.x PCM input and output.
// Copyright (C) 1999-2004,2008 Kai Vehmanen
// Copyright (C) 2001,2002 Jeremy Hall 
//
// Attributes:
//     eca-style-version: 3
//
// References:
//     http://alsa-project.org/
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

#include <string>
#include <cstring>
#include <cstdio>
#include <dlfcn.h>
#include <unistd.h>
#include <errno.h>

#include <alsa/version.h>

#define MY_SND_LIB_VERSION(maj,min,sub) \
  ((maj<<16)| (min<<8)| sub)

  
/* error if alsa-lib older than 0.9.0, use old API if 0.9.0->0.9.8, 
   otherwise do nothing */
#if SND_LIB_MAJOR < 1 && SND_LIB_MINOR == 9
#if SND_LIB_SUBMINOR > 0 || SND_LIB_EXTRAVER >= 100004
#define ALSA_PCM_NEW_HW_PARAMS_API
#define ALSA_PCM_NEW_SW_PARAMS_API
#else
#error "Unable to compile ALSA-support. Alsa-lib version 0.9.0rc4 or newer is required!"
#endif
#endif

#include <alsa/asoundlib.h>

#include <kvu_dbc.h>
#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_utils.h>

#include "samplebuffer.h"
#include "audioio-device.h"
#include "audioio_alsa.h"

#include "eca-version.h"
#include "eca-error.h"
#include "eca-logger.h"

using std::cerr;
using std::endl;

#ifndef timersub
#define	timersub(a, b, result) \
do { \
	(result)->tv_sec = (a)->tv_sec - (b)->tv_sec; \
	(result)->tv_usec = (a)->tv_usec - (b)->tv_usec; \
	if ((result)->tv_usec < 0) { \
		--(result)->tv_sec; \
		(result)->tv_usec += 1000000; \
	} \
} while (0)
#endif

const string AUDIO_IO_ALSA_PCM::default_pcm_device_rep = "default";

AUDIO_IO_ALSA_PCM::AUDIO_IO_ALSA_PCM (int card, 
				      int device, 
				      int subdevice) 
  : AUDIO_IO_DEVICE()
{
  // ECA_LOG_MSG(ECA_LOGGER::system_objects, "construct");
  card_number_rep = card;
  device_number_rep = device;
  subdevice_number_rep = subdevice;
  trigger_request_rep = false;
  overruns_rep = underruns_rep = 0;
  nbufs_repp = 0;
  allocate_structs();
}

AUDIO_IO_ALSA_PCM::~AUDIO_IO_ALSA_PCM(void)
{
  if (is_open() == true && is_running()) stop();

  if (is_open() == true) {
    close();
  }

  if (io_mode() != io_read) {
    if (underruns_rep != 0) {
      cerr << "WARNING! While writing to ALSA-pcm device ";
      cerr << "C" << card_number_rep << "D" << device_number_rep;
      cerr << ", there were " << underruns_rep << " underruns.\n";
    }
  }
  else {
    if (overruns_rep != 0) {
      cerr << "WARNING! While reading from ALSA-pcm device ";
      cerr << "C" << card_number_rep << "D" << device_number_rep;
      cerr << ", there were " << overruns_rep << " overruns.\n";
    }
  }

  if (nbufs_repp != 0)
    delete nbufs_repp;

  deallocate_structs();
}

AUDIO_IO_ALSA_PCM* AUDIO_IO_ALSA_PCM::clone(void) const
{
  AUDIO_IO_ALSA_PCM* target = new AUDIO_IO_ALSA_PCM();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void AUDIO_IO_ALSA_PCM::allocate_structs(void)
{
  int err = snd_pcm_hw_params_malloc(&pcm_hw_params_repp);
  DBC_CHECK(!err);

  err = snd_pcm_sw_params_malloc(&pcm_sw_params_repp);
  DBC_CHECK(!err);
}

void AUDIO_IO_ALSA_PCM::deallocate_structs(void)
{
  snd_pcm_hw_params_free(pcm_hw_params_repp);
  snd_pcm_sw_params_free(pcm_sw_params_repp);
}


void AUDIO_IO_ALSA_PCM::open_device(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "open");

  // -------------------------------------------------------------------
  // Device name initialization

  string device_name = pcm_device_name();

  // -------------------------------------------------------------------
  // Open devices

  int err;
  if (io_mode() == io_read) {
    pcm_stream_rep = SND_PCM_STREAM_CAPTURE;
    err = snd_pcm_open(&audio_fd_repp, 
			 (char*)device_name.c_str(),
			 pcm_stream_rep,
			 SND_PCM_NONBLOCK);

    if (err < 0) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-ALSA: Unable to open ALSA--device for capture; error: " + 
			string(snd_strerror(err))));
    }
  }    
  else if (io_mode() == io_write) {
    pcm_stream_rep = SND_PCM_STREAM_PLAYBACK;
    err = snd_pcm_open(&audio_fd_repp, 
			 (char*)device_name.c_str(),
			 pcm_stream_rep,
			 SND_PCM_NONBLOCK);
    
    if (err < 0) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-ALSA: Unable to open ALSA-device for playback; error: " +  
			string(snd_strerror(err))));
    }
  }
  else if (io_mode() == io_readwrite) {
    throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-ALSA: Simultaneous input/output not supported."));
  }

  // -------------------------------------------------------------------
  // enables blocking mode
  snd_pcm_nonblock(audio_fd_repp, 0);
}

void AUDIO_IO_ALSA_PCM::set_audio_format_params(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "set_audio_format_params");
  format_rep = SND_PCM_FORMAT_LAST;
  switch(sample_format()) 
    {
    case ECA_AUDIO_FORMAT::sfmt_u8:  { format_rep = SND_PCM_FORMAT_U8; break; }
    case ECA_AUDIO_FORMAT::sfmt_s8:  { format_rep = SND_PCM_FORMAT_S8; break; }
    case ECA_AUDIO_FORMAT::sfmt_s16_le:  { format_rep = SND_PCM_FORMAT_S16_LE; break; }
    case ECA_AUDIO_FORMAT::sfmt_s16_be:  { format_rep = SND_PCM_FORMAT_S16_BE; break; }
    case ECA_AUDIO_FORMAT::sfmt_s24_le:  { format_rep = SND_PCM_FORMAT_S24_3LE; break; }
    case ECA_AUDIO_FORMAT::sfmt_s24_be:  { format_rep = SND_PCM_FORMAT_S24_3BE; break; }
    case ECA_AUDIO_FORMAT::sfmt_s32_le:  { format_rep = SND_PCM_FORMAT_S32_LE; break; }
    case ECA_AUDIO_FORMAT::sfmt_s32_be:  { format_rep = SND_PCM_FORMAT_S32_BE; break; }
      
    default:
      {
	throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-ALSA: Error when setting audio format not supported (1)"));
      }
    }
}

void AUDIO_IO_ALSA_PCM::print_pcm_info(void)
{
}

void AUDIO_IO_ALSA_PCM::fill_and_set_hw_params(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "fill_and_set_hw_params");

  /* 1. create one param combination */
  int err = snd_pcm_hw_params_any(audio_fd_repp, pcm_hw_params_repp);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up hwparams/any: " + string(snd_strerror(err))));
  
  /* 2. set interleaving mode */
  if (interleaved_channels() == true)
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Using interleaved stream format.");
  else
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Using noninterleaved stream format.");

  if (interleaved_channels() == true)
    err = snd_pcm_hw_params_set_access(audio_fd_repp, pcm_hw_params_repp,
					 SND_PCM_ACCESS_RW_INTERLEAVED);
  else
    err = snd_pcm_hw_params_set_access(audio_fd_repp, pcm_hw_params_repp,
					 SND_PCM_ACCESS_RW_NONINTERLEAVED);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up hwparams/access: " + string(snd_strerror(err))));

  /* 3. set sample format */
  err = snd_pcm_hw_params_set_format(audio_fd_repp, 
				     pcm_hw_params_repp, 
				     format_rep);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-ALSA: Audio format not supported."));

  /* 4. set channel count */
  err = snd_pcm_hw_params_set_channels(audio_fd_repp, 
					 pcm_hw_params_repp, 
					 channels());
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::channels, "AUDIOIO-ALSA: Channel count " +
				 kvu_numtostr(channels()) + " is out of range!"));

  /* 5. set sampling rate */
  unsigned int uivalue = samples_per_second();
  err = snd_pcm_hw_params_set_rate_near(audio_fd_repp, 
					pcm_hw_params_repp,
					&uivalue, 
					0);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::sample_rate, "AUDIOIO-ALSA: Sample rate " +
				 kvu_numtostr(samples_per_second()) + " is out of range!"));

  /* 6. create buffers for noninterleaved i/o */
  if (interleaved_channels() != true) {
    if (nbufs_repp == 0)
      nbufs_repp = new unsigned char* [channels()];
  }

  snd_pcm_uframes_t fvalue = buffersize();
  /* 7. sets period size (period = one fragment) */
  err = snd_pcm_hw_params_set_period_size_near(audio_fd_repp, 
					       pcm_hw_params_repp,
					       &fvalue, 
					       0);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::buffersize, "AUDIOIO-ALSA: buffersize " +
				 kvu_numtostr(buffersize()) + " is out of range!"));

  /* 8. sets buffer size */
  if (max_buffers() == true) {
      snd_pcm_uframes_t bufferreq = buffersize() * 1024;
      err = snd_pcm_hw_params_set_buffer_size_near(audio_fd_repp, 
						   pcm_hw_params_repp,
						   &bufferreq);
    if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected,
				   "AUDIOIO-ALSA: Error when setting up hwparams/btime (1): " + string(snd_strerror(err))));
  }
  else {
    snd_pcm_uframes_t bufferreq = buffersize() * 3;
    err = snd_pcm_hw_params_set_buffer_size_near(audio_fd_repp, 
						 pcm_hw_params_repp,
						 &bufferreq);
    if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected,
				   "AUDIOIO-ALSA: Error when setting up hwparams/btime (2): " + string(snd_strerror(err))));
  }
   
  /* 9. print debug information */
  snd_pcm_hw_params_get_period_time(pcm_hw_params_repp, &uivalue, 0);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "period time set to " + kvu_numtostr(uivalue) + " usecs.");
  
  snd_pcm_hw_params_get_period_size(pcm_hw_params_repp, &period_size_rep, 0);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "period time set to " + kvu_numtostr(period_size_rep) + " frames.");
  if (period_size_rep != static_cast<unsigned int>(buffersize())) {
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"Warning! Period-size differs from current client buffersize.");
  }

  snd_pcm_hw_params_get_buffer_time(pcm_hw_params_repp, &uivalue, 0);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "buffer time set to " + kvu_numtostr(uivalue) + " usecs.");

  snd_pcm_hw_params_get_buffer_size(pcm_hw_params_repp, &buffer_size_rep);
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "buffer time set to " + kvu_numtostr(buffer_size_rep) + " frames.");
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "total latency is " + kvu_numtostr(latency()) + " frames.");


  /* 9. all set, now active hw params */
  err = snd_pcm_hw_params(audio_fd_repp, pcm_hw_params_repp);
  if (err < 0) {
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up hwparams: " + string(snd_strerror(err))));
  }
}

void AUDIO_IO_ALSA_PCM::fill_and_set_sw_params(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "fill_and_set_sw_params");

  /* 1. get current params */
  snd_pcm_sw_params_current(audio_fd_repp, pcm_sw_params_repp);

  /* 2. set start threshold (should be big enough so that processing 
        won't start until a explicit snd_pcm_start() is issued */
  int err = snd_pcm_sw_params_set_start_threshold(audio_fd_repp, 
						  pcm_sw_params_repp,
						  buffer_size_rep * 2);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up pcm_sw_params/start_threshold: " + string(snd_strerror(err))));

#if SND_LIB_VERSION <= MY_SND_LIB_VERSION(1,0,15)
  /* note: deprecated in alsa-lib-1.0.16 (2008/Feb) */
  /* 3. set align to one frame (like the OSS-emulation layer) */
  err = snd_pcm_sw_params_set_xfer_align(audio_fd_repp,
                                         pcm_sw_params_repp,
                                         1);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up pcm_sw_params_repp/xfer_align: " + string(snd_strerror(err))));
#endif

  /* 4. activate params */
  err = snd_pcm_sw_params(audio_fd_repp, pcm_sw_params_repp);
  if (err < 0) throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ALSA: Error when setting up pcm_sw_params_repp: " + string(snd_strerror(err))));
}

void AUDIO_IO_ALSA_PCM::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  open_device();
  set_audio_format_params();
  fill_and_set_hw_params();
  print_pcm_info();
  fill_and_set_sw_params();

  AUDIO_IO_DEVICE::open();
}

void AUDIO_IO_ALSA_PCM::stop(void)
{
  snd_pcm_drop(audio_fd_repp); /* non-blocking */
  // snd_pcm_drain(audio_fd_repp); /* blocking */
  
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "stop - " + label() + ".");

  AUDIO_IO_DEVICE::stop();
}

void AUDIO_IO_ALSA_PCM::close(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "close - " + label() + ".");

  if (is_prepared() == true && is_running() == true) stop();
  snd_pcm_close(audio_fd_repp);

  AUDIO_IO_DEVICE::close();
}

void AUDIO_IO_ALSA_PCM::prepare(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "prepare - " + label() + ".");

  int err = snd_pcm_prepare(audio_fd_repp);
  if (err < 0)
    ECA_LOG_MSG(ECA_LOGGER::info, "Error when preparing stream: " + string(snd_strerror(err)));

  AUDIO_IO_DEVICE::prepare();
}

void AUDIO_IO_ALSA_PCM::start(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "start - " + label() + ".");
  snd_pcm_start(audio_fd_repp);

  AUDIO_IO_DEVICE::start();
}

long int AUDIO_IO_ALSA_PCM::read_samples(void* target_buffer, 
					   long int samples)
{
  // --
  DBC_REQUIRE(samples <= buffersize());
  // --

  long int realsamples = 0;

  if (interleaved_channels() == true) {
    realsamples = snd_pcm_readi(audio_fd_repp, target_buffer,
				buffersize());
    if (realsamples < 0) {
      /* Note! ALSA versions <=0.9.1 sometimes return -EIO in xrun-state;
       *       EPIPE=xrun, ESTRPIPE=xrun) */
      if (realsamples == -EPIPE || realsamples == -ESTRPIPE || realsamples == -EIO) {
	if (ignore_xruns() == true) {
	  handle_xrun_capture();
	  realsamples = snd_pcm_readi(audio_fd_repp, target_buffer,
				      buffersize());
	  if (realsamples < 0) realsamples = 0;
	}
	else {
	  cerr << "ALSA: Overrun! Stopping operation!" << endl;
	  stop();
	  close();
	}
      }
      else {
	cerr << "ALSA: Read error (" << realsamples << ")! Stopping operation." << endl;
	stop();
	close();
      }
    }
  }
  else {
    unsigned char* ptr_to_channel = reinterpret_cast<unsigned char*>(target_buffer);
    for (int channel = 0; channel < channels(); channel++) {
      nbufs_repp[channel] = ptr_to_channel;
      ptr_to_channel += samples * sample_size();
    }
    realsamples = snd_pcm_readn(audio_fd_repp, reinterpret_cast<void**>(target_buffer), buffersize());
    if (realsamples < 0) {
      /* Note! ALSA versions <=0.9.1 sometimes return -EIO in xrun-state;
       *       EPIPE=xrun, ESTRPIPE=xrun) */
      if (realsamples == -EPIPE || realsamples == -ESTRPIPE || realsamples == -EIO) {
	if (ignore_xruns() == true) {
	  handle_xrun_capture();
	  realsamples = snd_pcm_readn(audio_fd_repp, reinterpret_cast<void**>(target_buffer), buffersize());
	  if (realsamples < 0) realsamples = 0;
	}
	else {
	  cerr << "ALSA: Overrun! Stopping operation!" << endl;
	  stop();
	  close();
	}
      }
      else {
	cerr << "ALSA: Read error! Stopping operation." << endl;
	stop();
	close();
      }
    }
  }
  return realsamples;
}

void AUDIO_IO_ALSA_PCM::handle_xrun_capture(void)
{
  snd_pcm_status_t *status;
  snd_pcm_status_alloca(&status);

  int res = snd_pcm_status(audio_fd_repp, status);
  if (res >= 0) {
    snd_pcm_state_t state = snd_pcm_status_get_state(status);
    if (state == SND_PCM_STATE_XRUN) {
      struct timeval now, diff, tstamp;
      gettimeofday(&now, 0);
      snd_pcm_status_get_trigger_tstamp(status, &tstamp);
      timersub(&now, &tstamp, &diff);

      cerr << "WARNING: ALSA recording overrun, some audio samples were lost!" 
		<< " Break was at least " << kvu_numtostr(diff.tv_sec *
							  1000 +
							  diff.tv_usec /
							  1000.0) 
		<< " ms long." << endl;

      overruns_rep++;
      stop();
      prepare();
      start();
    }
    else if (state == SND_PCM_STATE_SUSPENDED) {
      cerr << "ALSA: Device suspended! Stopping operation!" << endl;
      stop();
      close();
    }
    else {
      cerr << "ALSA: Unknown device state '" 
	   << static_cast<int>(state) << "'" << endl;
    }
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::info, "snd_pcm_status() failed!");
  }
}

void AUDIO_IO_ALSA_PCM::write_samples(void* target_buffer, long int samples)
{
  if (trigger_request_rep == true) {
    trigger_request_rep = false;
    start();
  }

  if (interleaved_channels() == true) {
    long int count = snd_pcm_writei(audio_fd_repp, target_buffer, samples);
    if (count < 0) {
      /* Note! ALSA versions <=0.9.1 sometimes return -EIO in xrun-state;
       *       EPIPE=xrun, ESTRPIPE=xrun) */
      DBC_CHECK(count != -EINTR);
      if (count == -EPIPE || count == -EIO || count == -ESTRPIPE) {
	if (ignore_xruns() == true) {
	  handle_xrun_playback();
	  if (snd_pcm_writei(audio_fd_repp, target_buffer, samples) < 0) 
	    cerr << "ALSA: playback xrun handling failed!" << endl;
	  trigger_request_rep = true;
	}
	else {
	  cerr << "ALSA: Overrun! Stopping operation!" << endl;
	  stop();
	  close();
	}
      }
      else {
	cerr << "ALSA: Write error! Stopping operation (" << count << ")." << endl;
	stop();
	close();
      }
    }
  }
  else {
    unsigned char* ptr_to_channel = reinterpret_cast<unsigned char*>(target_buffer);
    for (int channel = 0; channel < channels(); channel++) {
      nbufs_repp[channel] = ptr_to_channel;
      // cerr << "Pointer to channel " << channel << ": " << reinterpret_cast<void*>(nbufs_repp[channel]) << endl;
      ptr_to_channel += samples * sample_size();
      // cerr << "Advancing pointer count by " << samples * sample_size() << " to " << reinterpret_cast<void*>(ptr_to_channel) << endl;
    }
    long int count =  snd_pcm_writen(audio_fd_repp,
				       reinterpret_cast<void**>(nbufs_repp), 
				       samples);
    if (count < 0) {
      /* Note! ALSA versions <=0.9.1 sometimes return -EIO in xrun-state;
       *       EPIPE=xrun, ESTRPIPE=xrun) */
      DBC_CHECK(count != -EINTR);
      if (count == -EPIPE || count == -EIO || count == -ESTRPIPE) {
	if (ignore_xruns() == true) {
	  handle_xrun_playback();
	  snd_pcm_writen(audio_fd_repp,
			   reinterpret_cast<void**>(nbufs_repp),
			   samples);
	  trigger_request_rep = true;
	}
	else {
	  cerr << "ALSA: Overrun! Stopping operation!" << endl;
	  stop();
	  close();
	}
      }
      else {
	cerr << "ALSA: Write error! Stopping operation." << endl;
	stop();
	close();
      }
    }
  }
}

void AUDIO_IO_ALSA_PCM::handle_xrun_playback(void)
{
  snd_pcm_status_t *status;
  snd_pcm_status_alloca(&status);

  int res = snd_pcm_status(audio_fd_repp, status);
  if (res >= 0) {
    snd_pcm_state_t state = snd_pcm_status_get_state(status);
    if (state == SND_PCM_STATE_XRUN) {
      struct timeval now, diff, tstamp;
      gettimeofday(&now, 0);
      snd_pcm_status_get_trigger_tstamp(status, &tstamp);
      timersub(&now, &tstamp, &diff);
      
      cerr << "WARNING: ALSA playback underrun, glitches in audio playback possible!" 
		<< " Break was at least " << kvu_numtostr(diff.tv_sec *
							  1000 +
							  diff.tv_usec /
							  1000.0) 
		<< " ms long." << endl;
      underruns_rep++;
      stop();
      prepare();
      trigger_request_rep = true;
    }
    else if (state == SND_PCM_STATE_SUSPENDED) {
      cerr << "ALSA: Device suspended! Stopping operation!" << endl;
      stop();
      close();
    }
    else {
      cerr << "ALSA: Unknown device state '" 
	   << static_cast<int>(state) << "'" << endl;
    }
  }
  else {
    ECA_LOG_MSG(ECA_LOGGER::info, "snd_pcm_status() failed!");
  }
}

long int AUDIO_IO_ALSA_PCM::delay(void) const
{
  snd_pcm_sframes_t delay = 0;

  if (is_running() == true) {
    if (snd_pcm_delay(audio_fd_repp, &delay) != 0) {
      delay = 0;
    }
  }

  return static_cast<long int>(delay);
}

void AUDIO_IO_ALSA_PCM::set_parameter(int param, 
				      string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    if (label().find("alsaplugin") != string::npos) {
      using_plugin_rep = true;
    }
    break;

  case 2: 
    card_number_rep = atoi(value.c_str());
    break;

  case 3: 
    device_number_rep = atoi(value.c_str());
    break;

  case 4: 
    subdevice_number_rep = atoi(value.c_str());
    break;
  }

  if (using_plugin_rep)
    pcm_device_name_rep = 
      string("plughw:") + 
      kvu_numtostr(card_number_rep) +
      "," +
      kvu_numtostr(device_number_rep) +
      "," +
      kvu_numtostr(subdevice_number_rep);
  else
    pcm_device_name_rep = 
      string("hw:") + 
      kvu_numtostr(card_number_rep) +
      "," +
      kvu_numtostr(device_number_rep) +
      "," +
      kvu_numtostr(subdevice_number_rep);
}

string AUDIO_IO_ALSA_PCM::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return kvu_numtostr(card_number_rep);

  case 3: 
    return kvu_numtostr(device_number_rep);

  case 4: 
    return kvu_numtostr(subdevice_number_rep);
  }
  return "";
}

void AUDIO_IO_ALSA_PCM::set_pcm_device_name(const string& n)
{ 
  if (n.size() > 0)
    pcm_device_name_rep = n; 
  else
    pcm_device_name_rep = default_pcm_device_rep;
}
