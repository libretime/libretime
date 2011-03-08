// ------------------------------------------------------------------------
// eca-chainsetup-parser.cpp: Functionality for parsing chainsetup 
//                            option syntax.
// Copyright (C) 2001-2006 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <algorithm> /* find() */

#include <kvu_dbc.h> /* DBC_* */
#include <kvu_message_item.h>
#include <kvu_numtostr.h>
#include <kvu_utils.h>

#include "audioio.h"
#include "file-preset.h"
#include "global-preset.h"
#include "midiio.h"
#include "midi-client.h"
#include "midi-server.h"
#include "generic-controller.h"
#include "eca-chain.h"

#include "eca-logger.h"
#include "eca-object-factory.h"
#include "eca-preset-map.h"

#include "eca-chainsetup.h"
#include "eca-chainsetup-parser.h"
#include "eca-chainsetup-bufparams.h"

using std::string;

ECA_CHAINSETUP_PARSER::ECA_CHAINSETUP_PARSER(ECA_CHAINSETUP* csetup) 
  : csetup_repp(csetup), 
    last_audio_add_vector_repp(0) 
{
}

/**
 * Interprets one option. This is the most generic variant of
 * the interpretation routines; both global and object specific
 * options are handled.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * 
 * @post (option succesfully interpreted && interpret_result() ==  true) ||
 *       (unknown or invalid option && interpret_result() != true)
 */
void ECA_CHAINSETUP_PARSER::interpret_option (const string& arg)
{
  interpret_entry();

  istatus_rep = false;
  interpret_global_option(arg);
  if (istatus_rep != true) interpret_object_option(arg);

  interpret_exit(arg);
}

/**
 * Interprets one option. All non-global options are ignored. Global
 * options can be interpreted multiple times and in any order.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @post (option succesfully interpreted && interpretation_result() ==  true) ||
 *       (unknown or invalid option && interpretation_result() == false)
 */
void ECA_CHAINSETUP_PARSER::interpret_global_option (const string& arg)
{
  interpret_entry();

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Interpreting global option \"" + arg + "\".");
  if (istatus_rep == false) interpret_general_option(arg);
  if (istatus_rep == false) interpret_processing_control(arg);
  if (istatus_rep == false) interpret_chains(arg);

  interpret_exit(arg);
}

/**
 * Interprets one option. All options not directly related to 
 * ecasound objects are ignored.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * 
 * @post (option succesfully interpreted && interpretation_result() ==  true) ||
 *       (unknown or invalid option && interpretation_result() == false)
 */
void ECA_CHAINSETUP_PARSER::interpret_object_option (const string& arg)
{
  interpret_entry();

  ECA_LOG_MSG(ECA_LOGGER::system_objects, "Interpreting object option \"" + arg + "\".");
  interpret_chains(arg);
  if (istatus_rep == false) interpret_audio_format(arg);
  if (istatus_rep == false) interpret_audioio_device(arg);
  if (istatus_rep == false) interpret_audioio_manager(arg);
  if (istatus_rep == false) interpret_midi_device(arg);
  if (istatus_rep == false) interpret_chain_operator(arg);
  if (istatus_rep == false) interpret_controller(arg);

  interpret_exit(arg);
}

/**
 * Interpret a vector of options.
 */
void ECA_CHAINSETUP_PARSER::interpret_options(const std::vector<string>& opts)
{
  int optcount = static_cast<int>(opts.size());
  int global_matches = 0;
  int other_matches = 0;

  interpret_set_result(true, ""); /* if opts.size() == 0 */

  /*
   * phase1: parse global options only */

  std::vector<string>::const_iterator p = opts.begin();
  while(p != opts.end()) {
    interpret_global_option(*p);

    /* note! below we make sure we don't calculate chain 
     *       definitions twice */
    if (interpret_match_found() == true &&
	!((*p)[0] == '-' && (*p)[1] == 'a')) global_matches++;

    ++p;
  }

  if (csetup_repp->chains.size() == 0) csetup_repp->add_default_chain();

  /* 
   * phase2: parse all options, including processing
   *         the global options again */

  p = opts.begin();
  while(p != opts.end()) {
    interpret_object_option(*p);
    if (interpret_match_found() == true) {
      other_matches++;
      if (interpret_result() != true) {
	/* invalid option format */
	break;
      }
    }
    else {
      /* hack to avoid printing the same info multiple times */
      int dlevel = ECA_LOGGER::instance().get_log_level_bitmask();
      ECA_LOGGER::instance().disable();
      interpret_global_option(*p);
      ECA_LOGGER::instance().set_log_level_bitmask(dlevel);
      if (interpret_match_found() != true) {
	interpret_set_result(false, string("Invalid argument, unable to parse: \"") + *p + "\"");
	break;
      }
      else {
	if (interpret_result() != true) {
	  /* invalid option format */
	  break;
	}
      }
    }
    ++p;
  }

  if (other_matches + global_matches != optcount) {
    ECA_LOG_MSG(ECA_LOGGER::info, 
		string("WARNING: Only ") + 
		kvu_numtostr(other_matches) +
		"+" +
		kvu_numtostr(global_matches) +
		" of the expected " +
		kvu_numtostr(optcount) +
		" parameters were recognized succesfully.");
  }
}

