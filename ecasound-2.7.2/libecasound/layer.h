/* layer.h

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

#ifndef _LAYER_
#define _LAYER_
/*=============================================================================
  HEADERs
=============================================================================*/
#include <stdio.h>

/*=============================================================================
  CLASSes
=============================================================================*/

//-----------------------------------------------------------------------------
// class Layer
//-----------------------------------------------------------------------------

/**
 * Mp3 header parsing
 */
class Layer {
 public:
  const char * mode_name(void);
  const char * layer_name(void);
  const char * version_name(void);
  const char * version_num(void);
  unsigned int bitrate(void);
  unsigned int sfreq(void);
  unsigned long length(void);
  unsigned int pcmPerFrame(void);
  int mode(void);
  bool get(const char* filename);

  static const char * mode_names[5];
  static const char * layer_names[3];
  static const char * version_names[3];
  static const char * version_nums[3];
  static const unsigned int bitrates[3][3][15];
  static const unsigned int s_freq[3][4];

  static const int MPG_MD_STEREO;
  static const int MPG_MD_JOINT_STEREO;
  static const int MPG_MD_DUAL_CHANNEL;
  static const int MPG_MD_MONO;

  static const int MPG_MD_LR_LR;
  static const int MPG_MD_LR_I;
  static const int MPG_MD_MS_LR;
  static const int MPG_MD_MS_I;
 
 private:
  int version_rep;
  int lay_rep;
  int error_protection_rep;
  int bitrate_index_rep;
  int sampling_frequency_rep;
  int padding_rep;
  int extension_rep;
  int mode_rep;
  int mode_ext_rep;
  int copyright_rep;
  int original_rep;
  int emphasis_rep;
  int stereo_rep;
  unsigned int pcm_rep;
  unsigned long fileSize_rep;
};

#endif
