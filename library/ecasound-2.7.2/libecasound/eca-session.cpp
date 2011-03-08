// ------------------------------------------------------------------------
// eca-session.cpp: Ecasound runtime setup and parameters.
// Copyright (C) 1999-2004,2007 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// This program is fre software; you can redistribute it and/or modify
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
#include <cstring>
#include <algorithm>
#include <vector>
#include <iostream>

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <kvu_utils.h>
#include <kvu_com_line.h>
#include <kvu_message_item.h>
#include <kvu_dbc.h>

#include "eca-resources.h"
#include "eca-version.h"

#include "eca-chain.h"
#include "audiofx.h"
#include "audioio.h"
#include "audioio-mp3.h"
#include "audioio-mikmod.h"
#include "audioio-timidity.h"
#include "audioio-ogg.h"
#include "audioio-flac.h"
#include "audioio-aac.h"

#include "osc-gen-file.h"

#include "eca-error.h"
#include "eca-logger.h"
#include "eca-logger.h"
#include "eca-session.h"
#include "eca-chainsetup.h"

using std::string;
using std::vector;

ECA_SESSION::ECA_SESSION(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Session created (empty)");
  connected_chainsetup_repp = 0;
  selected_chainsetup_repp = 0;
  cs_defaults_set_rep = false;
}

ECA_SESSION::~ECA_SESSION(void)
{
  ECA_LOG_MSG(ECA_LOGGER::system_objects,"ECA_SESSION destructor-in");

  for(std::vector<ECA_CHAINSETUP*>::iterator q = chainsetups_rep.begin(); q != chainsetups_rep.end(); q++) {
    delete *q;
  }

  ECA_LOG_MSG(ECA_LOGGER::system_objects,"ECA_SESSION destructor-out");
}

ECA_SESSION::ECA_SESSION(COMMAND_LINE& cline) throw(ECA_ERROR&)
{
  int errors = 0;

  connected_chainsetup_repp = 0;
  selected_chainsetup_repp = 0;
  cs_defaults_set_rep = false;

  cline.combine();

  std::vector<std::string> options,csoptions;
  create_chainsetup_options(cline, &options);
  preprocess_options(&options);
  errors += interpret_general_options(options,&csoptions);

  /* NOTE: must be printed after general options are parsed 
  *        in case user has specified -q (quiet operation) */
  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Session created");

  if (errors > 0) {
    throw(ECA_ERROR("ECA-SESSION", "Errors parsing session-level options. Unable to create session."));
  }

  set_cs_param_defaults();

  if (chainsetups_rep.size() == 0) {
    /* Try to create a valid chainsetup from the options given
     * on the command-line. */

    ECA_CHAINSETUP* comline_setup = new ECA_CHAINSETUP(csoptions);

    if (comline_setup->interpret_result() != true) {
      string temp = comline_setup->interpret_result_verbose();
      delete comline_setup;
      // std::cerr << "EXCEPTION DETECTED:'" << temp << "'. Core dump follows if you've compiled with gcc-3.3...\n";
      throw(ECA_ERROR("ECA-SESSION", temp));
    }
    else {
      add_chainsetup(comline_setup); /* ownership object transfered */
      if (selected_chainsetup_repp == 0) {
	/* adding the chainsetup failed */
	delete comline_setup;
      }
      else if (selected_chainsetup_repp->is_valid() != true) {
	ECA_LOG_MSG(ECA_LOGGER::info, "NOTE: Unable to create a valid chainsetup from the command-line arguments.");
      }
    }
  }
}

/**
 * Sets defaults values for various chainsetup parameters 
 * using ECA_RESOURCES class services.
 */
void ECA_SESSION::set_cs_param_defaults(void)
{
  if (cs_defaults_set_rep != true) {
    // ---
    // Interpret resources 

    ECA_RESOURCES ecaresources;
    string v;

    /* note: defaults specified in ecasoundrc(5) */

    v = ecaresources.resource("ext-cmd-mp3-input");
    if (v.size() > 0)
      MP3FILE::set_input_cmd(v);
    v = ecaresources.resource("ext-cmd-mp3-output");
    if (v.size() > 0)
      MP3FILE::set_output_cmd(v);
    v = ecaresources.resource("ext-cmd-mikmod");
    if (v.size() > 0)
      MIKMOD_INTERFACE::set_mikmod_cmd(v);
    v = ecaresources.resource("ext-cmd-timidity");
    if (v.size() > 0)
      TIMIDITY_INTERFACE::set_timidity_cmd(v);
    v = ecaresources.resource("ext-cmd-ogg-input");
    if (v.size() > 0)
      OGG_VORBIS_INTERFACE::set_input_cmd(v);
    v = ecaresources.resource("ext-cmd-ogg-output");
    if (v.size() > 0)
      OGG_VORBIS_INTERFACE::set_output_cmd(v);
    v = ecaresources.resource("ext-cmd-flac-input");
    if (v.size() > 0)
      FLAC_FORKED_INTERFACE::set_input_cmd(v);
    v = ecaresources.resource("ext-cmd-flac-output");
    if (v.size() > 0)
      FLAC_FORKED_INTERFACE::set_output_cmd(v);
    v = ecaresources.resource("ext-cmd-aac-input");
    if (v.size() > 0)
      AAC_FORKED_INTERFACE::set_input_cmd(v);
    v = ecaresources.resource("ext-cmd-aac-output");
    if (v.size() > 0)
      AAC_FORKED_INTERFACE::set_output_cmd(v);

    cs_defaults_set_rep = true;
  }
}


