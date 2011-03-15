// ------------------------------------------------------------------------
// audioio-mp3.cpp: Interface for mp3 decoders and encoders that support 
//                  input/output using standard streams. Defaults to
//                  mpg123 and lame.
// Copyright (C) 1999-2006,2008,2009 Kai Vehmanen
// Note! Routines for parsing mp3 header information were taken from XMMS
//       1.2.5's mpg123 plugin. Improvements to parsing logic were
//       contributed by Julian Dobson.
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// References:
//     http://www.mp3-tech.org/programmer/frame_header.html
//     http://www.mpg123.de/
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

#include <string>
#include <cmath>
#include <cstring>
#include <cstdlib> /* atol() */

#include <signal.h>
#include <unistd.h> /* stat() */
#include <sys/stat.h> /* stat() */
#include <sys/wait.h>

#include <kvu_inttypes.h>
#include <kvu_message_item.h>
#include <kvu_numtostr.h>

#include "audioio-mp3.h"
#include "audioio-mp3_impl.h"
#include "samplebuffer.h"
#include "audioio.h"

#include "eca-logger.h"

const char *default_input_cmd = "mpg123 --stereo -b 0 -q -s -k %o %f";
const char *default_output_cmd = "lame -b %B -s %S -r --big-endian -S - %f";
const long int default_output_bitrate = 128000;

std::string MP3FILE::conf_input_cmd = std::string(default_input_cmd);
std::string MP3FILE::conf_output_cmd = std::string(default_output_cmd);

long int MP3FILE::conf_default_output_bitrate = default_output_bitrate;

void MP3FILE::set_input_cmd(const std::string& value) { MP3FILE::conf_input_cmd = value; }
void MP3FILE::set_output_cmd(const std::string& value) { MP3FILE::conf_output_cmd = value; }

/***************************************************************
 * Routines for parsing mp3 header information. Taken from XMMS
 * 1.2.5's mpg123 plugin.
 **************************************************************/

#define         MAXFRAMESIZE            1792
#define         MPG_MD_STEREO           0
#define         MPG_MD_JOINT_STEREO     1
#define         MPG_MD_DUAL_CHANNEL     2
#define         MPG_MD_MONO             3

int tabsel_123[2][3][16] =
  {
    {
      {0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448,},
      {0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384,},
      {0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320,}},

    {
      {0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256,},
      {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160,},
      {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160,}}
  };

long mpg123_freqs[9] =
  {44100, 48000, 32000, 22050, 24000, 16000, 11025, 12000, 8000};

struct frame
{
  int stereo;
  int jsbound;
  int single;
  int II_sblimit;
  int down_sample_sblimit;
  int lsf;
  int mpeg25;
  int down_sample;
  int header_change;
  int lay;
  int error_protection;
  int bitrate_index;
  int sampling_frequency;
  int padding;
  int extension;
  int mode;
  int mode_ext;
  int copyright;
  int original;
  int emphasis;
  int framesize;		/* computed framesize */
};

static bool mpg123_head_check(unsigned long head)
{
  /* ref: http://www.mp3-tech.org/programmer/frame_header.html */

  /* frame sync must be 0xffe (11bits) */
  if ((head & 0xffe00000) != 0xffe00000) return false;
  /* layer must be non-null (2bits) */
  if (!((head >> 17) & 3)) return false;
  /* invalid bitrate index: all-ones (4bit) */
  if (((head >> 12) & 0xf) == 0xf) return false;
  /* invalid bitrate index: null (4bit) */
  if (!((head >> 12) & 0xf)) return false;
  /* invalid srate index: all-ones (2bit) */
  if (((head >> 10) & 0x3) == 0x3) return false;
#if 0
  /* invalid: mpeg2/2.5, layer I, protection bit off */
  if (((head >> 19) & 1) == 1 && ((head >> 17) & 3) == 3 && ((head >> 16) & 1) == 1) return false;
  /* - mpeg version 1, CRC protection bit */
  if ((head & 0xffff0000) == 0xfffe0000) return false;
#endif
	
  return true;
}

