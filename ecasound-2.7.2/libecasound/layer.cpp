/* layer.cpp

  Created by SMF aka Antoine Laydier <laydier@usa.net>.
  Minor modifications by Kai Vehmanen <k@eca.cx>.

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

 */

/*=============================================================================
  HEADERs
 =============================================================================*/
#include <stdio.h>
#include <stdlib.h>
#include <sys/stat.h>
#include <unistd.h>
#include "layer.h"

/*=============================================================================
 Class : Layers
 =============================================================================*/

const int Layer::MPG_MD_STEREO        = 0;
const int Layer::MPG_MD_JOINT_STEREO  = 1;
const int Layer::MPG_MD_DUAL_CHANNEL  = 2;
const int Layer::MPG_MD_MONO          = 3;

const int Layer::MPG_MD_LR_LR = 0;
const int Layer::MPG_MD_LR_I  = 1;
const int Layer::MPG_MD_MS_LR = 2;
const int Layer::MPG_MD_MS_I  = 3;

const char *Layer::mode_names[5] = {"stereo", "j-stereo", "dual-ch",
				    "single-ch", "multi-ch"};
const char *Layer::layer_names[3] = {"I", "II", "III"};
const char *Layer::version_names[3] = {"MPEG-1", "MPEG-2 LSF", "MPEG-2.5"};
const char *Layer::version_nums[3] = {"1", "2", "2.5"};
const unsigned int Layer::bitrates[3][3][15] =
{
  {
    {0, 32, 64, 96, 128, 160, 192, 224, 256, 288, 320, 352, 384, 416, 448},
    {0, 32, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320, 384},
    {0, 32, 40, 48, 56, 64, 80, 96, 112, 128, 160, 192, 224, 256, 320}
  },
  {
    {0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256},
    {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160},
    {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160}
  },
  {
    {0, 32, 48, 56, 64, 80, 96, 112, 128, 144, 160, 176, 192, 224, 256},
    {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160},
    {0, 8, 16, 24, 32, 40, 48, 56, 64, 80, 96, 112, 128, 144, 160}
  }
};

const unsigned int Layer::s_freq[3][4] =
{
  {44100, 48000, 32000, 0},
  {22050, 24000, 16000, 0},
  {11025, 8000, 8000, 0}
};

const char * Layer::mode_name(void) {  return (Layer::mode_names[mode_rep]); }
const char * Layer::layer_name(void) {  return (Layer::layer_names[lay_rep - 1]); }
const char * Layer::version_name(void) {  return (Layer::version_names[version_rep]); }
const char * Layer::version_num(void) {  return (Layer::version_nums[version_rep]); }
int Layer::mode(void) { return(mode_rep); }
unsigned int Layer::bitrate(void) 
  { return (Layer::bitrates[version_rep][lay_rep - 1][bitrate_index_rep]); }
unsigned int Layer::sfreq(void)
  {  return (Layer::s_freq[version_rep][sampling_frequency_rep]); }
unsigned long Layer::length(void) { return  bitrate() ? (fileSize_rep / (unsigned long)bitrate() /125) : 0; }
unsigned int Layer::pcmPerFrame(void) { return pcm_rep; }

bool Layer::get(const char* filename)
{
  unsigned char *buff = new unsigned char[1024];
  unsigned char *buffer;
  size_t temp;
  size_t readsize;
  struct stat buf;
  FILE *file;

 // --
 // 22.3.2000 - added the second parameter for getting 
 // around FILE* compatibility issues, k@eca.cx 

  stat(filename, &buf);
  fileSize_rep = (unsigned long)buf.st_size;

  /* Theoretically reading 1024 instead of just 4 means a performance hit
   * if we transfer over net filesystems... However, no filesystem I know
   * of uses block sizes under 1024 bytes.
   */
  file = fopen(filename,"r");
  if (!file) return(false);
  
  fseek(file, 0, SEEK_SET);
  readsize = fread(buff, 1, 1024, file);
  fclose(file);
  readsize -= 4;
  if (readsize <= 0) {
    delete[] buff;
    return (false);
  }
  
  buffer = buff-1;
  
  /* Scan through the block looking for a header */
  do {
    buffer++;
    temp = ((buffer[0] << 4) & 0xFF0) | ((buffer[1] >> 4) & 0xE);
  } while ((temp != 0xFFE) && ((size_t)(buffer-buff)<readsize));

  if (temp != 0xFFE) {
    delete[] buff;
    return (false);
  } else {
    switch ((buffer[1] >> 3 & 0x3)) {
    case 3:
      version_rep = 0;
      break;
    case 2:
      version_rep = 1;
      break;
    case 0:
      version_rep = 2;
      break;
    default:
      delete[] buff;
      return (false);
    }
    lay_rep = 4 - ((buffer[1] >> 1) & 0x3);
    error_protection_rep = !(buffer[1] & 0x1);
    bitrate_index_rep = (buffer[2] >> 4) & 0x0F;
    sampling_frequency_rep = (buffer[2] >> 2) & 0x3;
    padding_rep = (buffer[2] >> 1) & 0x01;
    extension_rep = buffer[2] & 0x01;
    mode_rep = (buffer[3] >> 6) & 0x3;
    mode_ext_rep = (buffer[3] >> 4) & 0x03;
    copyright_rep = (buffer[3] >> 3) & 0x01;
    original_rep = (buffer[3] >> 2) & 0x1;
    emphasis_rep = (buffer[3]) & 0x3;
    stereo_rep = (mode_rep == Layer::MPG_MD_MONO) ? 1 : 2;

    // Added by Cp
    pcm_rep = 32;
    if (lay_rep == 3) {
      pcm_rep *= 18;
      if (version_rep == 0)
        pcm_rep *= 2;
    }
    else{
      pcm_rep *= 12;
      if (lay_rep == 2)
        pcm_rep *= 3;
    }

    delete[] buff;
    return (true);
  }
}