/**
 * Add a new chainsetup
 *
 * require:
 *  name.empty() != true
 *
 * ensure:
 *  selected_chainsetup->name() == name ||
 *  chainsetup_names().size() has not changed
 */
void ECA_SESSION::add_chainsetup(const std::string& name)
{
  // --------
  DBC_REQUIRE(name != "");
  // --------

  set_cs_param_defaults();

  ECA_CHAINSETUP* newsetup = new ECA_CHAINSETUP;
  newsetup->set_name(name);
  add_chainsetup(newsetup);
  if (selected_chainsetup_repp == 0) {
    /* adding the chainsetup failed */
    delete newsetup;
  }

  // --------
  DBC_ENSURE((selected_chainsetup_repp != 0 &&
	      selected_chainsetup_repp->name() == name) ||
	     selected_chainsetup_repp == 0);
  // --------
}

/**
 * Add a new chainsetup. Ownership of the object given as argument
 * is passed along the call. If a chainsetup with the same name already
 * exists, the call will fail and the chainsetup given as argument
 * is deleted.
 *
 * require:
 *   comline_setup != 0
 *
 * ensure:
 *   (selected_chainsetup_repp == comline_setup && 
 *    static_cast<int>(chainsetups_rep.size()) == old_size + 1) ||
 *   (selected_chainsetup_repp == 0 && 
 *    static_cast<int>(chainsetups_rep.size()) == old_size)
 */
void ECA_SESSION::add_chainsetup(ECA_CHAINSETUP* comline_setup)
{
  // --------
  DBC_REQUIRE(comline_setup != 0);
  DBC_DECLARE(int old_size = chainsetups_rep.size());
  // --------

  selected_chainsetup_repp = comline_setup;

  set_cs_param_defaults();  

  std::vector<ECA_CHAINSETUP*>::const_iterator p = chainsetups_rep.begin();
  while(p != chainsetups_rep.end()) {
    if ((*p)->name() == comline_setup->name()) {
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "Unable to add chainsetup, chainsetup with the same name already exists.");
      selected_chainsetup_repp = 0;
      break;
    }
    ++p;
  }

  if (selected_chainsetup_repp != 0) {
    chainsetups_rep.push_back(selected_chainsetup_repp);
  }

  // --------
  DBC_ENSURE((selected_chainsetup_repp == comline_setup && 
	      static_cast<int>(chainsetups_rep.size()) == old_size + 1) ||
	     (selected_chainsetup_repp == 0 && 
	      static_cast<int>(chainsetups_rep.size()) == old_size));
  // --------
}

/**
 * Removes selected chainsetup
 *
 * require:
 *  connected_chainsetup != selected_chainsetup
 *
 * ensure:
 *  selected_chainsetup == 0
 */
void ECA_SESSION::remove_chainsetup(void)
{
  // --------
  // require:
  DBC_REQUIRE(connected_chainsetup_repp != selected_chainsetup_repp);
  // --------

  std::vector<ECA_CHAINSETUP*>::iterator p = chainsetups_rep.begin();
  while(p != chainsetups_rep.end()) {
    if (*p == selected_chainsetup_repp) {
      selected_chainsetup_repp = 0;
      delete *p;
      chainsetups_rep.erase(p);
      break;
    }
    ++p;
  }

  // --------
  DBC_ENSURE(selected_chainsetup_repp == 0);
  // --------
}

void ECA_SESSION::select_chainsetup(const std::string& name)
{
  // --------
  // require:
  DBC_REQUIRE(name.empty() != true);
  // --------

  selected_chainsetup_repp = 0;
  std::vector<ECA_CHAINSETUP*>::const_iterator p = chainsetups_rep.begin();
  while(p != chainsetups_rep.end()) {
    if ((*p)->name() == name) {
      //  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Chainsetup \"" + name + "\" selected.");
      selected_chainsetup_repp = *p;
      break;
    }
    ++p;
  }

  // --------
  DBC_ENSURE(selected_chainsetup_repp == 0 ||
	     selected_chainsetup_repp->name() == name);
  // --------
}

