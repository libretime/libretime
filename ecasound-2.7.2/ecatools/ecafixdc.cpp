// ------------------------------------------------------------------------
// ecatools-fixdc.cpp: A simple command-line tools for fixing DC-offset.
// Copyright (C) 1999-2003,2005-2006 Kai Vehmanen
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

#include <iostream>
#include <string>
#include <vector>
#include <cstdio>
#include <cmath> /* fabs() */

#include <signal.h>
#include <stdlib.h>

#include <kvutils/kvu_dbc.h>
#include <kvutils/kvu_com_line.h>
#include <kvutils/kvu_temporary_file_directory.h>
#include <kvutils/kvu_numtostr.h>
#include <kvutils/kvu_utils.h>

#include <eca-control-interface.h>

#include "ecicpp_helpers.h"

using std::cerr;
using std::cout;
using std::endl;
using std::string;

/**
 * Function declarations
 */

int main(int argc, char *argv[]);

static void ecafixdc_print_usage(void);
static void ecafixdc_signal_handler(int signum);

/**
 * Definitions and options 
 */

#define ECAFIXDC_PHASE_ANALYSIS   0
#define ECAFIXDC_PHASE_PROCESSING 1
#define ECAFIXDC_PHASE_MAX        2

static const string ecatools_fixdc_version = "20050316-30";
static string ecatools_fixdc_tempfile;

/**
 * Function definitions
 */

int main(int argc, char *argv[])
{
  struct sigaction es_handler;
  es_handler.sa_handler = ecafixdc_signal_handler;
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
    ecafixdc_print_usage();
    return(1);
  }

  std::string filename;
  std::string tempfile;
  std::vector<double> dcfix_values;
  int chcount = 0;

  ECA_CONTROL_INTERFACE eci;

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

  cline.begin();
  cline.next(); // skip the program name
  while(cline.end() == false) {
    filename = cline.current();

    ecatools_fixdc_tempfile = tempfile_dir_rep.create_filename("fixdc-tmp", ".wav");

    for(int m = 0;m < ECAFIXDC_PHASE_MAX; m++) {

      eci.command("cs-add default");
      eci.command("c-add default");

      if (m == ECAFIXDC_PHASE_ANALYSIS) {
	cout << "Calculating DC-offset for file \"" << filename << "\".\n";
	
	string format;
	if (ecicpp_add_file_input(&eci, filename, &format) < 0) break;

	cout << "Using audio format -f:" << format << "\n";

	chcount = ecicpp_format_channels(format);
	dcfix_values.resize(chcount);
	cout << "Setting up " << chcount << " separate channels for analysis." << endl;

	cout << "Opening temp file \"" << ecatools_fixdc_tempfile << "\".\n";
	if (ecicpp_add_output(&eci, ecatools_fixdc_tempfile, format) < 0) break;

	eci.command("cop-add -ezf");
	eci.command("cop-list");
	if (eci.last_string_list().size() != 1) {
	  cerr << eci.last_error() << endl;
	  cerr << "---\nError while adding DC-Find (-ezf) chainop. Exiting...\n";
	  break;
	}
      }
      else {
	// FIXME: list all channels (remember to fix audiofx_misc.cpp dcfix)
	cout << "Fixing DC-offset \"" << filename << ".\n";

	string format;
	if (ecicpp_add_file_input(&eci, ecatools_fixdc_tempfile, &format) < 0) break;
 
	cout << "Using audio format -f:" << format << "\n";

	if (ecicpp_add_output(&eci, filename, format) < 0) break;

	string dcfixstr;
	for(int n = 0; n < chcount; n++) {
	  dcfixstr += kvu_numtostr(dcfix_values[n]) + ",";
	}

	eci.command("cop-add -ezx:" + kvu_numtostr(chcount) + "," + dcfixstr);
	eci.command("cop-list");
	if (eci.last_string_list().size() != 1) {
	  cerr << eci.last_error() << endl;
	  cerr << "---\nError while adding DC-Fix (-ezx) chainop. Exiting...\n";
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

      if (m == ECAFIXDC_PHASE_ANALYSIS) {
	DBC_CHECK(static_cast<int>(dcfix_values.size()) >= chcount);
	double maxoffset = 0.0f;
	for(int nm = 0; nm < chcount; nm++) {
	  eci.command("cop-select 1");
	  eci.command("copp-select " + kvu_numtostr(nm + 1));
	  eci.command("copp-get");
	  dcfix_values[nm] = eci.last_float();
	  if (fabs(dcfix_values[nm]) > maxoffset) maxoffset = fabs(dcfix_values[nm]); 
	  cout << "DC-offset for channel " << nm + 1 << " is " <<
	    kvu_numtostr(dcfix_values[nm], 4) << "." << endl;
	}

	if (maxoffset <= 0.0f) {
	  cout << "File \"" << filename << "\" has no DC-offset. Skipping.";

	  eci.command("cs-disconnect");
	  eci.command("cs-select default");
	  eci.command("cs-remove");
	  break;
	}
      }

      eci.command("cs-disconnect");
      eci.command("cs-select default");
      eci.command("cs-remove");
    }

    remove(ecatools_fixdc_tempfile.c_str());

    cline.next();
  }

  return(0);
}

static void ecafixdc_print_usage(void)
{
  std::cerr << "****************************************************************************\n";
  std::cerr << "* ecafixdc, v" << ecatools_fixdc_version << " (" << VERSION << ")\n";
  std::cerr << "* (C) 1997-2004 Kai Vehmanen, released under the GPL license\n";
  std::cerr << "****************************************************************************\n";

  std::cerr << "\nUSAGE: ecafixdc file1 [ file2, ... fileN ]\n\n";
}

static void ecafixdc_signal_handler(int signum)
{
  std::cerr << "Unexpected interrupt... cleaning up.\n";
  remove(ecatools_fixdc_tempfile.c_str());
  exit(1);
}
