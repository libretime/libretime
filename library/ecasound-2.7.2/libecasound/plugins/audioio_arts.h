#ifndef INCLUDED_AUDIOIO_ARTS_H
#define INCLUDED_AUDIOIO_ARTS_H

#include <string>
#include <iostream>

#include "sample-specs.h"
#include "eca-version.h"

typedef void *arts_stream_t;

/**
 * Interface for communicating with aRts/MCOP.
 * @author Kai Vehmanen
 */
class ARTS_INTERFACE : public AUDIO_IO_DEVICE {

 public:

  string name(void) const { return("aRts client"); }
  string description(void) const { return("aRts client. Audio input and output using aRts server."); }

  /** @name Function reimplemented from AUDIO_IO */
  /*@{*/

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual int supported_io_modes(void) const { return(io_read | io_write); }
  virtual bool supports_nonblocking_mode(void) const { return(false); }
  virtual bool supports_seeking(void) const { return(false); }
  virtual bool locked_audio_format(void) const { return(false); }

  /*@}*/

  /** @name Function reimplemented from AUDIO_IO_DEVICE */
  /*@{*/

  virtual void start(void);
  virtual void stop(void);

  virtual long int latency(void) const { return(latency_rep); }

  /*@}*/

  ARTS_INTERFACE (const string& name = "arts");
  ~ARTS_INTERFACE(void);
    
  ARTS_INTERFACE* clone(void) const;
  ARTS_INTERFACE* new_expr(void) const { return new ARTS_INTERFACE(); }

  private:

  ARTS_INTERFACE(const ARTS_INTERFACE& x) { }
  ARTS_INTERFACE& operator=(const ARTS_INTERFACE& x) { return *this; }

  arts_stream_t stream_rep;
  SAMPLE_SPECS::sample_pos_t samples_rep;
  long int latency_rep;
  static int ref_rep;

};

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
extern "C" {
AUDIO_IO* audio_io_descriptor(void) { return(new ARTS_INTERFACE()); }
int audio_io_interface_version(void);
const char* audio_io_keyword(void);
const char* audio_io_keyword_regex(void);
};
#endif

#endif