void ECA_SESSION::save_chainsetup(void) throw(ECA_ERROR&)
{
  // --------
  // require:
  DBC_REQUIRE(selected_chainsetup_repp != 0);
  // --------

  selected_chainsetup_repp->save();
}

void ECA_SESSION::save_chainsetup(const std::string& filename) throw(ECA_ERROR&)
{
  // --------
  // require:
  DBC_REQUIRE(selected_chainsetup_repp != 0 && filename.empty() != true);
  // --------

  selected_chainsetup_repp->save_to_file(filename);
}

/**
 * Load a chainsetup from file (ecs). If operation fails,
 * selected_chainsetup_repp == 0, ie. no chainsetup 
 * selected.
 *
 * @post (selected_chainsetup_repp != 0 &&
 *	  selected_chainsetup_repp->filename() == filename || 
 *        selected_chainsetup_repp == 0)
 */
void ECA_SESSION::load_chainsetup(const std::string& filename)
{
  // --------
  DBC_REQUIRE(filename.empty() != true);
  // --------

  set_cs_param_defaults();

  ECA_CHAINSETUP* new_setup = new ECA_CHAINSETUP(filename);
  if (new_setup->interpret_result() != true) {
    string temp = new_setup->interpret_result_verbose();
    delete new_setup;
    selected_chainsetup_repp = 0;
    ECA_LOG_MSG(ECA_LOGGER::info, "Error loading chainsetup: " + temp);
  }
  else {
    add_chainsetup(new_setup);
    if (selected_chainsetup_repp == 0) {
      /* adding the chainsetup failed */
      delete new_setup;
    }
  }
  
  // --------
  DBC_ENSURE((selected_chainsetup_repp != 0 &&
	      selected_chainsetup_repp->filename() == filename) || 
	     selected_chainsetup_repp == 0);
  // --------
}

void ECA_SESSION::connect_chainsetup(void) throw(ECA_ERROR&)
{
  // --------
  DBC_REQUIRE(selected_chainsetup_repp != 0);
  DBC_REQUIRE(selected_chainsetup_repp->is_valid());
  // --------

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Connecting chainsetup");

  if (selected_chainsetup_repp == connected_chainsetup_repp) return;

  if (connected_chainsetup_repp != 0) {
    disconnect_chainsetup();
  }

  /** 
   * enable() throws an exception if it wasn't possibly
   * to open/activate all input and output objects 
   */
  selected_chainsetup_repp->enable();
  connected_chainsetup_repp = selected_chainsetup_repp;

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Chainsetup connected");
 
  // --------
  // ensure:
  DBC_ENSURE(selected_chainsetup_repp == connected_chainsetup_repp);
  // --------
}

void ECA_SESSION::disconnect_chainsetup(void)
{
  // --------
  DBC_REQUIRE(connected_chainsetup_repp != 0);
  // --------

  connected_chainsetup_repp->disable();
  connected_chainsetup_repp = 0;

  ECA_LOG_MSG(ECA_LOGGER::user_objects, "Chainsetup disconnected");

  // --------
  DBC_ENSURE(connected_chainsetup_repp == 0);
  // --------
}

std::vector<std::string> ECA_SESSION::chainsetup_names(void) const
{
  std::vector<std::string> result;
  std::vector<ECA_CHAINSETUP*>::const_iterator p = chainsetups_rep.begin();
  while(p != chainsetups_rep.end()) {
    result.push_back((*p)->name());
    ++p;
  }
  return result;
}

void ECA_SESSION::create_chainsetup_options(COMMAND_LINE& cline,
					    std::vector<std::string>* options)
{
  cline.begin();
  cline.next(); // skip the program name
  while(cline.end() == false) {
    options->push_back(cline.current());
    cline.next();
  }
}

/**
 * Tests whether the given argument is a session-level option.
 */
bool ECA_SESSION::is_session_option(const std::string& arg) const
{
  if (arg.size() < 2 ||
      arg[0] != '-') return false;

  switch(arg[1]) {
  case 'R':
  case 'd':
  case 'q':
    return true;

  case 's': 
    if (arg.size() > 2 && arg[2] == ':') return true;
  }
  return false;
}

/**
 * Preprocesses a set of options.
 * 
 * Notes! See also ECA_CHAINSETUP_PARSER::preprocess_options()
 *
 * ensure:
 *  all options valid for further processing (all options
 *  must start with a '-' sign)
 */
