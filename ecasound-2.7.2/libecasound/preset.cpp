// ------------------------------------------------------------------------
// preset.cpp: Class for representing effect presets
// Copyright (C) 2000-2002,2004-2007,2009 Kai Vehmanen
// Copyright (C) 2001 Arto Hamara
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

#include <cstdlib>
#include <iostream>
#include <vector>
#include <string>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>
#include <kvu_utils.h>

#include "eca-chain.h"
#include "eca-chainop.h"
#include "generic-controller.h"
#include "eca-object-factory.h"
#include "samplebuffer.h"
#include "eca-logger.h"
#include "eca-error.h"
#include "preset.h"
#include "preset_impl.h"

using std::string;
using std::vector;
using std::cerr;
using std::endl;

PRESET::PRESET(void)
{
  impl_repp = new PRESET_impl();
  impl_repp->parsed_rep = false;
}

PRESET::PRESET(const string& formatted_string)
{
  impl_repp = new PRESET_impl();
  parse(formatted_string);
}

PRESET::~PRESET(void)
{
  vector<CHAIN*>::iterator q = chains.begin();
  while(q != chains.end()) {
    delete *q;
    ++q;
  }

  vector<SAMPLE_BUFFER*>::iterator p = buffers.begin();
  while(p != buffers.end()) {
    if (p != buffers.begin()) delete *p; // first buffer points to an
                                         // outside buffer -> not
                                         // deleted here
    ++p;
  }

  for(size_t n = 0; n < impl_repp->pardesclist_rep.size(); n++) {
    delete impl_repp->pardesclist_rep[n];
    impl_repp->pardesclist_rep[n] = 0;
  }

  // NOTE: chainops and controllers are deleted in CHAIN::~CHAIN()

  delete impl_repp;
  impl_repp = 0;
}

PRESET* PRESET::clone(void) const
{ 
  vector<parameter_t> param_values;
  for(int n = 0; n < number_of_params(); n++) {
    param_values.push_back(get_parameter(n + 1));
  }
  PRESET* preset = new PRESET(impl_repp->parse_string_rep);
  for(int n = 0; n < preset->number_of_params(); n++) {
    preset->set_parameter(n + 1, param_values[n]);
  }
  return preset;
}

PRESET* PRESET::new_expr(void) const
{
  return new PRESET(impl_repp->parse_string_rep);
}

void PRESET::set_samples_per_second(SAMPLE_SPECS::sample_rate_t v)
{
  for(size_t q = 0; q < chains.size(); q++) {
    chains[q]->set_samples_per_second(v);
  }

  ECA_SAMPLERATE_AWARE::set_samples_per_second(v);
}

string PRESET::name(void) const
{
  return impl_repp->name_rep;
}

string PRESET::description(void) const
{
  return impl_repp->description_rep;
}

void PRESET::set_name(const string& v)
{
  impl_repp->name_rep = v;
}

/**
 * Whether preset data has been parsed
 */
bool PRESET::is_parsed(void) const
{
  return impl_repp->parsed_rep;
}

void PRESET::parameter_description(int param, struct PARAM_DESCRIPTION *pd) const
{
  if (param > 0 && param <= static_cast<int>(impl_repp->pardesclist_rep.size()))
    *pd = *impl_repp->pardesclist_rep[param - 1];
}

/**
 * Parse preset data from the formatted string given 
 * as argument.
 *
 * require:
 *  formatted_string.empty() == false
 * ensure:
 *  is_parsed() == true
 */
void PRESET::parse(const string& formatted_string)
{
  // --------
  DBC_REQUIRE(formatted_string.empty() == false);
  // --------

  impl_repp->parse_string_rep = formatted_string;
  chains.clear();
  chains.push_back(new CHAIN());
  chains.back()->set_samples_per_second(samples_per_second());

  // FIXME: add support for quotes (ie. "one token with space" style)
  vector<string> tokens = kvu_string_to_words(formatted_string);
  vector<string>::const_iterator p = tokens.begin();
  while(p != tokens.end()) {
    ECA_LOG_MSG(ECA_LOGGER::user_objects, "Parsing: " + *p + ".");

    /* case 1: new chain */
    if (*p == "|") {
      add_chain();
    }

    /* case 2: preset specific option */
    else if (is_preset_option(*p) == true) {
      parse_preset_option(*p);
    }

    /* case 3: ecasound cop option */
    else {
      parse_operator_option(*p);
    }

    ++p;
  }

  impl_repp->parsed_rep = true;

  // --------
  DBC_ENSURE(is_parsed() == true);
  // --------
}

bool PRESET::is_preset_option(const string& arg) const
{
  if (arg.size() < 2 ||
      arg[0] != '-') return false;

  switch(arg[1]) {
  case 'p':
    {
      if (arg.size() < 3) return false;
      switch(arg[2]) {
      case 'd':
      case 'p':
	return true;
	
      default: { }
      }
    }
  default: { }
  }
  return false;
}

