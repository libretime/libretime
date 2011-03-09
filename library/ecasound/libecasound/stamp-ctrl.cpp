// ------------------------------------------------------------------------
// stamp-ctrl.cpp: Controller sources that analyze audio stamps
//                 and produce control data.
// Copyright (C) 2000,2001,2008 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// This program is fre software; you can redistribute it and/or modify
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

#include "samplebuffer_functions.h"
#include "stamp-ctrl.h"

VOLUME_ANALYZE_CONTROLLER::VOLUME_ANALYZE_CONTROLLER(void)
{
}

CONTROLLER_SOURCE::parameter_t VOLUME_ANALYZE_CONTROLLER::value(double pos)
{
  parameter_t v = 0.0f;

  fetch_stamp(&sbuf_rep);

  if (rms_mode_rep != 0) 
    v = SAMPLE_BUFFER_FUNCTIONS::RMS_volume(sbuf_rep);
  else
    v = SAMPLE_BUFFER_FUNCTIONS::average_amplitude(sbuf_rep);
  if (!(v > 0.0f)) v = 0.0f;
  // cerr << "(volume-analyze-ctrl) Fetches a sbuf with value " << v  << endl;

  return v;
}

void VOLUME_ANALYZE_CONTROLLER::init(void)
{
}

void VOLUME_ANALYZE_CONTROLLER::set_parameter(int param, CONTROLLER_SOURCE::parameter_t value)
{
  switch (param) {
  case 1: 
    set_id(static_cast<int>(value));
    break;

  case 2: 
    rms_mode_rep = static_cast<int>(value);
    break;

  }
}

CONTROLLER_SOURCE::parameter_t VOLUME_ANALYZE_CONTROLLER::get_parameter(int param) const
{
  switch (param) {
  case 1: 
    return static_cast<parameter_t>(id());

  case 2: 
    return static_cast<parameter_t>(rms_mode_rep);
  }
  return 0.0f;
}
