// ------------------------------------------------------------------------
// audio-stamp.cpp: Classes for handling audio stamps and their clients
// Copyright (C) 2000 Kai Vehmanen
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

#include "audio-stamp.h"

AUDIO_STAMP::AUDIO_STAMP(void) 
  : id_rep(0),
    id_set_rep(false) { }

int AUDIO_STAMP::id(void) const { return(id_rep); }

void AUDIO_STAMP::set_id(int n) {
  id_rep = n;
  id_set_rep = true;
}

void AUDIO_STAMP::store(const SAMPLE_BUFFER* x) {
  buffer_rep.copy_all_content(*x);
}

void AUDIO_STAMP::fetch_stamp(SAMPLE_BUFFER* x) {
  x->copy_all_content(buffer_rep);
}

void AUDIO_STAMP_SERVER::register_stamp(AUDIO_STAMP* stamp) {
  stamp_map_rep[stamp->id()] = stamp;
}

void AUDIO_STAMP_SERVER::fetch_stamp(int id, SAMPLE_BUFFER* x) {
  if (stamp_map_rep.find(id) == stamp_map_rep.end()) {
    x->make_silent();
    // std::cerr << "(as-server) Making silent!" << std::endl;
  }
  else {
    AUDIO_STAMP* p = stamp_map_rep[id];
    p->fetch_stamp(x);
//      cerr << "(as-server) fetch stamp from id " << p->id() << "." << endl;
  }
}

AUDIO_STAMP_CLIENT::AUDIO_STAMP_CLIENT(void) 
  : id_rep(0),
    id_set_rep(false),
    server_repp(0) { }

int AUDIO_STAMP_CLIENT::id(void) const { return(id_rep); }

void AUDIO_STAMP_CLIENT::set_id(int n) {
  id_rep = n;
  id_set_rep = true;
}

void AUDIO_STAMP_CLIENT::fetch_stamp(SAMPLE_BUFFER* x) {
  if (server_repp != 0) {
    server_repp->fetch_stamp(id(), x);
//      cerr << "(as-client) fetch stamp id " << id() << "." << endl;
  }
  else {
//      cerr << "(as-client) Making silent!" << endl;
    x->make_silent();
  }
}

void AUDIO_STAMP_CLIENT::register_server(AUDIO_STAMP_SERVER* server) {
  server_repp = server;
}
