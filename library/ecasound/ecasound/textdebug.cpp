// ------------------------------------------------------------------------
// textdebug.cpp: Implementation of console logging subsystem.
// Copyright (C) 1999-2002,2004-2005,2008,2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <iostream>
#include <cstdlib>
#include <string>
#include <algorithm>

#include <eca-logger-interface.h>

#include "ecasound.h"
#include "textdebug.h"

#ifdef ECA_USE_NCURSES_H
#include <ncurses.h>
#include <term.h> /* for setupterm() */
#elif ECA_USE_NCURSES_NCURSES_H
#include <ncurses/ncurses.h>
#include <ncurses/term.h> /* for setupterm() */
#elif ECA_USE_CURSES_H
#include <curses.h>
#include <term.h> /* for setupterm() */
#endif

using namespace std;

const static int tb_terminal_width_default = ECASOUND_TERM_WIDTH_DEFAULT;

/**
 * Set terminal width used in pretty-printing ecasound console output.
 */
static int tb_terminal_width = tb_terminal_width_default;

/**
 * Set terminal width used in pretty-printing banners and 
 * other purely cosmetic traces.
 */
static int tb_terminal_width_banners = tb_terminal_width_default;

/**
 * Wraps text 'msg' by adding <newline> + "... " breaks so that none 
 * of the lines exceed 'width' characteds.
 */
static string tb_wrap(const string& msg, int width, int first_line_offset)
{
  string result;
  int wrlines = 0;
  int offset = first_line_offset;
  const string wrap_prefix ("... ");
  size_t wrap_offset = wrap_prefix.size();
  size_t begin, end;

#undef VERBOSE_DEBUG
#ifdef VERBOSE_DEBUG
  fprintf(stdout, 
	  "msg-in=<%s>\n",
	  msg.c_str());
#endif

  for(begin = 0, end = 0; end < msg.size(); end++) {

    if (begin == end)
      continue;

    /* case: trace messages has a newline itself, no wrap needed */
    if (msg[end] == '\n') {
      result += string(msg, begin, end - begin);
      begin = end;
      offset = 0;
      ++wrlines;
    }
    /* case: current line exceeds the width, wrap */
    else if (end - begin + offset >= static_cast<size_t>(width)) {
      string tmpstr (msg, begin, end - begin);
      size_t last_space = tmpstr.find_last_of(" ");

      /* case: spaces on the line, wrap before last token */
      if (last_space != string::npos) {
	result += string(tmpstr, 0, last_space);
	begin += last_space + 1;
      }
      /* case: no spaces on the line, cannot wrap */
      else {
	/* note: with first line, wrap all input */
	if (static_cast<size_t>(first_line_offset) > wrap_offset &&
	    wrlines == 0) {
	  /* nop */
	}
	else {
	  result += tmpstr;
	  begin = end;
	}
      }

      result += "\n" + wrap_prefix;
      offset = wrap_offset;
      ++wrlines;
    }
  }

  if ((end - begin) > 0) {
    result += string(msg, begin, end - begin);
  }

#ifdef VERBOSE_DEBUG
  fprintf(stdout, 
	  "msg-out=<%s>\n",
	  result.c_str());
#endif

  return result;
}

void TEXTDEBUG::stream(std::ostream* dos)
{
  dostream_repp = dos;
}

std::ostream* TEXTDEBUG::stream(void)
{
  return dostream_repp;
}

void TEXTDEBUG::do_flush(void) 
{
  dostream_repp->flush();
}

void TEXTDEBUG::do_msg(ECA_LOGGER::Msg_level_t level, const std::string& module_name, const std::string& log_message)
{
  if (is_log_level_set(level) == true) {
    int offset = 0;

    if (level == ECA_LOGGER::subsystems) {
#if defined(ECA_USE_NCURSES_H) || defined(ECA_USE_NCURSES_NCURSES_H) || defined(ECA_USE_CURSES_H)
      *dostream_repp << "- [ ";
      putp(tigetstr("bold"));
      offset += 4;
#endif
    }
    else if (module_name.size() > 0 &&
	     is_log_level_set(ECA_LOGGER::module_names) == true &&
	     level != ECA_LOGGER::eiam_return_values) {
      std::string module_name_without_ext 
	= ECA_LOGGER_INTERFACE::filter_module_name(module_name);
      *dostream_repp << "(" 
		     << module_name_without_ext
		     << ") ";
      offset += module_name_without_ext.size() + 3;
    }
    
    *dostream_repp << tb_wrap(log_message, tb_terminal_width, offset);

    if (level == ECA_LOGGER::subsystems) {
#if defined(ECA_USE_NCURSES_H) || defined(ECA_USE_NCURSES_NCURSES_H) || defined(ECA_USE_CURSES_H)
      putp(tigetstr("sgr0"));
      *dostream_repp << " ] ";
#else
      *dostream_repp << " ] ";
#endif
      offset += 3;
      int fillchars = tb_terminal_width_banners
	- (static_cast<int>(log_message.size()) + offset);
      if (fillchars > 0) {
	string fillstr (fillchars, '-');
	*dostream_repp << fillstr;
      }
    }
  
    *dostream_repp << endl;
  }
}

TEXTDEBUG::TEXTDEBUG(void)
{
  char *columns_str = getenv("COLUMNS");
  if (columns_str) {
    tb_terminal_width =
      std::atoi(columns_str) - 4;
    if (tb_terminal_width < 8)
      tb_terminal_width = tb_terminal_width_default;
  }
#if defined(ECA_USE_NCURSES_H) || defined(ECA_USE_NCURSES_NCURSES_H) || defined(ECA_USE_CURSES_H)
  else if (COLS > 0) {
    tb_terminal_width = COLS - 4;
  }
#endif

  if (tb_terminal_width < 
      tb_terminal_width_banners)
    tb_terminal_width_banners = tb_terminal_width;
      
  dostream_repp = &std::cout;
}

TEXTDEBUG::~TEXTDEBUG(void)
{
  flush();
}
