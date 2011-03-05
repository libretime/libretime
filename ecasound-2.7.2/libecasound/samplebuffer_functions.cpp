// ------------------------------------------------------------------------
// samplebuffer_functions.cpp: Extra functions for SAMPLE_BUFFER class
// Copyright (C) 2000,2001,2009 Kai Vehmanen
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

#include <string>
#include <cassert>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <ctime>

#include "kvu_dbc.h"
#include "kvu_inttypes.h"

#include "samplebuffer.h"
#include "samplebuffer_functions.h"

#undef NEVER_USED_CODE

void SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(SAMPLE_BUFFER *sbuf)
{
  std::srand(std::time(0));
  int ch_count = sbuf->number_of_channels();
  int i_count = sbuf->length_in_samples();

  for (int ch = 0; ch < ch_count; ch++) {
    SAMPLE_BUFFER::sample_t *buf = sbuf->buffer[ch];
    for (int i = 0; i < i_count; i++) {
      int foo = std::rand();
      assert(sizeof(SAMPLE_BUFFER::sample_t) <= sizeof(foo));
      std::memcpy(buf, &foo, sizeof(SAMPLE_BUFFER::sample_t));
    }
  }
}

/**
 * Returns true if 'a' and 'b' have the same exact signal content,
 * or if the two signals are sufficiently close to each 
 * other considering the limits imposed by implementation (e.g.
 * precision of the floating point type used to represent
 * a sample). 
 *
 * @param bitprec adjust precision (defaults to 24)
 * @param verbose_stderr whether to output comparison traces to
 *        stderr
 */
bool SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(const SAMPLE_BUFFER& a, const SAMPLE_BUFFER& b, int bitprec, bool verbose_stderr)
{
  if (a.number_of_channels() !=
      b.number_of_channels())
    return false;

  if (a.length_in_samples() !=
      b.length_in_samples())
    return false;
  
  int ch_count = a.number_of_channels();
  int i_count = a.length_in_samples();

  for (int ch = 0; ch < ch_count; ch++) {
    for (int i = 0; i < i_count; i++) {
      if (a.buffer[ch][i] != b.buffer[ch][i]) {

	/* note: the following is intended only for comparing
	 *       audio signals with a nominal range of [-1,1]
	 *       and precision of 'bitprec' bits */

	const SAMPLE_SPECS::sample_t diff_threshold = 1.0 / ((1 << bitprec) - 1);

	SAMPLE_SPECS::sample_t diff = 
	  std::fabs(a.buffer[ch][i] - b.buffer[ch][i]);

	if (verbose_stderr == true) {	
	  std::fprintf(stderr, 
		       "%s: diff for sample ch%d[%d], diff %.30f [%s], (a=%.30f to b=%.30f, thrshd %.30f)\n",
		       __FILE__, ch, i, 
		       diff,
		       diff > diff_threshold ? "MISMATCH" : "INRANGE",
		       a.buffer[ch][i], b.buffer[ch][i],
		       diff_threshold);
	}

	if (diff > diff_threshold) {
	    return false;
	}

	{
#if NEVER_USED_CODE /* integer-based comparison */
	  assert(sizeof(SAMPLE_BUFFER::sample_t) == sizeof(uint32_t));
	  /* allow diff of one in binary representation */
	  const int diff_threshold_ints = 1;
	  uint32_t aint = *reinterpret_cast<uint32_t*>(&a.buffer[ch][i]);
	  uint32_t bint = *reinterpret_cast<uint32_t*>(&b.buffer[ch][i]);
	  uint32_t diff 
	    = std::labs(aint - bint);
	  if (diff_total > diff_threshold_ints) {
	    return false;
	  }
#endif
	}

      }
    }
  }

  return true;
}
