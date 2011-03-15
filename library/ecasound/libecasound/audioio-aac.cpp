// ------------------------------------------------------------------------
// audioio-aac.cpp: Interface class for FAAC/FAAD AAC encoder/decoder.
// Copyright (C) 2004-2006,2008,2009 Kai Vehmanen
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

#include "audioio-aac.h"

#include "eca-logger.h"

/**
 * References:
 *    http://www.audiocoding.com/
 */

string AAC_FORKED_INTERFACE::default_input_cmd = "faad -w -b 1 -f 2 -d %f";
string AAC_FORKED_INTERFACE::default_output_cmd = "faac -P -o %f -R %s -B %b -C %c -";

void AAC_FORKED_INTERFACE::set_input_cmd(const std::string& value) { AAC_FORKED_INTERFACE::default_input_cmd = value; }
void AAC_FORKED_INTERFACE::set_output_cmd(const std::string& value) { AAC_FORKED_INTERFACE::default_output_cmd = value; }

AAC_FORKED_INTERFACE::AAC_FORKED_INTERFACE(const std::string& name)
  : triggered_rep(false),
    finished_rep(false)
{
  set_label(name);
}

AAC_FORKED_INTERFACE::~AAC_FORKED_INTERFACE(void)
{
  clean_child(true);
  if (is_open() == true) {
    close();
  }
}

void AAC_FORKED_INTERFACE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  std::string urlprefix;

  finished_rep = false;
  triggered_rep = false;

  /**
   * FIXME: we have no idea about the audio format of the 
   *        stream we get from ogg123... maybe we should force decoder
   *        to generate RIFF wave to a named pipe and parse the header...?
   */

  if (io_mode() == io_read) {
    struct stat buf;
    int ret = ::stat(label().c_str(), &buf);
    if (ret != 0) {
      size_t offset = label().find_first_of("://");
      if (offset == std::string::npos) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-AAC: Can't open file " + label() + "."));
      }
      else {
	urlprefix = std::string(label(), 0, offset);
	ECA_LOG_MSG(ECA_LOGGER::user_objects, "Found url; protocol '" + urlprefix + "'.");
      }
    }

    /* decoder supports: fixed channel count and sample format, 
                         sample rate unknown and must be set manually  */
    set_channels(2); /* 5.1 downmixed to stereo if needed by faad2 */
    /* FIXME: fix properly */
    set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_le);
  }
  else {
    /* encoder supports: coding, channel-count and srate configurable;
       fixed endianess */
    set_sample_endianess(ECA_AUDIO_FORMAT::se_big);
  }

  AUDIO_IO::open();
}

void AAC_FORKED_INTERFACE::close(void)
{
  if (pid_of_child() > 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Cleaning child process pid=" + kvu_numtostr(pid_of_child()) + ").");
      clean_child();
      triggered_rep = false;
  }

  AUDIO_IO::close();
}

long int AAC_FORKED_INTERFACE::read_samples(void* target_buffer, long int samples)
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
      ECA_LOG_MSG(ECA_LOGGER::info, "Can't start process \"" + AAC_FORKED_INTERFACE::default_input_cmd + "\". Please check your ~/.ecasound/ecasoundrc.");
    finished_rep = true;
    triggered_rep = false;
  }
  else 
    finished_rep = false;

  return bytes_rep / frame_size();
}

void AAC_FORKED_INTERFACE::write_samples(void* target_buffer, long int samples)
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
      ECA_LOG_MSG(ECA_LOGGER::info, "Can't start process \"" + AAC_FORKED_INTERFACE::default_output_cmd + "\". FAAC v1.24 or newer is required. Please check your ~/.ecasound/ecasoundrc.");
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

void AAC_FORKED_INTERFACE::set_parameter(int param, string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;
  }
}

string AAC_FORKED_INTERFACE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();
  }
  return "";
}

void AAC_FORKED_INTERFACE::fork_input_process(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, AAC_FORKED_INTERFACE::default_input_cmd);

  set_fork_command(AAC_FORKED_INTERFACE::default_input_cmd);
  set_fork_file_name(label());

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

void AAC_FORKED_INTERFACE::fork_output_process(void)
{
  string command = AAC_FORKED_INTERFACE::default_output_cmd;

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

void AAC_FORKED_INTERFACE::start_io(void)
{
  if (triggered_rep != true) {
    if (io_mode() == io_read) 
      fork_input_process();
    else
      fork_output_process();

    triggered_rep = true;
  }
}

void AAC_FORKED_INTERFACE::stop_io(void)
{
  if (triggered_rep == true) {
    if (io_mode() == io_read) 
      clean_child(true);
    else
      clean_child(false);

    triggered_rep = false;
  }
}
