// ------------------------------------------------------------------------
// audioio-audioseq.cpp: Audio clip sequencer class.
// Copyright (C) 2008,2010 Kai Vehmanen
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
#include <vector>
#include <iostream>
#include <fstream>
#include <cmath>

#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_dbc.h>

#include "eca-object-factory.h"
#include "samplebuffer.h"
#include "audioio-acseq.h"

#include "eca-error.h"
#include "eca-logger.h"

using std::cout;
using std::endl;
using SAMPLE_SPECS::sample_pos_t;

/**
 * FIXME notes  (last update 2008-03-04)
 *
 * - None.
 */

AUDIO_CLIP_SEQUENCER::AUDIO_CLIP_SEQUENCER ()
{
  set_label("audiocseq");

  /* note: index of last sequencer parameter (one if no
   *       extra parameters); params beyond this value are
   *       passed on to the child object */
  child_param_offset_rep = 1;

  cseq_mode_rep = AUDIO_CLIP_SEQUENCER::cseq_none;
}

AUDIO_CLIP_SEQUENCER::~AUDIO_CLIP_SEQUENCER(void)
{
}

AUDIO_CLIP_SEQUENCER* AUDIO_CLIP_SEQUENCER::clone(void) const
{
  AUDIO_CLIP_SEQUENCER* target = new AUDIO_CLIP_SEQUENCER();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}


void AUDIO_CLIP_SEQUENCER::open(void) throw(AUDIO_IO::SETUP_ERROR &)
{
  if (io_mode() != AUDIO_IO::io_read)
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ACLIPSEQ: Only read mode supported."));

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "Opening audio clip sequencer in mode: " 
	      + get_parameter(1));

  /* note: change behaviour based on first param */
  if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_loop) {
    /* following is specific to looping */
    AUDIO_SEQUENCER_BASE::toggle_looping(true);
    DBC_CHECK(finite_length_stream() != true);
    AUDIO_SEQUENCER_BASE::set_child_object_string(
      child_params_as_string(1 + child_param_offset_rep, &params_rep));
  }
  else if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_select) {
    AUDIO_SEQUENCER_BASE::toggle_looping(false);
    AUDIO_SEQUENCER_BASE::set_child_start_position(get_parameter(2));
    AUDIO_SEQUENCER_BASE::set_child_length(get_parameter(3));
    AUDIO_SEQUENCER_BASE::set_child_object_string(
      child_params_as_string(1 + child_param_offset_rep, &params_rep));
  }
  else if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_play_at) {
    /* following is specific to play-at */
    AUDIO_SEQUENCER_BASE::toggle_looping(false);
    AUDIO_SEQUENCER_BASE::set_child_offset(get_parameter(2));
    AUDIO_SEQUENCER_BASE::set_child_object_string(
      child_params_as_string(1 + child_param_offset_rep, &params_rep));
  }
  else
    throw(SETUP_ERROR(SETUP_ERROR::unexpected, "AUDIOIO-ACLIPSEQ: Unknown audio sequencing mode (loop, select, ...)."));

  AUDIO_SEQUENCER_BASE::open();

  /* step: set additional child params (if any) */
  int numparams = child()->number_of_params();
  for(int n = 0; n < numparams; n++) {
    child()->set_parameter(n + 1, get_parameter(n + 1 + child_param_offset_rep));
    if (child()->variable_params())
      numparams = child()->number_of_params();
  }
}

void AUDIO_CLIP_SEQUENCER::close(void)
{
  AUDIO_SEQUENCER_BASE::close();
}

std::string AUDIO_CLIP_SEQUENCER::parameter_names(void) const
{
  std::string baseparams;

  if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_loop)
    baseparams += std::string("audioloop");
  else if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_select)
    baseparams += std::string("select,start-sec,dur-sec");
  else if (cseq_mode_rep == AUDIO_CLIP_SEQUENCER::cseq_play_at)
    baseparams += std::string("playat,pos-sec");
  else
    baseparams += std::string("acseqtype");

  if (is_child_initialized() == true) {
    baseparams += "," + child()->parameter_names();
  }
  else {
    /* create a generic parameter name list */
    for (size_t i = 1; i < params_rep.size(); i++) {
      baseparams += ",param" + kvu_numtostr(i);
    }
  }
    
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      "param list: " + baseparams);

  return baseparams;
}

void AUDIO_CLIP_SEQUENCER::set_parameter(int param, string value)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      AUDIO_IO::parameter_set_to_string(param, value));

  if (param > static_cast<int>(params_rep.size())) params_rep.resize(param);

  if (param > 0)
    params_rep[param - 1] = value;

  if (param == 1) {
    set_label(value);
    if (value == "audioloop") {
      cseq_mode_rep = AUDIO_CLIP_SEQUENCER::cseq_loop;
      child_param_offset_rep = 1;
    }
    else if (value == "select") {
      cseq_mode_rep = AUDIO_CLIP_SEQUENCER::cseq_select;
      child_param_offset_rep = 3;
    }
    else if (value == "playat") {
      cseq_mode_rep = AUDIO_CLIP_SEQUENCER::cseq_play_at;
      child_param_offset_rep = 2;
    }
    else {
      cseq_mode_rep = AUDIO_CLIP_SEQUENCER::cseq_none;
      child_param_offset_rep = 1;
    }
  }

  if (param > child_param_offset_rep && 
      is_child_initialized() == true) {
    child()->set_parameter(param - child_param_offset_rep, value);
  }
  
  AUDIO_IO::set_parameter(param, value);
}

string AUDIO_CLIP_SEQUENCER::get_parameter(int param) const
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
	      AUDIO_IO::parameter_get_to_string(param));

  if (param > 0 && param < static_cast<int>(params_rep.size()) + 1) {
    if (param > child_param_offset_rep &&
	is_child_initialized() == true) {
      params_rep[param - 1] = child()->get_parameter(param - child_param_offset_rep);
    }
    return params_rep[param - 1];
  }

  return ""; 
}
