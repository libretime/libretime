#ifndef INCLUDED_OPERATOR_H
#define INCLUDED_OPERATOR_H

#include "sample-specs.h"
#include "dynamic-object.h"

/**
 * All ecasound objects which can be used as targets
 * for dynamic control, are called operators.
 *
 * @author Kai Vehmanen
 */
class OPERATOR : public DYNAMIC_OBJECT<SAMPLE_SPECS::sample_t> {

 public:

  typedef SAMPLE_SPECS::sample_t parameter_t;

  /**
   * Structure describing one operator parameter. 
   */
  struct PARAM_DESCRIPTION {
    /**
     * A reasonable default value.
     */
    parameter_t default_value;

    /**
     * Parameter description. 
     */
    std::string description;

    /**
     * Is parameter bounded above?
     */
    bool bounded_above;

    /**
     * If 'bounded_above', contains the bound value.
     */
    parameter_t upper_bound;

    /**
     * Is parameter bounded below?
     */
    bool bounded_below;

    /**
     * If 'bounded_below', contains the bound value.
     */
    parameter_t lower_bound;

    /**
     * Whether parameter should be treated as a boolean toggle?
     * Parameter value of less than or equal to zero (x <= 0) should
     * be considered `off' or `false,' and value above zero (x > 0) 
     * should be considered `on' or `true.
     */
    bool toggled;

    /** 
     * Parameter value is best represented as an integer.
     */
    bool integer;

    /** 
     * Value range from lower_bound to upper_bound is best 
     * represented on a  logarithmic scale.
     */
    bool logarithmic;

    /**
     * Parameter value can change during operation. This 
     * makes it possible to have separate parameters for
     * storing processing results.
     */
    bool output;
  };

  /**
   * An optional function for querying parameter descriptions.
   * This is meant primarily for building generic user-interfaces.
   * It's important to note that these values only serve as hints, 
   * they are not meant to be absolute.
   *
   * @param param parameter id
   * @pre param > 0 && param <= number_of_params()
   */
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual OPERATOR* clone(void) const = 0;
  virtual OPERATOR* new_expr(void) const = 0;

  virtual ~OPERATOR (void);
};

#endif
