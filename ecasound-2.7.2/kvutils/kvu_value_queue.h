// -*- mode: C++; -*-
#ifndef INCLUDE_KVU_VALUE_QUEUE_H
#define INCLUDE_KVU_VALUE_QUEUE_H

#include <utility>
#include <deque>

#include <pthread.h>
#include <stddef.h> /* ANSI C++: size_t */

/**
 * A thread-safe queue implementation for int-double 
 * value pairs.
 * @author Kai Vehmanen
 */
class VALUE_QUEUE {
    
 private:

  mutable pthread_mutex_t lock_rep;     // mutex ensuring exclusive access to buffer
  mutable pthread_cond_t cond_rep;

  std::pair<int,double> empty_rep;
  std::deque<std::pair<int,double> > cmds_rep;

public:
  /**
   * Adds a new item to the end of the queue.
   */
  void push_back(int key, double value);

  /**
   * Removes the first item.
   *
   * require:
   *   is_empty() == false
   */
  void pop_front(void);

  /**
   * Returns the first item.
   *
   * require:
   *   is_empty() == false
   */
  const std::pair<int,double>& front(void);

  /**
   * Blocks until 'is_empty() != true'. 'timeout_sec' and
   * 'timeout_usec' specify the upper time limit for blocking.
   */
  void poll(int timeout_sec, long int timeout_usec);

  /**
   * Is queue empty?
   *
   * require:
   *   is_empty() == false
   */
  bool is_empty(void) const;
  
  VALUE_QUEUE(void);
};

/**
 * A queue implementation for int-double value pairs, optimized for
 * two thread scenarios, and specifically cases in which the consumer 
 * thread is potentially run with higher static priority.
 * 
 * All the consumer operations are real-time safe, i.e. they have 
 * bounded execution time. Bounded execution is guaranteed until queue 
 * size reaches bounded_execution_queue_size_limit() items. Once this
 * limit is reached, consumer operation is switched to blocking behaviour.
 * This is done to avoid live-locks between the two threads.
 *
 * Note that this implementation is not thread-safe if the producer
 * thread is run with higher static priority or if more than two
 * threads access the same queue object.
 *
 * For a definition of static priority, see man sched_setscheduler(2).
 * See libkvutils_tester.cpp:kvu_test_4() for an example of how to
 * use this class.
 *
 * @author Kai Vehmanen
 */
class VALUE_QUEUE_RT_C {
    
public:

  void push_back(int key, double value);
  void pop_front(void);
  const std::pair<int,double>* front(void);
  const std::pair<int,double>* invalid_item(void) const { return &invalid_rep; }
  void poll(int timeout_sec, long int timeout_usec);
  bool is_empty(void) const;
  size_t bounded_execution_queue_size_limit(void) const;
  VALUE_QUEUE_RT_C(int bounded_exec_max_size = -1);

private:

  mutable pthread_mutex_t lock_rep;     // mutex ensuring exclusive access to buffer
  mutable pthread_cond_t cond_rep;
  
  size_t bounded_exec_max_size_rep;
  size_t pending_pops_rep; 

  std::pair<int,double> invalid_rep;
  std::deque<std::pair<int,double> > cmds_rep;

};

#endif
