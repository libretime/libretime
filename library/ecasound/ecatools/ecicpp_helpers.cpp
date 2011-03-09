// ------------------------------------------------------------------------
// ecicpp_helper.cpp: Helper routines for C++ ECI programming.
// Copyright (C) 2002-2007 Kai Vehmanen
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
#include <string>
#include <vector>

#include <cstdio>

#include <kvu_dbc.h>
#include <kvu_utils.h>

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

int ecicpp_add_file_input(ECA_CONTROL_INTERFACE* eci, const string& filename, string* format)
{
  if (filename.find(',') != string::npos) {
    cerr << "Error: Unable to handle filenames with commas. Exiting...\n";
    return -1;
  }

  return ecicpp_add_input(eci, filename, format);
}

int ecicpp_add_input(ECA_CONTROL_INTERFACE* eci, const string& input, string* format)
{
  eci->command("ai-add " + input);
  bool error = eci->error();
  eci->command("ai-list");
  if (error == true || eci->last_string_list().size() != 1) {
    cerr << eci->last_error() << endl;
    cerr << "---\nError while processing input " << input << ". Exiting...\n";
    return -1;
  }
  
  /* we must connect to get correct input format */
  eci->command("ao-add null");
  eci->command("cs-connect");
  
  eci->command("ai-iselect 1");
  eci->command("ai-get-format");
  *format = eci->last_string();

  /* disconnect and remove the null output */
  eci->command("cs-disconnect");
  eci->command("ao-iselect 1");
  eci->command("ao-remove");

  return 0;
}

int ecicpp_add_output(ECA_CONTROL_INTERFACE* eci, const string& output, const string& format)
{
  eci->command("cs-set-audio-format " +  format);

  eci->command("ao-add " + output);
  bool error = eci->error();
  eci->command("ao-list");
  if (error == true || eci->last_string_list().size() != 1) {
    cerr << eci->last_error() << endl;
    cerr << "---\nError while processing output " << output << ". Exiting...\n";
    return -1;
  }

  return 0;
}

int ecicpp_connect_chainsetup(ECA_CONTROL_INTERFACE* eci, const string& csname)
{
  eci->command("cs-connect");
  bool error = eci->error();
  string errorstr = eci->last_error();
  eci->command("cs-connected");
  if (error == true || eci->last_string() != csname) {
    cerr << endl << errorstr << endl;
    cerr << "---\nUnable to start processing. Exiting...\n";
    return -1;
  }

  return 0;
}

int ecicpp_format_channels(const string& format)
{
  std::vector<std::string> tokens = kvu_string_to_vector(format, ',');
  DBC_CHECK(tokens.size() >= 3);
  return atoi(tokens[1].c_str());
}