static double mpg123_compute_bpf(struct frame *fr)
{
  double bpf;

  switch (fr->lay)
    {
    case 1:
      bpf = tabsel_123[fr->lsf][0][fr->bitrate_index];
      bpf *= 12000.0 * 4.0;
      bpf /= mpg123_freqs[fr->sampling_frequency] << (fr->lsf);
      break;
    case 2:
    case 3:
      bpf = tabsel_123[fr->lsf][fr->lay - 1][fr->bitrate_index];
      bpf *= 144000;
      bpf /= mpg123_freqs[fr->sampling_frequency] << (fr->lsf);
      break;
    default:
      bpf = 1.0;
    }

  return bpf;
}

static double mpg123_compute_tpf(struct frame *fr)
{
  static int bs[4] =
    {0, 384, 1152, 1152};
  double tpf;

  tpf = (double) bs[fr->lay];
  tpf /= mpg123_freqs[fr->sampling_frequency] << (fr->lsf);
  return tpf;
}

/*
 * the code a header and write the information
 * into the frame structure
 */
static bool mpg123_decode_header(struct frame *fr, unsigned long newhead)
{
  if (newhead & (1 << 20))
    {
      fr->lsf = (newhead & (1 << 19)) ? 0x0 : 0x1;
      fr->mpeg25 = 0;
    }
  else
    {
      fr->lsf = 1;
      fr->mpeg25 = 1;
    }
  fr->lay = 4 - ((newhead >> 17) & 3);
  if (fr->mpeg25)
    {
      fr->sampling_frequency = 6 + ((newhead >> 10) & 0x3);
    }
  else
    fr->sampling_frequency = ((newhead >> 10) & 0x3) + (fr->lsf * 3);
  fr->error_protection = ((newhead >> 16) & 0x1) ^ 0x1;

  if (fr->mpeg25)		/* allow Bitrate change for 2.5 ... */
    fr->bitrate_index = ((newhead >> 12) & 0xf);

  fr->bitrate_index = ((newhead >> 12) & 0xf);
  fr->padding = ((newhead >> 9) & 0x1);
  fr->extension = ((newhead >> 8) & 0x1);
  fr->mode = ((newhead >> 6) & 0x3);
  fr->mode_ext = ((newhead >> 4) & 0x3);
  fr->copyright = ((newhead >> 3) & 0x1);
  fr->original = ((newhead >> 2) & 0x1);
  fr->emphasis = newhead & 0x3;

  fr->stereo = (fr->mode == MPG_MD_MONO) ? 1 : 2;

  if (!fr->bitrate_index) {
    ECA_LOG_MSG(ECA_LOGGER::errors, "Invalid bitrate!");
    return false;
  }

  int ssize = 0;
  switch (fr->lay)
    {
    case 1:
      //  	    fr->do_layer = mpg123_do_layer1;
      //  	    mpg123_init_layer2();
      fr->framesize = (long) tabsel_123[fr->lsf][0][fr->bitrate_index] * 12000;
      fr->framesize /= mpg123_freqs[fr->sampling_frequency];
      fr->framesize = ((fr->framesize + fr->padding) << 2) - 4;
      break;
    case 2:
      //  	    fr->do_layer = mpg123_do_layer2;
      //  	    mpg123_init_layer2();
      fr->framesize = (long) tabsel_123[fr->lsf][1][fr->bitrate_index] * 144000;
      fr->framesize /= mpg123_freqs[fr->sampling_frequency];
      fr->framesize += fr->padding - 4;
      break;
    case 3:
      //  	    fr->do_layer = mpg123_do_layer3;
      if (fr->lsf)
	ssize = (fr->stereo == 1) ? 9 : 17;
      else
	ssize = (fr->stereo == 1) ? 17 : 32;
      if (fr->error_protection)
	ssize += 2;
      fr->framesize = (long) tabsel_123[fr->lsf][2][fr->bitrate_index] * 144000;
      fr->framesize /= mpg123_freqs[fr->sampling_frequency] << (fr->lsf);
      fr->framesize = fr->framesize + fr->padding - 4;
      break;
    default:
      return false;
    }

  if(fr->framesize > MAXFRAMESIZE) {
    ECA_LOG_MSG(ECA_LOGGER::errors, "Invalid framesize!");
    return false;
  }

  return true;
}

