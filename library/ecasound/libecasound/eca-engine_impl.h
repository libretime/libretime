#ifndef INCLUDED_ECA_ENGINE_IMPL_H
#define INCLUDED_ECA_ENGINE_IMPL_H

#include <ctime>
#include <pthread.h>
#include <unistd.h>
#include <sys/time.h>

#include <kvu_message_queue.h>
#include <kvu_procedure_timer.h>

#include "eca-chainsetup.h"

/**
 * Private class used in ECA_ENGINE 
 * implementation.
 */
class ECA_ENGINE_impl {

  friend class ECA_ENGINE;

 private:

  PROCEDURE_TIMER looptimer_rep;
  PROCEDURE_TIMER looptimer_range_rep;

  double looptimer_low_rep;
  double looptimer_mid_rep;
  double looptimer_high_rep;

  MESSAGE_QUEUE_RT_C<ECA_ENGINE::complex_command_t> command_queue_rep;

  pthread_cond_t ecasound_stop_cond_repp;
  pthread_mutex_t ecasound_stop_mutex_repp;
  pthread_cond_t ecasound_exit_cond_repp;
  pthread_mutex_t ecasound_exit_mutex_repp;

  struct timeval multitrack_input_stamp_rep;
};

#endif /* INCLUDED_ECA_ENGINE_IMPL_H */