void ECA_CHAINSETUP_PARSER::reset_interpret_status(void) {
  istatus_rep = false;
}

/**
 * Preprocesses a set of options.
 * 
 * Notes! See also ECA_SESSION::preprocess_options()
 * 
 * @post all options valid for interpret_option()
 */
void ECA_CHAINSETUP_PARSER::preprocess_options(std::vector<string>& opts) const
{
  std::vector<string>::iterator p = opts.begin();
  while(p != opts.end()) {

    /* handle options not starting with an '-' sign */

    if (p->size() > 0 && (*p)[0] != '-') {
      /* hack1: rest as "-i:file" */
      ECA_LOG_MSG(ECA_LOGGER::info, "NOTE: Interpreting option " +
		    *p +
		    " as -i:" +
		    *p +
		    ".");
      *p = "-i:" + *p;
    }
    ++p;
  }
}

/**
 * Resets the interpretation logic.
 *
 * @post interpret_status() != true
 */
void ECA_CHAINSETUP_PARSER::interpret_entry(void)
{
  istatus_rep = false;
  interpret_set_result(true, "");

  DBC_ENSURE(interpret_match_found() != true);
}

/**
 * Exits the interpretation logic.
 *
 * @post interpret_result() == true && interpret_result_verbose() == "" ||
 *       interpret_result() == false && interpret_result_verbose() != ""
 */
void ECA_CHAINSETUP_PARSER::interpret_exit(const string& arg)
{
  if (istatus_rep != true) {
    /* option 'arg' was not found */
    interpret_set_result(false, string("Interpreting option \"") +
			 arg + 
			 "\" failed.");
  }
  else {
    /* option 'arg' was found, but incorrect */
    if (interpret_result() != true) {
      if (interpret_result_verbose() == "") {
	interpret_set_result(false, string("Interpreting option \"") +
			     arg + 
			     "\" failed.");
      }
      /* else -> otherwise error code is already set */
    }
  }

  DBC_ENSURE((interpret_result() == true && interpret_result_verbose() == "") ||
	     (interpret_result() == false && interpret_result_verbose() != ""));
}

