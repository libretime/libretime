#ifndef INCLUDED_AUDIOIO_TYPESELECT_H
#define INCLUDED_AUDIOIO_TYPESELECT_H

#include <string>
#include <vector>
#include <iostream>

#include "audioio-proxy.h"

/**
 * A proxy class for overriding default keyword 
 * and filename associations in ecasound's object
 * maps.
 *
 * Related design patterns:
 *     - Proxy (GoF207)
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_TYPESELECT : public AUDIO_IO_PROXY {

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_IO_TYPESELECT (void); 
  virtual ~AUDIO_IO_TYPESELECT(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(string("Typeselect => ") + child()->name()); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  virtual std::string parameter_names(void) const;
  virtual bool variable_params(void) const { return true; }
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_IO_TYPESELECT* clone(void) const;
  virtual AUDIO_IO_TYPESELECT* new_expr(void) const { return(new AUDIO_IO_TYPESELECT()); }

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  /*@}*/

 private:

  std::string type_rep;
  mutable std::vector<std::string> params_rep;
  bool init_rep;

  AUDIO_IO_TYPESELECT& operator=(const AUDIO_IO_TYPESELECT& x) { return *this; }
  AUDIO_IO_TYPESELECT (const AUDIO_IO_TYPESELECT& x) { }

};

#endif
