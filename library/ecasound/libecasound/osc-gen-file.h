#ifndef INCLUDED_GENERIC_OSCILLATOR_FILE_H
#define INCLUDED_GENERIC_OSCILLATOR_FILE_H

#include "osc-gen.h"
#include "eca-error.h"

/**
 * Generic oscillator using preset envelopes. 
 * Presets are read from an ascii configuration file.
 */
class GENERIC_OSCILLATOR_FILE : public GENERIC_OSCILLATOR {

 public:

  virtual std::string parameter_names(void) const { return("freq,mode,preset-number"); }
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;
  virtual std::string name(void) const { return("Generic oscillator (preset)"); }

  GENERIC_OSCILLATOR_FILE* clone(void) const  { return new GENERIC_OSCILLATOR_FILE(*this); }
  GENERIC_OSCILLATOR_FILE* new_expr(void) const { return new GENERIC_OSCILLATOR_FILE(*this); }
  GENERIC_OSCILLATOR_FILE (double freq = 0.0, int preset_number = 0);
  virtual ~GENERIC_OSCILLATOR_FILE (void);

 protected:

  void parse_envelope(const std::string& str);

 private:

  int preset_rep;
  void get_oscillator_preset(int preset);
};

#endif
