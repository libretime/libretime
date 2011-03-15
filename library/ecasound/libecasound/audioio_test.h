// ------------------------------------------------------------------------
// audioio_test.h: Unit test for AUDIO_IO
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

#include <string>

#include "audioio.h"
#include "eca-object-factory.h"
#include "eca-object-map.h"

#include "kvu_numtostr.h"

#include "eca-logger.h"
#include "eca-test-case.h"

using namespace std;

/**
 * Unit test for AUDIO_IO.
 *
 * FIXME: implementation not ready
 */
class AUDIO_IO_TEST : public ECA_TEST_CASE {

protected:

  virtual string do_name(void) const { return("Unit test for AUDIO_IO"); }
  virtual void do_run(void);

public:

  virtual ~AUDIO_IO(void) { }

};

void AUDIO_IOTEST::do_run(void)
{
  /**
   * - create AUDIO_IO types from ECA_OBJECT_FACTORY
   * - check that type supports both read and write modes
   * - create a test filename
   * - repeat for permutations of 'audio_format' 
   *   and buffersize:
   *     - open for write
   *     - verify is_open
'  *     - write random length of data (len1, predefined content)
   *     - close 
   *     - verify close
   *     - if read_n_write mode supported:
   *         - open for read_write
   *         - verify is_open
   *         - seek_to_end
   *         - write random length of data (len2, predefined content)
   *         - close
   *         - verify close
   *     - open for read
   *     - verify is_open
   *     - read clip1 from start (seek + read)
   *     - read clip2 from random pos
   *     - verify that clip1 and clip2 match written data
   *     - check file length against len1+len2
   *     - close 
   *     - verify close
   *     - remove file
   */
}
