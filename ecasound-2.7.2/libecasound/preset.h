#ifndef INCLUDED_PRESET_H
#define INCLUDED_PRESET_H

#include <string>
#include <vector>

class PRESET_impl;
class CHAIN;
class SAMPLE_BUFFER;

#include "eca-chainop.h"
#include "eca-samplerate-aware.h"

/**
 * Class for representing effect presets
 *
 * @author Arto Hamara
 * @author Kai Vehmanen
 */
class PRESET : public CHAIN_OPERATOR,
	       public ECA_SAMPLERATE_AWARE {

 public:

  /** @name Public virtual functions to notify about changes 
   *        (Reimplemented from ECA_SAMPLERATE_AWARE) */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

  /** @name Constructors and destructors */
  /*@{*/

  PRESET(void);
  PRESET(const std::string& formatted_string);

  /*@}*/

  /** @name Public API functions */
  /*@{*/

  virtual PRESET* clone(void) const;
  virtual PRESET* new_expr(void) const;
  virtual ~PRESET (void);

  virtual std::string name(void) const;
  virtual std::string description(void) const;

  void set_name(const std::string& v);

  virtual void init(SAMPLE_BUFFER* sbuf);
  virtual void release(void);
  virtual void process(void);
  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, parameter_t value);
  virtual parameter_t get_parameter(int param) const;
  virtual void parameter_description(int param, struct PARAM_DESCRIPTION *pd) const;

  void parse(const std::string& formatted_string);
  
  bool is_parsed(void) const;

  /*@}*/

 private:

  PRESET_impl* impl_repp;
  SAMPLE_BUFFER* first_buffer;
  std::vector<SAMPLE_BUFFER*> buffers;
  std::vector<CHAIN*> chains;

  bool is_preset_option(const std::string& arg) const;
  void add_chain(void);
  void extend_pardesc_vector(int number);
  void parse_preset_option(const std::string& arg);
  void parse_operator_option(const std::string& arg);
  void set_preset_defaults(const std::vector<std::string>& args);
  void set_preset_param_names(const std::vector<string>& args);
  void set_preset_lower_bounds(const std::vector<std::string>& args);
  void set_preset_upper_bounds(const std::vector<std::string>& args);
  void set_preset_toggles(const std::vector<std::string>& args);

  PRESET& operator=(const PRESET& x) { return *this; }
  PRESET(const PRESET& x) { }
};

#endif


