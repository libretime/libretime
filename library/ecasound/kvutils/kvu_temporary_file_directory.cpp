// ------------------------------------------------------------------------
// kvu_temporary_file_directory.cpp: Provides services for allocating and 
//                                   reserving secure, temporary 
//                                   directories.
// Copyright (C) 2001,2004 Kai Vehmanen
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

#include <stdlib.h> /* getenv() */
#include <sys/stat.h> /* mkdir() */
#include <sys/types.h> /* getuid(), geteuid(), mkdir(), lstat() */
#include <fcntl.h> /* mkdir() */
#include <unistd.h> /* getuid(), geteuid(), mkdir(), lstat(), getpid() */
#include <errno.h> /* errno */

#include "kvu_numtostr.h"
#include "kvu_temporary_file_directory.h"

/**
 * Constructor.
 */
TEMPORARY_FILE_DIRECTORY::TEMPORARY_FILE_DIRECTORY(void) 
  : valid_rep(false) { }

/**
 * Constructor that reserves a new directory.
 */
TEMPORARY_FILE_DIRECTORY::TEMPORARY_FILE_DIRECTORY(const std::string& dir) 
  : valid_rep(false) {
  reserve_directory(dir);
}

/** 
 * Reserves a new directory. In addition to argument 'dir',
 * UNIX environment variables TMPDIR, TMP are used when 
 * physically creating the directory. If these variables are 
 * not defined, default "/tmp" is used.
 */
void TEMPORARY_FILE_DIRECTORY::reserve_directory(const std::string& dir) {
  if (is_valid() == true) {
    release_directory();
  }

  tdir_rep = get_directory_prefix() + "/" + dir;

  /* FIXME: add 'unlink(tdir_rep.c_str());' ? */

  int result = mkdir(tdir_rep.c_str(), 0700);
  if (result == 0 ||
      errno == EEXIST) {
    check_validity();
    if (is_valid() != true) {
      release_directory();
    }
  }
  else {
    /* FIXME: should we try something else here? for instance
     *        selecting 'dir+string(n+1)'?
     */
//      cerr << "(kvutils) " << "mkdir(" << tdir_rep << ") failed" << endl;
    valid_rep = false;
  }
}

/**
 * Releases the directory. The physical directory 
 * is removed. For this to succeed, all created 
 * temporary files must first be removed.
 */
void TEMPORARY_FILE_DIRECTORY::release_directory(void) {
  rmdir(tdir_rep.c_str());
  valid_rep = false;
}

/**
 * Destructor. Releases the directory.
 */
TEMPORARY_FILE_DIRECTORY::~TEMPORARY_FILE_DIRECTORY(void) {
  if (is_valid() == true) {
    release_directory();
  }
}

void TEMPORARY_FILE_DIRECTORY::check_validity(void) {
  struct stat statbuf;

  valid_rep = true;

  lstat(tdir_rep.c_str(), &statbuf);

  if (statbuf.st_uid != geteuid()) {
    valid_rep = false;
    // cerr << "(kvutils) " << "st_uid doesn't match." << endl;
  }

  // kaiv, 10.10.2001 - removed as unnecessary
  //  if (statbuf.st_gid != getegid()) {
  //    valid_rep = false;
  //    cerr << "(kvutils) " << "st_gid doesn't match." << endl;
  //  }

  if (!S_ISDIR(statbuf.st_mode)) {
    valid_rep = false;
//      cerr << "(kvutils) " << "st_mode - not a directory." << endl;
  }

  if (S_ISLNK(statbuf.st_mode)) {
    valid_rep = false;
//      cerr << "(kvutils) " << "st_mode - a symbolic link." << endl;
  }

  if ((statbuf.st_mode & S_IRWXG) > 0) {
    valid_rep = false;
//      cerr << "(kvutils) " << "st_mode - group has access." << endl;
  }

  if ((statbuf.st_mode & S_IRWXO) > 0) {
    valid_rep = false;
//      cerr << "(kvutils) " << "st_mode - others have access." << endl;
  }
}

/**
 * Sets a new directory prefix (for instance "/tmp"). The new 
 * setting will take effect when the reserve_directory() is issued.
 */
void TEMPORARY_FILE_DIRECTORY::set_directory_prefix(const std::string& dir) {
  dirprefix_rep = dir;
}

std::string TEMPORARY_FILE_DIRECTORY::get_directory_prefix(void) const {
  if (dirprefix_rep.size() > 0) {
    return(dirprefix_rep);
  }
    
  std::string tmpname ("/tmp");
  if ((getuid() == geteuid()) && (getgid() == getegid())) {
    char* tmpdir_p = NULL;
    tmpdir_p = getenv("TMPDIR");
    if (tmpdir_p != NULL) {
      if (tmpdir_p != NULL) tmpname = std::string(tmpdir_p);
    }
    else {
      tmpdir_p = getenv("TMP");
      if (tmpdir_p != NULL) tmpname = std::string(tmpdir_p);
    }
  }
  return(tmpname);
}

/** 
 * Returns the whole path of the reserved directory. 
 */
std::string TEMPORARY_FILE_DIRECTORY::get_reserved_directory(void) const {
  if (is_valid() == true) 
    return(tdir_rep);

  return("");
}

/**
 * Returns a new unique name with prefix 'prefix'. Notice
 * that files won't be opened or removed, only a filename
 * is generated. Returns an empty string if directory is not 
 * in valid state, or an error has occured.
 */
std::string TEMPORARY_FILE_DIRECTORY::create_filename(const std::string& prefix, const std::string& postfix) {
  std::string fname (tdir_rep + "/" + prefix + "-" +
		kvu_numtostr(getpid()) + "-");
  struct stat statbuf;
  
  for(int n = 0; n < TEMPORARY_FILE_DIRECTORY::max_temp_files; n++) {
    if (is_valid() != true) break;

    std::string temp = fname + kvu_numtostr(tmp_index_rep) + postfix;
    if (tmp_index_rep > TEMPORARY_FILE_DIRECTORY::max_temp_files) tmp_index_rep = 0;
    ++tmp_index_rep;

    int res = lstat(temp.c_str(), &statbuf);
    if (res == -1 && errno == ENOENT) {
//        cerr << "(kvutils) Creating temp file " << temp << "." << endl;
      return(temp);
    }
    
  }

  return("");
}


/**
 * Whether directory is ready for use.
 */
bool TEMPORARY_FILE_DIRECTORY::is_valid(void) const {
  return(valid_rep);
}
