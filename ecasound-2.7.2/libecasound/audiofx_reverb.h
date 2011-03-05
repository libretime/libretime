#ifndef INCLUDED_AUDIOFX_REVERB_H
#define INCLUDED_AUDIOFX_REVERB_H

#include <vector>
#include "audiofx_timebased.h"

/**
 * Reverb effect
 *
 * May 2000, Stefan Fendt (C++ version by Kai Vehmanen)
 */
class ADVANCED_REVERB : public EFFECT_TIME_BASED {
 private:

  SAMPLE_ITERATOR_CHANNELS i_channels;
  parameter_t roomsize_rep;
  parameter_t feedback_rep;
  parameter_t wet_rep;

  class CHANNEL_DATA {
  public:
    std::vector<SAMPLE_SPECS::sample_t> buffer;
    std::vector<long int> dpos;
    std::vector<parameter_t> mul;
    long int bufferpos_rep;
    SAMPLE_SPECS::sample_t oldvalue, lpvalue;

    CHANNEL_DATA(void) : buffer(65536, 0.0), dpos(200), mul(200), bufferpos_rep(0) { }
  };
  std::vector<CHANNEL_DATA> cdata;

 public:

  virtual std::string name(void) const { return("Advanced reverb"); }
  virtual std::string parameter_names(void) const { return("Room-size,feedback-%,wet-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);

  ADVANCED_REVERB* clone(void) const { return new ADVANCED_REVERB(*this); }
  ADVANCED_REVERB* new_expr(void) const { return new ADVANCED_REVERB(); }
  ADVANCED_REVERB (parameter_t roomsize = 10.0, parameter_t feedback_percent = 50.0, parameter_t wet_percent = 50.0);
};

#endif
