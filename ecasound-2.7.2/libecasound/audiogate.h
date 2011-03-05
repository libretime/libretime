#ifndef INCLUDED_AUDIO_GATE_H
#define INCLUDED_AUDIO_GATE_H

#include <string>

#include "eca-chainop.h"
#include "eca-samplerate-aware.h"

class SAMPLE_BUFFER;

/**
 * Interface to gate effects. Gate processes sample data, but
 * doesn't modify it. Gate is either open or closed.
 */
class GATE_BASE : public CHAIN_OPERATOR {

private:

  bool gate_open;
  SAMPLE_BUFFER* target;
  
protected:

  void close_gate(void) { gate_open = false; }
  void open_gate(void) { gate_open = true; }
 
public:

  inline bool is_open(void) const { return(gate_open); }
  virtual void init(SAMPLE_BUFFER* sbuf);
  virtual void process(void);
  virtual void analyze(SAMPLE_BUFFER* sbuf) = 0;

  virtual GATE_BASE* clone(void) const = 0;   
  virtual ~GATE_BASE(void);

  GATE_BASE(void) { close_gate(); }
};

/**
 * A time crop gate. Initially the gate is closed, but is opened after 
 * 'open_at' seconds has elapsed. Gate remains open for 
 * 'duration' seconds. If 'duration' is 0, gate will stay open
 * forever.
 */
class TIME_CROP_GATE : public GATE_BASE,
		       public ECA_SAMPLERATE_AWARE
{

public:

  // Functions returning info about effect and its parameters.
  // ---
  parameter_t get_parameter(int param) const;
  void set_parameter(int param, parameter_t value);

  std::string name(void) const { return("Time crop gate"); }

  std::string parameter_names(void) const { return("open-at-sec,duration-sec"); }

  void analyze(SAMPLE_BUFFER* insample);

  /** @name Functions reimplemented from ECA_SAMPLERATE_AWARE */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value);

  /*@}*/

  TIME_CROP_GATE* clone(void) const { return new TIME_CROP_GATE(*this); }
  TIME_CROP_GATE* new_expr(void) const { return new TIME_CROP_GATE(); }
  TIME_CROP_GATE (parameter_t open_at, parameter_t duration);
  TIME_CROP_GATE (void) : position_in_samples_rep(0), begtime_rep(0.0), durtime_rep(0.0) {
    close_gate();
  }

private:

  SAMPLE_SPECS::sample_pos_t position_in_samples_rep;
  parameter_t begtime_rep, durtime_rep; 
};

/**
 * A threshold open gate. When the average volume goes 
 * over 'threshold_openlevel', gate is opened. In the 
 * same way, when the average volume drops below 
 * 'threshold_closelevel', gate is closed. Either 
 * peak or RMS level is used for calculating average 
 * volume. The thresholds are given in percents. Unlike
 * noise gates, threshold gate is opened and closed 
 * only once. 
 */
class THRESHOLD_GATE : public GATE_BASE {

public:

  // Functions returning info about effect and its parameters.
  // ---
  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual void init(SAMPLE_BUFFER* sbuf);

  virtual std::string name(void) const { return("Threshold gate"); }

  virtual std::string parameter_names(void) const { return("threshold-openlevel-%,threshold-closelevel-%,rms-enabled,reopen-count"); }

  virtual void analyze(SAMPLE_BUFFER* insample);

  THRESHOLD_GATE* clone(void) const { return new THRESHOLD_GATE(*this); }
  THRESHOLD_GATE* new_expr(void) const { return new THRESHOLD_GATE(); }
  THRESHOLD_GATE (parameter_t threshold_openlevel, parameter_t
		  threshold_closelevel,  bool use_rms = false);
  THRESHOLD_GATE (void) 
    : openlevel_rep(0.0), closelevel_rep(0.0), reopen_count_param_rep(0), reopens_left_rep(0), rms_rep(false), is_opened_rep(false), is_closed_rep(false) { }
  
private:
  
  parameter_t openlevel_rep, closelevel_rep, avolume_rep;
  int reopen_count_param_rep, reopens_left_rep;
  bool rms_rep;
  bool is_opened_rep, is_closed_rep;
};

/**
 * Manual gate. 
 *
 * A trivial object that ly changes gate state 
 */
class MANUAL_GATE : public GATE_BASE {

public:

  // Functions returning info about effect and its parameters.
  // ---
  virtual parameter_t get_parameter(int param) const;
  virtual void set_parameter(int param, parameter_t value);

  virtual std::string name(void) const { return("Manual gate"); }
  virtual std::string parameter_names(void) const { return("state"); }

  virtual void analyze(SAMPLE_BUFFER* insample);

  MANUAL_GATE* clone(void) const { return new MANUAL_GATE(*this); }
  MANUAL_GATE* new_expr(void) const { return new MANUAL_GATE(); }
  MANUAL_GATE (void) : open_rep(true) {}
  
private:
  
  bool open_rep;
};

#endif
