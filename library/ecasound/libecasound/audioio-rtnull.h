#ifndef INCLUDED_AUDIOIO_RTNULL_H
#define INCLUDED_AUDIOIO_RTNULL_H

#include <sys/time.h>
#include "audioio-device.h"

/**
 * Null audio object with realtime behaviour
 */
class REALTIME_NULL : public AUDIO_IO_DEVICE {
 public:

  virtual std::string name(void) const { return("Realtime null device"); }

  /** @name Function reimplemented from AUDIO_IO */
  /*@{*/

  virtual void open(void) throw (AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);

  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  /*@}*/

  /** @name Function reimplemented from AUDIO_IO_DEVICE */
  /*@{*/

  virtual void prepare(void);
  virtual void stop(void);
  virtual void start(void);

  virtual long int delay(void) const;
  virtual long int prefill_space(void) const;

  /*@}*/

  REALTIME_NULL(const std::string& name = "realtime null");
  virtual ~REALTIME_NULL(void);
  REALTIME_NULL* clone(void) const { return new REALTIME_NULL(*this); }
  REALTIME_NULL* new_expr(void) const { return new REALTIME_NULL(); }

 private:

  void calculate_device_position(void);
  void calculate_available_data(void) const;
  void block_until_data_available(void);

  int total_buffers_rep;
  mutable int xruns_rep;

  struct timeval start_time_rep;
  struct timeval time_since_start_rep;

  struct timeval buffer_length_rep;
  struct timeval total_buffer_length_rep;

  struct timeval data_processed_rep;
  mutable struct timeval avail_data_rep;
};

#endif
