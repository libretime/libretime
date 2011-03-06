// ------------------------------------------------------------------------
// audioio-raw.cpp: Raw/headerless audio file format input/output
// Copyright (C) 1999-2001 Kai Vehmanen
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
#include <string>
#include <cstring>
#include <cstdio>
#include <cassert>
#include <sys/stat.h>
#include <unistd.h>

#include "audioio-buffered.h"
#include "audioio-raw.h"
#include "eca-error.h"
#include "eca-logger.h"

RAWFILE::RAWFILE(const std::string& name)
{
  set_label(name);
  fio_repp = 0;
  mmaptoggle_rep = "0";
}

RAWFILE::~RAWFILE(void)
{
  if (is_open() == true) {
    close();
  }
}

RAWFILE* RAWFILE::clone(void) const
{
  RAWFILE* target = new RAWFILE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return(target);
}

/**
 * Opens the raw audio i/o device. 
 * 
 * Note! Cases where label() matches either "stdin", "stdout"
 *       or "stderr" are handled as special cases.
 */
void RAWFILE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{ 
  switch(io_mode()) {
  case io_read:
    {
      if (label() == "stdin" || label().at(0) == '-') {
	fio_repp = new ECA_FILE_IO_STREAM();
	fio_repp->open_stdin();
      }
      else {
	if (mmaptoggle_rep == "1") {
	  ECA_LOG_MSG(ECA_LOGGER::user_objects, "(audioio-wave) using mmap() mode for file access");
	  fio_repp = new ECA_FILE_IO_MMAP();
	}
	else fio_repp = new ECA_FILE_IO_STREAM();
	fio_repp->open_file(label(),"rb");
	if (fio_repp->is_file_ready() != true) {
	  throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-RAW: Couldn't open file " + label() + " for reading."));
	}
      }
      break;
    }
  case io_write: 
    {
      fio_repp = new ECA_FILE_IO_STREAM();
      if (label() == "stdout" || label().at(0) == '-') {
	std::cerr << "(audioio-raw) Outputting to standard output [w].\n";
	fio_repp->open_stdout();
      }
      else if (label() == "stderr" || label().at(0) == '-') {
	fio_repp->open_stderr();
      }
      else {
	fio_repp->open_file(label(),"wb");
	if (fio_repp->is_file_ready() != true) {
	  throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-RAW: Couldn't open file " + label() + " for writing."));
	}
      }
      break;
    }
  case io_readwrite: 
    {
      fio_repp = new ECA_FILE_IO_STREAM();
      if (label() == "stdout" || label().at(0) == '-') {
	std::cerr << "(audioio-raw) Outputting to standard output [rw].\n";
	fio_repp->open_stdout();
      }
      else {
	fio_repp->open_file(label(),"r+b");
	if (fio_repp->file_mode() == "") {
	  fio_repp->open_file(label(),"w+b");
	  if (fio_repp->is_file_ready() != true) {
	    throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-RAW: Couldn't open file " + label() + " for read&write."));
	  }
	}
      }
    }
  }
  set_length_in_bytes();

  AUDIO_IO::open();
}

void RAWFILE::close(void)
{
  if (fio_repp != 0) {
    fio_repp->close_file();
    delete fio_repp;
    fio_repp = 0;
  }

  AUDIO_IO::close();
}

bool RAWFILE::finished(void) const
{
 if (fio_repp->is_file_error() ||
     !fio_repp->is_file_ready()) 
   return true;

 return false;
}

long int RAWFILE::read_samples(void* target_buffer, long int samples)
{
  fio_repp->read_to_buffer(target_buffer, frame_size() * samples);
  return(fio_repp->file_bytes_processed() / frame_size());
}

void RAWFILE::write_samples(void* target_buffer, long int samples)
{
  fio_repp->write_from_buffer(target_buffer, frame_size() * samples);  
}

SAMPLE_SPECS::sample_pos_t RAWFILE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  if (is_open() == true) {
    fio_repp->set_file_position(pos * frame_size());
  }
  return pos;
}

void RAWFILE::set_length_in_bytes(void)
{
  set_length_in_samples(fio_repp->get_file_length() / frame_size());
}

void RAWFILE::set_parameter(int param, 
			    string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;

  case 2: 
    mmaptoggle_rep = value;
    break;
  }
}

string RAWFILE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return(label());

  case 2: 
    return(mmaptoggle_rep);
  }
  return("");
}
