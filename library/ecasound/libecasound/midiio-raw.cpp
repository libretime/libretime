// ------------------------------------------------------------------------
// midiio-raw.cpp: Input and output of raw MIDI streams using standard 
//                 UNIX file operations.
// Copyright (C) 2000,2005 Kai Vehmanen
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

#include <cstdio>
#include <fcntl.h>
#include <unistd.h>
#include "midiio-raw.h"

#include "eca-logger.h"

MIDI_IO_RAW::MIDI_IO_RAW(const std::string& name) { label("rawmidi"); device_name_rep = name; }

MIDI_IO_RAW::~MIDI_IO_RAW(void) { if (is_open()) close(); }

void MIDI_IO_RAW::open(void)
{
  int flags = 0;

  switch(io_mode()) {
  case io_read:
    {
      flags = O_RDONLY;
      break;
    }
  case io_write: 
    {
      flags = O_WRONLY;
      break;
    }
  case io_readwrite: 
    {
      flags = O_RDWR;
      break;
    }
  }
  if (nonblocking_mode() == true) flags |= O_NONBLOCK;

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Opening midi device \"" + device_name_rep + "\".");
  fd_rep = ::open(device_name_rep.c_str(), flags);
  if (fd_rep < 0) {
    toggle_open_state(false);
  }
  else {
    toggle_open_state(true);
  }

  finished_rep = false;
}

void MIDI_IO_RAW::close(void)
{
  ::close(fd_rep);
  toggle_open_state(false);
}

bool MIDI_IO_RAW::finished(void) const { return finished_rep; }

long int MIDI_IO_RAW::read_bytes(void* target_buffer, long int bytes)
{
  /* note: ignore bytes, already read one byte at a time */
  long int res = ::read(fd_rep, target_buffer, 1);
  if (res >= 0) return res;
  finished_rep = true;
  return 0;
}

long int MIDI_IO_RAW::write_bytes(void* target_buffer, long int bytes)
{
  long int res = ::write(fd_rep, target_buffer, bytes);
  if (res >= 0) return res;
  finished_rep = true;
  return 0;
}

void MIDI_IO_RAW::set_parameter(int param, 
				std::string value)
{
  switch (param) {
  case 1: 
    label(value);
    break;

  case 2: 
    device_name_rep = value;
    break;
  }
}

std::string MIDI_IO_RAW::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return device_name_rep;
  }
  return "";
}
