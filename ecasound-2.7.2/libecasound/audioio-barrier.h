#ifndef INCLUDED_AUDIO_IO_BARRIER_H
#define INCLUDED_AUDIO_IO_BARRIER_H

/**
 * Interface class that introduces audio i/o barriers. 
 * The barriers are used to signal that processing
 * will be started or stopped. 
 */ 
class AUDIO_IO_BARRIER {

public:

  /**
   * Starts I/O processing. 
   * 
   * The read_buffer()/write_buffer() functions will not be called
   * before I/O started. Also, it is guaranteed that stop_io() will
   * be called from the same thread as start_io() was called from.
   */
  virtual void start_io(void) = 0;

  /**
   * Stops I/O processing. 
   * 
   * The read_buffer()/write_buffer() functions will not be called
   * after I/O has been stopped.
   */
  virtual void stop_io(void) = 0;

  virtual ~AUDIO_IO_BARRIER(void) {}
};

#endif
