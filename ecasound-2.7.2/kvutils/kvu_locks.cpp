// ------------------------------------------------------------------------
// kvu_locks.cpp: Various lock related helper functions.
// Copyright (C) 2000-2002,2006 Kai Vehmanen
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

#include <signal.h>         /* ANSI-C: sig_atomic_t */
#include <pthread.h>        /* POSIX: threading */

#include "kvu_dbc.h"
#include "kvu_locks.h"

/* NOTE: This implementation is currenly a dummy one. The primary 
 *       reason to use ATOMIC_OINT() is to mark all code segments 
 *       where atomic read/write access is required.
 *
 * Tested platforms:
 *   - IA32 single- and multi-processor cases
 *
 * Platforms that should be atomic w.r.t. read/writes:
 *   - Alpha
 *   - ARM-v4 (ARM9) and older
 *   - IA64
 *   - MIPS
 *   - PowerPC
 *   - SH
 *   - S390
 *   - SPARC64
 *   - X86-64
 * 
 * Platforms that where writes from multiple threads do _NOT_ work:
 *   - ARM-v6 (ARM11) and up 
 *   - SPARC32
 *
 * References: 
 *   - architecture manuals
 *   - Linux kernel and glibc sources
 */

ATOMIC_INTEGER::ATOMIC_INTEGER(int value)
{
  value_rep = value;
}

ATOMIC_INTEGER::~ATOMIC_INTEGER(void) 
{
}

int ATOMIC_INTEGER::get(void) const
{
  return value_rep;
}

void ATOMIC_INTEGER::set(int value)
{
  value_rep = value;
}

KVU_GUARD_LOCK::KVU_GUARD_LOCK(pthread_mutex_t* lock_arg)
{
  lock_repp = lock_arg;
  DBC_CHECK(pthread_mutex_lock(lock_repp) == 0);
}

KVU_GUARD_LOCK::~KVU_GUARD_LOCK(void)
{
  DBC_CHECK(pthread_mutex_unlock(lock_repp) == 0);
}
