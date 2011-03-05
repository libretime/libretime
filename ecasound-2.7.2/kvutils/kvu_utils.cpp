// ------------------------------------------------------------------------
// kvu_utils.cpp: Miscellaneous helper routines
// Copyright (C) 1999-2004,2009 Kai Vehmanen
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

#include <algorithm> /* find() */
#include <cstdlib> /* atoi() */
#include <iostream>
#include <string>
#include <vector>

#if HAVE_LOCALE_H
#include <locale.h> /* setlocale */
#endif

#include <time.h> /* nanosleep() */
#include <sys/time.h> /* gettimeofday() */
#include <unistd.h> /* usleep() */
#include <ctype.h> /* isspace(), toupper() */

#include "kvu_dbc.h"
#include "kvu_utils.h"

using namespace std;

static string::const_iterator kvu_priv_find_next_instance(const string& arg, const string::const_iterator& curpos, const string::value_type value);
static void kvu_priv_strip_escapes(string* const input, const string& escaped_char);

/**
 * Returns a string where all regex metachars in 'arg'
 * have been quoted using a backslash.
 *
 * Reference: man regex(7)
 */
string kvu_string_regex_meta_escape(const string& arg)
{
  string result;

  /* regex metachar set: ^.[]$()|*+?{}\ */

  string::const_iterator p = arg.begin();
  while(p != arg.end()) {
    if (*p == '^') result += "\\^";
    else if (*p == '.') result += "\\.";
    else if (*p == '[') result += "\\[";
    else if (*p == ']') result += "\\]";
    else if (*p == '$') result += "\\$";
    else if (*p == '(') result += "\\(";
    else if (*p == ')') result += "\\)";
    else if (*p == '|') result += "\\|";
    else if (*p == '*') result += "\\*";
    else if (*p == '+') result += "\\+";
    else if (*p == '?') result += "\\?";
    else if (*p == '{') result += "\\{";
    else if (*p == '}') result += "\\}";
    else if (*p == '\\') result += "\\\\";
    else 
      result += *p;

    ++p;
  }

  return result;
}

/**
 * Returns a string where all meta characters in 'arg'
 * have been quoted using a backslash.
 *
 * Reference: man sh(1), man bash(1)
 */
string kvu_string_shell_meta_escape(const string& arg)
{
  string result;

  string::const_iterator p = arg.begin();
  while(p != arg.end()) {
    if (*p == '"') result += "\\\"";
    else if (*p == '\'') result += "\\\'";
    else if (*p == ' ') result += "\\ ";
    else if (*p == '|') result += "\\|";
    else if (*p == '&') result += "\\&";
    else if (*p == ';') result += "\\;";
    else if (*p == '(') result += "\\(";
    else if (*p == ')') result += "\\)";
    else if (*p == '<') result += "\\<";
    else if (*p == '>') result += "\\>";
    else 
      result += *p;

    ++p;
  }

  return result;
}

/**
 * Converts a string to a vector of strings (words).
 * Whitespace is used as the separator.
 *
 * Note! This function is obsolete, use @see string_to_tokens() 
 *       instead.
 */
vector<string> kvu_string_to_words(const string& s)
{
  return kvu_string_to_tokens(s);
}

/**
 * Converts a string to a vector of token strings.
 * Whitespace is used as the separator.
 */
vector<string> kvu_string_to_tokens(const string& s)
{
  vector<string> vec;
  string stmp = "";

  for(string::const_iterator p = s.begin(); p != s.end(); p++) {
    if (isspace(*p) == 0)
      stmp += *p;
    else {
      if (stmp == "") continue;
      vec.push_back(stmp);
      stmp = "";
    }
  }
  if (stmp.size() > 0)
    vec.push_back(stmp);

  return vec;
}

/**
 * Converts a string to a vector of token strings.
 * Whitespace is used as the token separator. 
 *
 * Unlike string_to_tokens(), quotes can be used to mark 
 * groups of words as tokens (e.g. "this is one token").
 * The tokens are not removed from the string. 
 * Single-quotes (') are not supported.
 *
 * It's also possible to add individual whitespace
 * characted by escaping them with a backclash (e.g.
 * 'this\ is\ one\ token\ '). Escaped characters are
 * not considered as possible separators.
 */
