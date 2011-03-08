#ifndef INCLUDED_FILEIO_MMAP_H
#define INCLUDED_FILEIO_MMAP_H

#include <sys/types.h>

#include "eca-fileio.h"

#ifndef MAP_FAILED
#define MAP_FAILED	((__ptr_t) -1)
#endif

#if !defined _CADDR_T && !defined __USE_BSD
#define _CADDR_T
typedef char* caddr_t;
#endif

/**
 * File-io and buffering using mmap for data transfers.
 */
class ECA_FILE_IO_MMAP : public ECA_FILE_IO {

 public:

  ECA_FILE_IO_MMAP(void);
  virtual ~ECA_FILE_IO_MMAP(void);

  // --
  // Open/close routines
  // ---
  virtual void open_file(const std::string& fname, const std::string& fmode);
  virtual void open_stdin(void) { }
  virtual void open_stdout(void) { }
  virtual void open_stderr(void) { }
  virtual void close_file(void);

  // --
  // Normal file operations
  // ---
  virtual void read_to_buffer(void* obuf, off_t bytes);
  virtual void write_from_buffer(void* obuf, off_t bytes);

  virtual void set_file_position(off_t newpos) { set_file_position(newpos,true); }
  virtual void set_file_position(off_t newpos, bool seek);
  virtual void set_file_position_advance(off_t fw);
  virtual void set_file_position_end(void);
  virtual off_t get_file_position(void) const;
  virtual off_t get_file_length(void) const;

  // --
  // Status
  // ---
  virtual bool is_file_ready(void) const;
  virtual bool is_file_error(void) const;
  virtual bool is_file_ended(void) const;
  virtual off_t file_bytes_processed(void) const;
  virtual const std::string& file_mode(void) const { return(mode_rep); }

 private:

  int fd_rep;
  caddr_t buffer_repp;
  off_t bytes_rep;
  off_t fposition_rep;
  off_t flength_rep;

  bool file_ready_rep;
  bool file_ended_rep;
  std::string mode_rep;
  std::string fname_rep;
};

#endif
