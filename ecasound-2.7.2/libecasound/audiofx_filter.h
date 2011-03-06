#ifndef INCLUDED_AUDIOFX_FILTER_H
#define INCLUDED_AUDIOFX_FILTER_H

#include <deque>
#include <string>
#include <vector>

#include "audiofx.h"
#include "samplebuffer_iterators.h"

/**
 * Virtual base for filter effects.
 * @author Kai Vehmanen
 */
class EFFECT_FILTER : public EFFECT_BASE {

 public:
  virtual ~EFFECT_FILTER(void);
};

/**
 * Base class for butterworth filter effects.
 * 
 * Based on SPKit Butterworth algorithms. 
 * (for more info, see http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_BW_FILTER : public EFFECT_FILTER {

private:
  
  SAMPLE_SPECS::sample_t outputSample;
  SAMPLE_ITERATOR_CHANNELS i;

  std::vector<std::vector<SAMPLE_SPECS::sample_t> > sin;
  std::vector<std::vector<SAMPLE_SPECS::sample_t> > sout;

  void init_values(void);

 protected:

  std::vector<SAMPLE_SPECS::sample_t> a;
  std::vector<SAMPLE_SPECS::sample_t> b;
   
public:

  void process_notused(SAMPLE_BUFFER* sbuf);
  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  virtual EFFECT_BW_FILTER* clone(void) const = 0;

  //  EFFECT_BW_FILTER(void) : sin(2), sout(2), a(3), b(2) {

  EFFECT_BW_FILTER(void) : a(3), b(2) {
    init_values();
  }
};

/**
 * Bandpass filter
 *  
 * Based on SPKit Butterworth algorithms
 * (for more info, see http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_BANDPASS: public EFFECT_BW_FILTER {

private:
    
  parameter_t center;
  parameter_t width;

  parameter_t C;
  parameter_t D;

public:
  
  virtual std::string name(void) const { return("Bandpass filter"); }
  virtual std::string parameter_names(void) const { return("center-freq,width"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  EFFECT_BANDPASS* clone(void) const  { return new EFFECT_BANDPASS(*this); }
  EFFECT_BANDPASS* new_expr(void) const { return new EFFECT_BANDPASS(); }
  EFFECT_BANDPASS (parameter_t centerf = 1000.0, parameter_t width = 1000.0);
};

/**
 * Bandreject filter
 *  
 * Based on SPKit Butterworth algorithms
 * (for more info, see http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_BANDREJECT: public EFFECT_BW_FILTER {

private:
    
  parameter_t center;
  parameter_t width;

  parameter_t C;
  parameter_t D;

public:

  virtual std::string name(void) const { return("Bandreject filter"); }
  virtual std::string parameter_names(void) const { return("center-freq,width"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  EFFECT_BANDREJECT* clone(void) const { return new EFFECT_BANDREJECT(*this); }
  EFFECT_BANDREJECT* new_expr(void) const { return new EFFECT_BANDREJECT(); }
  EFFECT_BANDREJECT (parameter_t centerf = 1000.0, parameter_t width = 1000.0);
};

/**
 * Highpass filter
 *
 * Based on SPKit Butterworth algorithms
 * (for more info, see http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_HIGHPASS : public EFFECT_BW_FILTER {
  
 private:

  parameter_t cutOffFreq;
  parameter_t C;
    
public:
    
  virtual std::string name(void) const { return("Highpass filter"); }
  virtual std::string parameter_names(void) const { return("cutoff-freq"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  EFFECT_HIGHPASS* clone(void) const { return new EFFECT_HIGHPASS(*this); }
  EFFECT_HIGHPASS* new_expr(void) const { return new EFFECT_HIGHPASS(); }
  EFFECT_HIGHPASS (parameter_t cutoff = 1000.0);
};

/**
 * Allpass filter
 */
class EFFECT_ALLPASS_FILTER : public EFFECT_FILTER {

  std::vector<std::deque<SAMPLE_SPECS::sample_t> > inbuf, outbuf;
  SAMPLE_ITERATOR_CHANNELS i;

  parameter_t feedback_gain;
  parameter_t D;

public:

