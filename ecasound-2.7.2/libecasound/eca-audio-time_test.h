// ------------------------------------------------------------------------
// eca-audio-time_test.h: Unit test for ECA_AUDIO_TIME
// Copyright (C) 2008 Kai Vehmanen
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

#include "kvu_utils.h" /* kvu_sleep() */

#include "eca-audio-time.h"
#include "eca-test-case.h"
using namespace std;

/**
 * Unit test for ECA_CONTROL.
 *
 * FIXME: implementation not ready
 */
class ECA_AUDIO_TIME_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for ECA_AUDIO_TIME"); }
  virtual void do_run(void);

public:

  virtual ~ECA_AUDIO_TIME_TEST(void) { }

private:

};

void ECA_AUDIO_TIME_TEST::do_run(void)
{
  cerr << "libecasound_tester: eca-audio-time" << endl;

  ECA_AUDIO_TIME v ("88200sa");
  v.set_samples_per_second(44100);
  if (v.samples() != 88200)
    ECA_TEST_FAILURE("Incorrect sample count (from-samples).");

  if (v.seconds() != 2.0f)
    ECA_TEST_FAILURE("Incorrect time in seconds (from-samples).");

  v.set_time_string("2.0");
  if (v.samples() != 88200)
    ECA_TEST_FAILURE("Incorrect sample count (from-secs).");

  if (v.seconds() != 2.0f)
    ECA_TEST_FAILURE("Incorrect time in seconds (from-secs).");

}
