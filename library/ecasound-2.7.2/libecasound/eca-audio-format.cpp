// ------------------------------------------------------------------------
// eca-audio-format.cpp: Class for representing audio format parameters
// Copyright (C) 1999-2002,2004 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
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

#include <kvutils/kvu_dbc.h>
#include <kvutils/kvu_numtostr.h>

#include "eca-audio-format.h"
#include "eca-error.h"

ECA_AUDIO_FORMAT::ECA_AUDIO_FORMAT(int channels, 
				   long int srate,
				   ECA_AUDIO_FORMAT::Sample_format format, 
				   bool ileaved)
{
  set_channels(channels);
  set_samples_per_second(srate);
  set_sample_format(format);
  toggle_interleaved_channels(ileaved);
}

ECA_AUDIO_FORMAT::ECA_AUDIO_FORMAT(void)
{ 
  set_channels(0);
  set_samples_per_second(-1);
  set_sample_format(sfmt_none);
  toggle_interleaved_channels(true);
}

ECA_AUDIO_FORMAT::~ECA_AUDIO_FORMAT(void) 
{
}

ECA_AUDIO_FORMAT ECA_AUDIO_FORMAT::audio_format(void) const
{
  return ECA_AUDIO_FORMAT(channels(),
			  samples_per_second(),
			  sample_format(),
			  interleaved_channels());
}

ECA_AUDIO_FORMAT::Sample_format ECA_AUDIO_FORMAT::sample_format(void) const
{
  Sample_format format (sfmt_none);
  try {
    format = string_to_sample_format(format_string());
  }
  catch(...) {}
  return format;
}

/**
 * Sets audio format to that of 'f'.
 */
void ECA_AUDIO_FORMAT::set_audio_format(const ECA_AUDIO_FORMAT& f)
{
  set_channels(f.channels());
  set_sample_format(f.sample_format());
  set_samples_per_second(f.samples_per_second());
  toggle_interleaved_channels(f.interleaved_channels());
}

void ECA_AUDIO_FORMAT::set_sample_format(ECA_AUDIO_FORMAT::Sample_format sfmt) throw(ECA_ERROR&)
{
  switch(sfmt) 
    {
    case sfmt_none:
      sc_rep = sc_unsigned;
      update_sample_endianess(se_native);
      align_rep = 0; 
      break;

    case sfmt_u8:
      sc_rep = sc_unsigned;
      update_sample_endianess(se_native);
      align_rep = 1;
      break;

    case sfmt_s8:
      sc_rep = sc_signed;
      update_sample_endianess(se_native);
      align_rep = 1;
      break;

    case sfmt_s16:
      sc_rep = sc_signed;
      update_sample_endianess(se_native);
      align_rep = 2;
      break;

    case sfmt_s16_le:
      sc_rep = sc_signed;
      update_sample_endianess(se_little);
      align_rep = 2;
      break;

    case sfmt_s16_be:
      sc_rep = sc_signed;
      update_sample_endianess(se_big);
      align_rep = 2;
      break;

    case sfmt_s24:
      sc_rep = sc_signed;
      update_sample_endianess(se_native);
      align_rep = 3;
      break;

    case sfmt_s24_le:
      sc_rep = sc_signed;
      update_sample_endianess(se_little);
      align_rep = 3;
      break;

    case sfmt_s24_be:
      sc_rep = sc_signed;
      update_sample_endianess(se_big);
      align_rep = 3;
      break;

    case sfmt_s32:
      sc_rep = sc_signed;
      update_sample_endianess(se_native);
      align_rep = 4;
      break;

    case sfmt_s32_le:
      sc_rep = sc_signed;
      update_sample_endianess(se_little);
      align_rep = 4;
      break;

    case sfmt_f32:
      sc_rep = sc_float;
      update_sample_endianess(se_native);
      align_rep = 4;
      break;

    case sfmt_s32_be:
      sc_rep = sc_signed;
      update_sample_endianess(se_big);
      align_rep = 4;
      break;

    case sfmt_f32_le:
      sc_rep = sc_float;
      update_sample_endianess(se_little);
      align_rep = 4;
      break;

    case sfmt_f32_be:
      sc_rep = sc_float;
      update_sample_endianess(se_big);
      align_rep = 4;
      break;

    case sfmt_f64:
      sc_rep = sc_float;
      update_sample_endianess(se_native);
      align_rep = 8;
      break;

    case sfmt_f64_le:
      sc_rep = sc_float;
      update_sample_endianess(se_little);
      align_rep = 8;
      break;

    case sfmt_f64_be:
      sc_rep = sc_float;
      update_sample_endianess(se_big);
      align_rep = 8;
      break;

    default: { throw(ECA_ERROR("ECA_AUDIO_FORMAT","Audio format not supported!")); }
    }

  DBC_ENSURE(se_rep == se_big || se_rep == se_little);
}

