// ------------------------------------------------------------------------
// audioio-rtnull.cpp: Null audio object with realtime behaviour
// Copyright (C) 1999,2002,2005 Kai Vehmanen
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

#include <cmath>
#include <iostream>
#include <string>
#include <sys/time.h> /* gettimeofday() */

#include <kvu_dbc.h>
#include <kvu_utils.h>

#include "audioio-device.h"
#include "audioio-rtnull.h"

#include "eca-error.h"
#include "eca-logger.h"

using std::cerr;
using std::endl;

/** 
 * Definitions from glibc's sys/time.h (LGPL 2.1)
 */

#ifndef timerclear
#define	timerclear(tvp)		((tvp)->tv_sec = (tvp)->tv_usec = 0)
#endif

#ifndef timeradd
#define	timeradd(a, b, result)						      \
  do {									      \
    (result)->tv_sec = (a)->tv_sec + (b)->tv_sec;			      \
    (result)->tv_usec = (a)->tv_usec + (b)->tv_usec;			      \
    if ((result)->tv_usec >= 1000000)					      \
      {									      \
	++(result)->tv_sec;						      \
	(result)->tv_usec -= 1000000;					      \
      }									      \
  } while (0)
#endif

#ifndef timercmp
#define	timercmp(a, b, CMP) 						      \
  (((a)->tv_sec == (b)->tv_sec) ? 					      \
   ((a)->tv_usec CMP (b)->tv_usec) : 					      \
   ((a)->tv_sec CMP (b)->tv_sec))
#endif

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

/**
 * Function definitions
 */

REALTIME_NULL::REALTIME_NULL(const std::string& name)
{
  xruns_rep = 0;
}

REALTIME_NULL::~REALTIME_NULL(void)
{ 
  if (is_open() == true && is_running()) stop();

  if (is_open() == true) {
    close();
  }

  if (xruns_rep != 0) {
    cerr << "(audioio-rtnull) WARNING! There were " << xruns_rep << " xruns while ";
    if (io_mode() != io_read) {
      cerr << "writing.\n";
    }
    else {
      cerr << "reading.\n";
    }
  }
}

void REALTIME_NULL::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "open");

  double t = static_cast<double>(buffersize()) / samples_per_second();

  buffer_length_rep.tv_sec = static_cast<time_t>(floor(t));
  buffer_length_rep.tv_usec = static_cast<long>((t - buffer_length_rep.tv_sec) * 1000000.0);

  total_buffers_rep = 3;
  if (max_buffers() == true) { 
    total_buffers_rep = 8;
  }
  total_buffer_length_rep.tv_sec = total_buffers_rep * buffer_length_rep.tv_sec;
  total_buffer_length_rep.tv_usec = total_buffers_rep * buffer_length_rep.tv_usec;

  AUDIO_IO_DEVICE::open();
}

void REALTIME_NULL::close(void)
{
  AUDIO_IO_DEVICE::close();
}

void REALTIME_NULL::prepare(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "prepare");

  timerclear(&data_processed_rep);

  AUDIO_IO_DEVICE::prepare();
}

void REALTIME_NULL::start(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "start");

  gettimeofday(&start_time_rep, NULL);

  AUDIO_IO_DEVICE::start();
}

void REALTIME_NULL::stop(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "stop");

  AUDIO_IO_DEVICE::stop();
}

/**
 * Calculates 'time_since_start_rep'.
 */
void REALTIME_NULL::calculate_device_position(void)
{
  struct timeval now;
  gettimeofday(&now, NULL);

  timersub(&now, &start_time_rep, &time_since_start_rep);
}

/**
 * Calculates 'avail_data_rep'.
 */
void REALTIME_NULL::calculate_available_data(void) const
{ 
  if (io_mode() == io_read) {
    /* capture: device is always ahead */
    timersub(&time_since_start_rep, &data_processed_rep, &avail_data_rep);
  }
  else {
    /* playback: device is always behind */
    struct timeval diff;
    timersub(&data_processed_rep, &time_since_start_rep, &diff);
    timersub(&total_buffer_length_rep, &diff, &avail_data_rep);
  }
  if (timercmp(&avail_data_rep, &total_buffer_length_rep, >)) {
    ++xruns_rep;
    // cerr << "(audioio-rtnull) xrun occured!" << endl;
  }
} 

void REALTIME_NULL::block_until_data_available(void) 
{
  calculate_device_position();
  calculate_available_data();

  while (timercmp(&avail_data_rep, &buffer_length_rep, <)) {
    struct timeval delay;

    timersub(&buffer_length_rep, &avail_data_rep, &delay);

    // cerr << "(audioio-rtnull) sleeping for: " << ndelay.tv_sec << " sec, " << ndelay.tv_nsec << " nanoseconds.\n";
    kvu_sleep(delay.tv_sec, delay.tv_usec * 1000);

    calculate_device_position();
    calculate_available_data();
  }
}

long int REALTIME_NULL::read_samples(void* target_buffer,
				     long int samples)
{
  DBC_CHECK(is_running() == true);

  for(int n = 0; n < samples * frame_size(); n++) ((char*)target_buffer)[n] = 0;

  block_until_data_available();

  /* read one buffer of audio */
  timeradd(&data_processed_rep, &buffer_length_rep, &data_processed_rep);

  return buffersize();
}

void REALTIME_NULL::write_samples(void* target_buffer, 
				  long int samples)
{ 
  if (is_running() != true) {
    /* prefill phase */

    /* write one buffer of audio */
    timeradd(&data_processed_rep, &buffer_length_rep, &data_processed_rep);
  }
  else {
    /* block until write space available */
    block_until_data_available();
  
    /* write one buffer of audio */
    timeradd(&data_processed_rep, &buffer_length_rep, &data_processed_rep);
  }
}

long int REALTIME_NULL::prefill_space(void) const
{
  if (io_mode() != io_read) return total_buffers_rep * buffersize();
  return 0;
}

long int REALTIME_NULL::delay(void) const
{ 
  long int delay = 0;

  if (is_running() == true) {
    calculate_available_data();
    
    double time = avail_data_rep.tv_sec * 1000000.0 + avail_data_rep.tv_usec;
    delay = static_cast<SAMPLE_SPECS::sample_pos_t>
      (time * samples_per_second() / 1000000.0);
  }

  DBC_CHECK(delay >= 0);
  return delay;
}