vector<string> kvu_string_to_tokens_quoted(const string& s)
{
  vector<string> vec;
  string stmp;
  bool quoteflag = false;

  for(string::const_iterator p = s.begin(); p != s.end(); p++) {
    if (*p == '\"') {
      quoteflag = !quoteflag;
      stmp += *p;
    }
    else if (*p == '\\') {
      p++;
      if (p == s.end()) break;
      stmp += *p;
    }
    else if (isspace(*p) == 0 || quoteflag == true) {
      stmp += *p;
    }
    else {
      /* note: token ready, add to vector if length is non-zero */
      if (stmp.size() == 0) continue;

      vec.push_back(stmp);
      stmp = "";
      DBC_CHECK(stmp.size() == 0);
    }
  }
  if (stmp.size() > 0)
    vec.push_back(stmp);

  return vec;
}

/**
 * Converts a string to a vector of strings.
 *
 * @param str string to be converted
 * @param separator character to be used for separating items
 */
vector<string> kvu_string_to_vector(const string& str, 
				    const string::value_type separator)
{
  vector<string> vec;
  string stmp = "";

  for(string::const_iterator p = str.begin(); p != str.end(); p++) {
    if (*p != separator)
      stmp += *p;
    else {
      if (stmp == "") continue;
      vec.push_back(stmp);
      stmp = "";
    }
  }
  if (stmp.size() > 0)
    vec.push_back(stmp);

  return vec;
}

/**
 * Converts a string to a vector of integers.
 *
 * @param str string to be converted
 * @param separator character to be used for separating items
 */
vector<int> kvu_string_to_int_vector(const string& str, 
				     const string::value_type separator)
{
  vector<int> vec;
  string stmp = "";

  for(string::const_iterator p = str.begin(); p != str.end(); p++) {
    if (*p != separator)
      stmp += *p;
    else {
      if (stmp == "") continue;
      vec.push_back(atoi(stmp.c_str()));
      stmp = "";
    }
  }
  if (stmp.size() > 0)
    vec.push_back(atoi(stmp.c_str()));

  return vec;
}

/**
 * Return a modified copy of vector 'str_vector' where 'from' has
 * been replaced with 'to' in all items.
 */
vector<string> kvu_vector_search_and_replace(const vector<string>& str_vector, 
					     const string& from,
					     const string& to)
{
  vector<string> vstmp;
  vector<string>::const_iterator p = str_vector.begin();
  while(p != str_vector.end()) {
    vstmp.push_back(kvu_string_search_and_replace(*p, from, to));
    ++p;
  }
  return vstmp;
}

/**
 * Converts a vector of strings to a single string.
 *
 * @param str vector of strings to be converted
 * @param separator string that is inserted between items
 */
string kvu_vector_to_string(const vector<string>& str, 
			    const string& separator)
{

  string stmp;

  vector<string>::const_iterator p = str.begin();
  while(p != str.end()) {
    stmp += *p;
    ++p;
    if (p != str.end()) 
      stmp += separator;
  }

  return stmp;
}

/**
 * Return a new string, where all 'from' characters are
 * replaced with 'to' characters.
 */
string kvu_string_search_and_replace(const string& str, 
				     const string::value_type from,
				     const string::value_type to)
{
  string stmp (str);
  for(vector<string>::size_type p = 0; p < str.size(); p++) {
    if (str[p] == from) stmp[p] = to;
    else stmp[p] = str[p];
  }

  return stmp;
}

/**
 * Return a new string, where all 'from' characters are
 * replaced with 'to' characters.
 */
string kvu_string_search_and_replace(const string& str, 
				     const string& from,
				     const string& to)
{
  string tmp (str);
  size_t pos = 0;
  while((pos = tmp.find(from, pos)) != string::npos) {
    tmp.replace(pos, from.size(), to);
    pos += (to.size() > from.size() ? to.size() : from.size());
  }
  return tmp;
}

/**
 * Case-insensitive string compare. Ignores preceding and 
 * trailing white space.
 */
bool kvu_string_icmp(const string& first, const string& second)
{
  string a = first;
  string b = second;

  a = kvu_remove_trailing_spaces(a);
  a = kvu_remove_preceding_spaces(a);
  a = kvu_convert_to_uppercase(a);

  b = kvu_remove_trailing_spaces(b);
  b = kvu_remove_preceding_spaces(b);
  b = kvu_convert_to_uppercase(b);

  return a == b;
}

/**
 * Removes all trailing white space
 */
