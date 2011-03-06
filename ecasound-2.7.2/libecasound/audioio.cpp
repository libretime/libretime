// ------------------------------------------------------------------------
// audioio.cpp: Routines common for all audio IO-devices.
// Copyright (C) 1999-2004,2008,2009 Kai Vehmanen
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

#include <cmath>
#include <string>

#include <kvu_dbc.h>
#include <kvu_message_item.h>
#include <kvu_numtostr.h>

#include "eca-error.h"
#include "audioio.h"
#include "eca-logger.h"

/**
 * FIXME notes  (last update 2008-03-11)
 *
 * - Modify default implementation of set_label()/label() so
 *   that is mapped directly to set/get_parameter(1, ...).
 */


const string& AUDIO_IO::SETUP_ERROR::message(void) const { return message_rep; }
AUDIO_IO::SETUP_ERROR::Error_type AUDIO_IO::SETUP_ERROR::type(void) const { return type_rep; }
AUDIO_IO::SETUP_ERROR::SETUP_ERROR(AUDIO_IO::SETUP_ERROR::Error_type type, 
				   const string& message) 
  : type_rep(type), message_rep(message) { }


// ===================================================================
// Constructors and destructors

AUDIO_IO::~AUDIO_IO(void)
{
  if (is_open() == true)
    close();
}

AUDIO_IO::AUDIO_IO(const string& name, 
		   int mode)
{
  set_label(name);
  set_io_mode(mode);
 
  nonblocking_rep = false; 
  open_rep = false;
}

// ===================================================================
// Attributes

/**
 * Returns info about supported I/O modes (bitwise-OR)
 *
 * Note that return value may change after device is
 * opened (some objects will refine their attributes after
 * external resources are acquired).
 *
 * By default, all I/O modes are supported.
 */
int AUDIO_IO::supported_io_modes(void) const { return (io_read | io_readwrite | io_write); }

/**
 * Whether device supports non-blocking I/O mode.
 *
 * Note that return value may change after device is
 * opened (some objects will refine their attributes after
 * external resources are acquired).
 *
 * By default, nonblocking mode is not supported.
 */
bool AUDIO_IO::supports_nonblocking_mode(void) const { return false; }

/**
 * Whether device supports seeking.
 *
 * Note that return value may change after device is
 * opened (some objects will refine their attributes after
 * external resources are acquired).
 *
 * By default, seeking is supported.
 *
 * @see supports_seeking_sample_accurate()
 */
bool AUDIO_IO::supports_seeking(void) const { return true; }

/**
 * Whether device supports sample accurate seeking.
 *
 * @see supports_seeking()
 *
 * Note that return value may change after device is
 * opened (some objects will refine their attributes after
 * external resources are acquired).
 *
 * By default, sample accurate seeking is supported if 
 * supports_seeking() returns 'true'.
 */
bool AUDIO_IO::supports_seeking_sample_accurate(void) const { return AUDIO_IO::supports_seeking(); }

/**
 * Whether audio stream has a distinct length. It's important
 * to note the difference between this attribute and 
 * 'supports_seeking()'. For example, a file read through 
 * a pipe mechanism is not seekable and its length is not 
 * known until 'finished()´ becomes true, but still, it is 
 * of finite length. A sine oscillator on the other hand 
 * can go on producing a signal forever, and is thus infinite.
 *
 * This attributes directly affects how 'finished()' should
 * to be interpreted. @see finished().
 *
 * Note that return value may change after device is
 * opened (some objects will refine their attributes after
 * external resources are acquired).
 *
 * By default, audio streams are finite length.
 */
bool AUDIO_IO::finite_length_stream(void) const { return true; }

/**
 * Whether audio format is locked. If this is true, audio object
 * has a known audio format, and doesn't allow overriding it.
 *
 * By default, audio format is not locked.
 */
bool AUDIO_IO::locked_audio_format(void) const { return false; }

// ===================================================================
// Configuration (setting and getting configuration parameters)

