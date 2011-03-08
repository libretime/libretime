// ------------------------------------------------------------------------
// eca-samplerate-aware.: Interface class implemented by all types that 
//                        require knowledge of system samplerate
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

#include "eca-samplerate-aware.h"

ECA_SAMPLERATE_AWARE::ECA_SAMPLERATE_AWARE(SAMPLE_SPECS::sample_rate_t srate)
  : srate_rep(srate)
{
}

ECA_SAMPLERATE_AWARE::~ECA_SAMPLERATE_AWARE(void)
{
}

void ECA_SAMPLERATE_AWARE::set_samples_per_second(long int v)
{
  srate_rep = v;
}
