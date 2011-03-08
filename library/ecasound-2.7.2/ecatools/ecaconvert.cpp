// ------------------------------------------------------------------------
// ecaconvert.cpp: A simple command-line tool for converting
//                 audio files.
// Copyright (C) 2000,2002,2005-2006 Kai Vehmanen
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
#include <cstdio>

#include <kvutils/kvu_com_line.h>
#include <kvutils/kvu_numtostr.h>

#include <eca-control-interface.h>

#include "ecicpp_helpers.h"

/**
 * Function declarations
 */

int main(int argc, char *argv[]);
void print_usage(void);

using std::cerr;
using std::cout;
using std::endl;
using std::string;

static const string ecatools_play_version = "20050316-18";

int main(int argc, char *argv[])
{
  COMMAND_LINE cline = COMMAND_LINE (argc, argv);

  if (cline.size() < 2) {
    print_usage();
    return(1);
  }

  string filename;

  ECA_CONTROL_INTERFACE eci;

  cline.begin();
  cline.next(); // skip the program name
  
  string extension (".raw");
  if (cline.end() != true) {
    extension = cline.current();
    cline.next();
  }
  
  while(cline.end() != true) {
    filename = cline.current();
    
    cout << "Converting file \"" << filename << "\" --> ";
    cout << "\"" << filename + extension << "\"." << endl;
    
    eci.command("cs-add default");
    eci.command("c-add default");

    string format;
    if (ecicpp_add_file_input(&eci, filename, &format) < 0) break;
    
    cout << "Using audio format -f:" << format << "\n";

    if (ecicpp_add_output(&eci, filename + extension, format) < 0) break;

    if (ecicpp_connect_chainsetup(&eci, "default") < 0) {
      break;
    }
    
    cout << "Starting processing...\n";
    
    // blocks until processing is done
    eci.command("run");
    
    cout << "Processing finished.\n";
    
    eci.command("cs-disconnect");
    eci.command("cs-select default");
    eci.command("cs-remove");
    
    cline.next();
  }

  return(0);
}

void print_usage(void)
{
  cerr << "****************************************************************************\n";
  cerr << "* ecaconvert, v" << ecatools_play_version << " (" << VERSION << ")\n";
  cerr << "* (C) 2000-2004 Kai Vehmanen, released under GPL licence \n";
  cerr << "****************************************************************************\n";

  cerr << "\nUSAGE: ecaconvert .extension file1 [ file2, ... fileN ]\n\n";
}