/**
 * Handle general options. 
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_general_option (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  DBC_REQUIRE(csetup_repp->is_enabled() != true);
  // --------

  bool match = true;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 'b':
    {
      int bsize = atoi(kvu_get_argument_number(1, argu).c_str());
      if (bsize > 0) {
	csetup_repp->set_buffersize(bsize);
	MESSAGE_ITEM mitemb;
	mitemb << "Setting buffersize to (samples) " << bsize << ".";
	ECA_LOG_MSG(ECA_LOGGER::info, mitemb.to_string()); 
      }
      else {
	ECA_LOG_MSG(ECA_LOGGER::info, "Invalid buffersize given; using default value.");
      }
      break;
    }

  case 'B':
    {
      string temp = kvu_get_argument_number(1, argu);
      if (temp == "auto") {
	csetup_repp->set_buffering_mode(ECA_CHAINSETUP::cs_bmode_auto);
	ECA_LOG_MSG(ECA_LOGGER::info, "Buffering mode is selected automatically.");
      }
      else if (temp == "nonrt") {
	csetup_repp->set_buffering_mode(ECA_CHAINSETUP::cs_bmode_nonrt);
	ECA_LOG_MSG(ECA_LOGGER::info, "Buffering mode 'nonrt' selected.");
      }
      else if (temp == "rt") {
	csetup_repp->set_buffering_mode(ECA_CHAINSETUP::cs_bmode_rt);
	ECA_LOG_MSG(ECA_LOGGER::info, "Buffering mode 'rt' selected.");
      }
      else if (temp == "rtlowlatency") {
	csetup_repp->set_buffering_mode(ECA_CHAINSETUP::cs_bmode_rtlowlatency);
	ECA_LOG_MSG(ECA_LOGGER::info, "Buffering mode 'rtlowlatency' selected.");
      }
      else {
	csetup_repp->set_buffering_mode(ECA_CHAINSETUP::cs_bmode_auto);
	ECA_LOG_MSG(ECA_LOGGER::info, "Unknown buffering mode; 'auto' mode is used instead.");
      }
      break;
    }

  case 'n':
    {
      csetup_repp->set_name(kvu_get_argument_number(1, argu));
      ECA_LOG_MSG(ECA_LOGGER::info, "Setting chainsetup name to \""
		  + csetup_repp->name() + "\".");
      break;
    }

  case 'r':
    {
      int prio = ::atoi(kvu_get_argument_number(1, argu).c_str());
      if (prio < 0) {
	ECA_LOG_MSG(ECA_LOGGER::info, "Raised-priority mode disabled.");
	csetup_repp->toggle_raised_priority(false);
      }
      else {
	if (prio == 0) prio = 50;
	csetup_repp->set_sched_priority(prio);
	ECA_LOG_MSG(ECA_LOGGER::info, "Raised-priority mode enabled. (prio:" + 
		      kvu_numtostr(prio) + ")");
	csetup_repp->toggle_raised_priority(true);
      }
      break;
    }

  case 's':
    {
      if (argu.size() > 2 && argu[2] == 'r') {
	ECA_LOG_MSG(ECA_LOGGER::info, "Option '-sr' is obsolete. Use syntax '-f:sfmt,channels,srate,ileaving' instead.");
      }
      break;
    }

  case 'x':
    {
      ECA_LOG_MSG(ECA_LOGGER::info, "Truncating outputs (overwrite-mode).");
      csetup_repp->set_output_openmode(AUDIO_IO::io_write);
      break;
    }

  case 'X':
    {
      ECA_LOG_MSG(ECA_LOGGER::info, "Updating outputs (rw-mode).");
      csetup_repp->set_output_openmode(AUDIO_IO::io_readwrite);
      break;
    }

  case 'z':
    {
      string first_arg (kvu_get_argument_number(1, argu));
      if (first_arg == "db") {
	long int bufs = atol(kvu_get_argument_number(2, argu).c_str());
	if (bufs == 0) bufs = 100000;
	csetup_repp->set_double_buffer_size(bufs);
	ECA_LOG_MSG(ECA_LOGGER::info, "Using double-buffer of " + 
		    kvu_numtostr(bufs) + " sample frames.");
	csetup_repp->toggle_double_buffering(true);
      }
      else if (first_arg == "nodb") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Double-buffering disabled.");
	csetup_repp->toggle_double_buffering(false);
      }
      else if (first_arg == "intbuf") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Enabling extra buffering on realtime devices.");
	csetup_repp->toggle_max_buffers(true);
      }
      else if (first_arg == "nointbuf") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Disabling extra buffering on realtime devices.");
	csetup_repp->toggle_max_buffers(false);
      }
      else if (first_arg == "multitrack") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Enabling multitrack-mode (override).");
	long int samples = -1;
	if (kvu_get_number_of_arguments(argu) > 1) {
	  /* -z:multitrack,XXX */
	  samples = atol(kvu_get_argument_number(2, argu).c_str());
	}
	csetup_repp->multitrack_mode_offset_rep = samples;
	csetup_repp->multitrack_mode_override_rep = true;
	csetup_repp->multitrack_mode_rep = true;
      }
      else if (first_arg == "nomultitrack") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Disabling multitrack-mode (override).");
	csetup_repp->multitrack_mode_override_rep = true;
	csetup_repp->multitrack_mode_offset_rep = 0;
	csetup_repp->multitrack_mode_rep = false;
      }
      else if (first_arg == "psr") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Enabling precise-sample-rates with OSS audio devices.");
	csetup_repp->toggle_precise_sample_rates(true);
      }
      else if (first_arg == "nopsr") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Disabling precise-sample-rates with OSS audio devices.");
	csetup_repp->toggle_precise_sample_rates(false);
      }
      else if (first_arg == "xruns") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Processing is stopped if an xrun occurs.");
	csetup_repp->toggle_ignore_xruns(false);
      }
      else if (first_arg == "noxruns") {
	ECA_LOG_MSG(ECA_LOGGER::info, "Ignoring xruns during processing.");
	csetup_repp->toggle_ignore_xruns(true);
      }
      else if (first_arg == "mixmode") {
	if (kvu_get_argument_number(2, argu) == "sum") {
	  ECA_LOG_MSG(ECA_LOGGER::info, "Enabling 'sum' mixmode.");
	  csetup_repp->set_mix_mode(ECA_CHAINSETUP::cs_mmode_sum);
	}
	else {
	  ECA_LOG_MSG(ECA_LOGGER::info, "Enabling 'avg' mixmode.");
	  csetup_repp->set_mix_mode(ECA_CHAINSETUP::cs_mmode_avg);
	}
      }
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}

