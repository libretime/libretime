#ifndef INCLUDED_GLOBAL_PRESET_H
#define INCLUDED_GLOBAL_PRESET_H

#include "preset.h"

/**
 * Effect preset that is read from a global 
 * preset database.
 *
 * @author Kai Vehmanen
 */
class GLOBAL_PRESET : public PRESET {

 private:

  std::string preset_name_rep;

 public:

  virtual GLOBAL_PRESET* clone(void) const;
  virtual GLOBAL_PRESET* new_expr(void) const { return(new GLOBAL_PRESET(preset_name_rep)); }
  virtual ~GLOBAL_PRESET (void) { }

  GLOBAL_PRESET(const std::string& preset_name = "");
};

#endif
