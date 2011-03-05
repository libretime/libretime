// ------------------------------------------------------------------------
// audioio-ogg.cpp: Interface for ogg vorbis decoders and encoders.
// Copyright (C) 2000-2002,2004-2006,2008,2009 Kai Vehmanen
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

#include <string>
#include <cstdlib> /* atol() */
#include <unistd.h> /* stat() */
#include <sys/stat.h> /* stat() */

#include <kvu_message_item.h>
#include <kvu_numtostr.h>

#include "audioio-ogg.h"

#include "eca-logger.h"

string OGG_VORBIS_INTERFACE::default_input_cmd = "ogg123 -d raw -o byteorder:%E --file=- %f";
string OGG_VORBIS_INTERFACE::default_output_cmd = "oggenc -b %B --raw --raw-bits=%b --raw-chan=%c --raw-rate=%s --raw-endianness 0 --output=%f -";
long int OGG_VORBIS_INTERFACE::default_output_default_bitrate = 128000;

void OGG_VORBIS_INTERFACE::set_input_cmd(const std::string& value) { OGG_VORBIS_INTERFACE::default_input_cmd = value; }
void OGG_VORBIS_INTERFACE::set_output_cmd(const std::string& value) { OGG_VORBIS_INTERFACE::default_output_cmd = value; }

OGG_VORBIS_INTERFACE::OGG_VORBIS_INTERFACE(const std::string& name)
  : triggered_rep(false),
    finished_rep(false)
{
  set_label(name);
  bitrate_rep = OGG_VORBIS_INTERFACE::default_output_default_bitrate;
}

OGG_VORBIS_INTERFACE::~OGG_VORBIS_INTERFACE(void)
{
  clean_child(true);
  if (is_open() == true) {
    close();
  }
}

void OGG_VORBIS_INTERFACE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  std::string urlprefix;

  triggered_rep = false;
  finished_rep = false;

  if (io_mode() == io_read) {
    struct stat buf;
    int ret = ::stat(label().c_str(), &buf);
    if (ret != 0) {
      size_t offset = label().find_first_of("://");
      if (offset == std::string::npos) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-OGG: Can't open file " + label() + "."));
      }
      else {
	urlprefix = std::string(label(), 0, offset);
	ECA_LOG_MSG(ECA_LOGGER::user_objects, "Found url; protocol '" + urlprefix + "'.");
      }
    }

    /* decoder supports: nothing configurable nor fixed
     * 
     * FIXME: we have no idea about the audio format of the 
     *        stream we get from the decoder... ybe we should force the decoder
     *        to generate RIFF wave to a named pipe and parse the header...? 
     */
  }
  else {
    /* encoder supports: coding, channel-count and srate configurable,
     *                   fixed to little endian */
    ECA_AUDIO_FORMAT::set_sample_endianess(ECA_AUDIO_FORMAT::se_little);
  }

  AUDIO_IO::open();
}

void OGG_VORBIS_INTERFACE::close(void)
{
  if (pid_of_child() > 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Cleaning child process pid=" + kvu_numtostr(pid_of_child()) + ".");
      clean_child();
      triggered_rep = false;
  }

  AUDIO_IO::close();
}

long int OGG_VORBIS_INTERFACE::read_samples(void* target_buffer, long int samples)
{
  if (triggered_rep != true) { 
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: triggering an external program in real-time context"); 
    triggered_rep = true;
    fork_input_process();
  }

  if (f1_rep != 0) {
    bytes_rep = std::fread(target_buffer, 1, frame_size() * samples, f1_rep);
  }
  else {
    bytes_rep = 0;
  }

  if (bytes_rep < samples * frame_size() || bytes_rep == 0) {
    if (position_in_samples() == 0) 
      ECA_LOG_MSG(ECA_LOGGER::info, "Can't start process \"" + fork_command() + "\". Please check your ~/.ecasound/ecasoundrc.");
    finished_rep = true;
    triggered_rep = false;
  }
  else 
    finished_rep = false;

  return bytes_rep / frame_size();
}

void OGG_VORBIS_INTERFACE::write_samples(void* target_buffer, long int samples)
{
  if (triggered_rep != true) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: triggering an external program in real-time context"); 
    triggered_rep = true;
    fork_output_process();
  }

  if (wait_for_child() != true) {
    finished_rep = true;
    triggered_rep = false;
    ECA_LOG_MSG(ECA_LOGGER::errors, "Attempt to write after child process has terminated.");
  }
  else {
    if (filedes_rep > 0) {
      bytes_rep = ::write(filedes_rep, target_buffer, frame_size() * samples);
    }
    else {
      bytes_rep = 0;
    }
    if (bytes_rep < frame_size() * samples) {
      finished_rep = true;
      triggered_rep = false;
      ECA_LOG_MSG(ECA_LOGGER::errors, 
		  "Error in writing to child process (to write " 
		  + kvu_numtostr(frame_size() * samples) 
		  + ", result "
		  + kvu_numtostr(bytes_rep) 
		  + ").");
    }
    else 
      finished_rep = false;
  }
}

void OGG_VORBIS_INTERFACE::set_parameter(int param, string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;

  case 2: 
    long int numvalue = atol(value.c_str());
    if (numvalue > 0) 
      bitrate_rep = numvalue;
    else
      bitrate_rep = OGG_VORBIS_INTERFACE::default_output_default_bitrate;
    break;
  }
}

string OGG_VORBIS_INTERFACE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return kvu_numtostr(bitrate_rep);
  }
  return "";
}

void OGG_VORBIS_INTERFACE::fork_input_process(void)
{
  string command = OGG_VORBIS_INTERFACE::default_input_cmd;

  // replace with 'little/big' byteorder
  if (command.find("%E") != string::npos) {
    string byteorder ("big");
    if (sample_endianess() == ECA_AUDIO_FORMAT::se_little) byteorder = "little";
    command.replace(command.find("%E"), 2, byteorder);
  }

  set_fork_command(command);
  set_fork_file_name(label());
  set_fork_pipe_name();
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
  else
    f1_rep = 0;
}

void OGG_VORBIS_INTERFACE::fork_output_process(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Starting to encode " + label() + " with vorbize.");
  string command = OGG_VORBIS_INTERFACE::default_output_cmd;

  // replace with bitrate
  if (command.find("%B") != string::npos) {
    command.replace(command.find("%B"), 2, kvu_numtostr((long int)(bitrate_rep / 1000)));
  }

  set_fork_command(command);
  set_fork_file_name(label());

  set_fork_bits(bits());
  set_fork_channels(channels());
  set_fork_sample_rate(samples_per_second());

  fork_child_for_write();
  if (child_fork_succeeded() == true) {
    filedes_rep = file_descriptor();
  }
  else {
    filedes_rep = 0;
  }
}

void OGG_VORBIS_INTERFACE::start_io(void)
{
  if (triggered_rep != true) {
    if (io_mode() == io_read) 
      fork_input_process();
    else
      fork_output_process();

    triggered_rep = true;
  }
}

void OGG_VORBIS_INTERFACE::stop_io(void)
{
  if (triggered_rep == true) {
    if (io_mode() == io_read) {
      fclose(f1_rep);
      f1_rep = 0;
      clean_child(true);
    }
    else
      clean_child(false);

    triggered_rep = false;
  }
}