/**
 * Handle processing control
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_processing_control (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  bool match = true;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 't': 
    { 
      if (argu.size() < 3) return;
      switch(argu[2]) {
      case ':': 
	{
	  /* note! here we set the _maximum_ length of the chainsetup */
	  csetup_repp->set_max_length_in_seconds(atof(kvu_get_argument_number(1, argu).c_str()));
	  ECA_LOG_MSG(ECA_LOGGER::info, "Set processing time to "
		      + kvu_numtostr(csetup_repp->max_length_in_seconds_exact()) + ".");
	  break;
	}
	
      case 'l': 
	{
	  csetup_repp->toggle_looping(true);
	  if (csetup_repp->max_length_set() != true)
	    ECA_LOG_MSG(ECA_LOGGER::info, "Looping enabled. Length of input objects will be used to set the loop point.");
	  else
	    ECA_LOG_MSG(ECA_LOGGER::info, "Looping enabled.");
	  break;
	}
      }
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}

/**
 * Handle chain options.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_chains (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  bool match = true;
  if (argu.size() < 2) return;  
  switch(argu[1]) {
  case 'a':
    {
      DBC_CHECK(csetup_repp->is_enabled() != true);

      std::vector<string> schains = kvu_get_arguments(argu);
      if (std::find(schains.begin(), schains.end(), "all") != schains.end()) {
	csetup_repp->select_all_chains();
	ECA_LOG_MSG(ECA_LOGGER::system_objects, "Selected all chains.");
      }
      else {
	csetup_repp->select_chains(schains);
	csetup_repp->add_new_chains(schains);
	MESSAGE_ITEM mtempa;
	mtempa << "Selected chain ids: ";
	for (std::vector<string>::const_iterator p = schains.begin(); p !=
	       schains.end(); p++) { mtempa << *p << " "; }
	ECA_LOG_MSG(ECA_LOGGER::system_objects, mtempa.to_string());
      }
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}


/**
 * Handle chainsetup options.
 *
 * @pre argu.size() > 0
 * @pre  argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_audio_format (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  bool match = true;
  if (argu.size() < 2) return; 
  switch(argu[1]) {
  case 'f':
    {
      ECA_AUDIO_FORMAT active_sinfo;
      int channels = atoi(kvu_get_argument_number(2, argu).c_str());
      long int srate = atol(kvu_get_argument_number(3, argu).c_str());
      string sample_fmt = 
	kvu_get_argument_number(1, argu);

      /* initialize to current defaults */
      active_sinfo.set_audio_format(csetup_repp->default_audio_format());
      
      try {
	if (sample_fmt.size() > 0) 
	  active_sinfo.set_sample_format_string(sample_fmt);
      }
      catch(ECA_ERROR& e) {
	interpret_set_result(false, 
			     string("Unable to parse sample format \"") +
			     sample_fmt + "\" passed to -f.");
	istatus_rep = true;
	return;
      }

      if (channels > 0)
	active_sinfo.set_channels(channels);
      if (srate > 0)
	active_sinfo.set_samples_per_second(srate);
      if (kvu_get_argument_number(4, argu) == "n")
	active_sinfo.toggle_interleaved_channels(false);
      else
	active_sinfo.toggle_interleaved_channels(true);

      /* modify the defaults */
      csetup_repp->set_default_audio_format(active_sinfo);
      
      MESSAGE_ITEM ftemp;
      ftemp << "Changed active format to (bits/channels/srate/interleave): ";
      ftemp << csetup_repp->default_audio_format().format_string() 
	    << "/" << csetup_repp->default_audio_format().channels() 
	    << "/" << csetup_repp->default_audio_format().samples_per_second();
      if (csetup_repp->default_audio_format().interleaved_channels() == true) {
	ftemp << "/i";
      }
      else { 
	ftemp << "/n";
      }
      ECA_LOG_MSG(ECA_LOGGER::user_objects, ftemp.to_string());
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}

