// ------------------------------------------------------------------------
// kvu_timestamp.cpp: Monotonic timestamps
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

/* note: code assumes timespect struct to be defined in
 *       time.h (i.e. SUSv2 or later) */

#include <time.h>

#ifdef HAVE_SYS_TIME_H
#include <sys/time.h>
#endif

#include <kvu_timestamp.h>

#if HAVE_CLOCK_GETTIME

/* IMPLEMENTATION: clock_gettime() */
/* *********************************/

int kvu_clock_is_monotonic(void)
{
  int res = -1;

#if defined CLOCK_MONOTONIC
  struct timespec tp;
  res = clock_getres(CLOCK_MONOTONIC, &tp);
#endif

  if (res == 0)
    return 1;

  return 0;
}

int kvu_clock_getres(struct timespec *dst)
{
  struct timespec tp;
  int res = -1;

#if defined CLOCK_MONOTONIC
  res = clock_getres(CLOCK_MONOTONIC, &tp);
#endif

  if (res < 0) 
    res =
      clock_getres(CLOCK_REALTIME, &tp);
  if (res == 0) {
    dst->tv_sec = tp.tv_sec;
    dst->tv_sec = tp.tv_nsec;
  }

  return res;
}

int kvu_clock_gettime(struct timespec *dst)
{
  struct timespec tp;
  int res = -1;

#if defined CLOCK_MONOTONIC
  res = clock_gettime(CLOCK_MONOTONIC, &tp);
#endif

  if (res < 0) 
    res =
      clock_gettime(CLOCK_REALTIME, &tp);
  if (res == 0) {
    dst->tv_sec = tp.tv_sec;
    dst->tv_nsec = tp.tv_nsec;
  }

  return res;
}

#elif HAVE_GETTIMEOFDAY

/* IMPLEMENTATION: gettimeofday() */
/* ********************************/

int kvu_clock_is_monotonic(void)
{
  return 0;
}

int kvu_clock_getres(struct timespec *arg)
{
  /* note: an estimate, actual resolution may
   *       be worse */
  arg->tv_sec = 0;
  arg->tv_nsec = 1000;
  return 0;
}

int kvu_clock_gettime(struct timespec *arg)
{
  struct timeval tmp;
  int res =
    gettimeofday(&tmp, NULL);
  if (res == 0) {
    arg->tv_sec = tmp.tv_sec;
    arg->tv_nsec = tmp.tv_usec * 1000;
  }

  return res;
}

#else

/* IMPLEMENTATION: fallback / no-op */
/* **********************************/

int kvu_clock_is_monotonic(void)
{
  DBC_NEVER_REACHED();
  return 0;
}

int kvu_clock_getres(struct timespec *res)
{
  DBC_NEVER_REACHED();
  return -1;
}

int kvu_clock_gettime(struct timespec *tp)
{
  DBC_NEVER_REACHED();
  return -1;
}

#endif
