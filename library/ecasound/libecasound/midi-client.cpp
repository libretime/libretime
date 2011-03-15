// ------------------------------------------------------------------------
// midi-client.cpp: Top-level interface for MIDI-clients
// Copyright (C) 2001,2005 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
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

#include "midi-server.h"
#include "midi-client.h"

MIDI_CLIENT::MIDI_CLIENT(void) 
  : id_rep(0),
    id_set_rep(false),
    server_repp(0)
{
}

int MIDI_CLIENT::id(void) const { return(id_rep); }

void MIDI_CLIENT::set_id(int n)
{
  id_rep = n;
  id_set_rep = true;
}

void MIDI_CLIENT::register_server(MIDI_SERVER* server)
{
  server_repp = server;
}