void PRESET::parse_preset_option(const string& arg)
{
  if (arg.size() < 2) return;
  if (arg[0] != '-') return;
  switch(arg[1]) {
  case 'p':
    {
      if (arg.size() < 3) return;
      switch(arg[2]) {
      case 'd':
	{
	  /* -pd:preset_description */
	  impl_repp->description_rep = kvu_get_argument_number(1, arg);
	  break;
	}

      case 'p': 
	{
	  if (arg.size() < 4) return;
	  switch(arg[3]) {
	  case 'd': 
	    {
	      /* -ppd:x,y,z (param default values) */
	      set_preset_defaults(kvu_get_arguments(arg));
	      break;
	    }

	  case 'n': 
	    {
	      /* -ppn:x,y,z (param names) */
	      set_preset_param_names(kvu_get_arguments(arg));
	      break;
	    }

	  case 'l': 
	    {
	      /* -ppl:x,y,z (param lower bounds) */
	      set_preset_lower_bounds(kvu_get_arguments(arg));
	      break;
	    }

	  case 'u':
	    {
	      /* -ppu:x,y,z (param upper bounds) */
	      set_preset_upper_bounds(kvu_get_arguments(arg));
	      break;
	    }

	  case 't':
	    {
	      /* -ppt:x,y,z (param toggle) */
	      set_preset_toggles(kvu_get_arguments(arg));
	      break;
	    }
	    
	  default: 
	    { 
	      ECA_LOG_MSG(ECA_LOGGER::info, "Unknown preset option (1) " + arg + ".");
	      break; 
	    }
	  }

	  break; /* -pp */
	}

      default: { ECA_LOG_MSG(ECA_LOGGER::info, "Unknown preset option (2) " + arg + "."); break; }
	
      }

      break; /* -p */
 
    }

  default: { ECA_LOG_MSG(ECA_LOGGER::info, "Unknown preset option (3) " + arg + "."); break; }
    
  }
}

void PRESET::extend_pardesc_vector(int number)
{
  while (static_cast<int>(impl_repp->pardesclist_rep.size()) < number) {
    DBC_DECLARE(size_t oldsize = impl_repp->pardesclist_rep.size());
    impl_repp->pardesclist_rep.push_back(new OPERATOR::PARAM_DESCRIPTION());
    //  cerr << "(preset) adding pardesclist_rep item." << endl;
    DBC_CHECK(impl_repp->pardesclist_rep.size() == oldsize + 1);
  }
}

void PRESET::set_preset_defaults(const vector<string>& args)
{
  extend_pardesc_vector(args.size());
  for(size_t n = 0; n < args.size(); n++) {
    if (args[n].size() > 0 && args[n][0] == '-') continue;
    impl_repp->pardesclist_rep[n]->default_value = atof(args[n].c_str());
    set_parameter(n + 1, impl_repp->pardesclist_rep[n]->default_value);
    //  cerr << "(preset) setting default for " << n << " to " << impl_repp->pardesclist_rep[n]->default_value << "." << endl;
  }
}

void PRESET::set_preset_param_names(const vector<string>& args)
{
  impl_repp->preset_param_names_rep.resize(args.size());
  for(size_t n = 0; n < args.size(); n++) {
    impl_repp->preset_param_names_rep[n] = args[n];
    //  cerr << "(preset) setting param name for " << n << " to " << impl_repp->preset_param_names_rep[n] << "." << endl;
  }
}

void PRESET::set_preset_lower_bounds(const vector<string>& args)
{
  extend_pardesc_vector(args.size());
  for(size_t n = 0; n < args.size(); n++) {
    if (args[n].size() > 0 && args[n][0] == '-') {
      impl_repp->pardesclist_rep[n]->bounded_below = false;
    }
    else {
      impl_repp->pardesclist_rep[n]->bounded_below = true;
      impl_repp->pardesclist_rep[n]->lower_bound = atof(args[n].c_str());
      //  cerr << "(preset) setting lowbound for " << n << " to " << impl_repp->pardesclist_rep[n]->lower_bound << "." << endl;
    }
  }
}

void PRESET::set_preset_upper_bounds(const vector<string>& args)
{
  extend_pardesc_vector(args.size());
  for(size_t n = 0; n < args.size(); n++) {
    if (args[n].size() > 0 && args[n][0] == '-') {
      impl_repp->pardesclist_rep[n]->bounded_above = false;
    }
    else {
      impl_repp->pardesclist_rep[n]->bounded_above = true;
      impl_repp->pardesclist_rep[n]->upper_bound = atof(args[n].c_str());
      //  cerr << "(preset) setting upperbound for " << n << " to " << impl_repp->pardesclist_rep[n]->upper_bound << "." << endl;
    }
  }
}

