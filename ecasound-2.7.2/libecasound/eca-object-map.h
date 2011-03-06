#ifndef INCLUDED_ECA_OBJECT_MAP_H
#define INCLUDED_ECA_OBJECT_MAP_H

#include <string>
#include <map>
#include <list>

#include "eca-object.h"

/**
 * A virtual base class representing generic object maps.
 *
 * Object maps are used to centralize object creation.
 * To add an object to the map, a prototype instance
 * is registered. Each object is associated with 
 * a keyword and a regex expression that can be used 
 * for object look up and creation.
 *
 * Most parts of the library don't require info about
 * object details. Object maps make it possible to 
 * hide these details completely, and in one place.
 *
 * Related design patterns:
 *     - Prototype (GoF117)
 *     - Factory Method (GoF107)
 *
 * @author Kai Vehmanen
 */
class ECA_OBJECT_MAP {

 public:

  /** @name Constructors and destructors */
  /*@{*/

  ECA_OBJECT_MAP(void);
  virtual ~ECA_OBJECT_MAP(void);

  /*@}*/

  /** @name Object map features */
  /*@{*/
  
  void toggle_case_sensitive_expressions(bool v);
  bool case_sensitive_expressions(void) const;
  
  /*@}*/

  /** @name Object registration */
  /*@{*/

  virtual void register_object(const std::string& keyword, const std::string& expr, ECA_OBJECT* object);
  virtual void unregister_object(const std::string& keyword);

  /*@}*/

  /** @name Object creation using object prototypes instances  */
  /*@{*/

  virtual const ECA_OBJECT* object(const std::string& keyword) const;
  virtual const ECA_OBJECT* object_expr(const string& expr) const;

  /*@}*/

  /** @name Query the object map */
  /*@{*/

  virtual const std::list<std::string>& registered_objects(void) const;
  virtual bool has_keyword(const std::string& keyword) const;
  virtual bool has_object(const ECA_OBJECT* obj) const;
  virtual std::string expr_to_keyword(const std::string& expr) const;
  virtual std::string keyword_to_expr(const std::string& keyword) const;
  virtual std::string object_identifier(const ECA_OBJECT* object) const;

  /*@}*/

 private:

  ECA_OBJECT_MAP(const ECA_OBJECT_MAP&);
  ECA_OBJECT_MAP& operator=(const ECA_OBJECT_MAP&);

  std::list<std::string> object_keywords_rep;
  mutable std::map<std::string, ECA_OBJECT*> object_map;
  mutable std::map<std::string,std::string> object_expr_map;

  bool expr_case_sensitive_rep;
};

#endif
