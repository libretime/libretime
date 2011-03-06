// ------------------------------------------------------------------------
// audioio-wave.cpp: RIFF WAVE audio file input/output.
// Copyright (C) 1999-2003,2005,2008,2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// References:
//     - http://ccrma.stanford.edu/courses/422/projects/WaveFormat/
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

#include <cstdio>
#include <cstring>
#include <cmath>
#include <string>
#ifdef HAVE_SYS_TYPES_H
#include <sys/types.h> /* off_t */
#endif

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "sample-specs.h" /* for system endianess */
#include "samplebuffer.h"
#include "audioio-wave.h"

#include "eca-fileio-mmap.h"
#include "eca-fileio-stream.h"

#include "eca-logger.h"

/**
 * Private macro definitions
 */

#ifndef UINT32_MAX
#define UINT32_MAX 4294967295U
#endif

#ifdef WORDS_BIGENDIAN
static const bool is_system_littleendian = false;
#else
static const bool is_system_littleendian = true;
#endif

/**
 * Private function declarations 
 */

static uint16_t little_endian_uint16(uint16_t arg);
static uint32_t little_endian_uint32(uint32_t arg);

/**
 * Private function definitions
 */

static uint16_t little_endian_uint16(uint16_t arg)
{
  if (is_system_littleendian != true) {
    return ((arg >> 8) & 0x00ff) | ((arg << 8) & 0xff00);
  }
  return arg;
}

static uint32_t little_endian_uint32(uint32_t arg)
{
  if (is_system_littleendian != true) {
    return ((arg >> 24) & 0x000000ff) |
	   ((arg >> 8)  & 0x0000ff00) |
	   ((arg << 8)  & 0x00ff0000) |
	   ((arg << 24) & 0xff000000);
  }
  return arg;
}

/**
 * Public definitions
 */

/**
 * Print extra debug information about RIFF header 
 * contents to stdout when opening files.
 */
// #define DEBUG_WAVE_HEADER

WAVEFILE::WAVEFILE (const std::string& name)
{
  set_label(name);
  fio_repp = 0;
  mmaptoggle_rep = "0";
}

WAVEFILE::~WAVEFILE(void)
{
  if (is_open() == true) {
    close();
  }
}

WAVEFILE* WAVEFILE::clone(void) const
{
  WAVEFILE* target = new WAVEFILE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void WAVEFILE::format_query(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  // --------
  DBC_REQUIRE(is_open() != true);
  // --------

  if (io_mode() == io_write) return;

  fio_repp = new ECA_FILE_IO_STREAM();
  if (fio_repp == 0) {
    throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Critical error when opening file \"" + label() + "\" for reading."));
  }
  fio_repp->open_file(label(), "rb");
  if (fio_repp->file_mode() != "") {
    set_length_in_bytes();
    read_riff_fmt();     // also sets format()
    find_riff_datablock();
    fio_repp->close_file();
  }
  delete fio_repp;
  fio_repp = 0;

  // -------
  DBC_ENSURE(!is_open());
  DBC_ENSURE(fio_repp == 0);
  // -------
}

void WAVEFILE::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  switch(io_mode()) {
  case io_read:
    {
      if (mmaptoggle_rep == "1") {
	ECA_LOG_MSG(ECA_LOGGER::user_objects, "using mmap() mode for file access");
	fio_repp = new ECA_FILE_IO_MMAP();
      }
      else  fio_repp = new ECA_FILE_IO_STREAM();
      if (fio_repp == 0) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Critical error when opening file \"" + label() + "\" for reading."));
      }
      fio_repp->open_file(label(), "rb");
      if (fio_repp->is_file_ready() != true) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Couldn't open file \"" + label() + "\" for reading."));
      }
      read_riff_header();
      read_riff_fmt();     // also sets format()
      set_length_in_bytes();
      find_riff_datablock();
      break;
    }
  case io_write:
    {
      fio_repp = new ECA_FILE_IO_STREAM();
      if (fio_repp == 0) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Critical error when opening file \"" + label() + "\" for writing."));
      }
      fio_repp->open_file(label(), "w+b");
      if (fio_repp->is_file_ready() != true) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Couldn't open file \"" + label() + "\" for writing."));
      }
      write_riff_header();
      write_riff_fmt();
      write_riff_datablock();
      break;
    }

  case io_readwrite:
    {
      fio_repp = new ECA_FILE_IO_STREAM();
      if (fio_repp == 0) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Critical error when opening file \"" + label() + "\" for read&write."));
      }
      fio_repp->open_file(label(), "r+b");
      if (fio_repp->file_mode() != "") {
	set_length_in_bytes();
	read_riff_fmt();     // also sets format()
	find_riff_datablock();
      }
      else {
	fio_repp->open_file(label(), "w+b");
	if (fio_repp->is_file_ready() != true) 
	  throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Couldn't open file \"" + label() + "\" for read&write."));

	write_riff_header();
	write_riff_fmt();
	write_riff_datablock();
      }
      if (fio_repp->is_file_ready() != true) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-WAVE: Couldn't open file \"" + label() + "\" for read&write."));
      }
    }
  }


  if (little_endian_uint16(riff_format_rep.bits) > 8 && 
      format_string()[0] == 'u')
    throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-WAVE: unsigned sample format accepted only with 8bit."));

  if (little_endian_uint16(riff_format_rep.bits) > 8 && 
      format_string().size() > 4 &&
      format_string()[4] == 'b') {
    /* force little-endian operation / affects only write-mode */
    set_sample_format_string(format_string()[0] + kvu_numtostr(bits()) + "_le");
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "forcing little-endian operation (" + format_string() + ")");
    DBC_CHECK(format_string().size() > 4 && format_string()[4] != 'b');
  }

  AUDIO_IO::open();
}