  virtual std::string name(void) const { return("Allpass filter"); }
  virtual std::string parameter_names(void) const { return("delay-samples,feedback-%"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_ALLPASS_FILTER* clone(void) const { return new EFFECT_ALLPASS_FILTER(*this); }  
  EFFECT_ALLPASS_FILTER* new_expr(void) const { return new EFFECT_ALLPASS_FILTER(); }
  EFFECT_ALLPASS_FILTER (void);
};


/**
 * Comb filter
 *
 * The basic theory behind this can be found from Ken Steiglitz's book 
 * "A digital signal processing primer", page 103.
 */
class EFFECT_COMB_FILTER : public EFFECT_FILTER {

  std::vector<std::deque<SAMPLE_SPECS::sample_t> > buffer;
  std::vector<SAMPLE_SPECS::sample_t> temp;
  SAMPLE_ITERATOR_CHANNELS i;

  parameter_t C;
  parameter_t D;

public:

  virtual std::string name(void) const { return("Comb filter"); }
  virtual std::string parameter_names(void) const { return("delay-samples,radius"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_COMB_FILTER* clone(void) const { return new EFFECT_COMB_FILTER(*this); }  
  EFFECT_COMB_FILTER* new_expr(void) const { return new EFFECT_COMB_FILTER(); }
  EFFECT_COMB_FILTER (int delay_in_samples = 1, parameter_t constant = 1.0);
};

/**
 * Inverse comb filter
 *
 * The basic theory behind this can be found from Ken Steiglitz's book 
 * "A digital signal processing primer", page 77.
 */
class EFFECT_INVERSE_COMB_FILTER : public EFFECT_FILTER {

  std::vector<parameter_t> laskuri;
  std::vector<std::deque<SAMPLE_SPECS::sample_t> > buffer;
  std::vector<SAMPLE_SPECS::sample_t> temp;
  SAMPLE_ITERATOR_CHANNELS i;

  parameter_t C;
  parameter_t D;

public:

  virtual std::string name(void) const { return("Inverse comb filter"); }
  virtual std::string parameter_names(void) const { return("delay-samples,radius"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_INVERSE_COMB_FILTER* clone(void) const { return new EFFECT_INVERSE_COMB_FILTER(*this); }  
  EFFECT_INVERSE_COMB_FILTER* new_expr(void) const { return new EFFECT_INVERSE_COMB_FILTER(); }
  EFFECT_INVERSE_COMB_FILTER (int delay_in_samples = 10, parameter_t constant = 1.0);
};

/**
 * Lowpass filter
 *  
 * Based on SPKit Butterworth algorithms
 * (for more info, see http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_LOWPASS: public EFFECT_BW_FILTER {

private:

  parameter_t cutOffFreq;

  parameter_t C;

public:

  virtual std::string name(void) const { return("Lowpass filter"); }
  virtual std::string parameter_names(void) const { return("cutoff-freq"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  void set_cutoff(parameter_t value, long int srate);

  EFFECT_LOWPASS* clone(void) const { return new EFFECT_LOWPASS(*this); }  
  EFFECT_LOWPASS* new_expr(void) const { return new EFFECT_LOWPASS(); }
  EFFECT_LOWPASS (parameter_t cutoff = 1000.0);
};

/**
 * A simple lowpass filter
 *                                              
 *   Algorithm:  1nd order filter.             
 *   From Fugue source code:                   
 *                                             
 *    output[N] = input[N] * A + input[N-1] * B
 *                                             
 *    A = 2.0 * pi * center                    
 *    B = exp(-A / frequency)
 */                                            
class EFFECT_LOWPASS_SIMPLE : public EFFECT_FILTER {

private:

  parameter_t cutOffFreq;
  parameter_t A, B;
  std::vector<SAMPLE_SPECS::sample_t> outhist, tempin, temphist;
  SAMPLE_ITERATOR_CHANNELS i;

public:

  virtual std::string name(void) const { return("Simple lowpass filter"); }
  virtual std::string parameter_names(void) const { return("cutoff-freq"); }

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  EFFECT_LOWPASS_SIMPLE* clone(void) const { return new EFFECT_LOWPASS_SIMPLE(*this); }
  EFFECT_LOWPASS_SIMPLE* new_expr(void) const { return new EFFECT_LOWPASS_SIMPLE(); }
  EFFECT_LOWPASS_SIMPLE (parameter_t cutoff = 1000.0);
};

/**
 * Resonant bandpass filter
 */
class EFFECT_RESONANT_BANDPASS : public EFFECT_FILTER {

private:

  std::vector<SAMPLE_SPECS::sample_t> outhist1, outhist2;
  
  parameter_t center;
  parameter_t width;
  
  parameter_t a, b, c, R;
  parameter_t pole_angle;

  SAMPLE_ITERATOR_CHANNELS i;

public:

  virtual std::string name(void) const { return("Resonant bandpass filter"); }
  virtual std::string parameter_names(void) const { return("center-freq,width"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_RESONANT_BANDPASS* clone(void) const { return new EFFECT_RESONANT_BANDPASS(*this); }  
  EFFECT_RESONANT_BANDPASS* new_expr(void) const { return new EFFECT_RESONANT_BANDPASS(); }  
  EFFECT_RESONANT_BANDPASS (parameter_t centerf = 1000.0, parameter_t width = 1000.0);
};

/**
 * Resonant lowpass filter
 *
 * Algorithm is based on a sample filter-routine (iir_filter) posted to comp.dsp.
 */
class EFFECT_RESONANT_LOWPASS : public EFFECT_FILTER {

  SAMPLE_ITERATOR_CHANNELS i;
    
  std::vector<SAMPLE_SPECS::sample_t> outhist0, outhist1, outhist2, outhist3;
  std::vector<SAMPLE_SPECS::sample_t> newhist0, newhist1;
    
  class TRIPLE_COEFS {
  public:
    parameter_t a0, a1, a2;       // numerator coefficients
    parameter_t b0, b1, b2;       // denominator coefficients
  };

  class FILTER_COEFS {
  public:
    parameter_t A, B, C, D;       // filter coefficients
  };
    
  std::vector<TRIPLE_COEFS> ProtoCoef;         // Filter prototype coefficients,
                                          // for each filter section
  std::vector<FILTER_COEFS> Coef;
    
  parameter_t cutoff, Q, gain, gain_orig;
  parameter_t pi;
  parameter_t laskuri;

  parameter_t ad, bd, wp;      // for szxform()

  void szxform(int section);
  void refresh_values(void);

public:

  virtual std::string name(void) const { return("Resonant lowpass filter"); }
  virtual std::string parameter_names(void) const { return("cutoff-freq,resonance,gain"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_RESONANT_LOWPASS* clone(void) const { return new EFFECT_RESONANT_LOWPASS(*this); }  
  EFFECT_RESONANT_LOWPASS* new_expr(void) const { return new EFFECT_RESONANT_LOWPASS(); }  
  EFFECT_RESONANT_LOWPASS (parameter_t cutoff = 1000.0,
			   parameter_t resonance = 1.0,
			   parameter_t gain = 1.0);
};

/**
 * Resonating bandpass filter
 *
 * Based on a second order all-pole (IIR) band-pass filter from SPKit 
 * (for more info, see: http://www.music.helsinki.fi/research/spkit)
 */
class EFFECT_RESONATOR : public EFFECT_FILTER {

private:

  SAMPLE_ITERATOR_CHANNELS i;
    
  parameter_t center;
  parameter_t width;

  std::vector<SAMPLE_SPECS::sample_t> cona;
  std::vector<SAMPLE_SPECS::sample_t> conb;

  std::vector<SAMPLE_SPECS::sample_t> saout0, saout1;

public:

  virtual std::string name(void) const { return("Resonator filter"); }
  virtual std::string parameter_names(void) const { return("center-freq,width"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_RESONATOR* clone(void) const { return new EFFECT_RESONATOR(*this); }
  EFFECT_RESONATOR* new_expr(void) const { return new EFFECT_RESONATOR(); }  
  EFFECT_RESONATOR (parameter_t center = 1000.0, parameter_t width = 1000.0);
};

#endif
