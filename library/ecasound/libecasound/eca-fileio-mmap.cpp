// ------------------------------------------------------------------------
// eca-fileio-mmap.cpp: mmap based file-I/O and buffering routines.
// Copyright (C) 1999-2002,2009 Kai Vehmanen
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

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cstdio>
#include <cstring> /* memcpy */
#include <unistd.h>
#include <fcntl.h>
#include <errno.h>
#ifdef HAVE_SYS_MMAN_H
#include <sys/mman.h>
#endif
#include <sys/stat.h>
#include <sys/types.h>

#include "eca-fileio.h"
#include "eca-fileio-mmap.h"

ECA_FILE_IO_MMAP::ECA_FILE_IO_MMAP(void) { }
ECA_FILE_IO_MMAP::~ECA_FILE_IO_MMAP(void) { }

void ECA_FILE_IO_MMAP::open_file(const std::string& fname, 
				 const std::string& fmode)
{
#ifdef HAVE_MMAP
  int openflags = O_RDWR;
  int mmapflags = PROT_READ | PROT_WRITE;
  fname_rep = fname;
  
  if (fmode == "rb") {
    openflags = O_RDONLY;
    mmapflags = PROT_READ;
  }
  else if (fmode == "wb") {
    openflags = O_WRONLY;
    mmapflags = PROT_WRITE;
  }

  fd_rep = ::open(fname.c_str(), openflags);
  if (!fd_rep) {
    file_ready_rep = false;
    mode_rep = "";
  }
  else {
    file_ready_rep = true;
    file_ended_rep = false;
    mode_rep = fmode;
    fposition_rep = 0;
    flength_rep = get_file_length();
//      cerr << fname_rep << ": mmaping region from 0 to " <<
//        flength_rep << "." << endl;
    buffer_repp = (caddr_t)::mmap(0,
				  flength_rep,
				  mmapflags,
				  MAP_SHARED,
				  fd_rep,
				  0);
    
    if (buffer_repp == MAP_FAILED) {
      file_ready_rep = false;
      mode_rep = "";
    }
  }
#else /* HAVE_MMAP */
  file_ready_rep = false;
  mode_rep = "";
#endif
}

void ECA_FILE_IO_MMAP::close_file(void) {
//    cerr << fname_rep << ": munmaping region." << endl;
#ifdef HAVE_MMAP
  ::munmap(buffer_repp, flength_rep);
  ::close(fd_rep);
#endif
}

void ECA_FILE_IO_MMAP::read_to_buffer(void* obuf, off_t bytes) {
  if (is_file_ready() == false) {
    bytes_rep = 0;
    file_ended_rep = true;
    return;
  }

  if (fposition_rep + bytes > flength_rep)
    bytes = flength_rep - fposition_rep;

//    cerr << fname_rep << ": mmap read " << fposition_rep << " -> ";
  std::memcpy(obuf, buffer_repp + fposition_rep, bytes);
//    cerr <<  fposition_rep + bytes << "." << endl;
  set_file_position(fposition_rep + bytes, false);
  bytes_rep = bytes;
}

void ECA_FILE_IO_MMAP::write_from_buffer(void* obuf, off_t bytes) { 
  if (is_file_ready() == false) {
    bytes_rep = 0;
    file_ended_rep = true;
    return;
  }
  
  if (fposition_rep + bytes > flength_rep)
    bytes = flength_rep - fposition_rep;

  std::memcpy(buffer_repp + fposition_rep, obuf, bytes);
  set_file_position(fposition_rep + bytes, false);
  bytes_rep = bytes;
}

off_t ECA_FILE_IO_MMAP::file_bytes_processed(void) const { return(bytes_rep); }
bool ECA_FILE_IO_MMAP::is_file_ready(void) const { return(file_ready_rep); }
bool ECA_FILE_IO_MMAP::is_file_ended(void) const { return(file_ended_rep); }
bool ECA_FILE_IO_MMAP::is_file_error(void) const { return(!file_ready_rep && !file_ended_rep); }

void ECA_FILE_IO_MMAP::set_file_position(off_t newpos, bool seek) { 
  fposition_rep = newpos;
  if (fposition_rep >= flength_rep) {
    fposition_rep = flength_rep;
    file_ready_rep = false;
    file_ended_rep = true;
  }
  else {
    file_ready_rep = true;
    file_ended_rep = false;
  }
}

void ECA_FILE_IO_MMAP::set_file_position_advance(off_t fw) { 
  set_file_position(fposition_rep + fw, false);
}

void ECA_FILE_IO_MMAP::set_file_position_end(void) { 
  fposition_rep = get_file_length();
}

off_t ECA_FILE_IO_MMAP::get_file_position(void) const { return(fposition_rep); }

off_t ECA_FILE_IO_MMAP::get_file_length(void) const {
  struct stat stattemp;
  fstat(fd_rep, &stattemp);
  return((off_t)stattemp.st_size);
}
