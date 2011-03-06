#ifndef INCLUDED_AUDIOIO_REVERSE_H
#define INCLUDED_AUDIOIO_REVERSE_H

#include <string>
#include <vector>
#include <iostream>

#include "audioio-proxy.h"

class SAMPLE_BUFFER;

/**
 * A proxy class that reverts the child 
 * object's data.
 *
 * Related design patterns:
 *     - Proxy (GoF207
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_REVERSE : public AUDIO_IO_PROXY {

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_IO_REVERSE (void); 
  virtual ~AUDIO_IO_REVERSE(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(string("Reverse => ") + child()->name()); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_IO_REVERSE* clone(void) const;
  virtual AUDIO_IO_REVERSE* new_expr(void) const { return(new AUDIO_IO_REVERSE()); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /* -- not reimplemented 
   * virtual SAMPLE_SPECS::sample_pos_t position_in_samples(void) const { return(child_repp->position_in_samples()); }
   * virtual SAMPLE_SPECS::sample_pos_t length_in_samples(void) const { return(); }
   * virtual void set_length_in_samples(SAMPLE_SPECS::sample_pos_t pos);
   * virtual void set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos);
   */

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual int supported_io_modes(void) const { return(io_read); }
  virtual bool supports_seeking(void) const { return(true); }
  virtual bool finite_length_stream(void) const { return(true); }

  virtual void read_buffer(SAMPLE_BUFFER* sbuf);
  virtual void write_buffer(SAMPLE_BUFFER* sbuf) { child()->write_buffer(sbuf); }

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  virtual bool finished(void) const;

  /*@}*/

 private:

  mutable std::vector<std::string> params_rep;
  bool init_rep;
  bool finished_rep;
  SAMPLE_BUFFER* tempbuf_repp;

  static const int child_parameter_offset = 1;

  AUDIO_IO_REVERSE& operator=(const AUDIO_IO_REVERSE& x) { return *this; }
  AUDIO_IO_REVERSE (const AUDIO_IO_REVERSE& x) { }

};

#endif