string kvu_remove_trailing_spaces(const string& a)
{
  string r = "";
  string::const_reverse_iterator p;
  for(p = a.rbegin(); p != a.rend(); p++) {
    if (*p != ' ') break;
  }
  for(; p != a.rend(); p++) {
    r = *p + r;
  }
  return  r;
}

/**
 * Removes all preciding white space
 */
string kvu_remove_preceding_spaces(const string& a)
{
  string r = "";
  string::const_iterator p;
  for(p = a.begin(); p != a.end(); p++) {
    if (*p != ' ') break;
  }
  for(; p != a.end(); p++) {
    r += *p;
  }
  return r;
}

/**
 * Removes all surrounding white spaces
 */
string kvu_remove_surrounding_spaces(const string& a)
{
  string::const_iterator p,q;
  for(p = a.begin(); p != a.end() && *p == ' '; p++);
  for(q = (a.end() - 1); q != a.begin() && *q == ' '; q--);
  return string(p,q + 1);
}

/**
 * Converts string to uppercase using toupper(int)
 */
string kvu_convert_to_uppercase(const string& a)
{
  string r = a;
  for(string::iterator p = r.begin(); p != r.end(); p++)
    *p = toupper(*p);
  return r;
}

/**
 * Converts string to lowercase using tolower(int)
 */
string kvu_convert_to_lowercase(const string& a)
{ 
  string r = a;
  for(string::iterator p = r.begin(); p != r.end(); p++)
    *p = tolower(*p);
  return r;
}

/**
 * Converts string to uppercase using toupper(int)
 * Modifies the parameter object.
 */
void kvu_to_uppercase(string& a)
{
  string::iterator p = a.begin();
  while(p != a.end()) {
    *p = toupper(*p);
    ++p;
  }
}

/**
 * Converts string to lowercase using tolower(int)
 * Modifies the parameter object.
 */
void kvu_to_lowercase(string& a)
{
  string::iterator p = a.begin();
  while(p != a.end()) {
    *p = tolower(*p);
    ++p;
  }
}

/**
 * Finds the next instance of character 'value' and returns its
 * position.
 * 
 * All backslash escaped instances of 'value' ("\<value>")
 * are ignored in the search. Also instance of 'value' within 
 * a pair of double quotes are ignored.
 *
 * Note that general backlash escaping is not supported, i.e. 
 * "\<character>" is not interpreted as "<character>".
 *
 * @return position of next 'value' or arg.end() if not found
 */
static string::const_iterator kvu_priv_find_next_instance(const string& arg, const string::const_iterator& start, const string::value_type value)
{
  string::const_iterator curpos = start, ret = arg.end(), nextquote = arg.end();

  while(curpos != arg.end()) {
    ret = find(curpos, arg.end(), value);

    nextquote = find(curpos, arg.end(), '"'); 
    if (nextquote < ret) {

      /* step: ignore quoted part of the input string */
      nextquote = find(nextquote + 1, arg.end(), '"');
      if (nextquote != arg.end()) {
	curpos = nextquote + 1;
	ret = find(curpos, arg.end(), value);
      }
    }

    if (ret != arg.end() && ret != arg.begin()) {
      string::const_iterator prev = ret - 1;
      if ((*prev) == '\\') {
	curpos = ret + 1;
	continue;
      }
    }

    break;
  }

  return ret;
}

/**
 * Returns the nth argument from a formatted string
 *
 * @param number the argument number 
 * @param argu a formatted string: "something:arg1,arg2,...,argn"
 *
 * require:
 *  number >= 1
 */
string kvu_get_argument_number(int number, const string& arg)
{
  string result;
  vector<string> temp = kvu_get_arguments(arg);
  if (static_cast<int>(temp.size()) >= number) {
    result = temp[number - 1];
  }
  return result;
}

/**
 * Converts all backslash-commas into commas and returns
 * the result.
 *
 * @pre escaped_char.size() == 1
 */
static void kvu_priv_strip_escapes(string* const input, const string& escaped_char)
{
  size_t pos; 
  while((pos = input->find(string("\\") + escaped_char)) != string::npos) {
    input->replace(pos, 2, escaped_char);
  }
}

/**
 * Strips the outer quotes of type 'quote_char' from the string.
 * The string is only modified if the the string starts with,
 * and ends to, a character matching 'quote_char'.
 *
 * @pre escaped_char.size() == 1
 */
