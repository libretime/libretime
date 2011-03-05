// ------------------------------------------------------------------------
// audioio-typeselect.cpp: A proxy class for overriding default keyword
//                         and filename associations.
// Copyright (C) 2001,2002,2008,2009 Kai Vehmanen
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

#include "eca-logger.h"
#include "eca-object-factory.h"
#include "audioio-null.h"
#include "audioio-typeselect.h"

/**
 * Constructor.
 */
AUDIO_IO_TYPESELECT::AUDIO_IO_TYPESELECT (void)
{
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "constructor " + label() + ".");  
  init_rep = false;
}

/**
 * Destructor.
 */
AUDIO_IO_TYPESELECT::~AUDIO_IO_TYPESELECT (void)
{
  //  ECA_LOG_MSG(ECA_LOGGER::user_objects, "destructor " + label() + ".");  
}

AUDIO_IO_TYPESELECT* AUDIO_IO_TYPESELECT::clone(void) const
{
  AUDIO_IO_TYPESELECT* target = new AUDIO_IO_TYPESELECT();
  for(int n = 0; n < number_of_params(); n++) {
    target->set_parameter(n + 1, get_parameter(n + 1));
  }
  return target;
}

void AUDIO_IO_TYPESELECT::open(void) throw(AUDIO_IO::SETUP_ERROR&)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "open " + label() + ".");  

  if (init_rep != true) {
    AUDIO_IO* tmp = 0;

    const string& objname = 
      child_params_as_string(2, &params_rep);

    tmp = 
      ECA_OBJECT_FACTORY::create_audio_object(objname);

    if (tmp == 0)
      throw(SETUP_ERROR(SETUP_ERROR::io_mode, "AUDIOIO-TYPESELECT: unable to open child object '" + objname + "'"));

    set_child(tmp);

    int numparams = child()->number_of_params();
    for(int n = 0; n < numparams; n++) {
      child()->set_parameter(n + 1, get_parameter(n + 3));
      if (child()->variable_params())
	numparams = child()->number_of_params(); 
    }

    init_rep = true; /* must be set after dyn. parameters */
  }

  pre_child_open();
  child()->open();
  post_child_open();

  /* if child changed the format during open; 
   * fetch the changes */
  if (child()->locked_audio_format() == true) {
    set_audio_format(child()->audio_format());
  }

  set_label(child()->label());
  set_length_in_samples(child()->length_in_samples());

  AUDIO_IO_PROXY::open();
}

void AUDIO_IO_TYPESELECT::close(void)
{ 
  if (child()->is_open() == true) child()->close();

  AUDIO_IO_PROXY::close();
}

string AUDIO_IO_TYPESELECT::parameter_names(void) const
{ 
  return string("typeselect,format,") + child()->parameter_names(); 
}

void AUDIO_IO_TYPESELECT::set_parameter(int param, string value)
{ 
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"set_parameter "
		+ label() + ".");  

  if (param > static_cast<int>(params_rep.size())) params_rep.resize(param);

  if (param > 0) {
    params_rep[param - 1] = value;
  }
  
  if (param > 2 && 
      init_rep == true) {
    child()->set_parameter(param - 2, value);
  }
}

string AUDIO_IO_TYPESELECT::get_parameter(int param) const
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		"get_parameter "
		+ label() + ".");  

  if (param > 0 && param < static_cast<int>(params_rep.size()) + 1) {
    if (param > 2 &&
	init_rep == true) {
      params_rep[param - 1] = child()->get_parameter(param - 2);
    }
    return params_rep[param - 1];
  }

  return "";
}
