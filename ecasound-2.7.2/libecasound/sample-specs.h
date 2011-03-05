// ------------------------------------------------------------------------
// sample-specs.h: Sample value defaults and constants.
// Copyright (C) 1999-2004 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
//     public-libecasound-API: yes
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

#ifndef INCLUDED_SAMPLE_SPECS_H
#define INCLUDED_SAMPLE_SPECS_H

/**
 * Sample value defaults and constants.
 */
namespace SAMPLE_SPECS {
  
  /**
   * Type used to represent one sample value; should
   * be a floating point value (floating-point type)
   */
  typedef float sample_t;

  /**
   * Type used to represent position in sample 
   * frames (signed integer).
   */
#if defined _ISOC99_SOURCE || defined _ISOC9X_SOURCE || defined __GNUG__
  typedef long long int sample_pos_t;
#else
  typedef long int sample_pos_t;
#endif

  /**
   * Type used to represent sample rate values (signed integer).
   */
  typedef long int sample_rate_t;

  /**
   * Type used to identify individual channels (signed integer).
   */
  typedef int channel_t;

  static const sample_t silent_value = 0.0f;     // do not change!
  static const sample_t max_amplitude = 1.0f;
  static const sample_t impl_max_value = silent_value + max_amplitude;
  static const sample_t impl_min_value = silent_value - max_amplitude;

  static const channel_t ch_left = 0;
  static const channel_t ch_right = 1;
}

#endif
