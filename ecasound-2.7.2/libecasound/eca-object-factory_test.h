// ------------------------------------------------------------------------
// eca-object-factory_test.h: Unit test for ECA_OBJECT_FACTORY
// Copyright (C) 2002,2008,2009 Kai Vehmanen
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

#include <cmath> /* std::fabs() */
#include <iostream>
#include <string>
#include <typeinfo> /* typeid() */

#include "kvu_numtostr.h"

#include "audioio.h"
#include "audioio-device.h"
#include "eca-chainop.h"
#include "audiofx_ladspa.h"
#include "preset.h"
#include "generic-controller.h"
#include "midiio.h"
#include "eca-operator.h"
#include "eca-object-factory.h"
#include "eca-object-map.h"
#include "eca-preset-map.h"

#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_OBJECT_FACTORY.
 *
 * 1. Checks that all factory object maps have 
 *    at least one registered object.
 * 2. Creates one instance of each object and 
 *    verifies that DYNAMIC_OBJECT::new_expr() and
 *    DYNAMIC_OBJECT::clone() member functions
 *    are correctly defined.
 * 3. Checks that LADSPA id and unique_name  
 *    maps have same number of registered objects.
 */
class ECA_OBJECT_FACTORY_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("ECA_OBJECT_FACTORY_TEST"); }
  virtual void do_run(void);

public:

  virtual ~ECA_OBJECT_FACTORY_TEST(void) { }

private:

  template<class T> void test_map(const ECA_OBJECT_MAP& objmap);
  template<class T> void test_object_types(const T* source, const T* target, const string& description);
};

template<class T> 
void ECA_OBJECT_FACTORY_TEST::test_object_types(const T* source, const T* target, const string& description)
{
  if (target == 0) {
    ECA_TEST_FAILURE("Unable to create object (" + description + ") of type \"" + source->name() + "\"");
  }
  else if (target->name() != source->name()) {
    ECA_TEST_FAILURE("Type mismatch (name) between (" + description + ") and original object; type name \"" + source->name() + "\"");
  }
  else if (typeid(target) != typeid(source)) {
    ECA_TEST_FAILURE("Type mismatch (type_id) between (" + description + ") and original object; type name \"" + source->name() + "\"");
  }
}

template<class T>
void ECA_OBJECT_FACTORY_TEST::test_map(const ECA_OBJECT_MAP& objmap)
{
  const list<string>& regobjs = objmap.registered_objects();

  list<string>::const_iterator p = regobjs.begin();
  while(p != regobjs.end()) {
    const T* obj = dynamic_cast<const T*>(objmap.object(*p));
    if (obj == 0) {
      ECA_TEST_FAILURE("Unable to create object of type \"" + obj->name()
		       + "\" from keyword \"" + *p + "\"");
    }
    else {
      ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		  "libecasound_tester: Object type \"" +
		  obj->name() +
		  "\" succesfully created.");
      
      T* target = dynamic_cast<T*>(obj->new_expr());
      test_object_types<T>(obj, target, "new_expr");
      if (target != 0) delete target;
      
      target = dynamic_cast<T*>(obj->clone());
      test_object_types<T>(obj, target, "clone");
      
      if (target != 0) {
	if (obj->number_of_params() != target->number_of_params() &&
	    target->variable_params() != true) {
	  ECA_TEST_FAILURE("Cloned object has different number of arguments " 
			   "than original object; type name \"" + 
			   obj->name() + "\".");
	}
	else {
	  const OPERATOR* operator_source = dynamic_cast<const OPERATOR*>(obj);
	  OPERATOR* operator_target = dynamic_cast<OPERATOR*>(target);
	  
	  const AUDIO_IO* audioio_source = dynamic_cast<const AUDIO_IO*>(obj);
	  AUDIO_IO* audioio_target = dynamic_cast<AUDIO_IO*>(target);
	  
	  for(int n = 0; n < obj->number_of_params(); n++) {
	    if (obj->get_parameter_name(n + 1) != 
		target->get_parameter_name(n + 1)) {
	      ECA_TEST_FAILURE("Cloned object has different parameter name \"" + 
			       target->get_parameter_name(n + 1) + 
			       "\" than that of original object \"" + 
			       obj->get_parameter_name(n + 1) + 
			       "\"; type name \"" + 
			       obj->name() + "\".");
	    }
	    else {
	      if (operator_source != 0 && operator_target != 0) {
		/* OPERATOR binds parameter type to 'SAMPLE_SPECS::sample_t' (float) */
		
		SAMPLE_SPECS::sample_t diffval = 
		    std::fabs(operator_source->get_parameter(n + 1) -
			      operator_target->get_parameter(n + 1));
		if (diffval > 0.1f) {
		  ECA_TEST_FAILURE("Cloned object has different parameter value \"" + 
				   kvu_numtostr(operator_target->get_parameter(n + 1)) + 
				   "\" than that of the original object \"" + 
				   kvu_numtostr(operator_source->get_parameter(n + 1)) + 
				   "\", diff " +
				   kvu_numtostr(diffval) + "; type name \"" + 
				   operator_source->name() + "\".");
		}
	      }
	      else if (audioio_source != 0 && audioio_target != 0) {
		/* AUDIO_IO binds parameter type to 'std::string' */
		
		if (audioio_source->get_parameter(n + 1) != 
		    audioio_target->get_parameter(n + 1)) {
		  ECA_TEST_FAILURE("Cloned object has different parameter value \"" + 
				   audioio_target->get_parameter(n + 1) + 
				   "\" than that of the original object \"" + 
				   audioio_source->get_parameter(n + 1) + 
				   "\"; type name \"" + 
				   audioio_source->name() + "\".");
		}
	      }
	    }
	  }
	}
      }
      delete target;
    }
    ++p;
  }
}

void ECA_OBJECT_FACTORY_TEST::do_run(void)
{
  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - rt-map");

  ECA_OBJECT_MAP& rt_map = ECA_OBJECT_FACTORY::audio_io_rt_map();
  test_map<AUDIO_IO_DEVICE>(rt_map);

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - nonrt-map");

  ECA_OBJECT_MAP& nonrt_map = ECA_OBJECT_FACTORY::audio_io_nonrt_map();
  test_map<AUDIO_IO>(nonrt_map);

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - chainop-map");

  ECA_OBJECT_MAP& chainop_map = ECA_OBJECT_FACTORY::chain_operator_map();
  test_map<CHAIN_OPERATOR>(chainop_map);

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - LADSPA-map");

  ECA_OBJECT_MAP& ladspa_map = ECA_OBJECT_FACTORY::ladspa_plugin_map();
  test_map<EFFECT_LADSPA>(ladspa_map);

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - LADSPA-id-map");

  ECA_OBJECT_MAP& ladspa_id_map = ECA_OBJECT_FACTORY::ladspa_plugin_id_map();
  test_map<EFFECT_LADSPA>(ladspa_id_map);

  if (ladspa_map.registered_objects().size() != 
      ladspa_id_map.registered_objects().size()) {
      ECA_TEST_FAILURE("LADSPA plugin name and id maps have different number of plugins!");
  }

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factories - preset-map");

  ECA_PRESET_MAP& preset_map = ECA_OBJECT_FACTORY::preset_map();
  test_map<PRESET>(preset_map);

  ECA_LOG_MSG(ECA_LOGGER::info, "libecasound_tester: object factory test done");
}