void PRESET::set_preset_toggles(const vector<string>& args)
{
  extend_pardesc_vector(args.size());
  for(size_t n = 0; n < args.size(); n++) {

    impl_repp->pardesclist_rep[n]->integer = false;
    impl_repp->pardesclist_rep[n]->logarithmic = false;
    impl_repp->pardesclist_rep[n]->output = false;
    impl_repp->pardesclist_rep[n]->toggled = false;

    if (args[n].find("i") != string::npos) 
      impl_repp->pardesclist_rep[n]->integer = true;
    if (args[n].find("l") != string::npos) 
      impl_repp->pardesclist_rep[n]->logarithmic = true;
    if (args[n].find("o") != string::npos) 
      impl_repp->pardesclist_rep[n]->output = true;
    if (args[n].find("t") != string::npos) 
      impl_repp->pardesclist_rep[n]->toggled = true;

    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		string("Setting preset toggles: integer=")
		+ kvu_numtostr(impl_repp->pardesclist_rep[n]->integer)
		+ ", log="
		+ kvu_numtostr(impl_repp->pardesclist_rep[n]->logarithmic)
		+ ", output="
		+ kvu_numtostr(impl_repp->pardesclist_rep[n]->output)
		+ ", toggle="
		+ kvu_numtostr(impl_repp->pardesclist_rep[n]->toggled)
		+ ".");
  }
}

void PRESET::parse_operator_option(const string& arg)
{
  CHAIN_OPERATOR *cop;
  GENERIC_CONTROLLER* gctrl;

  /* phase 1: parse for cop definitions -eabc:1.0,%param1,2.0 */
  vector<int> arg_indices;
  vector<int> arg_slave_indices;
  vector<string> ps_parts(kvu_get_number_of_arguments(arg));
  for(int i = 0; i < kvu_get_number_of_arguments(arg); i++) {
    string onearg = kvu_get_argument_number(i + 1, arg);
    if(onearg.size() > 0 && onearg[0] == '%') {

      // FIXME: what if %xxx options are given in the "wrong" order?

      size_t preset_index = atoi(kvu_get_argument_number(i + 1, arg).substr(1).c_str());
      if (preset_index <= arg_indices.size()) {
	preset_index = arg_indices.size() + 1;
      }
      arg_indices.push_back(preset_index);
      arg_slave_indices.push_back(i + 1);
      ps_parts[i] = "0.0";

      // make sure param is mentioned in param_names list
      if (impl_repp->preset_param_names_rep.size() < preset_index) {
	impl_repp->preset_param_names_rep.resize(preset_index);
	impl_repp->preset_param_names_rep[preset_index - 1] = string("arg-") + kvu_numtostr(preset_index);
      }

    } 
    else {
      ps_parts[i] = onearg;
    }
  }

  DBC_CHECK(arg_indices.size() == arg_slave_indices.size());

  /* phase 2: 'ps' is set to -eabc:1.0,2.0,2.0 (no %-params) */
  string ps = "-" + kvu_get_argument_prefix(arg) + ":" + kvu_vector_to_string(ps_parts, ",");
  //  cerr << "Creating object from '" << ps << "'."  << endl;

  /* phase 3: create an object using 'ps' */
  DYNAMIC_OBJECT<SAMPLE_SPECS::sample_t>* object = 0;
  cop = 0;
  cop = ECA_OBJECT_FACTORY::create_chain_operator(ps);
  if (cop == 0) cop = ECA_OBJECT_FACTORY::create_ladspa_plugin(ps);
  if (cop != 0) {
    chains.back()->add_chain_operator(cop);
    chains.back()->selected_chain_operator_as_target();
    object = cop;
  }
  else {
    if (kvu_get_argument_prefix(ps) == "kx") 
      chains.back()->selected_controller_as_target();
    else {
      gctrl = ECA_OBJECT_FACTORY::create_controller(ps);
      if (gctrl != 0) {
	impl_repp->gctrls_rep.push_back(gctrl);
	chains.back()->add_controller(gctrl);
      }
      object = gctrl;
    }
  }

  /* phase 4: create an object using 'ps' */
  if (object != 0) {
    for(int i = 0; i < static_cast<int>(arg_indices.size()); i++) {

      // NOTE: there's a one-to-many connection between 
      //       presets' parameters and 'object-param' pairs
      //       (ie. one preset-parameter can control 
      //       multiple object params (param_objects and 
      //       param_indices)

      size_t preset_index = arg_indices[i];

      // NOTE: for instance for LADSPA plugins -el:label,par1,par2 
      //       number_of_args is 3, but number_of_params is 2!
      int slave_index = arg_slave_indices[i];
      slave_index -= kvu_get_number_of_arguments(arg) - object->number_of_params();

      if (preset_index > impl_repp->slave_param_objects_rep.size()) {
	impl_repp->slave_param_objects_rep.resize(preset_index);
	impl_repp->slave_param_indices_rep.resize(preset_index);
      }

      impl_repp->slave_param_objects_rep[preset_index - 1].push_back(object);
      impl_repp->slave_param_indices_rep[preset_index - 1].push_back(slave_index);

      // cerr << "Linking preset parameter " << preset_index << " to object '" << impl_repp->slave_param_objects_rep[preset_index - 1].back()->name()  << "', and its parameter '" << impl_repp->slave_param_objects_rep[preset_index -	1].back()->get_parameter_name(impl_repp->slave_param_indices_rep[preset_index - 1].back()) << "'." << endl;
      
      DBC_CHECK(impl_repp->slave_param_objects_rep.size() == impl_repp->slave_param_indices_rep.size());
    }
  }
}

