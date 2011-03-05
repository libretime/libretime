// ------------------------------------------------------------------------
// kvu_timestamp.h: Monotonic timestamps
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#ifndef INCLUDED_KVU_TIMESTAMP_H
#define INCLUDED_KVU_TIMESTAMP_H

#include <time.h>

/**
 * Returns non-zero if clock is monotonic.
 *
 * See POSIX 'CLOCK_MONOTONIC'
 */
int kvu_clock_is_monotonic(void);

/**
 * Finds the resolution (precision) of the clock
 * source.
 *
 * See POSIX clock_getres().
 */
int kvu_clock_getres(struct kvu_timespec *res);

/**
 * Retrieves a timestamp
 *
 * See POSIX clock_gettime()
 */
int kvu_clock_gettime(struct timespec *tp);

/**
 * Returns the timestamp as seconds
  */
static inline double kvu_timespec_seconds(struct timespec *tp)
{
  return tp->tv_sec + 
    (static_cast<double>(tp->tv_nsec) / 1000000000.0);
}

/** 
 * Adapted from macro definitions in glibc's sys/time.h (LGPL 2.1)
 */
static inline void kvu_timespec_clear(struct timespec *tvp)
{
  tvp->tv_sec = tvp->tv_nsec = 0;
}

static inline void kvu_timespec_assign(struct timespec *x, const struct timespec *y)
{
  x->tv_sec = y->tv_sec;
  x->tv_nsec = y->tv_nsec;
}

/** 
 * result = a + b
 *
 * Adapted from macro definitions in glibc's sys/time.h (LGPL 2.1)
 */
static inline void kvu_timespec_add(const struct timespec *a, 
				    const struct timespec *b, 
				    struct timespec *result)
{
  (result)->tv_sec = (a)->tv_sec + (b)->tv_sec;			      
  (result)->tv_nsec = (a)->tv_nsec + (b)->tv_nsec;
  if ((result)->tv_nsec >= 1000000000) {
    ++(result)->tv_sec;
    (result)->tv_nsec -= 1000000000;
  }
}
			
/** 
 * Returns 'a < b'
 *
 * Adapted from macro definitions in glibc's sys/time.h (LGPL 2.1)
 */							
static inline int kvu_timespec_cmp_lt(const struct timespec *a, 
				      const struct timespec *b)
{ 
  return (((a)->tv_sec == (b)->tv_sec) ?
	  ((a)->tv_nsec < (b)->tv_nsec) :
	  ((a)->tv_sec < (b)->tv_sec));
}

/**
 * Returns 'a > b'
 *
 * Adapted from macro definitions in glibc's sys/time.h (LGPL 2.1)
 */
static inline int kvu_timespec_cmp_gt(const struct timespec *a, 
				      const struct timespec *b)
{ 
  return (((a)->tv_sec == (b)->tv_sec) ?
	  ((a)->tv_nsec > (b)->tv_nsec) :
	  ((a)->tv_sec > (b)->tv_sec));
}

/**
 * result = a - b 
 *
 * Adapted from macro definitions in glibc's sys/time.h (LGPL 2.1)
 */
static inline void kvu_timespec_sub(const struct timespec *a, 
				    const struct timespec *b, 
				    struct timespec *result)
{
  (result)->tv_sec = (a)->tv_sec - (b)->tv_sec;
  (result)->tv_nsec = (a)->tv_nsec - (b)->tv_nsec;
  if ((result)->tv_nsec < 0) {
    --(result)->tv_sec;
    (result)->tv_nsec += 1000000000;
  }
}                                                                         \

#endif /* INCLUDED_KVU_TIMESTAMP_H */
