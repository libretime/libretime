// ------------------------------------------------------------------------
// audiofx_amplitu_test.h: Unit tests for EFFECT_AMPLIFY* classes
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
#include <cassert>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <ctime>

#include "kvu_dbc.h"
#include "kvu_inttypes.h"

#include "audiofx_amplitude.h"
#include "samplebuffer_functions.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for EFFECT_AMPLIFY
 */
class EFFECT_AMPLIFY_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("EFFECT_AMPLIFY"); }
  virtual void do_run(void);

public:

  virtual ~EFFECT_AMPLIFY_TEST(void) { }

private:

};

void EFFECT_AMPLIFY_TEST::do_run(void)
{
  const int bufsize = 1024;
  const int channels = 12;
  const SAMPLE_BUFFER::sample_t multiplier = 112.1f;

  std::fprintf(stdout, "%s: tests for %s class\n",
	       name().c_str(), __FILE__);

  /* case: multiply_by */
  {
    std::fprintf(stdout, "%s: EFFECT_AMPLIFY::process\n",
		 __FILE__);
    SAMPLE_BUFFER sbuf_test (bufsize, channels);
    SAMPLE_BUFFER sbuf_ref (bufsize, channels);

    EFFECT_AMPLIFY amp_test;
    EFFECT_AMPLIFY amp_ref;
    
    SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(&sbuf_ref);
    sbuf_test.copy_all_content(sbuf_ref);

    amp_test.init(&sbuf_test);
    amp_ref.init(&sbuf_ref);
    
    amp_test.set_parameter(1, multiplier);
    amp_ref.set_parameter(1, multiplier);

    amp_test.process();
    amp_ref.process_ref();
    
    if (SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(sbuf_ref, sbuf_test) != true) {
      ECA_TEST_FAILURE("optimized EFFECT_AMPLIFY");
    }
  }
}

/**
 * Unit test for EFFECT_AMPLIFY_CHANNEL
 */
class EFFECT_AMPLIFY_CHANNEL_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("EFFECT_AMPLIFY_CHANNEL"); }
  virtual void do_run(void);

public:

  virtual ~EFFECT_AMPLIFY_CHANNEL_TEST(void) { }

private:

};

void EFFECT_AMPLIFY_CHANNEL_TEST::do_run(void)
{
  const int bufsize = 1024;
  const int channels = 12;
  const SAMPLE_BUFFER::sample_t multiplier = 112.1f;

  std::fprintf(stdout, "%s: tests for %s class\n",
	       name().c_str(), __FILE__);

  /* case: process() */
  {
    std::fprintf(stdout, "%s: process()\n", __FILE__);
    SAMPLE_BUFFER sbuf_test (bufsize, channels);
    SAMPLE_BUFFER sbuf_ref (bufsize, channels);

    EFFECT_AMPLIFY_CHANNEL amp_test;
    EFFECT_AMPLIFY_CHANNEL amp_ref;
    
    SAMPLE_BUFFER_FUNCTIONS::fill_with_random_samples(&sbuf_ref);
    sbuf_test.copy_all_content(sbuf_ref);

    amp_test.init(&sbuf_test);
    amp_ref.init(&sbuf_ref);
    
    amp_test.set_parameter(1, multiplier);
    amp_test.set_parameter(2, 3);
    amp_ref.set_parameter(1, multiplier);
    amp_ref.set_parameter(2, 3);

    amp_test.process();
    amp_ref.process_ref();
    
    if (SAMPLE_BUFFER_FUNCTIONS::is_almost_equal(sbuf_ref, sbuf_test) != true) {
      ECA_TEST_FAILURE("optimized process()");
    }
  }
}