/* not used anymore, kaiv 2005/03 */
#if 0
static uint32_t convert_to_header(uint8_t * buf)
{

  return (buf[0] << 24) + (buf[1] << 16) + (buf[2] << 8) + buf[3];
}
#endif

static bool mpg123_detect_by_content(const char* filename, struct frame* frp)
{
  FILE *file;
  uint8_t tmp[4]; /* room for the 32bit head */
  uint32_t head = 0;
  bool data_left = true;
  bool header_found = false;
  size_t offset = 0;

  if((file = std::fopen(filename, "rb")) == NULL) {
    ECA_LOG_MSG(ECA_LOGGER::errors, string("Unable to open file ") + filename + ".");
    data_left = false;
  }
  /* search for headers in the first 262kB of data */
  while(data_left == true && offset < (1<<18)) {
    /* octet-by-octet search */
    if (std::fread(tmp, 1, 1, file) != 1) {
      ECA_LOG_MSG(ECA_LOGGER::errors, "End of mp3 file, no valid header data found.");
      data_left = false;
      break;
    }

    head <<= 8;
    head |= tmp[0]; 
    offset += 1;

    if (offset > 3) {
      /* verify the header and if ok, fetch mp3 parameters and store
	 them to 'frp' */
      if (mpg123_head_check(head) && mpg123_decode_header(frp, head)) {
	if (header_found == true) {
	  /* two headers found, stop searching */
	  data_left = false;
	}
	else {
	  /* after the first header is found, skip to the next 
	     valid frame to verify that the first frame is not 
	     dummy frame (id3 or something similar) */
	  if (std::fseek(file, frp->framesize, SEEK_CUR) != 0) {
	    data_left = false;
	  }
	  header_found = true;
	}
	ECA_LOG_MSG(ECA_LOGGER::user_objects, "Found mp3 header at offset " + 
		    kvu_numtostr(static_cast<int>(offset - 4)));
      }
    }
  }

  return header_found;
}

/***************************************************************
 * MP3FILE specific parts.
 **************************************************************/

MP3FILE::MP3FILE(const std::string& name)
  :  finished_rep(false),
     triggered_rep(false)
{
  set_label(name);
  filedes_rep = -1;
  filehandle_rep = 0;
  mono_input_rep = false;
  pcm_rep = 1;
  bitrate_rep = MP3FILE::conf_default_output_bitrate;
}

MP3FILE::~MP3FILE(void)
{
  /* see notes in stop_io() */
  clean_child(io_mode() == io_read ? true : false);
  if (is_open() == true) {
    close();
  }
}

void MP3FILE::open(void) throw(AUDIO_IO::SETUP_ERROR &)
{ 
  if (io_mode() == io_read) {
    /* decoder supports: fixed channel count and sample format, 
                         sample rate set by parsing mp3 header */
    get_mp3_params(label());
  }
  else {
    /* encoder supports: srate configurable, fixed channel
                         count and sample format */
    set_channels(2);
    set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_le);

    /* note: 'lame' command-line syntax, and default related to them, 
     *       have changed slightly in lame 3.98, so we need this hack 
     *       to support both old and new versions. In the past,
     *       Ecasound wrote little-endian samples and used lame
     *       option "-x". Newer lame versions (3.97) introduced
     *       "--litle-endian" and "--big-endian", but these were 
     *       buggy still in 3.97 (fixed in 3.98). And with 3.98, 
     *       additional options (e.g. "-r") need to be passed, or
     *       otherwise lame will exit with an error.
     * 
     *       In addition to above problems, we also need to remember
     *       people updating to a newer Ecasound, but who do not update
     *       their custom 'lame' launch commands in
     *       ~/.ecasound/ecasoundrc (ecasound must continue to output
     *       little-endian samples by default).
     */
    if (MP3FILE::conf_output_cmd.find("lame ") != std::string::npos &&
	MP3FILE::conf_output_cmd.find(" --big-endian ") != std::string::npos) {
      set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_be);
    }
  }

  triggered_rep = false;

  AUDIO_IO::open();
}

