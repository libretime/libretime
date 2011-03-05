// ------------------------------------------------------------------------
// audioio-device.cpp: Virtual base class for real-time devices.
// Copyright (C) 1999-2001 Kai Vehmanen
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

#include <kvu_dbc.h>
#include <kvu_message_item.h>

#include "audioio-device.h"

AUDIO_IO_DEVICE::AUDIO_IO_DEVICE(void) 
  : is_running_rep(false),
    is_prepared_rep(false),
    ignore_xruns_rep(true),
    max_buffers_rep(true) 
{
}

AUDIO_IO_DEVICE::~AUDIO_IO_DEVICE(void)
{
  if (is_open() == true)
    close();

  DBC_CHECK(is_prepared() != true);
  DBC_CHECK(is_running() != true);
}

bool AUDIO_IO_DEVICE::is_realtime_object(const AUDIO_IO* aobj)
{
  const AUDIO_IO_DEVICE* p = dynamic_cast<const AUDIO_IO_DEVICE*>(aobj);
  if (p != 0) return(true);
  return(false);
}

string AUDIO_IO_DEVICE::status(void) const
{
  MESSAGE_ITEM mitem;

  mitem << "realtime-device; position ";
  mitem << position_in_samples() << ", delay ";
  mitem << delay() << ".\n -> ";
  
  if (is_open() == true) 
    mitem << "open, ";
  else 
    mitem << "closed, ";

  mitem << format_string() << "/" << channels() << "ch/" << samples_per_second();
  mitem << "Hz, buffer " << buffersize() << ".";

  return(mitem.to_string());
}
