#ifndef INCLUDED_DYNAMIC_OBJECT_H
#define INCLUDED_DYNAMIC_OBJECT_H

#include "dynamic-parameters.h"
#include "eca-object.h"

/**
 * Virtual class for objects supporting dynamic parameter
 * control.
 *
 * Related design patterns:
 *     - Factory Method / Virtual Constructor (GoF107)
 *
 * @author Kai Vehmanen
 */
template<class T>
class DYNAMIC_OBJECT : public DYNAMIC_PARAMETERS<T>,
                       public ECA_OBJECT {

 public:

  virtual ~DYNAMIC_OBJECT (void) {}

  /**
   * Virtual method that clones the current object and returns 
   * a pointer to it. This must be implemented by all subclasses!
   */
  virtual DYNAMIC_OBJECT<T>* clone(void) const = 0;

  /**
   * Virtual method that creates a new object of current type.
   * This must be implemented by all subclasses!
   */
  virtual DYNAMIC_OBJECT<T>* new_expr(void) const = 0;
};

#endif
