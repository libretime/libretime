// ------------------------------------------------------------------------
// ecanormalize.cpp: A simple command-line tools for normalizing
//                   sample volume.
// Copyright (C) 1999-2006 Kai Vehmanen
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

#include <string>
#include <iostream>
#include <cstdio>
#include <signal.h>
#include <stdlib.h>

#include <kvutils/kvu_com_line.h>
#include <kvutils/kvu_temporary_file_directory.h>
#include <kvutils/kvu_numtostr.h>

#include <eca-control-interface.h>

#include "ecicpp_helpers.h"

/**
 * Definitions and options 
 */

using std::cerr;
using std::cout;
using std::endl;
using std::string;

/**
 * Function declarations
 */

int main(int argc, char *argv[]);

static void ecanormalize_print_usage(void);
static void ecanormalize_signal_handler(int signum);

/**
 * Definitions and options 
 */

#define ECANORMALIZE_PHASE_ANALYSIS   0
#define ECANORMALIZE_PHASE_PROCESSING 1
#define ECANORMALIZE_PHASE_MAX        2

/** 
 * Global variables
 */

static const string ecatools_normalize_version = "20050316-27";
static string ecatools_normalize_tempfile;

/**
 * Function definitions
 */

int main(int argc, char *argv[])
{
  struct sigaction es_handler;
  es_handler.sa_handler = ecanormalize_signal_handler;
  sigemptyset(&es_handler.sa_mask);
  es_handler.sa_flags = 0;

  sigaction(SIGTERM, &es_handler, 0);
  sigaction(SIGINT, &es_handler, 0);
  sigaction(SIGQUIT, &es_handler, 0);
  sigaction(SIGABRT, &es_handler, 0);

  struct sigaction ign_handler;
  ign_handler.sa_handler = SIG_IGN;
  sigemptyset(&ign_handler.sa_mask);
  ign_handler.sa_flags = 0;

  /* ignore the following signals */
  sigaction(SIGPIPE, &ign_handler, 0);
  sigaction(SIGFPE, &ign_handler, 0);

  COMMAND_LINE cline = COMMAND_LINE (argc, argv);

  if (cline.size() < 2) {
    ecanormalize_print_usage();
    return(1);
  }

  try {
    string filename;
    double multiplier = 1.0f;
    
    TEMPORARY_FILE_DIRECTORY tempfile_dir_rep;
    string tmpdir ("ecatools-");
    char* tmp_p = getenv("LOGNAME");
    if (tmp_p == NULL) tmp_p = getenv("USER");
    if (tmp_p != NULL) {
      tmpdir += string(tmp_p);
      tempfile_dir_rep.reserve_directory(tmpdir);
    }
    if (tempfile_dir_rep.is_valid() != true) {
      cerr << "---\nError while creating temporary directory \"" << tmpdir << "\". Exiting...\n";
      return(0);
    }

    ecatools_normalize_tempfile = tempfile_dir_rep.create_filename("normalize-tmp", ".wav");

    ECA_CONTROL_INTERFACE eci;

    cline.begin();
    cline.next(); // skip the program name
    while(cline.end() == false) {
      filename = cline.current();

      for(int m = 0; m < ECANORMALIZE_PHASE_MAX; m++) {

	eci.command("cs-add default");
	eci.command("c-add default");
	if (m == ECANORMALIZE_PHASE_ANALYSIS) {
	  cout << "Analyzing file \"" << filename << "\".\n";

	  string format;
	  if (ecicpp_add_file_input(&eci, filename, &format) < 0) break;
	  cout << "Using audio format -f:" << format << "\n";

	  cout << "Opening temp file \"" << ecatools_normalize_tempfile << "\".\n";
	  if (ecicpp_add_output(&eci, ecatools_normalize_tempfile, format) < 0) break;

	  eci.command("cop-add -ev");
	  eci.command("cop-list");
	  if (eci.last_string_list().size() != 1) {
	    cerr << eci.last_error() << endl;
	    cerr << "---\nError while adding -ev chainop. Exiting...\n";
	    break;
	  }
	}
	else {
	  string format;
	  if (ecicpp_add_file_input(&eci, ecatools_normalize_tempfile, &format) < 0) break;
	  cout << "Using audio format -f:" << format << "\n";

	  if (ecicpp_add_output(&eci, filename, format) < 0) break;

	  eci.command("cop-add -ea:" + kvu_numtostr(multiplier * 100.0f));
	  eci.command("cop-list");
	  if (eci.last_string_list().size() != 1) {
	    cerr << eci.last_error() << endl;
	    cerr << "---\nError while adding -ev chainop. Exiting...\n";
	    break;
	  }
	}

	cout << "Starting processing...\n";	

	if (ecicpp_connect_chainsetup(&eci, "default") < 0) {
	  break;
	}
	else {
	  // blocks until processing is done
	  eci.command("run");
	}

	cout << "Processing finished.\n";

	if (m == ECANORMALIZE_PHASE_ANALYSIS) {
	  eci.command("cop-select 1");
	  eci.command("copp-select 2"); /* 2nd param of -ev, first one
	                                 * sets the mode */
	  eci.command("copp-get");
	  multiplier = eci.last_float();
 	  if (multiplier <= 1.0) {
	    cout << "File \"" << filename << "\" is already normalized.\n";

	    eci.command("cs-disconnect");
	    eci.command("cs-select default");
	    eci.command("cs-remove");
	    break;
	  }
	  else {
	    cout << "Normalizing file \"" << filename << "\" (amp-%: ";
	    cout << multiplier * 100.0 << ").\n";
	  }
	}

	eci.command("cs-disconnect");
	eci.command("cs-select default");
	eci.command("cs-remove");
      }

      cout << "Removing temp file \"" << ecatools_normalize_tempfile << "\".\n";

      remove(ecatools_normalize_tempfile.c_str());

      cline.next();
    }
  }
  catch(...) {
    cerr << "\nCaught an unknown exception.\n";
  }
  return(0);
}

static void ecanormalize_print_usage(void) 
{
  cerr << "****************************************************************************\n";
  cerr << "* ecanormalize, v" << ecatools_normalize_version << " (" << VERSION << ")\n";
  cerr << "* (C) 1997-2004 Kai Vehmanen, released under the GPL license\n";
  cerr << "****************************************************************************\n";

  cerr << "\nUSAGE: ecanormalize file1 [ file2, ... fileN ]\n\n";
}

static void ecanormalize_signal_handler(int signum)
{
  cerr << "Unexpected interrupt... cleaning up.\n";
  remove(ecatools_normalize_tempfile.c_str());
  exit(1);
}
