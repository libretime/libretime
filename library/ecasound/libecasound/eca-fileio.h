#ifndef INCLUDED_FILEIO_H
#define INCLUDED_FILEIO_H

#include <string>
#include <cstdio>

#include <sys/types.h> /* off_t */

/**
 * Interface for blocking file input/output with buffering
 */
class ECA_FILE_IO {
 public:

  virtual ~ECA_FILE_IO(void) { }

  // -----
  // Open/close routines

  virtual void open_file(const std::string& fname,
			 const std::string& fmode) = 0;
  virtual void open_stdin(void) = 0;
  virtual void open_stdout(void) = 0;
  virtual void open_stderr(void) = 0;
  virtual void close_file(void) = 0;

  // ----
  // Normal file operations

  virtual void read_to_buffer(void* obuf, off_t bytes) = 0;
  virtual void write_from_buffer(void* obuf, off_t bytes) = 0;

  virtual void set_file_position(off_t newpos) = 0;
  virtual void set_file_position_advance(off_t fw) = 0;
  virtual void set_file_position_end(void) = 0;
  virtual off_t get_file_position(void) const = 0;
  virtual off_t get_file_length(void) const = 0;

  // -----
  // Status

  virtual bool is_file_ready(void) const = 0;
  virtual bool is_file_error(void) const = 0;
  virtual off_t file_bytes_processed(void) const = 0;
  virtual const std::string& file_mode(void) const = 0;

};

#endif
