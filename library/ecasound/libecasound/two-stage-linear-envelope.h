#ifndef INCLUDED_TWO_STAGE_LINEAR_ENVELOPE_H
#define INCLUDED_TWO_STAGE_LINEAR_ENVELOPE_H

#include <string>

#include "ctrl-source.h"

/**
 * Two-stage linear envelope
 */
class TWO_STAGE_LINEAR_ENVELOPE : public CONTROLLER_SOURCE {

 public:

  std::string name(void) const { return("Two-stage linear envelope"); }
  virtual parameter_t value(double pos_secs);
  virtual void set_initial_value(parameter_t arg) {}
  virtual void init(void);

  std::string parameter_names(void) const { return("1st-stage-sec,2nd-stage-sec"); }
  void set_parameter(int param, parameter_t value);
  parameter_t get_parameter(int param) const;

  TWO_STAGE_LINEAR_ENVELOPE(void); 
  TWO_STAGE_LINEAR_ENVELOPE* clone(void) const { return new TWO_STAGE_LINEAR_ENVELOPE(*this); }
  TWO_STAGE_LINEAR_ENVELOPE* new_expr(void) const { return new TWO_STAGE_LINEAR_ENVELOPE(); }

  private:

  parameter_t first_stage_length_rep, second_stage_length_rep;
};

#endif
