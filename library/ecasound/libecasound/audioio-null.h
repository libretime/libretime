#ifndef INCLUDED_AUDIOIO_NULL_H
#define INCLUDED_AUDIOIO_NULL_H

#include "audioio-buffered.h"

/**
 * Audio object that endlessly consumes and produces audio data.
 * And is incredibly fast. :)
 */
class NULLFILE : public AUDIO_IO_BUFFERED {
 public:

  virtual std::string name(void) const { return("Null audio object"); }

  virtual void open(void) throw (AUDIO_IO::SETUP_ERROR &) { AUDIO_IO::open(); }
  virtual void close(void) { AUDIO_IO::close(); }

  virtual long int read_samples(void* target_buffer, long int samples) { 
    for(long int n = 0; n < static_cast<long int>(samples * frame_size()); n++) ((char*)target_buffer)[n] = 0;
    return(samples); 
  }
  virtual void write_samples(void* target_buffer, long int samples) { }

  virtual bool finished(void) const { return false; }
  virtual bool supports_seeking(void) const { return true; }
  virtual bool finite_length_stream(void) const { return false; }
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos) { return pos; }

  NULLFILE(const std::string& name = "null");
  virtual ~NULLFILE(void);
  NULLFILE* clone(void) const { return new NULLFILE(*this); }
  NULLFILE* new_expr(void) const { return new NULLFILE(); }
};

#endif
