// ------------------------------------------------------------------------
// eca-sample-conversion_test.h: Unit test for sample conversion routines
// Copyright (C) 2002 Kai Vehmanen
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

#include <cstdlib>
#include <string>

#include "eca-sample-conversion.h"
#include "kvu_numtostr.h"

#include "eca-logger.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for ECA_SAMPLE-CONVERSION.
 */
class ECA_SAMPLE_CONVERSION_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for eca-sample-conversion"); }
  virtual void do_run(void);

public:

  virtual ~ECA_SAMPLE_CONVERSION_TEST(void) { }
};

void ECA_SAMPLE_CONVERSION_TEST::do_run(void)
{
  double dmax = 1.0f;
  double dmin = -1.0f;
  double dzero = 0.0f;

  uint8_t u8min = eca_sample_convert_float_to_u8(dmin);
  if (abs((int)(u8min - UINT8_MIN)) > 1) {
    cout << "u8min=" << (int)u8min << "\n";
    ECA_TEST_FAILURE("abs(u8min - UINT8_MIN) > 1");
  }

  uint8_t u8max = eca_sample_convert_float_to_u8(dmax);
  if (abs((int)(u8max - UINT8_MAX)) > 1) {
    cout << "u8max=" << (int)u8max << "\n";
    ECA_TEST_FAILURE("abs(u8max - UINT8_MAX) > 1");
  }

  uint8_t u8zero = eca_sample_convert_float_to_u8(dzero);
  if (u8zero != 128) {
    cout << "u8zero=" << (int)u8zero << "\n";
    ECA_TEST_FAILURE("u8zero != 128");
  }

  int16_t s16min = eca_sample_convert_float_to_s16(dmin);
  if (abs(s16min - INT16_MIN) > 1) {
    cout << "s16min=" << s16min << "\n";
    ECA_TEST_FAILURE("abs(s16min - INT16_MIN) > 1");
  }
  
  int16_t s16max = eca_sample_convert_float_to_s16(dmax);
  if (abs(s16max - INT16_MAX) > 1) {
    cout << "s16max=" << s16max << "\n";
    ECA_TEST_FAILURE("abs(s16max - INT16_MAX) > 1");
  }

  int16_t s16zero = eca_sample_convert_float_to_s16(dzero);
  if (s16zero != 0) {
    ECA_TEST_FAILURE("s16zero != 0");
  }

  int32_t s32min = eca_sample_convert_float_to_s32(dmin);
  if (labs(s32min - INT32_MIN) > 1) {
    ECA_TEST_FAILURE("labs(s32min - INT32_MIN) > 1");
  }
  
  int32_t s32max = eca_sample_convert_float_to_s32(dmax);
  if (labs(s32max - INT32_MAX) > 1) {
    cout << "s32max=" << s32max << "\n";
    ECA_TEST_FAILURE("labs(s32max - INT32_MAX) > 1");
  }

  int32_t s32zero = eca_sample_convert_float_to_s32(dzero);
  if (s32zero != 0) {
    ECA_TEST_FAILURE("s32zero != 0");
  }

  float cur = eca_sample_convert_u8_to_float(UINT8_MIN);
  if (cur > -1.0f || cur < -1.0f) {
    cout << "u8min cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: u8min");
  }

  cur = eca_sample_convert_u8_to_float(UINT8_MAX);
  if (cur < 1.0f || cur > 1.0f) {
    cout << "u8max cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: u8max");
  }

  cur = eca_sample_convert_s16_to_float(INT16_MIN);
  if (cur > -1.0f || cur < -1.0f) {
    cout << "s16min cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: s16min");
  }

  cur = eca_sample_convert_s16_to_float(INT16_MAX);
  if (cur < 1.0f || cur > 1.0f) {
    cout << "s16max cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: s16max");
  }

  cur = eca_sample_convert_s32_to_float(INT32_MIN);
  if (cur > -1.0f || cur < -1.0f) {
    cout << "s32min cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: s32min");
  }

  cur = eca_sample_convert_s32_to_float(INT32_MAX);
  if (cur < 1.0f || cur > 1.0f) {
    cout << "s32max cur=" << cur << "\n";
    ECA_TEST_FAILURE("to_float: s32max");
  }
}
