#ifndef INCLUDED_AUDIOFX_ENVELOPE_MODULATION_H
#define INCLUDED_AUDIOFX_ENVELOPE_MODULATION_H

#include <string>

#include "samplebuffer_iterators.h"
#include "audiofx.h"

/**
 * Virtual base for envelope modulation effects.
 * @author Rob Coker
 */
class EFFECT_ENV_MOD : public EFFECT_BASE {

 public:
  virtual ~EFFECT_ENV_MOD(void);
};

/**
 * Pulse shaped gate
 * @author Rob Coker
 */
class EFFECT_PULSE_GATE: public EFFECT_ENV_MOD {

  SAMPLE_ITERATOR_INTERLEAVED i;
  parameter_t freq_rep;
  parameter_t on_time_rep;
  long int period_rep;
  long int on_from_rep;
  long int current_rep;

 public:

  virtual std::string name(void) const { return("Pulse Gate"); }
  virtual std::string parameter_names(void) const  { return("freq-Hz,on-time-%"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_PULSE_GATE (parameter_t freq_Hz = 1.0, parameter_t onTime_percent = 50.0);
  virtual ~EFFECT_PULSE_GATE(void);
  EFFECT_PULSE_GATE* clone(void) const { return new EFFECT_PULSE_GATE(*this); }
  EFFECT_PULSE_GATE* new_expr(void) const { return new EFFECT_PULSE_GATE(); }

  /** @name Protected virtual functions to notify about changes 
   *        (Reimplemented from ECA_SAMPLERATE_AWARE) */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/
};

/**
 * Wrapper class for pulse shaped gate providing 
 * a beats-per-minute (bpm) based parameters.
 *
 * @author Kai Vehmanen
 */
class EFFECT_PULSE_GATE_BPM : public EFFECT_ENV_MOD {

  EFFECT_PULSE_GATE pulsegate_rep;

 public:

  virtual std::string name(void) const { return("Pulse gate BPM"); }
  virtual std::string parameter_names(void) const  { return("bpm,on-time-msec"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_PULSE_GATE_BPM (parameter_t bpm = 120.0, parameter_t ontime_percent = 5.0);
  virtual ~EFFECT_PULSE_GATE_BPM(void);
  EFFECT_PULSE_GATE_BPM* clone(void) const { return new EFFECT_PULSE_GATE_BPM(*this); }
  EFFECT_PULSE_GATE_BPM* new_expr(void) const { return new EFFECT_PULSE_GATE_BPM(); }

  /** @name Protected virtual functions to notify about changes 
   *        (Reimplemented from ECA_SAMPLERATE_AWARE) */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/
};

/**
 * Tremolo
 * @author Rob Coker
 */
class EFFECT_TREMOLO: public EFFECT_ENV_MOD {

  SAMPLE_ITERATOR_INTERLEAVED i;
  parameter_t freq;
  parameter_t depth;
  parameter_t currentTime;
  parameter_t incrTime;

 public:

  virtual std::string name(void) const { return("Tremolo"); }
  virtual std::string parameter_names(void) const  { return("bpm,depth-%"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_TREMOLO (parameter_t freq_bpm = 60.0, parameter_t depth_percent = 100.0);
  virtual ~EFFECT_TREMOLO(void);
  EFFECT_TREMOLO* clone(void) const { return new EFFECT_TREMOLO(*this); }
  EFFECT_TREMOLO* new_expr(void) const { return new EFFECT_TREMOLO(); }
};

#endif