/**
 * Returns info about the current I/O mode.
 */
int AUDIO_IO::io_mode(void) const { return io_mode_rep; }

/**
 * Set object input/output-mode. If the requested mode isn't
 * supported, the nearest supported mode is used. Because 
 * of this, it's wise to afterwards check whether the requested
 * mode was accepted.
 *
 * require:
 *  is_open() != true
 */
void AUDIO_IO::set_io_mode(int mode) { io_mode_rep = mode; }

/**
 * Sets the object label. Label is used to identify the object instance.
 * Unlike ECA_OBJECT::name(), which is typically the same for all 
 * instances of the class, label() is instance specific. Still it
 * is not guaranteed to be unique for each object. Device and file 
 * names are typical label values.
 *
 * require:
 *  is_open() != true
 */
void AUDIO_IO::set_label(const string& id_label) { id_label_rep = id_label; }

/**
 * Enable/disbale nonblocking mode.
 *
 * require:
 *  is_open() != true
 */
void AUDIO_IO::toggle_nonblocking_mode(bool value)
{ 
  nonblocking_rep = value;
}

/**
 * Returns the current label. See documentation for 
 * label(const string&).
 */
const string& AUDIO_IO::label(void) const { return id_label_rep; }


/**
 * Returns a string containing info about sample format parameters.
 */
string AUDIO_IO::format_info(void) const
{
  MESSAGE_ITEM otemp;
  if (locked_audio_format() == true && is_open() != true) {
    otemp << "Using audio format specified in file header data (file not yet opened).";
  } else {
    otemp << "Format: " << format_string();
    otemp << ", channels " << channels();
    otemp << ", srate " << samples_per_second();

    if (interleaved_channels() == true) 
      otemp << ", interleaved";
    else
      otemp << ", noninterleaved";

    if (locked_audio_format() == true)
      otemp << " (locked params).";
    else
      otemp << ".";
  }
  return otemp.to_string();
}

void AUDIO_IO::set_parameter(int param, 
			     string value)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      AUDIO_IO::parameter_set_to_string(param, value));
  if (param == 1) set_label(value);
}

string AUDIO_IO::get_parameter(int param) const
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      AUDIO_IO::parameter_get_to_string(param));

  if (param == 1) return label();
  return "";
}

/** 
 * Returns a debugging string for a parameter value change.
 */
string AUDIO_IO::parameter_get_to_string(int param) const
{
  return string("get param ")
    + kvu_numtostr(param) + " of \"" + label() + "\": \"" 
    + kvu_numtostr(param) + "\"";
}

/** 
 * Returns a debugging string for a parameter value change.
 */
string AUDIO_IO::parameter_set_to_string(int param, string value) const
{
  return string("set param ")
    + kvu_numtostr(param) + " of \"" 
    + label() + "\" to \"" + value + "\"";
}

// ===================================================================
// Main functionality

void AUDIO_IO::open(void) throw (AUDIO_IO::SETUP_ERROR &)
{
  DBC_REQUIRE(is_open() != true);

  DBC_CHECK(channels() > 0);
  DBC_CHECK(sample_format() != ECA_AUDIO_FORMAT::sfmt_none);
  DBC_CHECK(samples_per_second() > 0);

  open_rep = true;
  if (supports_seeking() == true)
    seek_position(position_in_samples());
  else
    /* note: if seeking is not supported, object always starts 
     *       at position 0 when opened */
    set_position_in_samples(0);
}

void AUDIO_IO::close(void)
{
  DBC_REQUIRE(is_open() == true);
  open_rep = false;
}

// ===================================================================
// Runtime information

/**
 * If applicable, returns total length of the audio data stored
 * into current audio object. In many situations it's impossible
 * enquire the whole length of the object. For instance, if the 
 * object is streaming a finite length audio stream audio object
 * from other applications using some type of standard IPC, 
 * the actual length won't be known until the whole stream has
 * been read. As a general rule, if 'supports_seeking() == true', 
 * length can be known right after initialization. Then again,
 * if 'finite_length_stream() == true', the whole stream must
 * be processed before we know the actual length. In other
 * cases, length is unknown or infinite.
 */