/**
 * Handle effect preset options.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_effect_preset (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  bool match = true;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 'p':
    {
      ECA_LOG_MSG(ECA_LOGGER::system_objects, "Interpreting preset \"" + argu + "\".");
      CHAIN_OPERATOR* cop = 0;

      if (csetup_repp->selected_chainids.size() != 1) {
	ECA_LOG_MSG(ECA_LOGGER::info, 
		    "ERROR: Exactly one chain should be selected when adding chain operators.");
	match = false;
      }

      if (argu.size() < 3) return;  
      switch(argu[2]) {
      case 'f':
	{
#ifndef ECA_DISABLE_EFFECTS
          cop = dynamic_cast<CHAIN_OPERATOR*>(new FILE_PRESET(kvu_get_argument_number(1,argu)));
#endif
	  break;
	}

      case 'n': 
	{
#ifndef ECA_DISABLE_EFFECTS
	  string name = kvu_get_argument_number(1,argu);
	  const PRESET* preset = dynamic_cast<const PRESET*>(ECA_OBJECT_FACTORY::preset_map().object(name));
	  if (preset != 0)
	    cop = dynamic_cast<CHAIN_OPERATOR*>(preset->new_expr());
	  else
	    cop = 0;
#endif
	  break;
	}
	
      default: { }
      }
      if (cop != 0) {
          for(int n = 0; n < cop->number_of_params(); n++) {
              cop->set_parameter(n + 1, atof(kvu_get_argument_number(n + 2, argu).c_str()));
          }
	  csetup_repp->add_chain_operator(cop);
      }
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}

/**
 * Handle audio-IO-devices and files.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 */
void ECA_CHAINSETUP_PARSER::interpret_audioio_device (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------
 
  string tname = kvu_get_argument_number(1, argu);

  bool match = true;
  bool print_error = false;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 'i':
    {
      DBC_CHECK(csetup_repp->is_enabled() != true);

      AUDIO_IO* audio_input = ECA_OBJECT_FACTORY::create_audio_object(argu);
      if (audio_input == 0) 
	audio_input = ECA_OBJECT_FACTORY::create_loop_input(argu, &csetup_repp->loop_map);
      if (audio_input != 0) {
	if ((audio_input->supported_io_modes() &
	     AUDIO_IO::io_read) != AUDIO_IO::io_read) {
	  interpret_set_result(false, 
			       string("Audio object \"") + 
			       tname +
			       "\" cannot be opened for input.");
	}
	else {
	  ECA_LOG_MSG(ECA_LOGGER::system_objects,"adding file \"" + tname + "\".");
	  csetup_repp->add_input(audio_input);
	  last_audio_add_vector_repp = &csetup_repp->inputs; /* for -y parsing */
	}
      }
      else {
	print_error = true;
      }
      break;
    }

  case 'o':
    {
      DBC_CHECK(csetup_repp->is_enabled() != true);

      AUDIO_IO* audio_output = ECA_OBJECT_FACTORY::create_audio_object(argu);
	
      if (audio_output == 0) audio_output = ECA_OBJECT_FACTORY::create_loop_output(argu, &csetup_repp->loop_map);
      if (audio_output != 0) {
	bool truncate = false;
	int mode_tmp = csetup_repp->output_openmode();
	if (mode_tmp == AUDIO_IO::io_readwrite) {
	  if ((audio_output->supported_io_modes() &
	      AUDIO_IO::io_readwrite) != AUDIO_IO::io_readwrite) {
	    mode_tmp = AUDIO_IO::io_write;
	    truncate = true;
	  }
	}
	else {
	  truncate = true;
	}
	if (((audio_output->supported_io_modes() & mode_tmp) != mode_tmp)) {
	  interpret_set_result(false, string("io_write/io_readwrite access modes not supported by output \"") + audio_output->name() + "\".");
	}
	else {
	  ECA_LOG_MSG(ECA_LOGGER::system_objects,"adding file \"" + tname + "\".");
	  csetup_repp->add_output(audio_output, truncate);
	  last_audio_add_vector_repp = &csetup_repp->outputs; /* for -y parsing */
	}
      }
      else {
	print_error = true;
      }
      break;
    }

  case 'y':
    {
      DBC_CHECK(csetup_repp->is_enabled() != true);

      if (last_audio_add_vector_repp == 0) {
	ECA_LOG_MSG(ECA_LOGGER::info, 
		    "ERROR: Non-existant last audio object.");
      }
      else {
	AUDIO_IO* last_object = (*last_audio_add_vector_repp).back();
	double newpos = atof(kvu_get_argument_number(1, argu).c_str());

	if (newpos > 0.0f &&
	    last_object &&
	    last_object->supports_seeking() != true) {
	  interpret_set_result(false, string("Audio object does not support seeking, unable to set a non-zero starting offset. Object generating the error is \"") + last_object->name() + "\".");
	}
	else {

	  last_object->seek_position_in_seconds(newpos);

	  if (last_object->io_mode() == AUDIO_IO::io_read) {
	    csetup_repp->input_start_pos[csetup_repp->input_start_pos.size() - 1] = last_object->position_in_seconds_exact();
	  }
	  else {
	    csetup_repp->output_start_pos[csetup_repp->output_start_pos.size() - 1] = last_object->position_in_seconds_exact();
	  }

	  ECA_LOG_MSG(ECA_LOGGER::info, "Setting starting position for audio object \""
		      + last_object->label() 
		      + "\": "
		      + kvu_numtostr(last_object->position_in_seconds_exact()) 
		      + " seconds.");
	}
	break;
      }
    }

  default: { match = false; }
  }

  if (match == true) istatus_rep = true;

  if (print_error == true) {
    interpret_set_result(false, 
			 string("Audio object \"") +
			 tname + 
			 "\" does not match any of the known audio device types or "
			 "file formats. You can check the list of supported "
			 "audio object types by issuing the command 'aio-register' in "
			 "ecasound's interactive mode.");
    
  }
}