void ECA_SESSION::preprocess_options(std::vector<std::string>* opts)
{
  std::vector<std::string>::iterator p = opts->begin();
  while(p != opts->end()) {

    if (p->size() > 0 && (*p)[0] != '-') {
      /* hack1: options ending with .ecs as "-s:file.ecs" */
      string::size_type pos = p->find(".ecs");
      if (pos + 4 == p->size()) {
	ECA_LOG_MSG(ECA_LOGGER::info, "NOTE: Interpreting option " +
		    *p +
		    " as -s:" +
		    *p +
		    ".");
	*p = "-s:" + *p;
      }
    }
    ++p;
  }
}

/**
 * Interprets all session specific options from 'inopts'.
 * All unprocessed options are copied to 'outopts'. 
 *
 * @return number of parsing errors
 */
int ECA_SESSION::interpret_general_options(const std::vector<std::string>& inopts,
					   std::vector<std::string>* outopts) 
{
  int errors = 0;

  std::vector<std::string>::const_iterator p = inopts.begin();
  while(p != inopts.end()) {
    if (p->size() > 0 && (*p)[0] == '-')
      errors += interpret_general_option(*p);
    ++p;
  }

  p = inopts.begin();
  while(p != inopts.end()) {
    if (p->size() > 0 && (*p)[0] == '-')
      errors += interpret_chainsetup_option(*p);

    if (is_session_option(*p) != true) outopts->push_back(*p);

    ++p;
  }

  return errors;
}

/**
 * Parses session option 'argu'.
 *
 * See also is_session_parameter()
 *
 * @return number of parsing errors
 */
int ECA_SESSION::interpret_general_option (const std::string& argu)
{
  if (argu.size() < 2) return 0;
  if (argu[0] != '-') return 0;

  switch(argu[1]) {
  case 'd':
    {
      if (argu.size() > 2 && argu[2] == ':') {
	/* argu == "-d:XXX" */
	ECA_LOGGER::instance().set_log_level_bitmask(atoi(kvu_get_argument_number(1, argu).c_str()));
      }
      else {
	/* argu == "-dXXX" */
	
	ECA_LOGGER::instance().set_log_level_bitmask(0);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::errors, 1);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::info, 1);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::subsystems, 1);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::module_names, 1);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::user_objects, 1);
	ECA_LOGGER::instance().set_log_level(ECA_LOGGER::eiam_return_values, 1);

	if (argu.size() > 2 && argu[2] == 'd') {
	  /* argu == "-dd" */
	  ECA_LOGGER::instance().set_log_level(ECA_LOGGER::system_objects, 1);	  

	  if (argu.size() > 3 && argu[3] == 'd') {
	    /* argu == "-ddd" */
	    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::functions, 1);
	    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::continuous, 1);
	  }
	}
      }

      MESSAGE_ITEM mtempd;
      mtempd << "Set debug level to: " << ECA_LOGGER::instance().get_log_level_bitmask();
      ECA_LOG_MSG(ECA_LOGGER::info, mtempd.to_string());
      break;
    }

  case 'R':
    {
      string tname = kvu_get_argument_number(1, argu);
      /* note: options are normalized, so ':' is always present 
       *       if there are any argument to an option */
      if (argu.size() > 2 && argu[2] == ':') {
	ECA_RESOURCES::rc_override_file = tname;
	cs_defaults_set_rep = false;
	ECA_LOG_MSG(ECA_LOGGER::info, 
		    "Using resource file \"" +
		    tname + 
		    "\". Disabling use of global/user resource files.");
      }
    }
    break;

  case 'q':
    ECA_LOGGER::instance().disable();
    break;

  default: { }
  }

  return 0;
}

/**
 * Parses session chainsetup option 'argu'.
 *
 * @return number of parsing errors
 */
int ECA_SESSION::interpret_chainsetup_option (const std::string& argu)
{
  int errors = 0;

  if (argu.size() == 0) return errors;
  
  string tname = kvu_get_argument_number(1, argu);
 
  if (argu.size() < 2) return errors;
  switch(argu[1]) {
  case 's': {
    if (argu.size() > 2 && argu[2] == ':') {
      load_chainsetup(tname);
      if (selected_chainsetup_repp == 0 || 
	  selected_chainsetup_repp->is_valid_for_connection(true) != true) {
	ECA_LOG_MSG(ECA_LOGGER::info, "Chainsetup loaded from \"" + tname + "\" is not valid!");
	++errors;
      }
    }
    break;
  }
  default: { }
  }
  
  return errors;
}
