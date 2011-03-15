#ifndef INCLUDED_ECA_PRESET_MAP_H
#define INCLUDED_ECA_PRESET_MAP_H

#include <list>
#include <string>

#include "preset.h"
#include "eca-object-map.h"
#include "resource-file.h"

class ECA_OBJECT;

/**
 * Dynamic register for storing effect presets
 *
 * @see ECA_OBJECT_MAP
 *
 * @author Kai Vehmanen
 */
class ECA_PRESET_MAP : public ECA_OBJECT_MAP {

 public:

  /** @name Constructors and destructors */
  /*@{*/

  ECA_PRESET_MAP(void);
  virtual ~ECA_PRESET_MAP(void);

  /*@}*/

  /** @name Object registration */
  /*@{*/

  virtual void register_object(const std::string& keyword, const std::string& matchstr, ECA_OBJECT* object);
  virtual void unregister_object(const std::string& keyword);

  /*@}*/

  /** @name Object creation using object prototypes instances  */
  /*@{*/

  virtual const ECA_OBJECT* object(const std::string& keyword) const;
  virtual const ECA_OBJECT* object_expr(const string& expr) const;

  /*@}*/

  /** @name Query the object map */
  /*@{*/

  virtual bool has_keyword(const std::string& keyword) const;
  virtual const std::list<std::string>& registered_objects(void) const;

  /*@}*/

 private:

  std::list<std::string> preset_keywords_rep;
  void load_preset_file(const std::string& fname);
};

#endif
