// ------------------------------------------------------------------------
// eca-object-map: A virtual base for dynamic object maps 
// Copyright (C) 2000-2004,2009 Kai Vehmanen
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

#include <algorithm>
#include <list>
#include <string>
#include <map>
#include <sys/types.h>
#include <regex.h>

#include "eca-object-map.h"
#include "eca-logger.h"

using std::find;
using std::map;
using std::string;
using std::list;

ECA_OBJECT_MAP::ECA_OBJECT_MAP(void)
  : expr_case_sensitive_rep(false)
{
}

ECA_OBJECT_MAP::~ECA_OBJECT_MAP(void)
{ 
  map<string, ECA_OBJECT*>::iterator p = object_map.begin();
  while(p != object_map.end()) {
    if (p->second != 0) {
      ECA_OBJECT* next_obj = p->second;
      // std::cerr << "Deleting " << next_obj->name() << "." << std::endl;
      map<string, ECA_OBJECT*>::iterator q = p;
      ++q;
      while(q != object_map.end()) {
	if (q->second != 0 &&
	    q->second == p->second) {
	  // std::cerr << "Deleting sub-object with keyword " << q->first << "." << std::endl;
	  q->second = 0;
	}
	++q;
      }
      p->second = 0;
      delete next_obj;
    }
    ++p;
  }
}

void ECA_OBJECT_MAP::toggle_case_sensitive_expressions(bool v)
{
  expr_case_sensitive_rep = v;
}

bool ECA_OBJECT_MAP::case_sensitive_expressions(void) const
{
  return expr_case_sensitive_rep;
}

/**
 * Registers a new object to the object map. Map object will take care
 * of deleting the registered objects. Notice that it's possible 
 * to register the same physical object with different keywords.
 * Object map will take care that objects with multiple registered
 * keywords are destructed properly.
 *
 * @arg keyword tag string that identifies the registered object
 * @arg expr    regex that is used to map strings to objects 
 *              (note! 'keyword' should match 'expr')
 */
void ECA_OBJECT_MAP::register_object(const string& keyword, const string& expr, ECA_OBJECT* object)
{
  object_keywords_rep.push_back(keyword);
  object_map[keyword] = object;
  object_expr_map[keyword] = expr;

  if (expr_to_keyword(keyword) != keyword &&
      object != 0) {
    ECA_LOG_MSG(ECA_LOGGER::info, 
		"WARNING: Registered keyword " + keyword + 
		" doesn't match the associated regex " + expr + 
		", for object '" + object->name() + 
		"' (" + expr_to_keyword(expr) + ").");
  }
}

/**
 * Unregisters object with keyword 'keyword'. Does not physically
 * delete the assigned object, because one object can be 
 * registered with multiple keywords.
 */
void ECA_OBJECT_MAP::unregister_object(const string& keyword)
{
  object_keywords_rep.remove(keyword);
  object_map[keyword] = 0;
  object_expr_map[keyword] = "";
}

/**
 * Returns a list of registered objects.
 */
const list<string>& ECA_OBJECT_MAP::registered_objects(void) const
{
  return object_keywords_rep;
}

bool ECA_OBJECT_MAP::has_keyword(const std::string& keyword) const
{
  if (find(object_keywords_rep.begin(), object_keywords_rep.end(), keyword) == object_keywords_rep.end())
    return false;

  return true;
}

bool ECA_OBJECT_MAP::has_object(const ECA_OBJECT* obj) const
{
  map<string, ECA_OBJECT*>::const_iterator p = object_map.begin();
  while(p != object_map.end()) {
    if (obj->name() == p->second->name()) {
      return true;
    }
    ++p;
  }
  return false;
}

/**
 * Returns the object identified by 'keyword'. If no keyword 
 * matches, 0 is returned.
 *
 * As a const object pointer is returned, clone() and new_expr() 
 * methods should be used to create new non-const objects of 
 * the returned type.
 */
const ECA_OBJECT* ECA_OBJECT_MAP::object(const string& keyword) const
{
  const ECA_OBJECT* object = 0;
  if (object_map.find(keyword) != object_map.end()) {
    object = object_map[keyword];
  }
  return object;
}

/**
 * A convenience function which directly returns an object matching
 * search string 'input'.
 */
const ECA_OBJECT* ECA_OBJECT_MAP::object_expr(const string& input) const
{
  return object(expr_to_keyword(input));
}

/**
 * Returns the identifying keyword for input string 'input'.
 * 
 * If 'case_sensitive_expressions() != true', the pattern 
 * matching will be case insensitive.
 */
string ECA_OBJECT_MAP::expr_to_keyword(const string& input) const
{
  map<string,string>::const_iterator p = object_expr_map.begin();
  regex_t preg;
  string result;
  while(p != object_expr_map.end()) {
    int cflags = REG_EXTENDED | REG_NOSUB;
    
    if (case_sensitive_expressions() != true)
      cflags |= REG_ICASE;

    regcomp(&preg, p->second.c_str(), cflags);
    if (regexec(&preg, input.c_str(), 0, 0, 0) == 0) {
      ECA_LOG_MSG(ECA_LOGGER::functions, "match (1): " + input + " to regexp " + p->second);
      result = p->first;
      regfree(&preg);
      break;
    }
    regfree(&preg);
    ++p;
  }
  return result;
}

/**
 * Returns the identifying keyword that matches the expression 'expr'.
 */
string ECA_OBJECT_MAP::keyword_to_expr(const string& keyword) const
{
  if (object_expr_map.find(keyword) != object_expr_map.end())
    return object_expr_map[keyword];

  return "";
}

/**
 * Returns the matching identifying keyword for 'object'.
 */
string ECA_OBJECT_MAP::object_identifier(const ECA_OBJECT* object) const
{
  map<string, ECA_OBJECT*>::const_iterator p = object_map.begin();
  while(p != object_map.end()) {
    if (object->name() == p->second->name()) {
      return p->first;
    }
    ++p;
  }
  return "";
}
