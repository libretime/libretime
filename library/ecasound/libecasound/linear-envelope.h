#ifndef INCLUDED_LINEAR_ENVELOPE_H
#define INCLUDED_LINEAR_ENVELOPE_H

#include <string>

#include "ctrl-source.h"

/**
 * Linear envelope
 */
class LINEAR_ENVELOPE : public CONTROLLER_SOURCE {

 public:

  LINEAR_ENVELOPE(void);
  virtual ~LINEAR_ENVELOPE(void);

  LINEAR_ENVELOPE* clone(void) const { return new LINEAR_ENVELOPE(*this); }
  LINEAR_ENVELOPE* new_expr(void) const { return new LINEAR_ENVELOPE(*this); }

  std::string name(void) const { return("Linear envelope"); }

  virtual void init(void);
  virtual parameter_t value(double pos_secs);
  virtual void set_initial_value(parameter_t arg) {}

  std::string parameter_names(void) const { return("length-sec"); }
  void set_parameter(int param, parameter_t value);
  parameter_t get_parameter(int param) const;

private:
  
  parameter_t stages_len_rep;
};

#endif

