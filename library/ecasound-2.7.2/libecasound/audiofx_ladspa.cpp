// ------------------------------------------------------------------------
// audiofx_ladspa.cpp: Wrapper class for LADSPA plugins
// Copyright (C) 2000-2004 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// References:
//     http://www.ladspa.org
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

#include <dlfcn.h>
#include <kvu_utils.h>
#include <kvu_dbc.h>
#include <kvu_numtostr.h>
#include "samplebuffer.h"
#include "audiofx_ladspa.h"
#include "eca-error.h"
#include "eca-logger.h"

EFFECT_LADSPA::EFFECT_LADSPA (const LADSPA_Descriptor *pdesc) throw(ECA_ERROR&)
{
  plugin_desc = pdesc;
  if ((plugin_desc->Properties & LADSPA_PROPERTY_INPLACE_BROKEN) ==
      LADSPA_PROPERTY_INPLACE_BROKEN)
    throw(ECA_ERROR("AUDIOFX_LADSPA", "Inplace-broken plugins not supported."));

  /* FIXME: strip linefeeds and other forbidden characters; write down to
   *        to ECA_OBJECT docs what chars are allowed and what are not... */
  name_rep = string(plugin_desc->Name);
  unique_rep = string(plugin_desc->Label);
  maker_rep = string(plugin_desc->Maker);
  unique_number_rep = static_cast<long int>(plugin_desc->UniqueID);
  buffer_repp = 0;

  init_ports();
}

EFFECT_LADSPA::~EFFECT_LADSPA (void)
{
  release();
  
  if (plugin_desc != 0) {
    for(unsigned int n = 0; n < plugins_rep.size(); n++) {
      if (plugin_desc->deactivate != 0) 
	plugin_desc->deactivate(plugins_rep[n]);
      if (plugin_desc->cleanup != 0)
	plugin_desc->cleanup(plugins_rep[n]);
    }
  }
}

std::string EFFECT_LADSPA::description(void) const
{
  return name_rep + " - Author: '" + maker_rep + "'";
}

EFFECT_LADSPA* EFFECT_LADSPA::clone(void) const
{ 
  EFFECT_LADSPA* result = new EFFECT_LADSPA(plugin_desc);
  for(int n = 0; n < number_of_params(); n++) {
    result->set_parameter(n + 1, get_parameter(n + 1));
  }
  return result;
}

void EFFECT_LADSPA::init_ports(void)
{
  // note: run from plugin constructor

  port_count_rep = plugin_desc->PortCount;
  in_audio_ports = 0;
  out_audio_ports = 0;

  for(unsigned long m = 0; m < port_count_rep; m++) {
    if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_AUDIO) == LADSPA_PORT_AUDIO) {
      if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_INPUT) == LADSPA_PORT_INPUT)
	++in_audio_ports;
      else
	++out_audio_ports;
    }

    if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_CONTROL) == LADSPA_PORT_CONTROL) {

      struct PARAM_DESCRIPTION pd; 
      parse_parameter_hint_information(m, params.size() + 1, &pd);

      params.push_back(pd.default_value);
      param_descs_rep.push_back(pd);
      if (params.size() > 1) param_names_rep += ",";
      string tmp (kvu_string_search_and_replace(string(plugin_desc->PortNames[m]), ",", "\\,"));
      param_names_rep += kvu_string_search_and_replace(tmp, ":", "\\:");
    }
  }
}

