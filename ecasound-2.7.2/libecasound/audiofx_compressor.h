#ifndef INCLUDED_AUDIOFX_COMPRESSOR_H
#define INCLUDED_AUDIOFX_COMPRESSOR_H

#include <vector>
#include "audiofx_amplitude.h"

/**
 * C++ version of John Dyson's advanced compressor design.
 * @author Kai Vehmanen
 */
class ADVANCED_COMPRESSOR : public EFFECT_AMPLITUDE {

  SAMPLE_ITERATOR_INTERLEAVED iter;

 public:

 static const int NFILT = 12;
 static const int NEFILT = 17;

 public:

  ADVANCED_COMPRESSOR (void) 
    : rlevelsqn(ADVANCED_COMPRESSOR::NFILT), 
      rlevelsqe(ADVANCED_COMPRESSOR::NEFILT) {
    init_values();
    //    map_parameters();
  }
  
  virtual ~ADVANCED_COMPRESSOR (void);

  virtual std::string name(void) const { return("Advanced compressor"); }
  virtual std::string parameter_names(void) const { return("peak-limit-%,release-time-sec,fast-crate,overall-crate"); }

  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual void init(SAMPLE_BUFFER *insample);
  virtual void process(void);
  virtual int output_channels(int i_channels) const { return(2); }

  ADVANCED_COMPRESSOR* clone(void) const { return new ADVANCED_COMPRESSOR(*this); }
  ADVANCED_COMPRESSOR* new_expr(void) const { return new ADVANCED_COMPRESSOR(); }
  ADVANCED_COMPRESSOR (double peak_limit, double release_time, double cfrate, double crate);

 private:

  double rlevelsq0, rlevelsq1;
  double rlevelsq0filter, rlevelsq1filter;
  std::vector<double> rlevelsqn; // [NFILT];
  double rlevelsqefilter;
  std::vector<double> rlevelsqe; // [NEFILT];
  double rlevelsq0ffilter;
  int ndelay; /* delay for rlevelsq0ffilter delay */
  int ndelayptr; /* ptr for the input */
  std::vector<double> rightdelay;
  std::vector<double> leftdelay;
/* Simple gain running average */
  double rgain;
  double rgainfilter;
  double lastrgain;
/* Max fast agc gain, slow agc gain */
  double maxfastgain, maxslowgain;
/* Fast gain compression ratio */
/*	Note that .5 is 2:1, 1.0 is infinity (hard) */
  double fastgaincompressionratio;
  double compressionratio;
/* Max level, target level, floor level */
  double maxlevel, targetlevel, floorlevel;
/* Gainriding gain */
  double rmastergain0filter;
  double rmastergain0;
/* Peak limit gain */
  double rpeakgain0, rpeakgain1, rpeakgainfilter;
  int peaklimitdelay, rpeaklimitdelay;
/* Running total gain */
  double totalgain;
/* Idle gain */
  double npeakgain;
/* Compress enabled */
  int compress;

  double level, levelsq0, levelsq1, levelsqe;
  double gain, qgain, tgain;
  double newright, newleft;
  double efilt;
  double fastgain, slowgain, tslowgain;
  double leveldelta;
  double right, left, rightd, leftd;
  int delayed;
  double nrgain, nlgain, ngain, ngsq;
  double sqrtrpeakgain;
  int i;
  int skipmode;

  double extra_maxlevel;
  int parm;
  double maxgain, mingain;
  int ch;
  double fratio;
  double ratio;
  double releasetime;
  double peakpercent;

  double tnrgain;

  void init_values(void);
  double hardlimit(double value, double knee, double limit);

};

#endif
