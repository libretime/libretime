#ifndef INCLUDED_GENERIC_OSCILLATOR_H
#define INCLUDED_GENERIC_OSCILLATOR_H

#include <vector>
#include <string>

#include "oscillator.h"

#include "eca-error.h"

/**
 * Generic oscillator
 *
 * Oscillator value varies according to discrete 
 * envelope points.
 */
class GENERIC_OSCILLATOR : public OSCILLATOR {

 public:

  virtual void init(void);
  virtual parameter_t value(double pos_secs);
  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  std::string name(void) const { return("Generic oscillator"); }

  GENERIC_OSCILLATOR* clone(void) const { return new GENERIC_OSCILLATOR(*this); }
  GENERIC_OSCILLATOR* new_expr(void) const { return new GENERIC_OSCILLATOR(*this); }
  GENERIC_OSCILLATOR(double freq = 0.0f, int mode = 0);
  virtual ~GENERIC_OSCILLATOR (void);

 protected:

  // FIXME: replace this with a function: void set_point(int n);
  std::vector<double> ienvelope_rep;

  void set_start_value(double v) { start_value_rep = v; }
  void set_end_value(double v) { end_value_rep = v; }
  void prepare_envelope(void);
  void update_current_static(void);
  void update_current_linear(void);

private:

  void set_param_count(int n);

  int mode_rep;
  double start_value_rep, end_value_rep;
  double loop_length_rep; // loop length in seconds
  double loop_pos_rep; // current position in seconds
  double next_pos_rep;
  double last_pos_rep;
  double last_global_pos_rep;
  int epairs_rep;
  int eindex_rep;
  int pindex_rep;
  double current_value_rep;
  std::string param_names_rep;
};

#endif
