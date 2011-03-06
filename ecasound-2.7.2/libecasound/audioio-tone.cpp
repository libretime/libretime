// ------------------------------------------------------------------------
// audioio-tone.cpp: Tone generator
//
// Adaptation to Ecasound:
// Copyright (C) 2007-2009 Kai Vehmanen (adaptation to Ecasound)
//
// Sources for sine generation (cmt-src-1.15/src/sine.cpp):
//
// Computer Music Toolkit - a library of LADSPA plugins. Copyright (C)
// 2000-2002 Richard W.E. Furse. The author may be contacted at
// richard@muse.demon.co.uk.
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
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

#include <algorithm>
#include <string>
#include <iostream>
#include <fstream>
#include <cstdlib>

#include <math.h> /* C++'s standard <cmath> does not define M_PI */

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "eca-object-factory.h"
#include "samplebuffer.h"
#include "audioio-tone.h"

#include "eca-error.h"
#include "eca-logger.h"

/**
 * FIXME notes  (last update 2008-03-06)
 *
 *  - define the syntax: is this 'tone', 'sinetone', 
 *    'tone=sine', ..., or?
 *  - add support for multichannel testing (different
 *    frequnecies for different channels?)
 */

using std::cout;
using std::endl;
using std::atof;
using std::string;

/* Sine table size is given by (1 << SINE_TABLE_BITS). */
#define SINE_TABLE_BITS 14
#define SINE_TABLE_SHIFT (8 * sizeof(unsigned long) - SINE_TABLE_BITS)

SAMPLE_SPECS::sample_t *g_pfSineTable = NULL;
SAMPLE_SPECS::sample_t g_fPhaseStepBase = 0;

static void initialise_sine_wavetable(void)
{
  if (g_pfSineTable == NULL) {
    unsigned long lTableSize = (1 << SINE_TABLE_BITS);
    double dShift = (double(M_PI) * 2) / lTableSize;
    g_pfSineTable = new SAMPLE_SPECS::sample_t[lTableSize];
    if (g_pfSineTable != NULL)
      for (unsigned long lIndex = 0; lIndex < lTableSize; lIndex++)
	g_pfSineTable[lIndex] = SAMPLE_SPECS::sample_t(sin(dShift * lIndex));
  }
  if (g_fPhaseStepBase == 0) {
    g_fPhaseStepBase = (SAMPLE_SPECS::sample_t)pow(2, sizeof(unsigned long) * 8);
  }
}

AUDIO_IO_TONE::AUDIO_IO_TONE (const std::string& name) 
  :  m_lPhaseStep(0), 
     m_fCachedFrequency(0),
     m_fLimitFrequency(0),
     m_fPhaseStepScalar(0)
{
  set_label(name);
  initialise_sine_wavetable();
}

AUDIO_IO_TONE::~AUDIO_IO_TONE(void)
{
}

AUDIO_IO_TONE* AUDIO_IO_TONE::clone(void) const
{
  AUDIO_IO_TONE* target = new AUDIO_IO_TONE();

  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }

  target->set_position_in_samples(position_in_samples());
  if (ECA_AUDIO_POSITION::length_set())
    target->ECA_AUDIO_POSITION::set_length_in_samples(ECA_AUDIO_POSITION::length_in_samples());

  target->buffersize_rep = buffersize_rep;
  target->finished_rep = finished_rep;
  target->m_lPhase = m_lPhase;
  target->m_lPhaseStep = m_lPhaseStep;
  DBC_CHECK(target->m_fCachedFrequency == m_fCachedFrequency);
  target->m_fLimitFrequency = m_fLimitFrequency;
  target->m_fPhaseStepScalar = m_fPhaseStepScalar;

  return target;
}

void AUDIO_IO_TONE::open(void) throw(AUDIO_IO::SETUP_ERROR &)
{
  DBC_CHECK(samples_per_second() != 0);

  if (io_mode() != AUDIO_IO::io_read)
    throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIO_IO_TONE: Writing to tone generator not allowed!"));

  finished_rep = false;
  m_fLimitFrequency
    = SAMPLE_SPECS::sample_t(samples_per_second() * 0.5);
  m_fPhaseStepScalar
    = SAMPLE_SPECS::sample_t(g_fPhaseStepBase / samples_per_second());

  /* recalculate m_fLimitFrequency and mfPhaseStepScalar */
  if (m_fCachedFrequency) 
    setPhaseStepFromFrequency(m_fCachedFrequency, true);

  AUDIO_IO::open();
}

