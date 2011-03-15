#ifndef INCLUDED_FILEIO_STREAM_H
#define INCLUDED_FILEIO_STREAM_H

#include <string>
#include <cstdio>
#include "eca-fileio.h"


/**
 * File-I/O and buffering routines using normal file streams.
 */
class ECA_FILE_IO_STREAM : public ECA_FILE_IO {

 public:

  ECA_FILE_IO_STREAM (void) { }
  virtual ~ECA_FILE_IO_STREAM(void);

  // --
  // Open/close routines
  // ---
  virtual void open_file(const std::string& fname, const std::string& fmode);
  virtual void open_stdin(void);
  virtual void open_stdout(void);
  virtual void open_stderr(void);
  virtual void close_file(void);

  // --
  // Normal file operations
  // ---
  virtual void read_to_buffer(void* obuf, off_t bytes);
  virtual void write_from_buffer(void* obuf, off_t bytes);

  virtual void set_file_position(off_t newpos);
  virtual void set_file_position_advance(off_t fw);
  virtual void set_file_position_end(void);
  virtual off_t get_file_position(void) const;
  virtual off_t get_file_length(void) const;

  // --
  // Status
  // ---
  virtual bool is_file_ready(void) const;
  virtual bool is_file_error(void) const;
  virtual off_t file_bytes_processed(void) const;
  virtual const std::string& file_mode(void) const { return(mode_rep); }

 private:

  FILE *f1;
  off_t curpos_rep;
  off_t last_rep;

  std::string mode_rep;
  std::string fname_rep;
  bool standard_mode;
};

#endif
