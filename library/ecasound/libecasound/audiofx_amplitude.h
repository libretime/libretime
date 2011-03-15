#ifndef INCLUDED_AUDIOFX_AMPLITUDE_H
#define INCLUDED_AUDIOFX_AMPLITUDE_H

#include <vector>
#include <string>

#include "samplebuffer_iterators.h"
#include "audiofx.h"

/**
 * Virtual base for amplitude effects and dynamic processors.
 * @author Kai Vehmanen
 */
class EFFECT_AMPLITUDE : public EFFECT_BASE {

 public:

  static parameter_t db_to_linear(parameter_t value);

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void release(void);
  virtual ~EFFECT_AMPLITUDE(void);

protected:

  SAMPLE_BUFFER *cur_sbuf_repp;

};

#include "audiofx_compressor.h"

/**
 * Amplifier for adjusting signal level (linear)
 * @author Kai Vehmanen
 */
class EFFECT_AMPLIFY: public EFFECT_AMPLITUDE {

  parameter_t gain_rep;
  SAMPLE_ITERATOR i;
  SAMPLE_BUFFER* sbuf_repp;

 public:

  virtual std::string name(void) const { return("Amplify"); }
  virtual std::string parameter_names(void) const  { return("amp-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void release(void);
  virtual void process(void);
  virtual void process_ref(void);

  EFFECT_AMPLIFY (parameter_t multiplier_percent = 100.0);
  virtual ~EFFECT_AMPLIFY(void);
  EFFECT_AMPLIFY* clone(void) const { return new EFFECT_AMPLIFY(*this); }
  EFFECT_AMPLIFY* new_expr(void) const { return new EFFECT_AMPLIFY(); }
};

/**
 * Amplifier for adjusting signal level (dB)
 * @author Kai Vehmanen
 */
class EFFECT_AMPLIFY_DB: public EFFECT_AMPLITUDE {
 
 private:

  parameter_t gain_rep;
  parameter_t gain_db_rep; 
  int channel_rep;
  SAMPLE_BUFFER *sbuf_repp;
  SAMPLE_ITERATOR_CHANNEL i_ch;
  SAMPLE_ITERATOR i_all;

 public:

  virtual std::string name(void) const { return("Amplify (dB)"); }
  virtual std::string parameter_names(void) const  { return("gain-db,channel"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void release(void);
  virtual void process(void);
  virtual void process_ref(void);

  virtual int output_channels(int i_channels) const;

  EFFECT_AMPLIFY_DB(parameter_t gain = 0.0f, int channel = 0);
  virtual ~EFFECT_AMPLIFY_DB(void);
  EFFECT_AMPLIFY_DB* clone(void) const { return new EFFECT_AMPLIFY_DB(*this); }
  EFFECT_AMPLIFY_DB* new_expr(void) const { return new EFFECT_AMPLIFY_DB(); }
};

/**
 * Amplifier with clip control.
 * @author Kai Vehmanen
 */
class EFFECT_AMPLIFY_CLIPCOUNT : public EFFECT_AMPLITUDE {

  parameter_t gain;
  int nm, num_of_clipped, maxnum_of_clipped;
  SAMPLE_ITERATOR i;

 public:

  virtual std::string name(void) const { return("Amplify with clipping control"); }
  virtual std::string parameter_names(void) const { return("amp-%,max-clipped-samples"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_AMPLIFY_CLIPCOUNT* new_expr(void) const { return new EFFECT_AMPLIFY_CLIPCOUNT(); }
  EFFECT_AMPLIFY_CLIPCOUNT* clone(void) const { return new EFFECT_AMPLIFY_CLIPCOUNT(*this); }
  EFFECT_AMPLIFY_CLIPCOUNT (parameter_t multiplier_percent = 100.0, int max_clipped = 0);
};

/**
 * Channel amplifier
 * @author Kai Vehmanen
 */
class EFFECT_AMPLIFY_CHANNEL: public EFFECT_AMPLITUDE {

  parameter_t gain;
  int channel_rep;
  SAMPLE_ITERATOR_CHANNEL i;

 public:

  virtual std::string name(void) const { return("Channel amplify"); }
  virtual std::string parameter_names(void) const  { return("amp-%,channel"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual int output_channels(int i_channels) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);
  virtual void process_ref(void);

  EFFECT_AMPLIFY_CHANNEL* clone(void) const { return new EFFECT_AMPLIFY_CHANNEL(*this); }
  EFFECT_AMPLIFY_CHANNEL* new_expr(void) const { return new EFFECT_AMPLIFY_CHANNEL(); }
  EFFECT_AMPLIFY_CHANNEL (parameter_t multiplier_percent = 100.0, int channel = 1);
};

/**
 * Limiter effect
 * @author Kai Vehmanen
 */
class EFFECT_LIMITER: public EFFECT_AMPLITUDE {

  parameter_t limit_rep;
  SAMPLE_ITERATOR i;

 public:

  virtual std::string name(void) const { return("Limiter"); }
  virtual std::string parameter_names(void) const  { return("limit-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_LIMITER (parameter_t multiplier_percent = 100.0);
  virtual ~EFFECT_LIMITER(void);
  EFFECT_LIMITER* clone(void) const { return new EFFECT_LIMITER(*this); }
  EFFECT_LIMITER* new_expr(void) const { return new EFFECT_LIMITER(); }
};

/**
 * Dynamic compressor.
 * @author Kai Vehmanen
 */
class EFFECT_COMPRESS : public EFFECT_AMPLITUDE {

  parameter_t crate;
  parameter_t threshold;
  SAMPLE_ITERATOR_CHANNELS i;

  parameter_t delta, ratio, new_value;
  bool first_time;

  std::vector<SAMPLE_SPECS::sample_t> lastin, lastout;

 public:

  virtual std::string name(void) const { return("Compressor"); }
  virtual std::string parameter_names(void) const  { return("compression-rate-dB,threshold-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_COMPRESS* clone(void) const { return new EFFECT_COMPRESS(*this); }
  EFFECT_COMPRESS* new_expr(void) const { return new EFFECT_COMPRESS(); }
  EFFECT_COMPRESS (const EFFECT_COMPRESS& x);
  EFFECT_COMPRESS (parameter_t compress_rate = 1.0, parameter_t thold = 10.0);
};

/**
 * Noise gate with attack and release
 * @author Kai Vehmanen
 */
class EFFECT_NOISEGATE : public EFFECT_AMPLITUDE {

  SAMPLE_ITERATOR_CHANNELS i;

  parameter_t th_level;
  parameter_t th_time;
  parameter_t atime, htime, rtime;
  
  std::vector<parameter_t> th_time_lask;
  std::vector<parameter_t> attack_lask;
  std::vector<parameter_t> hold_lask;
  std::vector<parameter_t> release_lask;
  std::vector<parameter_t> gain;

  enum { ng_waiting, ng_attacking, ng_active, ng_holding, ng_releasing };

  std::vector<int> ng_status;
  
 public:
  
  virtual std::string name(void) const { return("Noisegate"); }
  virtual std::string description(void) const { return("Noise gate with attack and release."); }
  virtual std::string parameter_names(void) const {
    return("threshold-level-%,pre-hold-time-msec,attack-time-msec,post-hold-time-msec,release-time-msec");
  }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_NOISEGATE* clone(void) const { return new EFFECT_NOISEGATE(*this); }
  EFFECT_NOISEGATE* new_expr(void) const { return new EFFECT_NOISEGATE(); }
  EFFECT_NOISEGATE (parameter_t thlevel_percent = 100.0, 
		    parameter_t thtime = 50.0, 
		    parameter_t atime = 50.0, 
		    parameter_t htime = 50.0, 
		    parameter_t rtime = 50.0);
};

/**
 * Panning effect for controlling the stereo image.
 * @author Kai Vehmanen
 */
class EFFECT_NORMAL_PAN : public EFFECT_AMPLITUDE {

private:

  SAMPLE_ITERATOR_CHANNEL i;

  parameter_t right_percent_rep;
  parameter_t l_gain, r_gain;
  
public:

  virtual std::string name(void) const { return("Normal pan"); }
  virtual std::string description(void) const { return("Panning effect for controlling the stereo image."); }
  virtual std::string parameter_names(void) const { return("right-%"); }
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  virtual int output_channels(int i_channels) const { return(2); }
    
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);
  virtual void process_ref(void);
    
  EFFECT_NORMAL_PAN* clone(void) const { return new EFFECT_NORMAL_PAN(*this); }
  EFFECT_NORMAL_PAN* new_expr(void) const { return new EFFECT_NORMAL_PAN(); }
  EFFECT_NORMAL_PAN(parameter_t right_percent = 50.0);
};

#endif
