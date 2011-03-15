#ifndef INCLUDE_MIDI_CC_H
#define INCLUDE_MIDI_CC_H

#include <string>

#include "ctrl-source.h"
#include "midi-client.h"

/**
 * Interface to MIDI continuous controllers
 */
class MIDI_CONTROLLER : public CONTROLLER_SOURCE,
			public MIDI_CLIENT {
    
 public:

  /** @name Functions implemented from CONTROLLER_SOURCE */
  /*@{*/

  virtual void init(void);
  virtual parameter_t value(double pos_secs);
  virtual void set_initial_value(parameter_t arg);

  /*@}*/

  /** @name Functions implemented from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  std::string parameter_names(void) const { return("controller,channel"); }
  void set_parameter(int param, parameter_t value);
  parameter_t get_parameter(int param) const;

  std::string name(void) const {  return("MIDI-Controller"); }

  /*@}*/

  /** @name Functions implemented from ECA_OBJECT */
  /*@{*/
 
  MIDI_CONTROLLER* clone(void) const { return new MIDI_CONTROLLER(*this); }
  MIDI_CONTROLLER* new_expr(void) const { return new MIDI_CONTROLLER(); }

  /*@}*/

  MIDI_CONTROLLER(int controller_number = 0, int midi_channel = 0);

  private:

  int controller_rep, channel_rep;
  parameter_t init_value_rep;
  bool trace_request_rep;

};

#endif
