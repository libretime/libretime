#ifndef INCLUDED_DYNAMIC_PARAMETERS_H
#define INCLUDED_DYNAMIC_PARAMETERS_H

#include <string>
#include <kvu_utils.h>

/**
 * Virtual template class that provides a system for dynamically 
 * controlling a set of parameters. Supports getting and setting
 * parameter values, verbose parameter names, etc.
 *
 * @author Kai Vehmanen
 */
template<class T>
class DYNAMIC_PARAMETERS {

 public:

  typedef T parameter_t;

 public:

  virtual ~DYNAMIC_PARAMETERS (void) {}

  /**
   * Gets the total number of of parameters.
   */
  int number_of_params(void) const { return kvu_get_number_of_arguments(parameter_names()); }

  /**
   * Whether parameters (and number_of_params()) may be added
   * during object life-cycle.
   */
  virtual bool variable_params(void) const { return false; } 

  /**
   * Gets name of parameter with index 'id'.
   * @param id parameter id number
   * 
   */
  std::string get_parameter_name(int id) const { return(kvu_get_argument_number(id, parameter_names())); }

  /**
   * A comma-separated list of parameters names. Derived classes 
   * must implement this.
   */
  virtual std::string parameter_names(void) const = 0;

  /**
   * Sets the parameter value. Implementations should be able to
   * handle arbitrary values of 'value'. Argument validity 
   * can be tested by a combination of set_parameter() and 
   * get_parameter() calls. Parameter value is valid, if 
   * get_parameter() returns it without changes.
   *
   * @param param parameter id, require: param > 0
   * @param value new value
   */
  virtual void set_parameter(int param, T value) = 0;

  /**
   * Get parameter value
   *
   * @param param parameter id, require: param > 0
   */
  virtual T get_parameter(int param) const = 0;
};

#endif
