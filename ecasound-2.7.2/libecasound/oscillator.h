#ifndef INCLUDED_OSCILLATOR_H
#define INCLUDED_OSCILLATOR_H

#include <cmath>
#include <string>

#include "ctrl-source.h"

/**
 * Base class for various oscillators
 *
 * Unlike finite controller sources, oscillators
 * produce new values infinately.
 */
class OSCILLATOR : public CONTROLLER_SOURCE {

 public:

  virtual void set_initial_value(parameter_t arg) {}

  /**
   * Constructor
   * 
   * @param freq Oscillator frequency
   * @param phase Initial phase, multiple of pi
   */
  OSCILLATOR(parameter_t freq = 0, parameter_t initial_phase = 0) { 
    freq_value = freq;
    phase_value = initial_phase * M_PI;
  }

 private:
  
  parameter_t freq_value, phase_value;

 protected:

  parameter_t phase_offset(void) const { return(phase_value); }  
  parameter_t frequency(void) const { return(freq_value); }

  void phase_offset(parameter_t v) { phase_value = v; }
  void frequency(parameter_t v) { freq_value = v; }
};

#endif