void AUDIO_IO_TONE::close(void)
{
  AUDIO_IO::close();
}

bool AUDIO_IO_TONE::finite_length_stream(void) const
{
  return ECA_AUDIO_POSITION::length_set();
}

void AUDIO_IO_TONE::read_buffer(SAMPLE_BUFFER* sbuf)
{
  /* write to sbuf->buffer[ch], similarly as the LADSPA
   * chainops */

  sbuf->number_of_channels(channels());

  /* set the length according to our buffersize */
  if ((ECA_AUDIO_POSITION::length_set() == true) &&
      ((position_in_samples() + buffersize()) 
       >= ECA_AUDIO_POSITION::length_in_samples())) {
    /* over requested duration, adjust buffersize */
    SAMPLE_BUFFER::buf_size_t partialbuflen = 
      ECA_AUDIO_POSITION::length_in_samples() 
      - position_in_samples();
    if (partialbuflen < 0)
      partialbuflen = 0;
    DBC_CHECK(partialbuflen <= buffersize());
    sbuf->length_in_samples(partialbuflen);
    sbuf->event_tag_set(SAMPLE_BUFFER::tag_end_of_stream);
    finished_rep = true;
  }
  else
    sbuf->length_in_samples(buffersize());
  
  i.init(sbuf);
  i.begin();

  while(!i.end()) {
    for(int n = 0; n < channels(); n++) {
      if (i.end()) 
	break;

      *(i.current(n)) 
	= g_pfSineTable[m_lPhase >> SINE_TABLE_SHIFT];

    }

    m_lPhase += m_lPhaseStep;

    i.next();
  }

  change_position_in_samples(sbuf->length_in_samples());

  DBC_ENSURE(sbuf->number_of_channels() == channels());
}

void AUDIO_IO_TONE::write_buffer(SAMPLE_BUFFER* sbuf)
{
  /* NOP */
  DBC_CHECK(false);
}

SAMPLE_SPECS::sample_pos_t AUDIO_IO_TONE::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{
  /* note: phase must be correct after arbitrary seeks */
  m_lPhase = m_lPhaseStep * pos;

  if (ECA_AUDIO_POSITION::length_set() == true &&
      pos <
      ECA_AUDIO_POSITION::length_in_samples())
    finished_rep = false;

  return pos;
}

void AUDIO_IO_TONE::setPhaseStepFromFrequency(const SAMPLE_SPECS::sample_t fFrequency, bool force)
{
  if (fFrequency != m_fCachedFrequency || force == true) {
    if (fFrequency >= 0 && fFrequency < m_fLimitFrequency) 
      m_lPhaseStep = (unsigned long)(m_fPhaseStepScalar * fFrequency);
    else 
      m_lPhaseStep = 0;
    m_fCachedFrequency = fFrequency;
  }
}

void AUDIO_IO_TONE::set_parameter(int param, 
				  string value)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      AUDIO_IO::parameter_set_to_string(param, value));

  switch (param)
    {
    case 1: 
      {
	AUDIO_IO::set_parameter (param, value);
	break;
      }
    case 2:
      {
	/* type; only "sine" supported */
	break;
      }
    case 3: 
      {
	setPhaseStepFromFrequency (atof(value.c_str()), false);
	break;
      }
    case 4:
      {
	double duration = atof(value.c_str());
	if (duration > 0.0f)
	  ECA_AUDIO_POSITION::set_length_in_seconds(duration);
	break;
      }
    }
}

string AUDIO_IO_TONE::get_parameter(int param) const
{
  switch (param) 
    {
    case 1: return AUDIO_IO::get_parameter(param);
    case 2: return "sine";
    case 3: return kvu_numtostr(m_fCachedFrequency);
    case 4: 
      {
	if (ECA_AUDIO_POSITION::length_set() == true)
	  return kvu_numtostr(ECA_AUDIO_POSITION::length_in_seconds_exact());
	else
	  return kvu_numtostr(-1.0f);
      }
    default: break;
    }

  return std::string();
}