void WAVEFILE::close(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects,"Closing file " + label());
  if (is_open() == true && fio_repp != 0) {
    update();
    fio_repp->close_file();
    delete fio_repp;
    fio_repp = 0;
  }

  AUDIO_IO::close();
}

void WAVEFILE::update (void)
{
  if (io_mode() != io_read) {
    update_riff_datablock();
    write_riff_header();
    set_length_in_bytes();
  }
}

void WAVEFILE::find_riff_datablock (void) throw(AUDIO_IO::SETUP_ERROR&)
{
  if (find_block("data", 0) != true) {
    throw(ECA_ERROR("AUDIOIO-WAVE", "no RIFF data block found", ECA_ERROR::retry));
  }
  data_start_position_rep = fio_repp->get_file_position();
}

void WAVEFILE::read_riff_header (void) throw(AUDIO_IO::SETUP_ERROR&) 
{
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "read_riff_header()");
   
  fio_repp->read_to_buffer(&riff_header_rep, sizeof(riff_header_rep));

  //  fread(&riff_header_rep,1,sizeof(riff_header_rep),fobject);
  if ((memcmp("RIFF",riff_header_rep.id,4) == 0  &&
       memcmp("WAVE",riff_header_rep.wname,4) == 0) != true) {
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-WAVE: invalid RIFF-header (read)"));
  }
}

void WAVEFILE::write_riff_header (void) throw(AUDIO_IO::SETUP_ERROR&) 
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "write_riff_header()");

  off_t savetemp = fio_repp->get_file_position();
    
  memcpy(riff_header_rep.id,"RIFF",4);
  memcpy(riff_header_rep.wname,"WAVE",4);

  /* hack for 64bit wav files */
#if _FILE_OFFSET_BITS == 64
  if (fio_repp->get_file_length() > static_cast<off_t>(UINT32_MAX))
    riff_header_rep.size = little_endian_uint32(UINT32_MAX);
  else 
#endif
    if (fio_repp->get_file_length() > static_cast<off_t>(sizeof(riff_header_rep)))
      riff_header_rep.size = little_endian_uint32(fio_repp->get_file_length() - sizeof(riff_header_rep));
  else
    riff_header_rep.size = little_endian_uint32(0);

  fio_repp->set_file_position(0);
  //  fseek(fobject,0,SEEK_SET);

  fio_repp->write_from_buffer(&riff_header_rep, sizeof(riff_header_rep));
  //  fwrite(&riff_header_rep,1,sizeof(riff_header_rep),fobject);
  if (memcmp("RIFF",riff_header_rep.id,4) != 0 || 
      memcmp("WAVE",riff_header_rep.wname,4) != 0)
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-WAVE: invalid RIFF-header (write)"));

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Wave data size " + kvu_numtostr(little_endian_uint32(riff_header_rep.size)));

  fio_repp->set_file_position(savetemp);
}

