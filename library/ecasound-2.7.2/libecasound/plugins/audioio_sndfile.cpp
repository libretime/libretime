// ------------------------------------------------------------------------
// audioio_sndfile.cpp: Interface to the sndfile library.
// Copyright (C) 2003-2004,2006-2007,2009 Kai Vehmanen
// Copyright (C) 2004 Jesse Chappell
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// References:
//     http://www.mega-nerd.com/libsndfile/
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

#include <algorithm>
#include <string>
#include <iostream>
#include <fstream>
#include <cmath>
#include <cstring>

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "audioio_sndfile.h"
#include "samplebuffer.h"
#include "eca-version.h"
#include "eca-error.h"
#include "eca-logger.h"

#ifdef WORDS_BIGENDIAN
static const ECA_AUDIO_FORMAT::Sample_format audioio_sndfile_sfmt = ECA_AUDIO_FORMAT::sfmt_f32_be;
#else
static const ECA_AUDIO_FORMAT::Sample_format audioio_sndfile_sfmt = ECA_AUDIO_FORMAT::sfmt_f32_le;
#endif

using namespace std;

SNDFILE_INTERFACE::SNDFILE_INTERFACE (const string& name)
{
  finished_rep = false;
  snd_repp = 0;
  closing_rep = false;
  set_label(name);
}

SNDFILE_INTERFACE::~SNDFILE_INTERFACE(void)
{
  if (is_open() == true) {
    close();
  }
}

SNDFILE_INTERFACE* SNDFILE_INTERFACE::clone(void) const
{
  SNDFILE_INTERFACE* target = new SNDFILE_INTERFACE();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

/**
 * Parses the information given in 'sfinfo'. 
 */ 
void SNDFILE_INTERFACE::open_parse_info(const SF_INFO* sfinfo) throw(AUDIO_IO::SETUP_ERROR&)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "audio file format: " + kvu_numtostr(sfinfo->format & SF_FORMAT_SUBMASK)); 

  string format;

  set_samples_per_second(static_cast<long int>(sfinfo->samplerate));
  set_channels(sfinfo->channels);

  switch(sfinfo->format & SF_FORMAT_SUBMASK) 
    {
    case SF_FORMAT_PCM_S8: { format = "s8"; break; }
    case SF_FORMAT_PCM_U8: { format = "u8"; break; }
    case SF_FORMAT_PCM_16: { format = "s16"; break; }
    case SF_FORMAT_PCM_24: { format = "s24"; break; }
    case SF_FORMAT_PCM_32: { format = "s32"; break; }
    default: 
      {
	/* SF_FORMAT_FLOAT */ 
	format = "f32"; 
	break; 
      }
    }
  
  if (sfinfo->format & SF_ENDIAN_LITTLE) 
    format += "_le";
  else if (sfinfo->format & SF_ENDIAN_BIG) 
    format += "_be";
  
  set_sample_format_string(format);
  
  /* note: we have no way to find out whether frame count of 
   *       zero means an empty file, or that that the audio
   *       format does not provide this information, so we 
   *       lean towards being cautious; see also finished() */
  if (sfinfo->frames > 0)
    set_length_in_samples(sfinfo->frames);

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      string("file length (frames): ") +
	      kvu_numtostr(sfinfo->frames));

}

/**
 * Returns a list of support file extensions in lower-case.
 */
std::list<std::string> SNDFILE_INTERFACE::supported_extensions(void) const
{
  list<string> exts;
  int i, count;
  SF_FORMAT_INFO format_info;

  sf_command (0, SFC_GET_FORMAT_MAJOR_COUNT, &count, sizeof (int));
  
  for (i = 0 ; i < count ; i++) {
    format_info.format = i;
    sf_command (0, SFC_GET_FORMAT_MAJOR, &format_info, sizeof (format_info)) ;
    exts.push_back(string(format_info.extension));
  } 

  return exts;
}

/**
 * Discovers a matching libsndfile file format for the filename
 * 'fname'. The filename extension is used to find a matching
 * format.
 *
 * @return sndfile major format identifier
 */
