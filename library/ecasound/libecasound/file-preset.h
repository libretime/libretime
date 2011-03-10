#ifndef INCLUDED_FILE_PRESET_H
#define INCLUDED_FILE_PRESET_H

#include <string>
#include "preset.h"

/**
 * File based effect preset
 *
 * @author Kai Vehmanen
 */
class FILE_PRESET : public PRESET {

  std::string filename_rep;

 public:

  std::string filename(void) const { return(filename_rep); }
  void set_filename(const std::string& v) { filename_rep = v; }

  virtual FILE_PRESET* clone(void) const;
  virtual FILE_PRESET* new_expr(void) const { return(new FILE_PRESET(filename_rep)); }
  virtual ~FILE_PRESET (void) { }

  FILE_PRESET(const std::string& file_name = "");
};

#endif
