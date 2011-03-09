// ------------------------------------------------------------------------
// eca-curses.cpp: Curses implementation of the console user interface.
// Copyright (C) 1999-2004,2007 Kai Vehmanen
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

#include <cstdlib>
#include <iostream>
#include <map>
#include <string>

#if defined(ECA_PLATFORM_CURSES) 

#ifdef ECA_USE_NCURSES_H
#include <ncurses.h>
#include <term.h> /* for setupterm() */
#elif ECA_USE_NCURSES_NCURSES_H
#include <ncurses/ncurses.h>
#include <ncurses/term.h> /* for setupterm() */
#else
#include <curses.h>
#include <term.h> /* for setupterm() */
#endif

#define READLINE_LIBRARY
#include <readline.h>
#include <history.h>

#include <eca-iamode-parser.h>
#include <eca-version.h>
#include <kvu_utils.h> /* kvu_string_search_and_replace() */

#include "ecasound.h"
#include "eca-curses.h"

#if defined(RL_READLINE_VERSION) && RL_READLINE_VERSION >= 0x0402
static char** ecasound_completion (const char *text, int start, int end);
static char* ecasound_command_generator (const char* text, int state);
#else 
static char** ecasound_completion (char *text, int start, int end);
static char* ecasound_command_generator (char* text, int state);
#endif

ECA_CURSES::ECA_CURSES(void)
{
  rl_initialized_rep = false;
  setupterm((char *)0, 1, (int *)0);
  init_readline_support();
}

ECA_CURSES::~ECA_CURSES(void)
{
  if (rl_initialized_rep == true) {
    rl_cleanup_after_signal();
  }
}

void ECA_CURSES::print(const std::string& msg)
{
  std::cout << msg << std::endl;
}

void ECA_CURSES::print_banner(void)
{
  int width = COLS - 4;
  if (width > ECASOUND_TERM_WIDTH_DEFAULT)
    width = ECASOUND_TERM_WIDTH_DEFAULT;
  string banner (width, '*');
  std::cout << banner << std::endl;
  std::cout << "*";
  putp(tigetstr("bold"));
  std::cout << "        ecasound v" 
       << ecasound_library_version
       << ECASOUND_COPYRIGHT;
  putp(tigetstr("sgr0"));
  std::cout << "\n";
  std::cout << banner << std::endl;
}

void ECA_CURSES::read_command(const std::string& prompt)
{
  if (rl_initialized_rep != true) rl_initialized_rep = true;
  last_cmdchar_repp = readline(const_cast<char*>(prompt.c_str()));
  if (last_cmdchar_repp != 0) {
    add_history(last_cmdchar_repp);
    last_cmd_rep = last_cmdchar_repp;
    free(last_cmdchar_repp);
  }
  else {
    /* handle EOF */
    last_cmd_rep = "q";
  }
}

const std::string& ECA_CURSES::last_command(void) const
{
  return last_cmd_rep;
}

void ECA_CURSES::init_readline_support(void)
{
  /* for conditional parsing of ~/.inputrc file. */
  rl_readline_name = "ecasound";

  /* we want to attempt completion first */
#if RL_READLINE_VERSION >= 0x0402
  rl_attempted_completion_function = (rl_completion_func_t*)ecasound_completion;
#else
  rl_attempted_completion_function = (CPPFunction *)ecasound_completion;
#endif
}

/* **************************************************************** */
/*                                                                  */
/*                  Interface to Readline Completion                */
/*                                                                  */
/* **************************************************************** */

/**
 * Attempt to complete on the contents of TEXT.  START and END bound the
 * region of rl_line_buffer that contains the word to complete.  TEXT is
 * the word to complete.  We can use the entire contents of rl_line_buffer
 * in case we want to do some simple parsing.  Return the array of matches,
 * or NULL if there aren't any.
 */
#if RL_READLINE_VERSION >= 0x0402
char** ecasound_completion (const char *text, int start, int end)
#else
char** ecasound_completion (char *text, int start, int end)
#endif
{
  char **matches;
  matches = (char **)NULL;

  /* complete only the first command, otherwise complete files in 
   * the current directory */
  if (start == 0) {
#if RL_READLINE_VERSION >= 0x0402
    matches = rl_completion_matches (text, (rl_compentry_func_t *)ecasound_command_generator);
#else
    matches = completion_matches (text, (CPFunction *)ecasound_command_generator);
#endif
  }
  return matches;
}

/**
 * Generator function for command completion.  STATE lets us know whether
 * to start from scratch; without any state (i.e. STATE == 0), then we
 * start at the top of the list.
 */
#if RL_READLINE_VERSION >= 0x0402
char* ecasound_command_generator (const char* text, int state)
#else
char* ecasound_command_generator (char* text, int state)
#endif
{
  static int list_index, len;
  static const std::map<std::string,int>& map_ref = ECA_IAMODE_PARSER::registered_commands();
  static std::map<std::string,int>::const_iterator p;
  static std::string cmd;

  /* If this is a new word to complete, initialize now.  This includes
   * saving the length of TEXT for efficiency, and initializing the index
   * variable to 0
   */
  if (!state) {
      list_index = 0;
      p = map_ref.begin();
      len = strlen (text);
  }

  /* Return the next name which partially matches from the command list */
  while (p != map_ref.end()) {
      cmd = p->first;
      list_index++;
      ++p;
      if (p != map_ref.end()) {
	std::string hyphenstr = kvu_string_search_and_replace(text, '_', '-');
	if (strncmp(hyphenstr.c_str(), cmd.c_str(), len) == 0) {
	  return strdup(cmd.c_str());
	}
      }
  }
  return (char *)0;
}

#else

#include "eca-curses.h"

ECA_CURSES::ECA_CURSES(void) {}
ECA_CURSES::~ECA_CURSES(void) {}
void ECA_CURSES::print(const std::string& msg) {}
void ECA_CURSES::print_banner(void) {}
void ECA_CURSES::read_command(const std::string& prompt) {}
const std::string& ECA_CURSES::last_command(void) const { static std::string empty; return empty; }
void ECA_CURSES::init_readline_support(void) {}

#endif /* ECA_PLATFORM_CURSES */
