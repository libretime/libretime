// ------------------------------------------------------------------------
// audioio-mikmod.cpp: Interface class for MikMod input. Uses FIFO pipes.
// Copyright (C) 1999-2000,2004-2006,2008,2009 Kai Vehmanen
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

#include "audioio-mikmod.h"
#include "eca-logger.h"

using namespace std;

string MIKMOD_INTERFACE::default_mikmod_cmd = "mikmod -d stdout -o 16s -q -f %s -p 0 --noloops %f";

void MIKMOD_INTERFACE::set_mikmod_cmd(const std::string& value)
{
  MIKMOD_INTERFACE::default_mikmod_cmd = value;
}

MIKMOD_INTERFACE::MIKMOD_INTERFACE(const std::string& name)
  : triggered_rep(false),
    finished_rep(false)
{
}

MIKMOD_INTERFACE::~MIKMOD_INTERFACE(void) 
{
  clean_child(true);
  if (is_open() == true) {
    close();
  }
}

void MIKMOD_INTERFACE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  std::string urlprefix;

  triggered_rep = false;
  finished_rep = false;

  string real_filename = label();
  if (real_filename == "mikmod") {
    real_filename = opt_filename_rep;
  }

  struct stat buf;
  int ret = ::stat(real_filename.c_str(), &buf);
  if (ret != 0) {
    size_t offset = real_filename.find_first_of("://");
    if (offset == std::string::npos) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-MIKMOD: Can't open file " + real_filename + "."));
    }
    else {
      urlprefix = std::string(real_filename, 0, offset);
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audioio-mikmod) Found url; protocol '" + urlprefix + "'.");
    }
  }

  /* decoder supports: s16 samples, 2 channels, srate configurable 
     no support for: endianess (use native)  */
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16);
  set_channels(2);

  AUDIO_IO::open();
}

void MIKMOD_INTERFACE::close(void)
{
  if (pid_of_child() > 0) {
    if (io_mode() == io_read) {
      kill_mikmod();
    }
  }
  AUDIO_IO::close();
}

long int MIKMOD_INTERFACE::read_samples(void* target_buffer, long int samples)
{
  if (triggered_rep != true) { 
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: triggering an external program in real-time context");
    triggered_rep = true;
    fork_mikmod();
  }

  bytes_read_rep = ::fread(target_buffer, 1, frame_size() * samples, f1_rep);
  if (bytes_read_rep < samples * frame_size() || bytes_read_rep == 0) {
    if (position_in_samples() == 0) 
      ECA_LOG_MSG(ECA_LOGGER::info, "(audioio-mikmod) Can't start process \"" + MIKMOD_INTERFACE::default_mikmod_cmd + "\". Please check your ~/.ecasound/ecasoundrc.");
    finished_rep = true;
    triggered_rep = false;
  }
  else finished_rep = false;
  return(bytes_read_rep / frame_size());
}

void MIKMOD_INTERFACE::kill_mikmod(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audioio-mikmod) Cleaning mikmod-child with pid " + kvu_numtostr(pid_of_child()) + ".");
  clean_child();
  triggered_rep = false;
}

void MIKMOD_INTERFACE::fork_mikmod(void)
{
  string real_filename = label();
  if (real_filename == "mikmod") {
    real_filename = opt_filename_rep;
  }

  set_fork_command(MIKMOD_INTERFACE::default_mikmod_cmd);
  set_fork_file_name(real_filename);
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
}

void MIKMOD_INTERFACE::set_parameter(int param, 
				     string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;

  case 2: 
    opt_filename_rep = value;
    break;
  }
}

string MIKMOD_INTERFACE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return(label());

  case 2: 
    return(opt_filename_rep);
  }
  return("");
}

void MIKMOD_INTERFACE::start_io(void)
{
  if (triggered_rep != true) {
    if (io_mode() == io_read) 
      fork_mikmod();

    triggered_rep = true;
  }
}

void MIKMOD_INTERFACE::stop_io(void)
{
  if (triggered_rep == true) {
    if (io_mode() == io_read) 
      clean_child(true);
    triggered_rep = false;
  }
}
