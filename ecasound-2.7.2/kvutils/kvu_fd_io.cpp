// ------------------------------------------------------------------------
// kvu_fd_io.cpp: Helper functions for reading from, writing to and 
//                waiting on UNIX file descriptors.
//
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

#include <unistd.h>      /* POSIX: read()/write() */
#include <sys/poll.h>    /* XPG4-UNIX: poll() */

#include "kvu_fd_io.h"

/**
 * Attempts to read up to 'count' bytes from file descriptor 'fd' 
 * into the buffer starting at 'buf'. If no data is available
 * for reading, up to 'timeout' milliseconds will be waited. 
 * A negative value means infinite timeout.
 */
ssize_t kvu_fd_read(int fd, void *buf, size_t count, int timeout)
{
  int nfds = 1;
  struct pollfd ufds;
  ssize_t rescount = 0;

  ufds.fd = fd;
  ufds.events = POLLIN | POLLPRI;
  ufds.revents = 0;
  
  int ret = poll(&ufds, nfds, timeout);
  if (ret > 0) {
    if (ufds.revents & POLLIN ||
	ufds.revents & POLLPRI) {
      rescount = ::read(fd, buf, count);
    }
  }
  else if (ret == 0) {
    /* timeout */
    rescount = -1;
  }
  return(rescount);
}

/**
 * Attempts to write up to 'count' bytes to file descriptor 'fd'
 * from the buffer starting at 'buf'. If no space is available
 * for writing, up to 'timeout' milliseconds will be waited. 
 * A negative value means infinite timeout.
 */
ssize_t kvu_fd_write(int fd, const void *buf, size_t count, int timeout)
{
  int nfds = 1;
  struct pollfd ufds;
  ssize_t rescount = 0;
  
  ufds.fd = fd;
  ufds.events = POLLOUT;
  ufds.revents = 0;

  int ret = poll(&ufds, nfds, timeout);
  if (ret > 0) {
    if (ufds.revents & POLLOUT) {
      rescount = ::write(fd, buf, count);
    }
  }
  else if (ret == 0) {
    /* timeout */
    rescount = -1;
  }
  return(rescount);
}

/**
 * Blocks until state of file descriptor 'fd' 
 * changes. State changes include 'fd' becoming
 * readable, writing or an error condition.
 * A maximum of 'timeout' milliseconds will be
 * waited. A negative value means infinite timeout.
 *
 * @return returns 1 for success, 0 for timeout 
 *         and -1 on error
 */
int kvu_fd_wait(int fd, int timeout)
{
  int nfds = 1;
  struct pollfd ufds;

  ufds.fd = fd;
  ufds.events = POLLIN | POLLPRI | POLLOUT;
  ufds.revents = 0;

  int ret = poll(&ufds, nfds, timeout);
  if (ret > 0) {
    if (ufds.revents & POLLERR ||
	ufds.revents & POLLHUP ||
	ufds.revents & POLLNVAL) {
      /* error */
      return(-1);
    }
    else {
      /* success */
      return(1);
    }
  }
  else if (ret == 0) {
    /* timeout */
    return(0);
  }

  /* error */
  return(-1);
}