void WAVEFILE::read_riff_fmt(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "read_riff_fmt()");

  off_t savetemp = fio_repp->get_file_position();    

  if (find_block("fmt ", 0) != true)
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-WAVE: no riff fmt-block found"));
  else {
    fio_repp->read_to_buffer(&riff_format_rep, sizeof(riff_format_rep));
    //    fread(&riff_format_rep,1,sizeof(riff_format_rep),fobject);

#ifdef DEBUG_WAVE_HEADER
    std::cout << "RF: format = " << little_endian_uint16(riff_format_rep.format) << std::endl;
    std::cout << "RF: channels = " << little_endian_uint16(riff_format_rep.channels) << std::endl;
    std::cout << "RF: srate = " << little_endian_uint32(riff_format_rep.srate) << std::endl;
    std::cout << "RF: byte_second = " << little_endian_uint32(riff_format_rep.byte_second) << std::endl;
    std::cout << "RF: align = " << little_endian_uint16(riff_format_rep.align) << std::endl;
    std::cout << "RF: bits = " << little_endian_uint16(riff_format_rep.bits) << std::endl;
#endif

    if (little_endian_uint16(riff_format_rep.format) != 1 &&
	little_endian_uint16(riff_format_rep.format) != 3) {
      throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-WAVE: Only WAVE_FORMAT_PCM and WAVE_FORMAT_IEEE_FLOAT are supported."));
    }
    
    set_samples_per_second(little_endian_uint32(riff_format_rep.srate));
    set_channels(little_endian_uint16(riff_format_rep.channels));

    if (little_endian_uint16(riff_format_rep.bits) == 32) {
      if (little_endian_uint16(riff_format_rep.format) == 3)
	set_sample_format(ECA_AUDIO_FORMAT::sfmt_f32_le);
      else
	set_sample_format(ECA_AUDIO_FORMAT::sfmt_s32_le);
    }
    else if (little_endian_uint16(riff_format_rep.bits) == 24) {
      if (riff_format_rep.align == little_endian_uint16(channels() * 3)) {
	/* packet s24 format */
	set_sample_format(ECA_AUDIO_FORMAT::sfmt_s24_le);
      }
      else if (riff_format_rep.align == little_endian_uint16(channels() * 4)) {
	/* unpacked s24 format */
	set_sample_format(ECA_AUDIO_FORMAT::sfmt_s32_le);
      }
      else {
	throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-WAVE: Invalid 24bit sample format combination."));
      }
    }
    else if (little_endian_uint16(riff_format_rep.bits) == 16)
      set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_le);
    else if (little_endian_uint16(riff_format_rep.bits) == 8)
      set_sample_format(ECA_AUDIO_FORMAT::sfmt_u8);
    else 
      throw(SETUP_ERROR(SETUP_ERROR::sample_format, "AUDIOIO-WAVE: Sample format not supported."));
  }

  DBC_CHECK(little_endian_uint16(riff_format_rep.channels) == channels());
  DBC_CHECK(little_endian_uint16(riff_format_rep.bits) == bits());
  DBC_CHECK(little_endian_uint32(riff_format_rep.srate) == static_cast<uint32_t>(samples_per_second()));
  DBC_CHECK(little_endian_uint32(riff_format_rep.byte_second) == static_cast<uint32_t>(bytes_per_second()));
  DBC_CHECK(little_endian_uint16(riff_format_rep.align) == frame_size());

  fio_repp->set_file_position(savetemp);
}

void WAVEFILE::write_riff_fmt(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "write_riff_fmt()");

  RB fblock;

  fio_repp->set_file_position_end();

  riff_format_rep.channels = little_endian_uint16(channels());
  riff_format_rep.bits = little_endian_uint16(bits());
  riff_format_rep.srate = little_endian_uint32(samples_per_second());
  riff_format_rep.byte_second = little_endian_uint32(bytes_per_second());
  riff_format_rep.align = little_endian_uint16(frame_size());
  if (sample_coding() == ECA_AUDIO_FORMAT::sc_float) {
    // WAVE_FORMAT_IEEE_FLOAT 0x0003 (Microsoft IEEE754 range [-1, +1))
    riff_format_rep.format = little_endian_uint16(3);
  }
  else {
    // WAVE_FORMAT_PCM (0x0001) Microsoft Pulse Code Modulation (PCM) format
    riff_format_rep.format = little_endian_uint16(1);
  }

  memcpy(fblock.sig, "fmt ", 4);
  fblock.bsize = little_endian_uint32(16);

  fio_repp->write_from_buffer(&fblock, sizeof(fblock));
  fio_repp->write_from_buffer(&riff_format_rep, sizeof(riff_format_rep));
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Wrote RIFF format header.");
}

void WAVEFILE::write_riff_datablock(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "write_riff_datablock()");

  RB fblock;

  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "write_riff_datablock()");
    
  fio_repp->set_file_position_end();

  memcpy(fblock.sig,"data",4);
  fblock.bsize = little_endian_uint32(0);
  fio_repp->write_from_buffer(&fblock, sizeof(fblock));
  data_start_position_rep = fio_repp->get_file_position();
}

void WAVEFILE::update_riff_datablock(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "update_riff_datablock()");

  RB fblock;
    
  memcpy(fblock.sig,"data",4);

  find_block("data", 0);
  off_t savetemp = fio_repp->get_file_position();

  fio_repp->set_file_position_end();

  /* hack for wav files with length over 2^32-1 bytes */