void PRESET::add_chain(void)
{
  chains.push_back(new CHAIN());
  buffers.push_back(new SAMPLE_BUFFER());
}


string PRESET::parameter_names(void) const
{
  return kvu_vector_to_string(impl_repp->preset_param_names_rep, ",");
}

void PRESET::set_parameter(int param, CHAIN_OPERATOR::parameter_t value)
{
  if (param > 0 && param <= static_cast<int>(impl_repp->slave_param_objects_rep.size())) {
    for(size_t n = 0; n < impl_repp->slave_param_objects_rep[param - 1].size(); n++) {
      DBC_CHECK(static_cast<int>(impl_repp->slave_param_indices_rep.size()) > param - 1);
      DBC_CHECK(impl_repp->slave_param_indices_rep[param - 1].size() > n);
      int index = impl_repp->slave_param_indices_rep[param - 1][n];
      //  cerr << "Setting preset " << name() << " param " << param << " (" << impl_repp->slave_param_objects_rep[param-1][n]->get_parameter_name(param) << "), of object " << impl_repp->slave_param_objects_rep[param-1][n]->name() << ", with index number " << index << ", to value " << value << "." << endl;
      impl_repp->slave_param_objects_rep[param-1][n]->set_parameter(index, value);
    }
  }
}

CHAIN_OPERATOR::parameter_t PRESET::get_parameter(int param) const
{
  if (param > 0 && param <= static_cast<int>(impl_repp->slave_param_objects_rep.size())) {
    DBC_CHECK(static_cast<int>(impl_repp->slave_param_indices_rep.size()) > param - 1);

    if (impl_repp->slave_param_indices_rep[param - 1].size() > 0) {
      int index = impl_repp->slave_param_indices_rep[param - 1][0];
      DBC_CHECK(index > 0);

      //  cerr << "Getting preset " << name() << " param " << param << ", index number " << index cerr << " with value " << impl_repp->slave_param_objects_rep[param-1][0]->get_parameter(index) << "." << endl;
      return impl_repp->slave_param_objects_rep[param-1][0]->get_parameter(index);
    }
  }
  return 0.0f;
}

void PRESET::init(SAMPLE_BUFFER *insample)
{
  DBC_CHECK(samples_per_second() > 0);

  first_buffer = insample;
  chains[0]->set_samples_per_second(samples_per_second());
  chains[0]->init(first_buffer, first_buffer->number_of_channels(), first_buffer->number_of_channels());
  for(size_t q = 1; q < chains.size(); q++) {
    DBC_CHECK(q - 1 < buffers.size());
    buffers[q - 1]->length_in_samples(first_buffer->length_in_samples());
    buffers[q - 1]->number_of_channels(first_buffer->number_of_channels());
    chains[q]->set_samples_per_second(samples_per_second());
    chains[q]->init(buffers[q - 1], 
		    first_buffer->number_of_channels(), 
		    first_buffer->number_of_channels());
  }

  for(size_t n = 0; n < impl_repp->gctrls_rep.size(); n++) {
    impl_repp->gctrls_rep[n]->init();
  }
}

void PRESET::release(void)
{
  /* reimplemented from CHAIN_OPERATOR base class; 
   * see init() */
  vector<CHAIN*>::iterator q = chains.begin();
  while(q != chains.end()) {
    (*q)->release();
    ++q;
  }

  first_buffer = 0;
}

void PRESET::process(void)
{
  vector<SAMPLE_BUFFER*>::iterator p = buffers.begin();
  while(p != buffers.end()) {
    (*p)->copy_all_content(*first_buffer);
    ++p;
  }

  vector<CHAIN*>::iterator q = chains.begin();
  while(q != chains.end()) {
    (*q)->process();
    ++q;
  }

  if (chains.size() > 1) {
    first_buffer->divide_by(chains.size());
    p = buffers.begin();
    while(p != buffers.end()) {
      first_buffer->add_with_weight(**p, static_cast<int>(chains.size()));
      ++p;
    }
  }
}