void EFFECT_LADSPA::parse_parameter_hint_information(int portnum, int paramnum, struct PARAM_DESCRIPTION *pd)
{
  LADSPA_PortRangeHintDescriptor hintdescriptor = plugin_desc->PortRangeHints[portnum].HintDescriptor;

  /* if srate not set, use 44.1kHz (used only for calculating
   * param hint values */
  SAMPLE_SPECS::sample_rate_t srate = samples_per_second();
  /* FIXME: this is just ugly! */
  if (srate <= 0) { srate = 44100; }
  
  /** 
   * For LADSPA v1.1, parameter hint information is 
   * used as advertised by the plugin. LADSPA v1.0 
   * API doesn't specify how to initialize control
   * ports to sane initial values, so we just try to 
   * make an educated guess based on the lower and
   * upper bounds.
   *
   * 1) v1.1 hint information available
   * 2) lowb == x and upperb == n/a
   *    a) x < 0, initval = 0
   *    b) x >= 0, initval = x
   * 3) lowb == n/a and upperb == x
   *    a) x > 0, initval = 0
   *    b) x <= 0, initval = x
   * 4) lowb == x and upperb == y 
   *    a) x < 0 and y > 0, initval = 0
   *    b) x < 0 and y < 0, initval = y
   *    c) x > 0 and y > 0, initval = x
   * 5) lowb == n/a and upperb == n/a, initval = 1
   */

  /* parameter name */
  pd->description = get_parameter_name(paramnum);

  /* upper and lower bounds */
  if (LADSPA_IS_HINT_BOUNDED_BELOW(hintdescriptor)) {
    pd->bounded_below = true;

    if (LADSPA_IS_HINT_SAMPLE_RATE(hintdescriptor)) 
      pd->lower_bound = plugin_desc->PortRangeHints[portnum].LowerBound * srate;
    else
      pd->lower_bound = plugin_desc->PortRangeHints[portnum].LowerBound;
  }
  else {
    pd->bounded_below = false;
  }

  if (LADSPA_IS_HINT_BOUNDED_ABOVE(hintdescriptor)) {
    pd->bounded_above = true;

    if (LADSPA_IS_HINT_SAMPLE_RATE(hintdescriptor)) 
      pd->upper_bound = plugin_desc->PortRangeHints[portnum].UpperBound * srate;
    else
      pd->upper_bound = plugin_desc->PortRangeHints[portnum].UpperBound;
  }
  else {
    pd->bounded_above = false;
  }

  /* defaults - case 1 */
  if (LADSPA_IS_HINT_HAS_DEFAULT(hintdescriptor)) {
    if (LADSPA_IS_HINT_DEFAULT_MINIMUM(hintdescriptor)) {
      pd->default_value = pd->lower_bound;
    }

    /* FIXME: add support for logarithmic defaults */

    else if (LADSPA_IS_HINT_DEFAULT_LOW(hintdescriptor)) {
      pd->default_value = pd->lower_bound * 0.75f + pd->upper_bound * 0.25f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_MIDDLE(hintdescriptor)) {
      pd->default_value = pd->lower_bound * 0.50f + pd->upper_bound * 0.50f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_HIGH(hintdescriptor)) {
      pd->default_value = pd->lower_bound * 0.25f + pd->upper_bound * 0.75f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_MAXIMUM(hintdescriptor)) {
      pd->default_value = pd->upper_bound;
    }
    else if (LADSPA_IS_HINT_DEFAULT_0(hintdescriptor)) {
      pd->default_value = 0.0f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_1(hintdescriptor)) {
      pd->default_value = 1.0f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_100(hintdescriptor)) {
      pd->default_value = 100.0f;
    }
    else if (LADSPA_IS_HINT_DEFAULT_440(hintdescriptor)) {
      pd->default_value = 440.0f;
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "No LADSPA hint info found for plugin '" + name_rep + "'.");
    }
  }

  /* defaults - case 2 */
  else if (LADSPA_IS_HINT_BOUNDED_BELOW(hintdescriptor) &&
	   !LADSPA_IS_HINT_BOUNDED_ABOVE(hintdescriptor)) {

    if (pd->lower_bound < 0) pd->default_value = 0.0f;
    else pd->default_value = pd->lower_bound;
  }

  /* defaults - case 3 */
  else if (!LADSPA_IS_HINT_BOUNDED_BELOW(hintdescriptor) &&
	   LADSPA_IS_HINT_BOUNDED_ABOVE(hintdescriptor)) {

    if (pd->upper_bound > 0) pd->default_value = 0.0f;
    else pd->default_value = pd->upper_bound;
  }

  /* defaults - case 4 */
  else if (LADSPA_IS_HINT_BOUNDED_BELOW(hintdescriptor) &&
	   LADSPA_IS_HINT_BOUNDED_ABOVE(hintdescriptor)) {

    if (pd->lower_bound < 0 && pd->upper_bound > 0) pd->default_value = 0.0f;
    else if (pd->lower_bound < 0 && pd->upper_bound < 0) pd->default_value = pd->upper_bound;
    else pd->default_value = pd->lower_bound;
  }

  /* defaults - case 5 */
  else {
    DBC_CHECK(!LADSPA_IS_HINT_BOUNDED_BELOW(hintdescriptor) &&
	      !LADSPA_IS_HINT_BOUNDED_ABOVE(hintdescriptor));

    if (LADSPA_IS_HINT_SAMPLE_RATE(hintdescriptor)) 
      pd->default_value = srate;
    else
      pd->default_value = 1.0f;
  }

  if (LADSPA_IS_HINT_TOGGLED(hintdescriptor))
    pd->toggled = true;
  else
    pd->toggled = false;
  
  if (LADSPA_IS_HINT_INTEGER(hintdescriptor))
    pd->integer = true;
  else
    pd->integer = false;
  
  if (LADSPA_IS_HINT_LOGARITHMIC(hintdescriptor))
    pd->logarithmic = true;
  else
    pd->logarithmic = false;

  if ((plugin_desc->PortDescriptors[portnum] & LADSPA_PORT_OUTPUT) == LADSPA_PORT_CONTROL)
    pd->output = true;
  else
    pd->output = false;
}

void EFFECT_LADSPA::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  DBC_CHECK(param >= 0);
  DBC_CHECK(param <= static_cast<int>(param_descs_rep.size()));
  *pd = param_descs_rep[param - 1];
}