#if _FILE_OFFSET_BITS == 64
  if (fio_repp->get_file_position() - savetemp > static_cast<off_t>(UINT32_MAX))
    fblock.bsize = little_endian_uint32(UINT32_MAX);
  else
#endif
    fblock.bsize = little_endian_uint32(fio_repp->get_file_position() - savetemp);

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "updating data block header length to " + 
	      kvu_numtostr(little_endian_uint32(fblock.bsize)));

  savetemp = savetemp - sizeof(fblock);
  if (savetemp > 0) {
    fio_repp->set_file_position(savetemp);
    fio_repp->write_from_buffer(&fblock, sizeof(fblock));
  }
}

bool WAVEFILE::next_riff_block(RB *t, off_t *offtmp)
{
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "next_riff_block()");

  fio_repp->read_to_buffer(t, sizeof(RB));
  if (fio_repp->file_bytes_processed() != sizeof(RB)) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "invalid RIFF block!");
    return false;
  }
    
  if (!fio_repp->is_file_ready()) return false;
  *offtmp = little_endian_uint32(t->bsize) + fio_repp->get_file_position();
  return true;
}

bool WAVEFILE::find_block(const char* fblock, uint32_t* blksize)
{
  off_t offset;
  RB block;

  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "find_block(): " + string(fblock,4));
    
  fio_repp->set_file_position(sizeof(riff_header_rep));
  while(next_riff_block(&block,&offset)) {
    // ECA_LOG_MSG(ECA_LOGGER::user_objects, "found RIFF-block ");
    if (memcmp(block.sig,fblock,4) == 0) {
      if (blksize != 0)
	*blksize = little_endian_uint32(block.bsize);
      return true;
    }
    fio_repp->set_file_position(offset);
  }

  return false;
}

bool WAVEFILE::finished(void) const
{
  if (io_mode() == io_read && 
      (length_set() == true &&
       position_in_samples() >= length_in_samples())) {
    return true;
  }

  if (fio_repp->is_file_error() ||
      !fio_repp->is_file_ready()) 
    return true;

  return false;
}

long int WAVEFILE::read_samples(void* target_buffer, long int samples)
{
  // --------
  DBC_REQUIRE(samples >= 0);
  DBC_REQUIRE(target_buffer != 0);
  // --------

  if (length_set() == true &&
      position_in_samples() + samples >= length_in_samples())
    samples = length_in_samples() - position_in_samples();
  
  fio_repp->read_to_buffer(target_buffer, frame_size() * samples);
  return fio_repp->file_bytes_processed() / frame_size();
}

void WAVEFILE::write_samples(void* target_buffer, long int samples)
{
  // --------
  DBC_REQUIRE(samples >= 0);
  DBC_REQUIRE(target_buffer != 0);
  // --------

  /* note: When writing in write-update mode (i.e. modifying an
   *       existing file), we do not honor the previous length value 
   *       in "data" header chunk. This may lead to overriding some
   *       non-data chunk at the end of the file.
   */

  fio_repp->write_from_buffer(target_buffer, frame_size() * samples);
}

SAMPLE_SPECS::sample_pos_t WAVEFILE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  if (is_open() == true) {
    fio_repp->set_file_position(data_start_position_rep + pos * frame_size());
  }

  return pos;
}

void WAVEFILE::set_length_in_bytes(void)
{
  off_t savetemp = fio_repp->get_file_position();
  uint32_t blksize = 0;

  find_block("data", &blksize);
  off_t datastart = fio_repp->get_file_position();

  fio_repp->set_file_position_end();
  off_t datalen = fio_repp->get_file_position() - datastart;

  /* note: If the audio stream length defined in "data" header
   *       block is zero (not updated), or it's set to maximum
   *       value, set the length according to actual file length.
   *       
   *       This is not strictly according to the RIFF WAVE spec,
   *       but allows to handle RIFF WAVE files with a broken 
   *       header, as well as files with size exceeding the 2GiB
   *       limit. */
  if (blksize != 0 &&
      blksize != UINT32_MAX) {
    set_length_in_samples(blksize / frame_size());
  }
  else 
    set_length_in_samples(datalen / frame_size());

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "data block length in header " + 
	      kvu_numtostr(blksize) + 
	      ", file length after data block " +
	      kvu_numtostr(datalen) + 
	      ", length set to " +
	      kvu_numtostr(length_in_samples()) + 
	      " samples");

  fio_repp->set_file_position(savetemp);
}

void WAVEFILE::set_parameter(int param, 
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

string WAVEFILE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return mmaptoggle_rep;
  }
  return "";
}
