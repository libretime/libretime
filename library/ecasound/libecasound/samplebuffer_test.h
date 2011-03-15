// ------------------------------------------------------------------------
// samplebuffer_test.h: Unit test for SAMPLE_BUFFER
// Copyright (C) 2009 Kai Vehmanen
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

#include <string>
#include <cstdio>

#include "kvu_dbc.h"
#include "kvu_inttypes.h"

#include "samplebuffer.h"
#include "samplebuffer_functions.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for SAMPLE_BUFFER
 */
class SAMPLE_BUFFER_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("SAMPLE_BUFFER"); }
  virtual void do_run(void);

public:

  virtual ~SAMPLE_BUFFER_TEST(void) { }

private:

};

void SAMPLE_BUFFER_TEST::do_run(void)
{
  const int bufsize = 1024;
  const int channels = 12;
  const SAMPLE_BUFFER::sample_t multiplier = 100.1f;

  std::fprintf(stdout, "%s: tests for SAMPLE_BUFFER class\n",
	       __FILE__);

  /* case: multiply_by */
  {
    std::fprintf(stdout, "%s: multiply_by\n",
		 __FILE__);
    SAMPLE_BUFFER sbuf_orig (bufsize, channels);
    SAMPLE_BUFFER sbuf_ref (bufsize, channels);
    SAMPLE_BUFFER sbuf_test (bufsize, channels);

    SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(&sbuf_orig);
    
    sbuf_test.copy_all_content(sbuf_orig);
    sbuf_ref.copy_all_content(sbuf_orig);
    
    sbuf_test.multiply_by(multiplier);
    sbuf_ref.multiply_by_ref(multiplier);
    
    if (SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(sbuf_ref, sbuf_test) != true) {
      ECA_TEST_FAILURE("optimized multiple_by");
    }
  }

  /* case: copy_all_content */
  {
    std::fprintf(stdout, "%s: copy_all_content\n",
		 __FILE__);

    SAMPLE_BUFFER sbuf_orig (bufsize, channels);
    SAMPLE_BUFFER sbuf_ref (bufsize, channels);
    SAMPLE_BUFFER sbuf_test (bufsize, channels);

    SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(&sbuf_orig);

    /* note: copy_all_content should modify the destination
     *       channel and sample counts to match those of
     *       the source object. */
    sbuf_test.number_of_channels(0);
    sbuf_test.length_in_samples(1);

    sbuf_test.copy_all_content(sbuf_orig);
    sbuf_ref.copy_matching_channels(sbuf_orig);

    if (SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(sbuf_ref, sbuf_test) != true) { 
      ECA_TEST_FAILURE("copy_all_content");
    }
  }

  /* case: multiply_by */
  {
    std::fprintf(stdout, "%s: add_matching_channels\n",
		 __FILE__);
    SAMPLE_BUFFER sbuf_orig (bufsize, channels);
    SAMPLE_BUFFER sbuf_ref (bufsize, channels);
    SAMPLE_BUFFER sbuf_test (bufsize, channels);

    SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(&sbuf_orig);

    sbuf_test.copy_all_content(sbuf_orig);
    sbuf_ref.copy_all_content(sbuf_orig);

    sbuf_test.add_matching_channels(sbuf_orig);
    sbuf_ref.add_matching_channels_ref(sbuf_orig);
    
    if (SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(sbuf_ref, sbuf_test) != true) { 
      ECA_TEST_FAILURE("optimized add_matching_channels");
    }
  }
}
