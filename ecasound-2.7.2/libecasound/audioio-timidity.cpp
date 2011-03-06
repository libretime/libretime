// ------------------------------------------------------------------------
// audioio-timidity.cpp: Interface class for Timidity++ input.
// Copyright (C) 2000,2002,2004-2006,2008,2009 Kai Vehmanen
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
#include <unistd.h> /* stat() */
#include <sys/stat.h> /* stat() */

#include <kvu_numtostr.h>

#include "audioio-timidity.h"
#include "eca-logger.h"

string TIMIDITY_INTERFACE::default_timidity_cmd = "timidity -Or1S -id -s %s -o - %f";

void TIMIDITY_INTERFACE::set_timidity_cmd(const std::string& value) { TIMIDITY_INTERFACE::default_timidity_cmd = value; }

TIMIDITY_INTERFACE::TIMIDITY_INTERFACE(const std::string& name)
  : triggered_rep(false),
    finished_rep(false)
{
}

TIMIDITY_INTERFACE::~TIMIDITY_INTERFACE(void)
{
  clean_child(true);
  if (is_open() == true) {
    close();
  }
}

void TIMIDITY_INTERFACE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{ 
  std::string urlprefix;
  struct stat buf;
  int ret = ::stat(label().c_str(), &buf);
  if (ret != 0) {
    size_t offset = label().find_first_of("://");
    if (offset == std::string::npos) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-TIMIDITY: Can't open file " + label() + "."));
    }
    else {
      urlprefix = std::string(label(), 0, offset);
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audioio-timidity) Found url; protocol '" + urlprefix + "'.");
    }
  }
  
  /* decoder supports: s16 samples, 2 channels, srate configurable 
     no support for: endianess (use native)  */
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16);
  set_channels(2);

  triggered_rep = false;
  finished_rep = false;

  AUDIO_IO::open();
}

void TIMIDITY_INTERFACE::close(void)
{
  if (pid_of_child() > 0) {
    if (io_mode() == io_read) {
      kill_timidity();
    }
  }

  AUDIO_IO::close();
}

long int TIMIDITY_INTERFACE::read_samples(void* target_buffer, 
					  long int samples)
{
  if (triggered_rep != true) { 
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: triggering an external program in real-time context");
    triggered_rep = true;
    fork_timidity();
  }

  bytes_read_rep = std::fread(target_buffer, 1, frame_size() * samples, f1_rep);
  if (bytes_read_rep < samples * frame_size() || bytes_read_rep == 0) {
    if (position_in_samples() == 0) 
      ECA_LOG_MSG(ECA_LOGGER::info, "Can't start process \"" + TIMIDITY_INTERFACE::default_timidity_cmd + "\". Please check your ~/.ecasound/ecasoundrc.");
    finished_rep = true;
    triggered_rep = false;
  }
  else finished_rep = false;
  return(bytes_read_rep / frame_size());
}

void TIMIDITY_INTERFACE::kill_timidity(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audioio-timidity) Cleaning Timidity++-child with pid=" + kvu_numtostr(pid_of_child()) + ".");
  clean_child();
  triggered_rep = false;
}

void TIMIDITY_INTERFACE::fork_timidity(void)
{
  set_fork_command(TIMIDITY_INTERFACE::default_timidity_cmd);
  set_fork_file_name(label());
  set_fork_bits(bits());
  set_fork_channels(channels());
  set_fork_sample_rate(samples_per_second());
  fork_child_for_read();
  if (child_fork_succeeded() == true) {
    /* NOTE: the file description will be closed by 
     *       AUDIO_IO_FORKED_STREAM::clean_child() */
    filedes_rep = file_descriptor();
    f1_rep = fdopen(filedes_rep, "r"); /* not part of <cstdio> */
    if (f1_rep == 0) {
      finished_rep = true;
      triggered_rep = false;
    }    
  }
  if (wait_for_child() != true) {
    finished_rep = true;
    triggered_rep = false;
  }
}

void TIMIDITY_INTERFACE::start_io(void)
{
  if (triggered_rep != true) {
    if (io_mode() == io_read) 
      fork_timidity();

    triggered_rep = true;
  }
}

void TIMIDITY_INTERFACE::stop_io(void)
{
  if (triggered_rep == true) {
    if (io_mode() == io_read) 
      clean_child(true);
    triggered_rep = false;
  }
}