int ECA_AUDIO_FORMAT::bits(void) const
{
  return align_rep * 8;
}

void ECA_AUDIO_FORMAT::set_channels(int v)
{
  channels_rep = v; 
}

void ECA_AUDIO_FORMAT::toggle_interleaved_channels(bool v)
{
  ileaved_rep = v; 
}

ECA_AUDIO_FORMAT::Sample_format ECA_AUDIO_FORMAT::string_to_sample_format(const std::string& str) const throw(ECA_ERROR&)
{
  Sample_format sfmt = sfmt_none;

  if (str == "u8") sfmt = sfmt_u8;
  else if (str == "s16") sfmt = sfmt_s16;
  else if (str == "s16_le") sfmt = sfmt_s16_le;
  else if (str == "s16_be") sfmt = sfmt_s16_be;
  else if (str == "s24") sfmt = sfmt_s24;
  else if (str == "s24_le") sfmt = sfmt_s24_le;
  else if (str == "s24_be") sfmt = sfmt_s24_be;
  else if (str == "s32") sfmt = sfmt_s32;
  else if (str == "s32_le") sfmt = sfmt_s32_le;
  else if (str == "s32_be") sfmt = sfmt_s32_be;
  else if (str == "f32") sfmt = sfmt_f32;
  else if (str == "f32_le") sfmt = sfmt_f32_le;
  else if (str == "f32_be") sfmt = sfmt_f32_be;
  else if (str == "f64") sfmt = sfmt_f64;
  else if (str == "f64_le") sfmt = sfmt_f64_le;
  else if (str == "f64_be") sfmt = sfmt_f64_be;
  else if (str == "8") sfmt = sfmt_u8;
  else if (str == "16") sfmt = sfmt_s16;
  else if (str == "24") sfmt = sfmt_s24;
  else if (str == "32") sfmt = sfmt_s32;
  else {
    if (str != "none")
      throw(ECA_ERROR("ECA_AUDIO_FORMAT", "Unknown sample format \""
		      + str + "\"."));
  }

  return sfmt;
}

void ECA_AUDIO_FORMAT::set_sample_format_string(const std::string& f_str) throw(ECA_ERROR&)
{
  /* note, may raise an exception */
  set_sample_format(string_to_sample_format(f_str));
}

/**
 * Internal helper function that sets sample endianess
 * and if needed, expands native to either little or big 
 * byteorder.
 * 
 * @see set_sample_endianess
 */
void ECA_AUDIO_FORMAT::update_sample_endianess(ECA_AUDIO_FORMAT::Sample_endianess v)
{
  if (v == se_native) {
#ifdef WORDS_BIGENDIAN
    se_rep = se_big;
#else
    se_rep = se_little;
#endif
  }
  else {
    se_rep = v;
  }
}

void ECA_AUDIO_FORMAT::set_sample_endianess(ECA_AUDIO_FORMAT::Sample_endianess v)
{
  update_sample_endianess(v);
  
  /* make sure classes that reimplement set_sample_format
   * see the change in sample endianess */
  set_sample_format_string(format_string());

  DBC_ENSURE(se_rep == se_big || se_rep == se_little);
}

void ECA_AUDIO_FORMAT::set_sample_coding(ECA_AUDIO_FORMAT::Sample_coding v)
{
  sc_rep = v;

  /* make sure classes that reimplement set_sample_format
   * see the change in sample coding */
  set_sample_format_string(format_string());
}

string ECA_AUDIO_FORMAT::format_string(void) const
{
  std::string format;
  if (align_rep > 0) {
    /* coding */
    switch(sc_rep)
      {
      case sc_unsigned: format += "u"; break;
      case sc_signed: format += "s"; break;
      case sc_float: format += "f"; break;
      }
    
    /* bits */
    format += kvu_numtostr(bits());
    
    DBC_CHECK(se_rep == se_big || se_rep == se_little);
    
    /* endianess */
    if (align_rep > 1) {
      if (se_rep == se_little) {
	format += "_le";
      }
      else {
	format += "_be";
      }
    }
  }
  else {
    /* align_rep == 0 -> sfmt is yet unspecified */
    format = "none";
  }

  return format;
}