/**
 * Handles audio-IO manager options.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 */
void ECA_CHAINSETUP_PARSER::interpret_audioio_manager(const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------
 
  string tname = kvu_get_argument_number(1, argu);

  bool match = true;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 'G':
    {
      DBC_CHECK(csetup_repp->is_enabled() != true);

      std::vector<string> args = kvu_get_arguments(argu);
      args.erase(args.begin());
      DBC_CHECK(args.size() == kvu_get_arguments(argu).size() - 1);
      csetup_repp->set_audio_io_manager_option(tname,
					       kvu_vector_to_string(args, ","));
      break;
    }
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;
}

/**
 * Handles MIDI-IO devices.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 */
void ECA_CHAINSETUP_PARSER::interpret_midi_device (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------
 
  bool match = true;
  if (argu.size() < 2) return;
  switch(argu[1]) {
  case 'M':
    {
      if (argu.size() < 3) return;
      switch(argu[2]) {
	case 'd': 
	  {
	    string tname = kvu_get_argument_number(1, argu);
	    ECA_LOG_MSG(ECA_LOGGER::system_objects,"MIDI-config: Adding device \"" + tname + "\".");
	    MIDI_IO* mdev = 0;
	    mdev = ECA_OBJECT_FACTORY::create_midi_device(argu);
	    if (mdev != 0) {
	      if ((mdev->supported_io_modes() & MIDI_IO::io_readwrite) == MIDI_IO::io_readwrite) {
		mdev->io_mode(MIDI_IO::io_readwrite);
		csetup_repp->add_midi_device(mdev);
		csetup_repp->midi_server_needed_rep = true;
	      }
	      else {
		ECA_LOG_MSG(ECA_LOGGER::info, "WARNING: I/O-mode 'io_readwrite' not supported by MIDI-device " + mdev->name());
	      }
	    }
	    break;
	  }

      case 'm': 
	{
	  if (argu.size() < 4) return;
	  switch(argu[3]) {
	  case 'r': 
	    {
	      // FIXME: not implemented!
	      int id = atoi(kvu_get_argument_number(1, argu).c_str());
	      ECA_LOG_MSG(ECA_LOGGER::info, 
			    "MIDI-config: Receiving MMC messages with id  \"" + 
			    kvu_numtostr(id) +
			    "\".");
	      csetup_repp->midi_server_repp->set_mmc_receive_id(id);
	      csetup_repp->midi_server_needed_rep = true;
	      break;
	    }
	  

	  case 's': 
	    {
	      int id = atoi(kvu_get_argument_number(1, argu).c_str());
	      ECA_LOG_MSG(ECA_LOGGER::info, 
			    "MIDI-config: Adding MMC-send to device id \"" + 
			    kvu_numtostr(id) +
			    "\".");
	      csetup_repp->midi_server_repp->add_mmc_send_id(id);
	      csetup_repp->midi_server_needed_rep = true;
	      break;
	    }
	  }
	  break;
	}

      case 's': 
	{
	  if (argu.size() < 4) return;
	  switch(argu[3]) {
	  case 'r': 
	    {
	      // FIXME: not implemented
	      ECA_LOG_MSG(ECA_LOGGER::info, 
			    "MIDI-config: Receiving MIDI-sync.");
	      csetup_repp->midi_server_needed_rep = true;
	      csetup_repp->midi_server_repp->toggle_midi_sync_receive(true);
	      break;
	    }
	  
	  case 's': 
	    {
	      // FIXME: not implemented
	      ECA_LOG_MSG(ECA_LOGGER::info, 
			    "MIDI-config: Sending MIDI-sync.");
	      csetup_repp->midi_server_repp->toggle_midi_sync_send(true);
	      csetup_repp->midi_server_needed_rep = true;
	      break;
	    }
	  }
	  break;
	}

      }
      break;
    }
    
  default: { match = false; }
  }
  if (match == true) istatus_rep = true;

  return;
}

