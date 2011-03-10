#ifndef INCLUDED_AUDIOIO_EWF_H
#define INCLUDED_AUDIOIO_EWF_H

#include <string>
#include "audioio-seqbase.h"
#include "samplebuffer.h"
#include "eca-audio-time.h"
#include "resource-file.h"

/**
 * Ecasound Wave File - a simple wrapper class for handling 
 * other audio objects. When writing .ewf files, it's possible to 
 * seek beyond end position. When first write_buffer() call is issued, 
 * current sample offset is stored into the .ewf file and corresponding 
 * child object is opened for writing. Read_buffer() calls return silent 
 * buffers until sample_offset is reached. After that, audio object is 
 * processed normally. Similarly .ewf supports audio relocation, looping, etc...
 *
 * Related design patterns:
 *     - Proxy (GoF207
 *
 * @author Kai Vehmanen
 */
class EWFFILE : public AUDIO_SEQUENCER_BASE {

 public:

  /** @name Public functions */
  /*@{*/

  EWFFILE (void);
  virtual ~EWFFILE(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return("Ecasound wave file"); }
  virtual std::string description(void) const { return("Special format acts as a wrapper for other file formats. It can used for looping, audio data relocation and other special tasks."); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS */
  /*@{*/

  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual EWFFILE* clone(void) const;
  virtual EWFFILE* new_expr(void) const { return new EWFFILE(); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  /* none */

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  /*@}*/

  /** @name New functions */
  /*@{*/

    
  /*@}*/

private:

  RESOURCE_FILE ewf_rc;

  void dump_child_debug(void);    
  void read_ewf_data(void) throw(ECA_ERROR&);
  void write_ewf_data(void);
  void init_default_child(void) throw(ECA_ERROR&);
  SAMPLE_SPECS::sample_pos_t priv_public_to_child_pos(SAMPLE_SPECS::sample_pos_t pubpos) const;
  
  EWFFILE& operator=(const EWFFILE& x) { return *this; }
  EWFFILE (const EWFFILE& x) { }

};

#endif
