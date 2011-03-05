// -*- mode: C++; -*-
#ifndef INCLUDED_KVU_LOCKS_H
#define INCLUDED_KVU_LOCKS_H

#include <pthread.h>

/**
 * Atomic access to single integer values. Implementation
 * may be based on direct atomic operations or traditional 
 * locking, depending on the underlying platform.
 *
 * On supported platforms, atomicity is guaranteed for 
 * both single- and multiprocessor concurrency. Ordering of 
 * concurrent reads and writes is however not guaranteed.
 *
 * Note! Atomic test-and-modify operations are not provided.
 */
class ATOMIC_INTEGER {

 public:

  /**
   * Returns the stored integer value.
   *
   * Non-blocking.
   */
  int get(void) const;

  /**
   * Sets the integer value to 'value'.
   *
   * Non-blocking. Atomic on most platforms.
   *
   * Atomic without limitations on most platforms. On some
   * platforms (e.g. sparc64 and armv6), writes from multiple
   * threads are unsafe.
   */
  void set(int value);

  ATOMIC_INTEGER(int value = 0);
  ~ATOMIC_INTEGER(void);

 private:

  volatile int value_rep;

  ATOMIC_INTEGER& operator=(const ATOMIC_INTEGER& v);
  ATOMIC_INTEGER(const ATOMIC_INTEGER& v);
};

/**
 * A simple guarded lock wrapper for pthread_mutex_lock
 * and pthread_mutex_unlock. Lock is acquired 
 * upon object creating and released during destruction.
 */
class KVU_GUARD_LOCK {

 public:

  KVU_GUARD_LOCK(pthread_mutex_t* lock_arg);
  ~KVU_GUARD_LOCK(void);

 private:

  pthread_mutex_t* lock_repp;

  KVU_GUARD_LOCK(void) {}
  KVU_GUARD_LOCK(const KVU_GUARD_LOCK&) {}
  KVU_GUARD_LOCK& operator=(const KVU_GUARD_LOCK&) { return *this; }
};

#endif
