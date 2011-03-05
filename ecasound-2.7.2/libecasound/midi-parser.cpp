// ------------------------------------------------------------------------
// midi-parser.cpp: Collection of static functions and small stateful 
//                  machines for parsing MIDI messages.
// Copyright (C) 2001 Kai Vehmanen
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

#include "midi-parser.h"

/**
 * Whether 'byte' belong to Voice Category status messages
 * (ie. 0x80 to 0xef)?
 */
bool MIDI_PARSER::is_voice_category_status_byte(unsigned char byte) {
  if (byte >= 0x80 && byte < 0xf0) return(true);
  return(false);
}

/**
 * Whether 'byte' belong to System Common Category status messages
 * (ie. 0xf0 to 0xf7)?
 */
bool MIDI_PARSER::is_system_common_category_status_byte(unsigned char byte) {
  if (byte >= 0xf0 && byte < 0xf8) return(true);
  return(false);
}

/**
 * Whether 'byte' belongs to Realtime Category status messages
 * (ie. 0xf8 to 0xff)?
 */
bool MIDI_PARSER::is_realtime_category_status_byte(unsigned char byte) {
  if (byte > 0xf7) return(true);
  return(false);
}

/**
 * Whether 'byte' is a status byte (0x80 to 0xff)?
 */
bool MIDI_PARSER::is_status_byte(unsigned char byte) {
  if (byte & 0x80) return(true);
  return(false);
}