void kvu_string_strip_outer_quotes(string* input, const std::string::value_type quote_char)
{
  if (input->size() >= 2 &&
      *input->begin() == quote_char &&
      *(input->end() - 1) == quote_char)
    *input = std::string(input->begin() + 1, input->end() - 1);
}

/** 
 * Returns number of arguments in formatted string 'arg'.
 */
int kvu_get_number_of_arguments(const string& arg)
{
  return kvu_get_arguments(arg).size();
}

/**
 * Returns a vector of all arguments from a formatted string
 *
 * Note: forces locale to "POSIX" as decimal separate must be
 *       be period (.) in order to argument separation to work
 *
 * @param argu a formatted string: "something:arg1,arg2,...,argn"
 */
vector<string> kvu_get_arguments(const string& argu)
{
  vector<string> resvec;
  char* locale_save;
  string::const_iterator b = kvu_priv_find_next_instance(argu, argu.begin(), ':');
  string::const_iterator e;

  if (b == argu.end()) {
    if (argu.size() > 0) b = argu.begin();
    else return resvec;
  }
  else 
    ++b;

#if HAVE_SETLOCALE
  locale_save = setlocale (LC_NUMERIC, "POSIX");
#else
  locale_save = NULL;
#endif

  for(; b != argu.end();) {
    e = kvu_priv_find_next_instance(argu, b, ',');
    string target = string(b, e);

    /* strip backslash-commas (and leave the commas in place) */
    kvu_priv_strip_escapes(&target, ",");
    kvu_priv_strip_escapes(&target, ":");
    kvu_string_strip_outer_quotes(&target, '"');
    resvec.push_back(target);

    if (e == argu.end()) 
      break;

    b = e;
    ++b;
    
    /* special case in which last argument is empty */
    if (b == argu.end() &&
	*e == ',')
      resvec.push_back("");
  }

#if HAVE_SETLOCALE
  /* restore original locale */
  if (locale_save)
    setlocale (LC_NUMERIC, locale_save);
#endif

  return resvec;
}

/**
 * Get the prefix part of a string argument
 * @param argument format used is -prefix:arg1, arg2, ..., argN
 *
 * require:
 *   argu.find('-') != string::npos
 *
 * ensure:
 *   result.size() >= 0
 */
string kvu_get_argument_prefix(const string& argu)
{
  // --------
  DBC_REQUIRE(argu.find('-') != string::npos);
  // --------

  string::const_iterator b = find(argu.begin(), argu.end(), '-');
  string::const_iterator e = find(argu.begin(), argu.end(), ':');

  if (b != argu.end()) {
    ++b;
    if (b !=  argu.end()) {
      return string(b,e);
    }
  }

  return "";
}

/**
 * Prints a time stamp to stderr
 */
void kvu_print_time_stamp(void)
{
  // --
  // not thread-safe!
  // --
  static bool first = true;
  static struct timeval last;
  struct timeval current;

  if (first) {
    ::gettimeofday(&last, 0);
    first = false;
  }

  ::gettimeofday(&current, 0);

  cerr << "(timestamp) " << current.tv_sec << "sec, " <<
    current.tv_usec << "msec.";
  
  long delta = current.tv_usec;
  delta -= last.tv_usec;
  delta += (current.tv_sec - last.tv_sec) * 1000000;
  cerr << " Delta " << delta << "msec." << endl;

  last.tv_sec = current.tv_sec;
  last.tv_usec = current.tv_usec;
}

/**
 * Put the calling execution context to sleeps for 
 * 'seconds.nanosecods'.
 *
 * Note! If available, implemented using nanosleep().
 *
 * @return 0 on success, non-zero if sleep was 
 *         interrupted for some reason
 */
int kvu_sleep(long int seconds, long int nanoseconds)
{
  int ret = 0;
#if defined(HAVE_NANOSLEEP) && !defined(__CYGWIN__)
  struct timespec len;
  len.tv_sec = static_cast<time_t>(seconds);
  len.tv_nsec = nanoseconds;
  ret = nanosleep(&len, NULL);

#elif HAVE_USLEEP
  ret = usleep(seconds * 1000000 + nanoseconds / 1000);

#else
  cerr << "(libkvutils) kvutils:: warning! neither nanosleep() or usleep() found!" << endl;
#endif

  return ret;
}
