#ifndef INCLUDED_AUDIOFX_TIMEBASED_H
#define INCLUDED_AUDIOFX_TIMEBASED_H

#include <vector>
#include <deque>
#include <string>

#include "audiofx.h"
#include "audiofx_filter.h"
#include "osc-sine.h"

typedef std::deque<SAMPLE_SPECS::sample_t> SINGLE_BUFFER;

/**
 * Base class for time-based effects (delays, reverbs, etc).
 */
class EFFECT_TIME_BASED : public EFFECT_BASE {

 public:

  virtual EFFECT_TIME_BASED* clone(void) const = 0;
};

/** 
 * Delay effect
 */
class EFFECT_DELAY : public EFFECT_TIME_BASED {

 private:

  SAMPLE_ITERATOR_CHANNEL l,r;

  parameter_t surround;
  parameter_t dnum;
  parameter_t dtime;
  parameter_t dtime_msec;
  parameter_t mix;
  parameter_t feedback;

  parameter_t laskuri;
  std::vector<std::vector<SINGLE_BUFFER> > buffer;

 public:

  virtual std::string name(void) const { return("Delay"); }
  virtual std::string parameter_names(void) const { return("delay-time-msec,surround-mode,number-of-delays,mix-%,feedback-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);
  virtual int output_channels(int i_channels) const { return(2); }

  parameter_t get_delta_in_samples(void) { return(dnum * dtime); }

  EFFECT_DELAY* clone(void) const { return new EFFECT_DELAY(*this); }
  EFFECT_DELAY* new_expr(void) const { return new EFFECT_DELAY(); }
  EFFECT_DELAY (parameter_t delay_time = 100.0, int surround_mode = 0, int num_of_delays = 1, parameter_t mix_percent = 50.0, parameter_t feedback_percent = 100.0);
};

/** 
 * Multi-tap delay
 */
class EFFECT_MULTITAP_DELAY : public EFFECT_TIME_BASED {

 private:

  SAMPLE_ITERATOR_INTERLEAVED i;

  parameter_t surround;
  parameter_t mix;

  parameter_t dtime_msec;
  long int dtime, dnum;

  std::vector<long int> delay_index;
  std::vector<std::vector<bool> > filled;
  std::vector<std::vector<SAMPLE_SPECS::sample_t> > buffer;

 public:

  virtual std::string name(void) const { return("Multitap delay"); }
  virtual std::string parameter_names(void) const { return("delay-time-msec,number-of-delays,mix-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);

  EFFECT_MULTITAP_DELAY* clone(void) const { return new EFFECT_MULTITAP_DELAY(*this); }
  EFFECT_MULTITAP_DELAY* new_expr(void) const { return new EFFECT_MULTITAP_DELAY(); }
  EFFECT_MULTITAP_DELAY (parameter_t delay_time = 100.0, int num_of_delays = 1, parameter_t mix_percent = 50.0);
};

/**
 * Transforms a mono signal to stereo using a panned delay signal.
 * Suitable delays values range from 1 to 40 milliseconds. 
 */
class EFFECT_FAKE_STEREO : public EFFECT_TIME_BASED {

  std::vector<std::deque<SAMPLE_SPECS::sample_t> > buffer;
  SAMPLE_ITERATOR_CHANNEL l,r;
  parameter_t dtime;
  parameter_t dtime_msec;

 public:

  std::string name(void) const { return("Fake stereo"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual std::string parameter_names(void) const { return("delay-time-msec"); }
  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);
  virtual int output_channels(int i_channels) const { return(2); }

  EFFECT_FAKE_STEREO* clone(void) const { return new EFFECT_FAKE_STEREO(*this); }
  EFFECT_FAKE_STEREO* new_expr(void) const { return new EFFECT_FAKE_STEREO(); }
  EFFECT_FAKE_STEREO (parameter_t delay_time = 20.0);
};

/**
 * Simple reverb (based on a iir comb filter)
 */
class EFFECT_REVERB : public EFFECT_TIME_BASED {

 private:
    
  std::vector<std::deque<SAMPLE_SPECS::sample_t>  > buffer;
  SAMPLE_ITERATOR_CHANNEL l,r;

  parameter_t surround;
  parameter_t feedback;
  parameter_t dtime;
  parameter_t dtime_msec;

 public:

  virtual std::string name(void) const { return("Reverb"); }
  virtual std::string parameter_names(void) const { return("delay-time,surround-mode,feedback-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);
  virtual int output_channels(int i_channels) const { return(2); }

  parameter_t get_delta_in_samples(void) { return(dtime); }

  EFFECT_REVERB* clone(void) const { return new EFFECT_REVERB(*this); }
  EFFECT_REVERB* new_expr(void) const { return new EFFECT_REVERB(); }
  EFFECT_REVERB (parameter_t delay_time = 20.0, int surround_mode = 0, parameter_t feedback_percent = 50.0);
};

/**
 * Base class for modulating delay effects
 */
class EFFECT_MODULATING_DELAY : public EFFECT_TIME_BASED {

 protected:

  std::vector<std::vector<SAMPLE_SPECS::sample_t> > buffer;
  SAMPLE_ITERATOR_CHANNELS i;
  double advance_len_secs_rep, lfo_pos_secs_rep;
  long int dtime;
  parameter_t dtime_msec;
  parameter_t feedback, vartime;
  SINE_OSCILLATOR lfo;
  std::vector<long int> delay_index;
  std::vector<bool> filled;

 public:

  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* insample);
  virtual void process(void);

  EFFECT_MODULATING_DELAY(parameter_t delay_time = 2.0,
			  long int vartime_in_samples = 20,
			  parameter_t feedback_percent = 50.0,
			  parameter_t lfo_freq = 0.4);
};

/**
 * Flanger
 */
class EFFECT_FLANGER : public EFFECT_MODULATING_DELAY {

 public:

  virtual std::string name(void) const { return("Flanger"); }
  virtual std::string parameter_names(void) const { return("delay-time-msec,variance-time-samples,feedback-%,lfo-freq"); }

  void process(void);

  EFFECT_FLANGER* clone(void) const { return new EFFECT_FLANGER(*this); }
  EFFECT_FLANGER* new_expr(void) const { return new EFFECT_FLANGER(); }
};

/**
 * Chorus
 */
class EFFECT_CHORUS : public EFFECT_MODULATING_DELAY {

 public:

  virtual std::string name(void) const { return("Chorus"); }
  virtual std::string parameter_names(void) const { return("delay-time-msec,variance-time-samples,feedback-%,lfo-freq"); }

  void process(void);

  EFFECT_CHORUS* clone(void) const { return new EFFECT_CHORUS(*this); }
  EFFECT_CHORUS* new_expr(void) const { return new EFFECT_CHORUS(); }
};

/**
 * Phaser
 */
class EFFECT_PHASER : public EFFECT_MODULATING_DELAY {

 public:

  virtual std::string name(void) const { return("Phaser"); }
  virtual std::string parameter_names(void) const { return("delay-time-msec,variance-time-samples,feedback-%,lfo-freq"); }

  void process(void);

  EFFECT_PHASER* clone(void) const { return new EFFECT_PHASER(*this); }
  EFFECT_PHASER* new_expr(void) const { return new EFFECT_PHASER(); }
};

#endif
