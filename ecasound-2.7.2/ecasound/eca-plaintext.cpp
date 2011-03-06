// ------------------------------------------------------------------------
// eca-plaintext.cpp: Plaintext implementation of the console user 
//                    interface.
// Copyright (C) 2002-2004 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 2
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

#include <iostream>
#include <string>

#include <cassert>

#include <eca-version.h>

#include "ecasound.h"
#include "eca-plaintext.h"

using namespace std;

ECA_PLAIN_TEXT::ECA_PLAIN_TEXT(std::ostream* ostr)
{
  ostream_repp = ostr;
}

ECA_PLAIN_TEXT::~ECA_PLAIN_TEXT(void)
{
}

void ECA_PLAIN_TEXT::print(const std::string& msg)
{
  *ostream_repp << msg << endl;
}

void ECA_PLAIN_TEXT::print_banner(void)
{
  *ostream_repp << ECASOUND_BANNER_ASTERISK_BAR;
  *ostream_repp << "*";
  *ostream_repp << "        ecasound v" 
       << ecasound_library_version
       << ECASOUND_COPYRIGHT;
  *ostream_repp << "\n";
  *ostream_repp << ECASOUND_BANNER_ASTERISK_BAR;
}

void ECA_PLAIN_TEXT::read_command(const string& prompt)
{
  if (ostream_repp->good() == true) {
    *ostream_repp << prompt;
    ostream_repp->flush();
    if (cin.good() == true) {
      getline(cin, last_cmd_rep);
    }
    else {
      last_cmd_rep = "q";
    }
  }
  else {
    last_cmd_rep = "q";
  }
}

const string& ECA_PLAIN_TEXT::last_command(void) const
{
  return last_cmd_rep;
}
