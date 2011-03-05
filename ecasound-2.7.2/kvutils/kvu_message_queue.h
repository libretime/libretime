#ifndef INCLUDE_KVU_MESSAGE_QUEUE_H
#define INCLUDE_KVU_MESSAGE_QUEUE_H

#include <deque>

#include <errno.h>
#include <pthread.h>
#include <sys/time.h>

#include "kvu_timestamp.h"

/**
 * Default for maximum size of the queue for operation in 
 * bounded execution time mode.
 */
static const size_t msg_queue_rt_max_size_const = 1024;

/**
 * A queue implementation for sending generic messages between
 * threads.
 * 
 * All the consumer operations are real-time safe, i.e. they have 
 * bounded execution time. However, the bounded execution is 
 * guaranteed only until queue size reaches max_rt_size() items. 
 * 
 * Once queue size reached max_rt_size(), consumer operation is 
 * switched to blocking behaviour. This is done as a safety 
 * measure in real-time use.
 *
 * @author Kai Vehmanen
 */
template<class T>
class MESSAGE_QUEUE_RT_C {
    
public:

  /**
   * Class constructor.
   *
   * @param max_rt_size Change queue behaviour to be non-determistic 
   *                    if the number of queued messaes reaches this 
   *                    limit. This can be used as a safety measure
   *                    real-time applications.
   *
   * Execution note: may block, may allocate memory
   */
  MESSAGE_QUEUE_RT_C(int max_rt_size = -1)
    : pending_pops_rep(0) {
    pthread_mutex_init(&lock_rep, NULL);
    pthread_cond_init(&cond_rep, NULL);

    if (max_rt_size == -1)
      max_rt_size_rep = msg_queue_rt_max_size_const;
    else 
      max_rt_size_rep = static_cast<size_t>(max_rt_size);
  }

  /**
   * Adds a new item to the end of the queue.
   *
   * Execution note: may block, may allocate memory
   */
  void push_back(const T& arg) {
    pthread_mutex_lock(&lock_rep);
    msgs_rep.push_back(arg);
    pthread_cond_broadcast(&cond_rep);
    pthread_mutex_unlock(&lock_rep);
  }

  /**
   * Fetches, and removes, the front item in the queue.
   * If the queue is empty, an error is returned.
   *
   * Execution note: rt-safe, does not block
   *
   * @return 1 on success, -1 if busy, 0 if empty
   */
  int pop_front(T* front_msg) {
    int res = 1;
    int lockres = pthread_mutex_trylock(&lock_rep);

    if (lockres == 0) {
      if (msgs_rep.size() > 0) {
	if (front_msg != 0)
	  *front_msg = msgs_rep.front();
	msgs_rep.pop_front();
      }
      else {
	res = 0;
      }
      pthread_mutex_unlock(&lock_rep);
    }
    else {
      res = -1;
    }

    return res;
  }

  /**
   * Clears the queue
   *
   * Execution note: may block
   */
  void clear(void) {
    pthread_mutex_lock(&lock_rep);
    msgs_rep.clear();
    pthread_cond_broadcast(&cond_rep);
    pthread_mutex_unlock(&lock_rep);
  }

  /**
   * Blocks until 'is_empty() != true'. 'timeout_sec' and
   * 'timeout_usec' specify the upper time limit for blocking.
   *
   * Execution: may block, may allocate memory
   *
   * @pre is_empty() != true
   */
  void poll(int timeout_sec, long int timeout_usec) {
    struct timeval nowtmp;
    struct timespec now, timeout;
    int retcode = 0;

    gettimeofday(&nowtmp, NULL);

    now.tv_sec = nowtmp.tv_sec;
    now.tv_nsec = nowtmp.tv_usec * 1000;
    timeout.tv_sec = timeout_sec;
    timeout.tv_nsec = timeout_usec * 1000;
    kvu_timespec_add(&now, &timeout, &timeout);

    pthread_mutex_lock(&lock_rep);
    while (msgs_rep.empty() == true && retcode != ETIMEDOUT) {
      retcode = pthread_cond_timedwait(&cond_rep, &lock_rep, &timeout);
    }
    pthread_mutex_unlock(&lock_rep);
    return;
  }

  /**
   * Is queue empty?
   *
   * Execution note: rt-safe if queue size within 'max_rt_size'
   */
  bool is_empty(void) const {
    bool emptyres = false;
    int ret = pthread_mutex_trylock(&lock_rep);

    /* note: msgs_rep.size() is accessed without holding 
     *       a lock, but that's safe as in the worst case 
     *       caller blocks unnecessarily */
    if (ret != 0 && 
	msgs_rep.size() >= max_rt_size_rep) {

      /* note: queue has grown beyond the rt-safe maximum size, 
       *       change to non-bounded mode to force synchronization
       *       between the producer and consumer threads 
       */
      ret = pthread_mutex_lock(&lock_rep);
    }

    if (ret == 0) {
      emptyres = (msgs_rep.size() == 0);
      pthread_mutex_unlock(&lock_rep);
    }

    return emptyres;
  }

  size_t max_rt_size(void) const {
    return max_rt_size_rep;
  }

private:

  mutable pthread_mutex_t lock_rep; // mutex ensuring exclusive access to buffer
  mutable pthread_cond_t cond_rep;
  
  size_t max_rt_size_rep;   // only modified in constructor
  size_t pending_pops_rep; 

  T invalid_rep;            // only modified in constructor
  std::deque<T> msgs_rep;

};

#endif /* INCLUDE_KVU_MESSAGE_QUEUE_H */
