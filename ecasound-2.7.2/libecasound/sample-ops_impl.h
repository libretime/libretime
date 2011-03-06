// ------------------------------------------------------------------------
// sample-ops_impl.h: Sample value defaults and constants.
// Copyright (C) 2004 Kai Vehmanen
// Copyright (C) 2004 Steve Harris, Tim Blechmann
//
// Attributes:
//     eca-style-version: 2
//     public-libecasound-API: no
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

#ifndef INCLUDED_SAMPLE_OPS_IMPL_H
#define INCLUDED_SAMPLE_OPS_IMPL_H

#include <kvu_inttypes.h>

/* 32 bit "pointer cast" union */
typedef union {
        float f;
        int32_t i;
} ls_pcast32;

/**
 * Truncates small float values 'f' to zero to avoid 
 * denormal floats in processing.
 * 
 * Taken from swh-plugins 0.4.11 package (ladspa-util.h).
 *
 * @author Steve Harris
 * @author Tim Blechmann
 */
static inline float ecaops_flush_to_zero(float f)
{
	ls_pcast32 v;

	v.f = f;

	// original: return (v.i & 0x7f800000) == 0 ? 0.0f : f;
	// version from Tim Blechmann
	return (v.i & 0x7f800000) < 0x08000000 ? 0.0f : f;
}

#endif
