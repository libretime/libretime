#ifndef INCLUDED_PRESET_IMPL_H
#define INCLUDED_PRESET_IMPL_H

#include <string>
#include <vector>
 
#include "eca-chainop.h"
#include "sample-specs.h"

using std::string;
using std::vector;

class AUDIO_IO;
class GENERIC_CONTROLLER;
class OPERATOR;

class PRESET_impl {

 public:

  friend class PRESET;

 private:

  vector<string> preset_param_names_rep;

  /** 
   * maps preset's public params 1...N (parent) to params
   * of slave objects (one-to-many mapping as multiple 
   * slave params can be associated with the same preset
   * param
   */
  vector<vector<int> > slave_param_indices_rep;

  /**
   * maps preset's public params 1...N (parent) to slave
   * objects (see slave_param_indices_rep)
   */
  vector<vector<DYNAMIC_OBJECT<SAMPLE_SPECS::sample_t>* > > slave_param_objects_rep;

  vector<GENERIC_CONTROLLER*> gctrls_rep;
  vector<OPERATOR::PARAM_DESCRIPTION*> pardesclist_rep;

  bool parsed_rep;
  std::string parse_string_rep;
  std::string name_rep;
  std::string description_rep;

};

#endif /* INCLUDED_PRESET_IMPL_H */
