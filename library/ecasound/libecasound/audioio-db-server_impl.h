#ifndef INCLUDED_AUDIOIO_DB_SERVER_IMPL_H
#define INCLUDED_AUDIOIO_DB_SERVER_IMPL_H

#include <pthread.h>
#include <kvu_procedure_timer.h>

class AUDIO_IO_DB_SERVER_impl {

 public:

  friend class AUDIO_IO_DB_SERVER;

 private:

  pthread_t io_thread_rep;
  pthread_cond_t client_cond_rep;
  pthread_mutex_t client_mutex_rep;
  pthread_cond_t data_cond_rep;
  pthread_mutex_t data_mutex_rep;
  pthread_cond_t full_cond_rep;
  pthread_mutex_t full_mutex_rep;
  pthread_cond_t stop_cond_rep;
  pthread_mutex_t stop_mutex_rep;
  pthread_cond_t flush_cond_rep;
  pthread_mutex_t flush_mutex_rep;

  size_t profile_full_rep;
  size_t profile_no_processing_rep;
  size_t profile_not_full_anymore_rep;
  size_t profile_processing_rep;
  size_t profile_read_xrun_danger_rep;
  size_t profile_write_xrun_danger_rep;
  size_t profile_rounds_total_rep;

  PROCEDURE_TIMER looptimer_rep;
};

#endif /* INCLUDED_AUDIOIO_DB_SERVER_IMPL_H */