void MP3FILE::close(void)
{
  if (pid_of_child() > 0) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Cleaning child process pid=" + kvu_numtostr(pid_of_child()) + ".");
      /* note: mp3 input/output can handle SIGTERM */
      clean_child(true);
      triggered_rep = false;
  }

  AUDIO_IO::close();
}

void MP3FILE::process_mono_fix(char* target_buffer, long int bytes) {
  for(long int n = 0; n < bytes;) {
    target_buffer[n + 2] = target_buffer[n];
    target_buffer[n + 3] = target_buffer[n + 1];
    n += 4;
  }
}

long int MP3FILE::read_samples(void* target_buffer, long int samples)
{
  if (triggered_rep != true) {
    ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: triggering an external program in real-time context"); 
    triggered_rep = true;
    fork_input_process();
  }

  bytes_rep = std::fread(target_buffer, 1, frame_size() * samples, filehandle_rep);
  if (bytes_rep < samples * frame_size() || bytes_rep == 0) {
    if (position_in_samples() == 0) 
      ECA_LOG_MSG(ECA_LOGGER::errors, "Can't start process \"" + MP3FILE::conf_input_cmd + "\". Please check your ~/.ecasound/ecasoundrc.");
    finished_rep = true;
    triggered_rep = false;
  }
  else
    finished_rep = false;

  last_position_rep += (bytes_rep / frame_size());
  
  return bytes_rep / frame_size();
}

void MP3FILE::write_samples(void* target_buffer, long int samples)
{
  if (triggered_rep != true) {
    triggered_rep = true;
    fork_output_process();
  }

  if (wait_for_child() != true) {
    finished_rep = true;
    triggered_rep = false;
    ECA_LOG_MSG(ECA_LOGGER::errors, "Attempt to write after child process has terminated.");
  }
  else {
    bytes_rep = ::write(filedes_rep, target_buffer, frame_size() * samples);

    if (bytes_rep < frame_size() * samples) {
      if (position_in_samples() == 0) 
	ECA_LOG_MSG(ECA_LOGGER::errors, "Can't start process \"" + MP3FILE::conf_output_cmd + "\". Please check your ~/.ecasound/ecasoundrc.");
      else
	ECA_LOG_MSG(ECA_LOGGER::errors, 
		    "Error in writing to child process (to write " 
		    + kvu_numtostr(frame_size() * samples) 
		    + ", result "
		    + kvu_numtostr(bytes_rep) 
		    + ").");

      finished_rep = true;
    }
    else 
      finished_rep = false;
  }
}

SAMPLE_SPECS::sample_pos_t MP3FILE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  finished_rep = false;
  if (triggered_rep == true &&
      last_position_rep != pos) {
    if (is_open() == true) {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Cleaning child process pid=" + kvu_numtostr(pid_of_child()) + ".");
      clean_child(true);
      triggered_rep = false;
    }
  }
  return pos;
}

void MP3FILE::set_parameter(int param, std::string value)
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
      bitrate_rep = MP3FILE::conf_default_output_bitrate;
    break;
  }
}

std::string MP3FILE::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return label();

  case 2: 
    return kvu_numtostr(bitrate_rep);
  }
  return "";
}

