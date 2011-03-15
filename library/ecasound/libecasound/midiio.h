#ifndef INCLUDED_MIDIIO_H
#define INCLUDED_MIDIIO_H

#include <string>

#include "dynamic-object.h"

/**
 * Virtual base for all MIDI I/O classes.
 *
 *  - type definitions
 *
 *  - attributes
 *
 *  - configuration (setting and getting configuration parameters)
 *
 *  - functionality (control and runtime information)
 *
 *  - runtime information
 *
 *  - constructors and destructors
 *
 * @author Kai Vehmanen
 */
class MIDI_IO : public DYNAMIC_OBJECT<std::string> {

 public:

  /** @name Public type definitions and constants */
  /*@{*/

  /**
   * Input/Output mode
   *
   * @see io_mode()
   *
   * io_read
   *
   * Object is opened for input. If opening a file, 
   * it must exist.
   *
   * io_write
   *
   * Object is opened for output. If opening a file and
   * and output exists, it is first truncated.
   * 
   * io_readwrite
   *
   * Object is opened for both reading and writing. If
   * opening a file, a new file is created if needed. 
   * When switching from read to write or vica versa,
   * position should be reset before using the device.
   **/
  enum Io_mode { io_read = 1, io_write = 2, io_readwrite = 4 };

  /*@}*/

 public:

  /** @name Constructors and destructors */
  /*@{*/

  virtual MIDI_IO* clone(void) const = 0;
  virtual MIDI_IO* new_expr(void) const = 0;
  virtual ~MIDI_IO(void);
  MIDI_IO(const std::string& name = "unknown",
	  int mode = io_read);

  /*@}*/

  /** @name Attribute functions */
  /*@{*/

  virtual int supported_io_modes(void) const;
  virtual bool supports_nonblocking_mode(void) const;

  /*@}*/

  /** @name Configuration 
   * 
   * For setting and getting configuration parameters.
   */
  /*@{*/
  
  int io_mode(void) const;
  const std::string& label(void) const;

  void io_mode(int mode);
  void label(const std::string& id_label);
  void toggle_nonblocking_mode(bool value);

  virtual std::string parameter_names(void) const { return("label"); }
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Main functionality */
  /*@{*/

 public:

  /**
   * Low-level routine for reading MIDI bytes. Number of read bytes
   * is returned. This must be implemented by all subclasses.
   */
  virtual long int read_bytes(void* target_buffer, long int bytes) = 0;

  /**
   * Low-level routine for writing MIDI bytes. Number of bytes written
   * is returned. This must be implemented by all subclasses.
   */
  virtual long int write_bytes(void* target_buffer, long int bytes) = 0;

  /**
   * Opens the MIDI object (possibly in exclusive mode).
   * This routine is meant for opening files and devices,
   * loading libraries, etc. 
   *
   * ensure:
   *  readable() == true || writable() == true
   */
  virtual void open(void) = 0;

  /**
   * Closes the MIDI object. After calling this routine, 
   * all resources (ie. soundcard) must be freed
   * (they can be used by other processes).
   *
   * ensure:
   *  readable() != true
   *  writable() != true
   */
  virtual void close(void) = 0;

  /*@}*/

  /** @name Runtime information */
  /*@{*/

  /**
   * Returns a file descriptor id suitable for poll() and 
   * select() system calls. If polling is not supported,
   * returns value of '-1'.
   */
  virtual int poll_descriptor(void) const { return(-1); }

  /**
   * Has device been opened (with open())?
   */
  bool is_open(void) const { return(open_rep); }

  /**
   * Whether all data has been processed? If opened in mode 'io_read', 
   * this means that end of stream has been reached. If opened in 
   * 'io_write' or 'io_readwrite' modes, finished status usually
   * means that an error has occured (no space left, etc). After 
   * finished() has returned 'true', further calls to read() 
   * and/or write() won't process any data.
   */
  virtual bool finished(void) const = 0;

  virtual bool nonblocking_mode(void) const;
  virtual bool readable(void) const;
  virtual bool writable(void) const;

  /*@}*/

 protected:

  void toggle_open_state(bool value);

 private:
  
  int io_mode_rep;
  std::string id_label_rep;

  bool nonblocking_rep;
  bool readable_rep;
  bool writable_rep;
  bool open_rep;
};

#endif
