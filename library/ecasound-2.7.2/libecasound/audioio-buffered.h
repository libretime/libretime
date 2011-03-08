#ifndef INCLUDED_AUDIOIO_BUFFERED_H
#define INCLUDED_AUDIOIO_BUFFERED_H

#include "audioio.h"

class SAMPLE_BUFFER;

/**
 * A lower level interface for audio I/O objects. Derived classes 
 * must implement routines for reading and/or writing buffers of raw data.
 */
class AUDIO_IO_BUFFERED : public AUDIO_IO {

 public:

  virtual ~AUDIO_IO_BUFFERED(void);
  AUDIO_IO_BUFFERED(void);

  virtual void read_buffer(SAMPLE_BUFFER* sbuf);
  virtual void write_buffer(SAMPLE_BUFFER* sbuf);

  virtual void set_buffersize(long int samples);
  virtual long int buffersize(void) const { return(buffersize_rep); }

  /**
   * Low-level routine for reading samples. Number of read sample
   * frames is returned. This must be implemented by all subclasses.
   */
  virtual long int read_samples(void* target_buffer, long int sample_frames) = 0;

  /**
   * Low-level routine for writing samples. This must be implemented 
   * by all subclasses.
   */
  virtual void write_samples(void* target_buffer, long int sample_frames) = 0;

  /** @name Reimplemented functions from ECA_AUDIO_FORMAT */
  /*@{*/

  virtual void set_channels(SAMPLE_SPECS::channel_t v);
  virtual void set_sample_format(Sample_format v) throw(ECA_ERROR&);

  /*@{*/

 protected:

  void reserve_buffer_space(long int bytes);
  unsigned char* get_iobuf(void) const { return(iobuf_uchar_repp); }
  size_t get_iobuf_size(void) const { return(iobuf_size_rep); }

 private:

  long int buffersize_rep;
  unsigned char* iobuf_uchar_repp;  // buffer for raw-I/O
  size_t iobuf_size_rep;
};

#endif // INCLUDED_AUDIO_IO_BUFFERED