void MP3FILE::get_mp3_params(const std::string& fname) throw(AUDIO_IO::SETUP_ERROR&)
{
  std::string urlprefix;
  struct frame fr;

  if (mpg123_detect_by_content(fname.c_str(), &fr) != true) {
    /* not a file, next search for an URL */
    size_t offset = fname.find_first_of("://");
    if (offset == std::string::npos) {
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-MP3: Can't open " + label() + " for reading."));
    }
    else {
      urlprefix = std::string(fname, 0, offset);
      ECA_LOG_MSG(ECA_LOGGER::user_objects, "Found url; protocol '" + urlprefix + "'.");
    }
  }
  else {
    /* file size */
    struct stat buf;
    ::stat(fname.c_str(), &buf);
    double fsize = (double)buf.st_size;
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Total file size (bytes): " + kvu_numtostr(fsize));
    
    /* bitrate */
    double bitrate = tabsel_123[fr.lsf][fr.lay - 1][fr.bitrate_index] * 1000;
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Bitrate (bits/s): " + kvu_numtostr(bitrate));

    /* sample freq */
    double sfreq = mpg123_freqs[fr.sampling_frequency];
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Sampling frequncy (Hz): " + kvu_numtostr(sfreq));
    set_samples_per_second(static_cast<SAMPLE_SPECS::sample_rate_t>(sfreq));

    /* channels */
    // notice! mpg123 always outputs 16bit samples, stereo
    mono_input_rep = (fr.mode == MPG_MD_MONO) ? true : false;

    /* temporal length */
    long int numframes =  static_cast<long int>((fsize / mpg123_compute_bpf(&fr)));
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Total length (frames): " + kvu_numtostr(numframes));
    double tpf = mpg123_compute_tpf(&fr);
    set_length_in_seconds(tpf * numframes);
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Total length (seconds): " + kvu_numtostr(length_in_seconds()));

    /* set pcm per frame value */
    static int bs[4] = {0, 384, 1152, 1152};
    pcm_rep = bs[fr.lay];
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Pcm per mp3 frames: " + kvu_numtostr(pcm_rep));
  }

  /* sample format (this comes from mpg123) */
  set_channels(2);
  set_sample_format(ECA_AUDIO_FORMAT::sfmt_s16_le);
}

void MP3FILE::start_io(void)
{
  if (triggered_rep != true) {
    if (io_mode() == io_read) 
      fork_input_process();
    else
      fork_output_process();

    triggered_rep = true;
  }
}

void MP3FILE::stop_io(void)
{
  if (triggered_rep == true) {
    /* note: it's safe to send a SIGTERM if the client is 
     *       an input and we know its PID (otherwise 
     *       cleanup will still work but will take more time, which
     *       is nasty if we are in a middle of a seek */
    if (io_mode() == io_read) 
      clean_child(true);
    else
      clean_child(false);

    triggered_rep = false;
  }
}

void MP3FILE::fork_input_process(void)
{
  std::string cmd = MP3FILE::conf_input_cmd;
  if (cmd.find("%o") != std::string::npos) {
    cmd.replace(cmd.find("%o"), 2, kvu_numtostr((long)(position_in_samples() / pcm_rep)));
  }
  last_position_rep = position_in_samples();
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "" + cmd);
  set_fork_command(cmd);
  set_fork_file_name(label());

  set_fork_bits(bits());
  set_fork_channels(channels());
  set_fork_sample_rate(samples_per_second()); /* for old mpg123 */

  fork_child_for_read();
  if (child_fork_succeeded() == true) {

    /* NOTE: the file description will be closed by 
     *       AUDIO_IO_FORKED_STREAM::clean_child() */
    filedes_rep = file_descriptor();
    filehandle_rep = fdopen(filedes_rep, "r"); /* not part of <cstdio> */
    if (filehandle_rep == 0) {
      finished_rep = true;
      triggered_rep = false;
    }
  }
}

void MP3FILE::fork_output_process(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "Starting to encode " + label() + " with lame.");
  last_position_rep = position_in_samples();
  std::string cmd = MP3FILE::conf_output_cmd;
  if (cmd.find("%B") != std::string::npos) {
    cmd.replace(cmd.find("%B"), 2, kvu_numtostr((long int)(bitrate_rep / 1000)));
  }

  set_fork_command(cmd);

  set_fork_file_name(label());
  set_fork_bits(bits());
  set_fork_channels(channels());

  set_fork_sample_rate(samples_per_second());
  fork_child_for_write();
  if (child_fork_succeeded() == true) {
    filedes_rep = file_descriptor();
  }
}
