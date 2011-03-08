#ifndef INCLUDE_CTRL_SOURCE_H
#define INCLUDE_CTRL_SOURCE_H

#include "eca-audio-position.h"
#include "eca-operator.h"

/**
 * Interface class for implementing control data
 * source objects. 
 */
class CONTROLLER_SOURCE : public OPERATOR {

 public:

  typedef SAMPLE_SPECS::sample_t parameter_t;

  /**
   * Initializes the controller source.
   *
   * This function is called at least once before 
   * the first call to value().
   */
  virtual void init(void) = 0;

  /**
   * Returns the current value for the given
   * position.
   */
  virtual parameter_t value(double pos_secs) = 0; 
  /**
   * Sets an initial value for the controller. 
   * 
   * Controllers that are driven by external sources, can use the 
   * initial value if there is an initial gap in control data. The 
   * standard value range ofs [0,1] should be used.
   *
   * Should be set before the first call to value().
   */
  virtual void set_initial_value(parameter_t arg) = 0; 

  virtual CONTROLLER_SOURCE* clone(void) const = 0;
  virtual CONTROLLER_SOURCE* new_expr(void) const = 0;
};

#endif
