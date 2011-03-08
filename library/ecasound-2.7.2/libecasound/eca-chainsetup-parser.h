#ifndef INCLUDED_ECA_CHAINSETUP_PARSER_H
#define INCLUDED_ECA_CHAINSETUP_PARSER_H

#include <vector>
#include <string>

class ECA_CHAINSETUP;
class AUDIO_IO;

/**
 * Functionality for parsing chainsetup 
 * option syntax.
 * 
 * Notes: Code was originally part of the 
 *        ECA_CHAINSETUP class.
 *
 * @author Kai Vehmanen
 */
class ECA_CHAINSETUP_PARSER {

 public:

  ECA_CHAINSETUP_PARSER(ECA_CHAINSETUP* csetup);

  // --
  // functions for std::string->state conversions

  /**
   * Returns the result of last call to interpret_option(), interpret_global_option() 
   * or interpret_object_option().
   *
   * @result true if options interpreted succesfully, otherwise false
   */
  bool interpret_result(void) const { return(interpret_result_rep); }
  const std::string& interpret_result_verbose(void) const { return(interpret_result_verbose_rep); }

  void interpret_option(const std::string& arg);
  void interpret_global_option(const std::string& arg);
  void interpret_object_option(const std::string& arg);
  void interpret_options(const std::vector<std::string>& opts);

  void reset_interpret_status(void);
  void preprocess_options(std::vector<std::string>& opts) const;

  // --
  // functions for state->string conversions

  std::string inputs_to_string(void) const;
  std::string outputs_to_string(void) const;
  std::string chains_to_string(void) const;
  std::string midi_to_string(void) const;
  std::string general_options_to_string(void) const;

 private:

  // --
  // functions for std::string->state conversions

  void interpret_entry(void);
  void interpret_exit(const std::string& arg);
  void interpret_set_result(bool result, const std::string& verbose) { interpret_result_rep = result; interpret_result_verbose_rep = verbose; }
  void interpret_general_option (const std::string& arg);
  void interpret_processing_control (const std::string& arg);
  void interpret_audio_format (const std::string& arg);
  void interpret_chains (const std::string& arg);
  void interpret_chain_operator (const std::string& arg);
  void interpret_controller (const std::string& arg);
  void interpret_effect_preset (const std::string& arg);
  void interpret_audioio_device (const std::string& argu);
  void interpret_audioio_manager (const std::string& argu);
  void interpret_midi_device (const std::string& arg);
  bool interpret_match_found(void) const { return(istatus_rep); }

  // --
  // data members
  
  ECA_CHAINSETUP* csetup_repp;

  std::vector<AUDIO_IO*>* last_audio_add_vector_repp;
  AUDIO_IO* last_audio_object_repp;
  bool istatus_rep; /* whether we have found an option match? */
  bool interpret_result_rep; /* whether we found an option match with correct format? */
  std::string interpret_result_verbose_rep;

};

#endif
