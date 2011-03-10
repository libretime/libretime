// ------------------------------------------------------------------------
// audioio-cdr.cpp: CDDA/CDR audio file format input/output
// Copyright (C) 1999,2001,2008 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cmath>
#include <cstdio>
#include <string>
#include <cstring>
#include <cassert>
#include <climits> /* LONG_MAX */
#include <sys/stat.h> /* stat() */
#include <unistd.h> /* stat() */
#ifdef HAVE_SYS_TYPES_H
#include <sys/types.h> /* off_t */
#else
typedef long int off_t;
#endif

#include <kvu_dbc.h>

#include "sample-specs.h"
#include "audioio-cdr.h"
#include "eca-logger.h"

CDRFILE::CDRFILE(const std::string& name)
{
  set_label(name);
}

CDRFILE::~CDRFILE(void)
{
  if (is_open() == true) {
    close();
  }
}

CDRFILE* CDRFILE::clone(void) const
{
  CDRFILE* target = new CDRFILE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void CDRFILE::open(void) throw(AUDIO_IO::SETUP_ERROR &)
{
  set_channels(2);
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_be);
  set_samples_per_second(44100);

  switch(io_mode()) {
  case io_read:
    {
      fobject = std::fopen(label().c_str(),"rb");
      if (!fobject)
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-CDR: Can't open " + label() + " for reading."));
      set_length_in_bytes();
      break;
    }
  case io_write: 
    {
      fobject = std::fopen(label().c_str(),"wb");
      if (!fobject) 
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-CDR: Can't open " + label() + " for writing."));
      break;
    }
  case io_readwrite:
    {
      fobject = std::fopen(label().c_str(),"r+b");
      if (!fobject) {
	fobject = std::fopen(label().c_str(),"w+b");
	if (!fobject)
	  throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-CDR: Can't open " + label() + " for read&write."));
      }
      set_length_in_bytes();
      break;
    }
  }

  AUDIO_IO::open();
}

void CDRFILE::close(void)
{ 
  if (io_mode() != io_read)
    pad_to_sectorsize();

  std::fclose(fobject);

  AUDIO_IO::close();
}

bool CDRFILE::finished(void) const
{
 if (std::ferror(fobject) ||
     std::feof(fobject))
   return true;

 return false;
}

long int CDRFILE::read_samples(void* target_buffer, long int samples)
{
  return std::fread(target_buffer, frame_size(), samples, fobject);
}

void CDRFILE::write_samples(void* target_buffer, long int samples)
{
  std::fwrite(target_buffer, frame_size(), samples, fobject);
}

SAMPLE_SPECS::sample_pos_t CDRFILE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  if (is_open() == true) {
    off_t curpos_rep = pos * frame_size();
    DBC_CHECK(curpos_rep >= 0);
/* fseeko doesn't seem to work with glibc 2.1.x */
#if _FILE_OFFSET_BITS==64
    off_t seekpos = 0;
    off_t seekstep = 0;
    int whence = SEEK_SET;
    while(curpos_rep - seekpos > 0) {
      if (curpos_rep - seekpos > LONG_MAX)
	seekstep = LONG_MAX;
      else
	seekstep = curpos_rep - seekpos;

      // std::cerr << "(audioio-cdr) fw-seeking from " << seekpos << " to " << seekpos+seekstep << std::endl;
      int res = std::fseek(fobject, seekstep, whence);
      if (res != 0) {
	  ECA_LOG_MSG(ECA_LOGGER::info, "fseek() error! (lfs).");
	  curpos_rep = 0;
	  std::fseek(fobject, 0, SEEK_SET);
	  break;
      }
      if (seekpos == 0) whence = SEEK_CUR;
      seekpos += seekstep;
    }
#else
    DBC_CHECK(sizeof(long int) == sizeof(off_t));
    std::fseek(fobject, static_cast<long int>(curpos_rep), SEEK_SET);
#endif
  }

  return pos;
}

void CDRFILE::pad_to_sectorsize(void)
{
  int padsamps = CDRFILE::sectorsize - ((length_in_samples() *
					frame_size()) % CDRFILE::sectorsize);

  if (padsamps == CDRFILE::sectorsize) {
    return;
  }
  for(int n = 0; n < padsamps; n++) ::fputc(0,fobject);

  DBC_DECLARE(long int endpos);
  DBC_DECLARE(endpos = std::ftell(fobject));
  DBC_CHECK((endpos %  CDRFILE::sectorsize) == 0);
}

void CDRFILE::set_length_in_bytes(void)
{
  struct stat temp;
  stat(label().c_str(), &temp);
  set_length_in_samples(temp.st_size / frame_size());
}
