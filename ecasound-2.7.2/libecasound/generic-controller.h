#ifndef INCLUDED_GENERIC_CONTROLLER_H
#define INCLUDED_GENERIC_CONTROLLER_H

#include "ctrl-source.h"
#include "eca-operator.h"

/**
 * Generic controller class that connects controller sources
 * to objects supporting dynamic parameter control (classes 
 * which inherit DYNAMIC_PARAMETERS).
 * 
 * Related design patterns:
 *     - Decorator (GoF175)
 */
class GENERIC_CONTROLLER : public CONTROLLER_SOURCE {

 public:

  /** @name Constructors and destructors */
  /*@{*/

  GENERIC_CONTROLLER(CONTROLLER_SOURCE* source,
		     OPERATOR* dobj = 0, 
		     int param_id = 0, 
		     double range_low = 0.0, 
		     double range_high = 0.0);

  GENERIC_CONTROLLER* clone(void) const { return(0); }
  GENERIC_CONTROLLER* new_expr(void) const { return(new GENERIC_CONTROLLER(0)); }

  /*@}*/

  /** @name Public functions reimplemented from CONTROLLER_SOURCE */
  /*@{*/

  virtual void init(void);

  /**
   * Returns the current value, scale it and
   * applies it to target objects.
   *
   * @pre is_valid() == true
   */
  virtual parameter_t value(double pos_secs);

  virtual void set_initial_value(parameter_t arg) { }

  /*@}*/

  /** @name Public functions reimplemented from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(source == 0 ? string("") : source->name()); }

  /*@}*/

  /** @name Public functions reimplemented from DYNAMIC_PARAMETERS */
  /*@{*/

  virtual bool variable_params(void) const { return true; }
  virtual std::string parameter_names(void) const { return("param-id,range-low,range-high," +  source->parameter_names()); }
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  /*@}*/

  /** @name Public functions  */
  /*@{*/

  bool is_valid(void) const { return(target != 0 && source != 0); }
  std::string status(void) const;

  void assign_target(OPERATOR* obj);
  void assign_source(CONTROLLER_SOURCE* obj);

  CONTROLLER_SOURCE* source_pointer(void) const { return(source); }
  OPERATOR* target_pointer(void) const { return(target); }

  /*@}*/
 
private:

  OPERATOR* target;
  CONTROLLER_SOURCE* source;

  bool init_called_rep;
  int param_id_rep;
  double rangelow_rep, rangehigh_rep;
  double last_value_pos_rep;
};

#endif
