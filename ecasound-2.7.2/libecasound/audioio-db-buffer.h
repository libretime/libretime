#ifndef INCLUDED_AUDIOIO_DB_BUFFER_H
#define INCLUDED_AUDIOIO_DB_BUFFER_H

#include <kvu_locks.h>
#include "audioio.h"

class SAMPLE_BUFFER;

/**
 * Buffer used between db server and client
 */
class AUDIO_IO_DB_BUFFER {

 public:

  ATOMIC_INTEGER readptr_rep;
  ATOMIC_INTEGER writeptr_rep;
  ATOMIC_INTEGER finished_rep;
  std::vector<SAMPLE_BUFFER*> sbufs_rep;
  AUDIO_IO::Io_mode io_mode_rep;

  void reset(void);
  int read_space(void);
  int write_space(void);
  void advance_read_pointer(void);
  void advance_write_pointer(void);

  AUDIO_IO_DB_BUFFER(int number_of_buffers,
		     long int buffersize,
		     int channels);
  ~AUDIO_IO_DB_BUFFER(void);
};

#endif