ECA_AUDIO_TIME AUDIO_IO::length(void) const
{
  return ECA_AUDIO_TIME(length_in_samples(), samples_per_second());
}

/**
 * Returns the current position.
 */
ECA_AUDIO_TIME AUDIO_IO::position(void) const
{
  return ECA_AUDIO_TIME(position_in_samples(), samples_per_second());
}

/**
 * Is nonblocking mode is enabled?
 */
bool AUDIO_IO::nonblocking_mode(void) const { return nonblocking_rep; }

/**
 * Is the audio object ready for reading? 
 */
bool AUDIO_IO::readable(void) const { return is_open() && io_mode() != io_write; }

/**
 * Is the audio object ready for writing? 
 */
bool AUDIO_IO::writable(void) const { return is_open() && io_mode() != io_read; }

/**
 * Sets the total length of audio object data.
 */
void AUDIO_IO::length(const ECA_AUDIO_TIME& v)
{
  set_length_in_samples(v.samples());
}

/**
 * Sets the current position.
 */
void AUDIO_IO::position(const ECA_AUDIO_TIME& v)
{
  if (v.samples_per_second() == samples_per_second())
    set_position_in_samples(v.samples());
  else {
    ECA_AUDIO_TIME modified (v);
    modified.set_samples_per_second_keeptime(samples_per_second());
    set_position_in_samples(modified.samples());
  }
}

/**
 * Optional status string
 *
 * An unformatted text string describing the state and status of 
 * the current object.
 */
string AUDIO_IO::status(void) const {
  MESSAGE_ITEM mitem;
  mitem.setprecision(3);

  mitem << "position (" << position_in_seconds_exact() << "/";
  if (finite_length_stream() == true)
    mitem << length_in_seconds_exact();
  else
    mitem << "inf";
  mitem << ") seconds.\n -> ";
  
  if (is_open() == true) 
    mitem << "open, ";
  else 
    mitem << "closed";

  if (locked_audio_format() == true &&
      is_open() != true) {
    mitem << ", audio format not available until object is opened.";
  }
  else {
    mitem << ", " << format_string() << "/" << channels() << "ch/" << samples_per_second();
    mitem << "Hz, buffer " << buffersize() << ".";
  }

  return mitem.to_string();
}

/**
 * Overrides the non-virtaul function 
 * ECA_SAMPLERATE_AWARE::samples_per_second(), that is 
 * present (through inheritance) in both ECA_AUDIO_FORMAT
 * and ECA_AUDIO_POSITION.
 */
SAMPLE_SPECS::sample_rate_t AUDIO_IO::samples_per_second(void) const
{
  DBC_CHECK(ECA_AUDIO_FORMAT::samples_per_second() == 
	    ECA_AUDIO_POSITION::samples_per_second());
  return ECA_AUDIO_FORMAT::samples_per_second();
}

void AUDIO_IO::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  ECA_AUDIO_FORMAT::set_samples_per_second(v);
  ECA_AUDIO_POSITION::set_samples_per_second(v);
  ECA_LOG_MSG(ECA_LOGGER::system_objects,
		"set srate, aobj \"" +
		name() + ":" + label() +
		"\" to " + 
		kvu_numtostr(v) + ".");
}

void AUDIO_IO::set_audio_format(const ECA_AUDIO_FORMAT& f_str)
{
  ECA_AUDIO_FORMAT::set_audio_format(f_str);
  ECA_AUDIO_POSITION::set_samples_per_second(f_str.samples_per_second());
}

SAMPLE_SPECS::sample_pos_t AUDIO_IO::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  if (supports_seeking() != true &&
      pos != 0) {
    ECA_LOG_MSG(ECA_LOGGER::info,
		"WARNING: seeking not supported by audio objects of type \"" +
		name() + "\".");
    return position_in_samples();
  }

  return pos;
}
