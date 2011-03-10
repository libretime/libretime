#ifndef INCLUDED_GENERIC_LINEAR_ENVELOPE_H
#define INCLUDED_GENERIC_LINEAR_ENVELOPE_H

#include <string>
#include <vector>

#include "ctrl-source.h"
#include "eca-audio-position.h"

/**
 * Generic multi-stage linear envelope
 */
class GENERIC_LINEAR_ENVELOPE : public CONTROLLER_SOURCE
{

public:

  GENERIC_LINEAR_ENVELOPE(void); 
  virtual ~GENERIC_LINEAR_ENVELOPE(void); 

  GENERIC_LINEAR_ENVELOPE* clone(void) const { return new GENERIC_LINEAR_ENVELOPE(*this); }
  GENERIC_LINEAR_ENVELOPE* new_expr(void) const { return new GENERIC_LINEAR_ENVELOPE(); }
  
  virtual void init(void);
  virtual parameter_t value(double pos);
  virtual void set_initial_value(parameter_t arg) {}

  virtual bool variable_params(void) const { return true; }  
  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;

  virtual std::string name(void) const { return("Generic linear envelope"); }
  
private:
  
  std::vector<parameter_t> pos_rep;
  std::vector<parameter_t> val_rep;
  parameter_t curval;
  int curstage;
  std::string param_names_rep;
  
  void set_param_count(int params);
};

#endif