int SNDFILE_INTERFACE::find_file_format(const std::string& fname)
{
  int file_format = -1, i, count;
  SF_FORMAT_INFO format_info;
  size_t pos = fname.rfind(".");
  string fext;
  if (pos != string::npos) {
    fext = string(fname, pos + 1);
  }

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      string("Searching for fileformat matching extension '") +
	      fext + "'.");

  sf_command (0, SFC_GET_FORMAT_MAJOR_COUNT, &count, sizeof (int));
  
  for (i = 0 ; i < count ; i++) {
    format_info.format = i;
    sf_command (0, SFC_GET_FORMAT_MAJOR, &format_info, sizeof (format_info)) ;
    if (fext == string(format_info.extension)) {
      file_format = format_info.format;
      ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		  string("Found matching file format: ") +
		  format_info.name + " (ext=." + fext + ")");
      break;
    }
  } 

  if (file_format < 0) {
    ECA_LOG_MSG(ECA_LOGGER::info,
		string("Warning! Unknown audio format extension '")
		+ fext + "', using WAV format instead.");
    file_format = SF_FORMAT_WAV;
  }

  return file_format;
}

void SNDFILE_INTERFACE::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  SF_INFO sfinfo;

  string real_filename = label();
  if (real_filename == "sndfile") {
    real_filename = opt_filename_rep;
  }

  string mod_filename = real_filename;
  if (!opt_format_rep.empty()) {
    mod_filename = opt_format_rep;
  }
  kvu_to_lowercase(mod_filename);

  // need to treat raw specially for read-only opening
  bool is_raw = false;
  if (strstr(mod_filename.c_str(),".raw") != 0) {
	  is_raw = true;
  }
  
  if (io_mode() == io_read && !is_raw) {
    ECA_LOG_MSG(ECA_LOGGER::info,
		"Using libsndfile to open file \"" + real_filename + "\" for reading.");

    snd_repp = sf_open(real_filename.c_str(), SFM_READ, &sfinfo);
    if (snd_repp == NULL) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-SNDFILE: Can't open file \"" + real_filename
			+ "\" for reading."));
    }
    else {
      open_parse_info(&sfinfo);
    }
  }
  else {
    /* write or readwrite */

    int file_format = -1;

    // note: support 1.0.0 formats by default, and others via
    // SF_FORMAT_GET_MAJOR

    file_format = find_file_format(mod_filename);    
    
    if (format_string()[0] == 'u' && bits() == 8)
      file_format |= SF_FORMAT_PCM_S8;
    else if (format_string()[0] == 's') {
      if (bits() == 8) { file_format |= SF_FORMAT_PCM_S8; }
      else if (bits() == 16) { file_format |= SF_FORMAT_PCM_16; }
      else if (bits() == 24) { file_format |= SF_FORMAT_PCM_24; }
      else if (bits() == 32) { file_format |= SF_FORMAT_PCM_32; }
      else { file_format = 0; }
    }
    else if (format_string()[0] == 'f') {
      if (bits() == 32) { file_format |= SF_FORMAT_FLOAT; }
      else if (bits() == 64) { file_format |= SF_FORMAT_DOUBLE; }
      else { file_format = 0; }
    }
    else { file_format = 0; }
    
    if (file_format == 0) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-SNDFILE: Error! Unknown audio format requested."));
    }

    // set endianess
    if (sample_endianess() == se_little) {
      file_format |= SF_ENDIAN_LITTLE;
    }
    else if (sample_endianess() == se_big) {
      file_format |= SF_ENDIAN_BIG;
    }

    /* set samplerate and channels */
    sfinfo.samplerate = samples_per_second();
    sfinfo.channels = channels();
    sfinfo.format = file_format;
  
    if (io_mode() == io_write) {
      ECA_LOG_MSG(ECA_LOGGER::info, "Using libsndfile to open file \"" +
		  real_filename + "\" for writing.");

      /* open the file */
      snd_repp = sf_open(real_filename.c_str(), SFM_WRITE, &sfinfo);
      if (snd_repp == NULL) {
	throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-SNDFILE: Can't open file \"" + real_filename
			  + "\" for writing."));
      }
    }
    else if (io_mode() == io_read) {
      ECA_LOG_MSG(ECA_LOGGER::info, "Using libsndfile to open file \"" +
		real_filename + "\" for reading.");


      snd_repp = sf_open(real_filename.c_str(), SFM_READ, &sfinfo);
      if (snd_repp == NULL) {
        throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-SNDFILE: Can't open file \"" + real_filename
			+ "\" for reading."));
      }
      else {
        open_parse_info(&sfinfo);
      }
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::info, "Using libsndfile to open file \"" +
		  real_filename + "\" for read/write.");

      DBC_CHECK(sf_format_check(&sfinfo));

      /* io_readwrite */
      snd_repp = sf_open(real_filename.c_str(), SFM_RDWR, &sfinfo);
      if (snd_repp == NULL) {
	/* if open fails, try with SFM_WRITE (formats like flac are not 
	 * supported in RDWR mode */
	snd_repp = sf_open(real_filename.c_str(), SFM_WRITE, &sfinfo);
	if (snd_repp == NULL) {
	  throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-SNDFILE: Can't open file \"" + real_filename
			    + "\" for updating (read/write)."));
	}
	else {
	  set_io_mode(io_write);
	  open_parse_info(&sfinfo);
	}
      }
      else {
	open_parse_info(&sfinfo);
      }
    }
  }

  /* we need to reserve extra memory as we using 32bit 
   * floats as the internal sample unit */
  reserve_buffer_space((sizeof(float) * channels()) * buffersize());

  AUDIO_IO::open();
}

