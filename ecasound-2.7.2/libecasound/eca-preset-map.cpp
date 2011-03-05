// ------------------------------------------------------------------------
// eca-preset-map: Dynamic register for storing effect presets
// Copyright (C) 2000-2003 Kai Vehmanen
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

#include <algorithm>
#include <list>
#include <vector>
#include <string>

#include <kvu_dbc.h>

#include "eca-object.h"
#include "eca-resources.h"
#include "eca-preset-map.h"
#include "global-preset.h"

using std::find;
using std::list;
using std::map;
using std::string;
using std::vector;

ECA_PRESET_MAP::ECA_PRESET_MAP(void)
{
#ifndef ECA_DISABLE_EFFECTS
  ECA_RESOURCES ecarc;

  string filename =
    ecarc.resource("user-resource-directory") + "/" + ecarc.resource("resource-file-effect-presets");

  string global_filename =
    ecarc.resource("resource-directory") + "/" + ecarc.resource("resource-file-effect-presets");

  load_preset_file(global_filename);
  load_preset_file(filename);
#endif
}

ECA_PRESET_MAP::~ECA_PRESET_MAP(void)
{
}

void ECA_PRESET_MAP::load_preset_file(const string& fname)
{
  RESOURCE_FILE preset_file;
  preset_file.resource_file(fname);
  preset_file.load();
  const vector<string>& pmap = preset_file.keywords();
  vector<string>::const_iterator p = pmap.begin();
  while(p != pmap.end()) {
    if (*p != "") preset_keywords_rep.push_back(*p);
    ++p;
  }
}

void ECA_PRESET_MAP::register_object(const string& keyword, const string& matchstr, ECA_OBJECT* object)
{
  if (find(preset_keywords_rep.begin(), preset_keywords_rep.end(), keyword) == preset_keywords_rep.end())
    preset_keywords_rep.push_back(keyword);

  ECA_OBJECT_MAP::register_object(keyword, matchstr, object);
}

void ECA_PRESET_MAP::unregister_object(const string& keyword)
{
  preset_keywords_rep.remove(keyword);
  ECA_OBJECT_MAP::unregister_object(keyword);
}

const list<string>& ECA_PRESET_MAP::registered_objects(void) const
{
  return(preset_keywords_rep);
}

bool ECA_PRESET_MAP::has_keyword(const std::string& keyword) const
{
  if (find(preset_keywords_rep.begin(), preset_keywords_rep.end(), keyword) == preset_keywords_rep.end())
    return(false);

  return (true);
}

const ECA_OBJECT* ECA_PRESET_MAP::object_expr(const string& expr) const
{
  if (find(preset_keywords_rep.begin(), preset_keywords_rep.end(), expr) != preset_keywords_rep.end()) {
    return(object(expr));
  }
  return(0);
}

const ECA_OBJECT* ECA_PRESET_MAP::object(const string& keyword) const
{
  const PRESET* retobj = 0;

#ifndef ECA_DISABLE_EFFECTS
  if (find(preset_keywords_rep.begin(), preset_keywords_rep.end(), keyword) != preset_keywords_rep.end()) {
    const list<string>& objlist = ECA_OBJECT_MAP::registered_objects();

    if (find(objlist.begin(), objlist.end(), keyword) == objlist.end()) {
      try {
	PRESET* obj = dynamic_cast<PRESET*>(new GLOBAL_PRESET(keyword));
	if (obj != 0) {
	  const_cast<ECA_PRESET_MAP*>(this)->register_object(keyword, "^" + keyword + "$", obj);
	  retobj = obj;
	  //  std::cerr << "(eca-preset-map) registering obj; " << keyword << ".\n";
	}
	//  else std::cerr << "(eca-preset-map) fail (3); " << keyword << ".\n";

      }
      catch(...) { retobj = 0; }

      DBC_CHECK(find(objlist.begin(), objlist.end(), keyword) != objlist.end() || retobj == 0);
    }
    else {
      retobj = dynamic_cast<const PRESET*>(ECA_OBJECT_MAP::object(keyword));
      //  if (retobj == 0) std::cerr << "(eca-preset-map) fail (2); " << keyword << ".\n";
    }
  }
  //  else std::cerr << "(eca-preset-map) fail (1); " << keyword << ".\n";
#endif

  return(retobj);
}
