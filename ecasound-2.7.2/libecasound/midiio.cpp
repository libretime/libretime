// ------------------------------------------------------------------------
// midiio.cpp: Routines common for all MIDI IO-devices.
// Copyright (C) 2000 Kai Vehmanen
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

#include <string>

#include <kvu_message_item.h>
#include "midiio.h"
#include "samplebuffer.h"
#include "eca-error.h"
#include "eca-logger.h"

// ===================================================================
// Attributes

/**
 * Returns info about supported I/O modes (bitwise-OR)
 *
 * By default, all I/O modes are supported.
 */
int MIDI_IO::supported_io_modes(void) const { return(io_read | io_readwrite | io_write); }

/**
 * Whether device supports non-blocking I/O mode.
 *
 * By default, nonblocking mode is not supported.
 */
bool MIDI_IO::supports_nonblocking_mode(void) const { return(false); }

// ===================================================================
// Configuration (setting and getting configuration parameters)

/**
 * Returns info about the current I/O mode.
 */
int MIDI_IO::io_mode(void) const { return(io_mode_rep); }

/**
 * Set object input/output-mode. If the requested mode isn't
 * supported, the nearest supported mode is used. Because 
 * of this, it's wise to afterwards check whether the requested
 * mode was accepted.
 *
 * require:
 *  is_open() != true
 */
void MIDI_IO::io_mode(int mode) { io_mode_rep = mode; }

/**
 * Sets object label. Label is used to identify the object instance.
 * Unlike ECA_OBJECT::name(), label() is not necessarily unique 
 * among different class instances. Device and file names are typical 
 * label values.
 *
 * require:
 *  is_open() != true
 */
void MIDI_IO::label(const std::string& id_label) { id_label_rep = id_label; }

/**
 * Enable/disbale nonblocking mode.
 *
 * require:
 *  is_open() != true
 */
void MIDI_IO::toggle_nonblocking_mode(bool value) { nonblocking_rep = value; }

/**
 * Returns the current label. See documentation for 
 * label(const std::string&).
 */
const std::string& MIDI_IO::label(void) const { return(id_label_rep); }


void MIDI_IO::set_parameter(int param, 
			    std::string value) {
  if (param == 1) label(value);
}

std::string MIDI_IO::get_parameter(int param) const {
  if (param == 1) return(label());
  return("");
}

// ===================================================================
// Runtime information

/**
 * Is nonblocking mode is enabled?
 */
bool MIDI_IO::nonblocking_mode(void) const { return(nonblocking_rep); }

/**
 * Is the MIDI object ready for reading? 
 */
bool MIDI_IO::readable(void) const { return(is_open() && io_mode() != io_write); }

/**
 * Is the MIDI object ready for writing? 
 */
bool MIDI_IO::writable(void) const { return(is_open() && io_mode() != io_read); }

/**
 * Sets device's state to enabled or disabled.
 */
void MIDI_IO::toggle_open_state(bool value) { open_rep = value; }

// ===================================================================
// Constructors and destructors

MIDI_IO::~MIDI_IO(void) { }

MIDI_IO::MIDI_IO(const std::string& name, 
		   int mode) {
  label(name);
  io_mode(mode);
  nonblocking_rep = false;  
  readable_rep = writable_rep = open_rep = false;
}