void EFFECT_LADSPA::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  if (param > 0 && (param - 1 < static_cast<int>(params.size()))) {
    //  cerr << "ladspa: setting param " << param << " to " << value << "." << endl;
    params[param - 1] = value;
  }
}

CHAIN_OPERATOR::parameter_t EFFECT_LADSPA::get_parameter(int param) const 
{
  if (param > 0 && (param - 1 < static_cast<int>(params.size()))) {
    //  cerr << "ladspa: getting param " << param << " with value " << params[param - 1] << "." << endl;
    return(params[param - 1]);
  }
  return(0.0);
}

int EFFECT_LADSPA::output_channels(int i_channels) const
{
  // note: We have two separate cases: either one plugin 
  //       is instantiated for each channel, or one plugin
  //       per chain. See EFFECT_LADSPA::init().

  if (in_audio_ports > 1 ||
      out_audio_ports > 1) {

    return out_audio_ports;
  }
  
  return i_channels;
}

void EFFECT_LADSPA::init(SAMPLE_BUFFER *insample)
{ 
  EFFECT_BASE::init(insample);

  DBC_CHECK(samples_per_second() > 0);

  if (buffer_repp != insample) {
    release();
    buffer_repp = insample;
    buffer_repp->get_pointer_reflock();
  }

  if (plugin_desc != 0) {
    for(unsigned int n = 0; n < plugins_rep.size(); n++) {
      if (plugin_desc->deactivate != 0) plugin_desc->deactivate(plugins_rep[n]);
      plugin_desc->cleanup(plugins_rep[n]);
    }
  }

  // NOTE: the fancy definition :)
  //       if ((in_audio_ports > 1 &&
  //            in_audio_ports <= channels() &&
  //            out_audio_ports <= channels()) ||
  //           (out_audio_ports > 1 &&
  //            in_audio_ports <= channels() &&
  //            out_audio_ports <= channels())) {}

  if (in_audio_ports > 1 ||
      out_audio_ports > 1) {
    plugins_rep.resize(1);
    plugins_rep[0] = reinterpret_cast<LADSPA_Handle*>(plugin_desc->instantiate(plugin_desc, samples_per_second()));
    int inport = 0;
    int outport = 0;
    for(unsigned long m = 0; m < port_count_rep; m++) {
      if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_AUDIO) == LADSPA_PORT_AUDIO) {
	if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_INPUT) == LADSPA_PORT_INPUT) {
	  if (inport < channels())
	    plugin_desc->connect_port(plugins_rep[0], m, buffer_repp->buffer[inport]);
	  ++inport;
	}
	else {
	  if (outport < channels())
	    plugin_desc->connect_port(plugins_rep[0], m, buffer_repp->buffer[outport]);
	  ++outport;
	}
      }
    }
    
    if (inport > channels())
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "WARNING: chain has less channels than plugin has input ports ("
		  + name() + ").");
    if (outport > channels())
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "WARNING: chain has less channels than plugin has output ports ("
		  + name() + ").");
  } 
  else {
    plugins_rep.resize(channels());
    for(unsigned int n = 0; n < plugins_rep.size(); n++) {
      plugins_rep[n] = reinterpret_cast<LADSPA_Handle*>(plugin_desc->instantiate(plugin_desc, samples_per_second()));

      for(unsigned long m = 0; m < port_count_rep; m++) {
	if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_AUDIO) == LADSPA_PORT_AUDIO) {
	  plugin_desc->connect_port(plugins_rep[n], m, buffer_repp->buffer[n]);
	}
      }
    }
  }

  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"Instantiated " +
		kvu_numtostr(plugins_rep.size()) + 
		" LADSPA plugin(s), each with " + 
		kvu_numtostr(in_audio_ports) + 
		" audio input port(s) and " +
		kvu_numtostr(out_audio_ports) +
		" output port(s), to chain with " +
		kvu_numtostr(channels()) +
		" channel(s) and srate of " +
		kvu_numtostr(samples_per_second()) +
		".");

  int data_index = 0;
  for(unsigned long m = 0; m < port_count_rep; m++) {
    if ((plugin_desc->PortDescriptors[m] & LADSPA_PORT_CONTROL) ==
	LADSPA_PORT_CONTROL) {
      for(unsigned int n = 0; n < plugins_rep.size(); n++) {
	plugin_desc->connect_port(plugins_rep[n], m, &(params[data_index]));
      }
      ++data_index;
    }
  }
  for(unsigned long m = 0; m < plugins_rep.size(); m++)
    if (plugin_desc->activate != 0) plugin_desc->activate(plugins_rep[m]);
}

void EFFECT_LADSPA::release(void)
{
  if (buffer_repp != 0) {
    buffer_repp->release_pointer_reflock();
  }
  buffer_repp = 0;
}

void EFFECT_LADSPA::process(void)
{
  for(unsigned long m = 0; m < plugins_rep.size(); m++)
    plugin_desc->run(plugins_rep[m], buffer_repp->length_in_samples());
}
