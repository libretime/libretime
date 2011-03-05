#ifndef _AUDIOFX_RC_LOWPASS_FILTER_H
#define _AUDIOFX_RC_LOWPASS_FILTER_H

#include <vector>
#include <deque>

#include "audiofx_filter.h"
#include "samplebuffer_iterators.h"

/**
 * Simulation of an 3rd-order 36dB active RC-lowpass
 *
 * 5th of February 2000 by Stefan Fendt
 * 
 * This is a quite realistic simulation of an analouge 
 * RC-lowpass as used in many old synthesisers. You can
 * increase filter-resonance up to the point were the 
 * filter starts oscillating (without any signal aplied
 * to it...) and this without digital clipping. Knowing 
 * that this was a design flaw (which had technical
 * reasons on real RC-filters) this was implemented in 
 * this simulation, too. I do not know any other 
 * filter-simulation which provides this.
 *
 * @author Stefan Fendt
 */
class EFFECT_RC_LOWPASS_FILTER : public EFFECT_FILTER {

  SAMPLE_ITERATOR_CHANNELS i;
  SAMPLE_SPECS::sample_t output_temp;
  std::vector<SAMPLE_SPECS::sample_t> lp1_old, lp2_old, lp3_old, hp1_old, feedback;
    
  parameter_t cutoff_rep;
  parameter_t resonance_rep;

public:

  virtual std::string name(void) const { return("RC-lowpass filter"); }
  virtual std::string parameter_names(void) const { return("cutoff-freq,resonance"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);

  EFFECT_RC_LOWPASS_FILTER* clone(void) const { return new EFFECT_RC_LOWPASS_FILTER(*this); }
  EFFECT_RC_LOWPASS_FILTER* new_expr(void) const { return new EFFECT_RC_LOWPASS_FILTER(); }
  EFFECT_RC_LOWPASS_FILTER (parameter_t cutoff = 0.25,
			    parameter_t resonance = 1.0);
};

#endif