/**
 * Handle chain operator options (chain operators, presets 
 * and plugins)
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_chain_operator (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  CHAIN_OPERATOR* t = ECA_OBJECT_FACTORY::create_chain_operator(argu);
  if (t == 0) t = ECA_OBJECT_FACTORY::create_ladspa_plugin(argu);
  if (t != 0) {
    if (csetup_repp->selected_chainids.size() == 1) {
      csetup_repp->add_chain_operator(t);
      istatus_rep = true;
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::info, 
		  "ERROR: Exactly one chain should be selected when adding chain operators.");
      delete t;
    }
  }
  else 
    interpret_effect_preset(argu);
}

/**
 * Handle controller sources and general controllers.
 *
 * @pre argu.size() > 0
 * @pre argu[0] == '-'
 * @pre istatus_rep == false
 */
void ECA_CHAINSETUP_PARSER::interpret_controller (const string& argu)
{
  // --------
  DBC_REQUIRE(argu.size() > 0);
  DBC_REQUIRE(argu[0] == '-');
  DBC_REQUIRE(istatus_rep == false);
  // --------

  string prefix = kvu_get_argument_prefix(argu);
  if (prefix == "kx") {
    csetup_repp->set_target_to_controller();
    ECA_LOG_MSG(ECA_LOGGER::system_objects, "Selected controllers as parameter control targets.");
    istatus_rep = true;
  }
  else {
    GENERIC_CONTROLLER* t = ECA_OBJECT_FACTORY::create_controller(argu);
    if (t != 0) {
      if (csetup_repp->selected_chainids.size() != 1) {
	ECA_LOG_MSG(ECA_LOGGER::info, 
		    "ERROR: Exactly one chain should be selected when adding controllers.");
	delete t;
      }
      else {
	MIDI_CLIENT* p = dynamic_cast<MIDI_CLIENT*>(t->source_pointer());
	if (p != 0) {
	  csetup_repp->midi_server_needed_rep = true;
	  p->register_server(csetup_repp->midi_server_repp);
	}
	csetup_repp->add_controller(t);
	istatus_rep = true;
      }
    }
  }
}

string ECA_CHAINSETUP_PARSER::general_options_to_string(void) const
{
  MESSAGE_ITEM t;

  int setparams = csetup_repp->override_buffering_parameters().number_of_set();
  ECA_LOG_MSG(ECA_LOGGER::system_objects, 
		"genopts tostring - " + kvu_numtostr(setparams) +
		" overridden parameters.");

  if (setparams > 0) {
    t << "-b:" << csetup_repp->buffersize();
    
    if (csetup_repp->raised_priority() == true)
      t << " -r:" << csetup_repp->get_sched_priority();
    else
      t << " -r:-1";

    if (csetup_repp->max_buffers() == true) 
      t << " -z:intbuf";
    else
      t << " -z:nointbuf";
    
    if (csetup_repp->double_buffering() == true) 
      t << " -z:db," << csetup_repp->double_buffer_size();
    else
      t << " -z:nodb";
  }
  else {
    ECA_CHAINSETUP::Buffering_mode_t bmode =
      csetup_repp->buffering_mode_rep;
    if (csetup_repp->active_buffering_mode_rep != ECA_CHAINSETUP::cs_bmode_none)
      bmode = csetup_repp->active_buffering_mode_rep;
    switch(bmode)
      {
      case ECA_CHAINSETUP::cs_bmode_nonrt: 
	{
	  t << "-B:nonrt";
	  break; 
	}
      case ECA_CHAINSETUP::cs_bmode_rt: 
	{ 
	  t << "-B:rt";
	  break; 
	}
      case ECA_CHAINSETUP::cs_bmode_rtlowlatency: 
	{ 
	  t << "-B:rtlowlatency";
	  break;
	}
      default: 
	{ 
	  t << " -B:auto";
	}
      }
  }

  t << " -n:\"" << csetup_repp->name() << "\"";

  if (csetup_repp->output_openmode() == AUDIO_IO::io_write) 
    t << " -x";
  else
    t << " -X";

  if (csetup_repp->multitrack_mode_override_rep == true) {
    if (csetup_repp->multitrack_mode() == true) {
      t << "-z:multitrack";
      if (csetup_repp->multitrack_mode_offset_rep != -1) {
	t << "," << csetup_repp->multitrack_mode_offset_rep;
      }
    }
    else {
      t << "-z:nomultitrack";
    }
  }

  if (csetup_repp->ignore_xruns() == true) 
    t << " -z:noxruns";
  else
    t << " -z:xruns";

  if (csetup_repp->precise_sample_rates() == true) 
    t << " -z:psr";
  else
    t << " -z:nopsr";

  if (csetup_repp->mix_mode() == ECA_CHAINSETUP::cs_mmode_avg)
    t << " -z:mixmode,avg";
  else
    t << " -z:mixmode,sum";

  t.setprecision(3);
  if (csetup_repp->max_length_set()) {
    t << " -t:" << csetup_repp->max_length_in_seconds_exact();
  }
  if (csetup_repp->looping_enabled()) t << " -tl";

  return t.to_string();
}