void SNDFILE_INTERFACE::close(void)
{
  if (is_open() == true) {
    DBC_CHECK(closing_rep != true);
    if (snd_repp != 0 && closing_rep != true) {
      closing_rep = true;
      sf_close(snd_repp);
      snd_repp = 0;
    }
  }
  AUDIO_IO::close();
  closing_rep = false;
}

bool SNDFILE_INTERFACE::finished(void) const
{
  if (finished_rep == true || 
      (length_set() == true &&
       (io_mode() == io_read && out_position()))) return true;

  return false;
}

long int SNDFILE_INTERFACE::read_samples(void* target_buffer, 
					 long int samples)
{
  // samples_read = sf_read_raw(snd_repp, target_buffer, frame_size() * samples);
  // samples_read /= frame_size();
  samples_read = sf_readf_float(snd_repp, (float*)target_buffer, samples);
  finished_rep = (samples_read < samples) ? true : false;
  return samples_read;
}

void SNDFILE_INTERFACE::write_samples(void* target_buffer, 
				      long int samples)
{
  //sf_write_raw(snd_repp, target_buffer, frame_size() * samples);
  sf_writef_float(snd_repp, (float*)target_buffer, samples);
}

void SNDFILE_INTERFACE::read_buffer(SAMPLE_BUFFER* sbuf)
{
  // --------
  DBC_REQUIRE(get_iobuf() != 0);
  DBC_REQUIRE(static_cast<long int>(get_iobuf_size()) >= buffersize() * frame_size());
  // --------

  /* note! modified from audioio-buffered.cpp */

  DBC_CHECK(interleaved_channels() == true);

  /* in normal conditions this won't cause memory reallocs */
  reserve_buffer_space((sizeof(float) * channels()) * buffersize());

  sbuf->import_interleaved(get_iobuf(),
			   read_samples(get_iobuf(), buffersize()),
			   audioio_sndfile_sfmt,
			   channels());
  change_position_in_samples(sbuf->length_in_samples());
}

void SNDFILE_INTERFACE::write_buffer(SAMPLE_BUFFER* sbuf)
{
  // --------
  DBC_REQUIRE(get_iobuf() != 0);
  DBC_REQUIRE(static_cast<long int>(get_iobuf_size()) >= buffersize() * frame_size());
  // --------

  /* note! modified from audioio-buffered.cpp */
  
  DBC_CHECK(interleaved_channels() == true);

  /* in normal conditions this won't cause memory reallocs */
  reserve_buffer_space((sizeof(float) * channels()) * buffersize());

  set_buffersize(sbuf->length_in_samples());

  sbuf->export_interleaved(get_iobuf(),
			   audioio_sndfile_sfmt,
			   channels());
  write_samples(get_iobuf(), sbuf->length_in_samples());
  change_position_in_samples(sbuf->length_in_samples());
  extend_position();
}

SAMPLE_SPECS::sample_pos_t SNDFILE_INTERFACE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  // FIXME: check if format supports seeking

  finished_rep = false;

  sf_count_t res =
    sf_seek(snd_repp, pos, SEEK_SET);

  if (res != pos) {
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"invalid seek for file " +
		opt_filename_rep + 
		", req was to " +
		kvu_numtostr(pos) + 
		", result was " +
		kvu_numtostr(res));
    if (res < 0) {
      res = sf_seek(snd_repp, 0, SEEK_CUR);
      DBC_CHECK(res >= 0);
      if (res >= 0)
	pos = res;
      else
	pos = position_in_samples();
    }
  }
  return pos;
}

void SNDFILE_INTERFACE::set_parameter(int param, 
				      string value)
{
  switch (param) {
  case 1: 
    set_label(value);
    break;

  case 2: 
    opt_filename_rep = value;
    break;

  case 3: 
    opt_format_rep = value;
    break;
  }
}

string SNDFILE_INTERFACE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return opt_filename_rep;

  case 3: 
    return opt_format_rep;
  }
  return "";
}
