// ------------------------------------------------------------------------
// kvu_com_line.cpp: A wrapper class for parsing command line arguments
// Copyright (C) 1999,2002 Kai Vehmanen
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

#include <string>
#include <vector>
#include <algorithm>

#include "kvu_com_line.h"

using namespace std;

COMMAND_LINE::COMMAND_LINE(void)
{
  current_rep = 0;
}

COMMAND_LINE::COMMAND_LINE(int argc, char *argv[])
{
  current_rep = 0;

  for(int t = 0; t < argc; t++) {
    cparams.push_back(argv[t]);
  }
}

COMMAND_LINE::COMMAND_LINE(const vector<string>& params)
{
  cparams = params;
}

void COMMAND_LINE::push_back(const string& argu)
{
  cparams.push_back(argu);
}


bool COMMAND_LINE::has(char option) const
{
  vector<string>::size_type savepos = current_rep;
  
  current_rep = 0;
  while (current_rep < cparams.size()) {
    if (cparams[current_rep].size() > 1) {
      if (cparams[current_rep].at(0) == '-' &&
	  cparams[current_rep].at(1) == option) {
	current_rep = savepos;
	return(true);
      }
    }
    ++current_rep;
  }
  current_rep = savepos;
  return(false);
}

bool COMMAND_LINE::has(const string& option) const
{
  vector<string>::size_type savepos = current_rep;

  current_rep = 0;
  while (current_rep < cparams.size()) {
    if (cparams[current_rep] == option) {
      current_rep = savepos;
      return(true);
    }
    ++current_rep;
  }
  current_rep = savepos;
  return(false);
}

void COMMAND_LINE::combine(void) {
  cparams = combine(cparams);
}

vector<string> COMMAND_LINE::combine(const vector<string>& source)
{
  vector<string> result;
  string first;
  vector<string>::const_iterator p = source.begin();
  while(p != source.end()) {
    if (p->size() == 0) {
      ++p;
      continue;
    }
    if ((*p)[0] == '-') {
      if (find(p->begin(), p->end(), ':') == p->end()) {
	first = *p;
	++p;
	if (p == source.end()) {
	  result.push_back(first);
	  break;
	}
	if ((*p)[0] != '-') {
	  first += ":" + *p;
	  result.push_back(first);
	}
	else {
	  --p;
	  result.push_back(first);
	}
      }
      else 
	result.push_back(*p);
    }
    else 
      result.push_back(*p);
    
    ++p;
  }
  return(result);
}