string ECA_CHAINSETUP_PARSER::midi_to_string(void) const
{
  MESSAGE_ITEM t;
  t.setprecision(3);

  std::vector<MIDI_IO*>::size_type p = 0;
  while (p < csetup_repp->midi_devices.size()) {
    t << "-Md:";
    for(int n = 0; n < csetup_repp->midi_devices[p]->number_of_params(); n++) {
      // FIXME: should quote/escape possible commas and whitespace
      t << csetup_repp->midi_devices[p]->get_parameter(n + 1);
      if (n + 1 < csetup_repp->midi_devices[p]->number_of_params()) t << ",";
    }
    ++p;
    if (p < csetup_repp->midi_devices.size()) t << " ";
  }

  return t.to_string();
}

string ECA_CHAINSETUP_PARSER::inputs_to_string(void) const
{
  MESSAGE_ITEM t; 
  t.setprecision(3);
  size_t p = 0;
  while (p < csetup_repp->inputs.size()) {
    t << "-a:";
    std::vector<string> c = csetup_repp->get_attached_chains_to_input(csetup_repp->inputs[p]);
    std::vector<string>::const_iterator cp = c.begin();
    while (cp != c.end()) {
      t << *cp;
      ++cp;
      if (cp != c.end()) t << ",";
    }
    t << " " 
      << ECA_OBJECT_FACTORY::audio_object_format_to_eos(csetup_repp->inputs[p]) 
      << " "
      << ECA_OBJECT_FACTORY::audio_object_to_eos(csetup_repp->inputs[p], "i");

    if (csetup_repp->input_start_pos[p] != 0) {
      t << " -y:" << csetup_repp->input_start_pos[p];
    }

    ++p;

    if (p < csetup_repp->inputs.size()) t << "\n";
  }

  return t.to_string();
}

string ECA_CHAINSETUP_PARSER::outputs_to_string(void) const
{
  MESSAGE_ITEM t; 
  t.setprecision(3);
  std::vector<AUDIO_IO*>::size_type p = 0;
  while (p < csetup_repp->outputs.size()) {
    t << "-a:";
    std::vector<string> c = csetup_repp->get_attached_chains_to_output(csetup_repp->outputs[p]);
    std::vector<string>::const_iterator cp = c.begin();
    while (cp != c.end()) {
      t << *cp;
      ++cp;
      if (cp != c.end()) t << ",";
    }
    t << " " 
      << ECA_OBJECT_FACTORY::audio_object_format_to_eos(csetup_repp->outputs[p]) 
      << " "
      << ECA_OBJECT_FACTORY::audio_object_to_eos(csetup_repp->outputs[p], "o");

    if (csetup_repp->output_start_pos[p] != 0) {
      t << " -y:" << csetup_repp->output_start_pos[p];
    }

    ++p;

    if (p < csetup_repp->outputs.size()) t << "\n";
  }

  return t.to_string();
}

string ECA_CHAINSETUP_PARSER::chains_to_string(void) const
{
  MESSAGE_ITEM t;

  std::vector<CHAIN*>::size_type p = 0;
  while (p < csetup_repp->chains.size()) {
    string tmpstr = csetup_repp->chains[p]->to_string();
    if (tmpstr.size() > 0) {
      t << "-a:" << csetup_repp->chains[p]->name() << " ";
      t << tmpstr;
      if (p + 1 < csetup_repp->chains.size()) t << "\n";
    }
    ++p;
  }

  return t.to_string();
}

