// ------------------------------------------------------------------------
// eca-chainsetup-parser_test.h: Unit test for ECA_CHAINSETUP_PARSER
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
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

#include "eca-chainsetup.h"
#include "kvu_numtostr.h"

#include "eca-logger.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_CHAINSETUP.
 *
 * FIXME: implementation not ready
 */
class ECA_CHAINSETUP_PARSER_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("ECA_CHAINSETUP_PARSER_TEST"); }
  virtual void do_run(void);

public:

  virtual ~ECA_CHAINSETUP_PARSER_TEST(void) { }

private:

  void do_run_format_options(void);

};

void ECA_CHAINSETUP_PARSER_TEST::do_run(void)
{
  do_run_format_options();
}

void ECA_CHAINSETUP_PARSER_TEST::do_run_format_options(void)
{
  ECA_CHAINSETUP csetup;
  ECA_CHAINSETUP_PARSER p(&csetup);

  p.interpret_option("-f:foo,bar");
  if (p.interpret_result() != false) {
    ECA_TEST_FAILURE("invalid sample_format accepted");
  }
  
  ECA_AUDIO_FORMAT afmt (4, 96000, ECA_AUDIO_FORMAT::sfmt_f64_be, false);
  csetup.set_default_audio_format(afmt);

  if (csetup.default_audio_format().sample_format() != ECA_AUDIO_FORMAT::sfmt_f64_be)
    ECA_TEST_FAILURE("failed to set initial audio fmt (1)");

  if (csetup.default_audio_format().channels() != 4 ||
      csetup.default_audio_format().samples_per_second() != 96000 ||
      csetup.default_audio_format().sample_format() != ECA_AUDIO_FORMAT::sfmt_f64_be ||
      csetup.default_audio_format().interleaved_channels() != false)
    ECA_TEST_FAILURE("failed to set initial audio fmt");

  /* note: Setting one audio format component should not affect other
   *       audio format components. The following section has
   *       multiple test cases related to this. */

  p.interpret_option("-f:s32_be");
  if (csetup.default_audio_format().sample_format() != ECA_AUDIO_FORMAT::sfmt_s32_be)
    ECA_TEST_FAILURE("unable to set sample format");

  if (csetup.default_audio_format().channels() != 4)
    ECA_TEST_FAILURE("setting sample format affected channels");

  ECA_LOG_MSG(ECA_LOGGER::info, afmt.format_string());
  ECA_LOG_MSG(ECA_LOGGER::info, csetup.default_audio_format().format_string());

  p.interpret_option("-f:,6,");
  if (csetup.default_audio_format().channels() != 6)
    ECA_TEST_FAILURE("unable to channels");

  if (csetup.default_audio_format().sample_format() != ECA_AUDIO_FORMAT::sfmt_s32_be)
    ECA_TEST_FAILURE("setting channels affected sample format");

  p.interpret_option("-f:,6,");
  if (csetup.default_audio_format().channels() != 6)
    ECA_TEST_FAILURE("unable to channels");

  /* note: Test setting all parameters */
  p.interpret_option("-f:u8,1,22050,i");
  if (csetup.default_audio_format().channels() != 1 ||
      csetup.default_audio_format().samples_per_second() != 22050 ||
      csetup.default_audio_format().sample_format() != ECA_AUDIO_FORMAT::sfmt_u8 ||
      csetup.default_audio_format().interleaved_channels() != true)
    ECA_TEST_FAILURE("failed to set all audio fmt components");
}
