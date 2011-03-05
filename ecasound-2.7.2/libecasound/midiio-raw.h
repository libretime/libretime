#ifndef INCLUDED_MIDIIO_RAW_H
#define INCLUDED_MIDIIO_RAW_H

#include <string>
#include "midiio.h"

/**
 * Input and output of raw MIDI streams using standard 
 * UNIX file operations.
 *
 * @author Kai Vehmanen
 */
class MIDI_IO_RAW : public MIDI_IO {

 public:

  MIDI_IO_RAW (const std::string& name = "");
  virtual ~MIDI_IO_RAW(void);
    
  virtual MIDI_IO_RAW* clone(void) const { return(new MIDI_IO_RAW(*this)); }
  virtual MIDI_IO_RAW* new_expr(void) const { return new MIDI_IO_RAW(); }    

  virtual std::string name(void) const { return("Raw MIDI"); }
  virtual int supported_io_modes(void) const { return(io_read | io_write | io_readwrite); }
  virtual bool supports_nonblocking_mode(void) const { return(true); }
  virtual int poll_descriptor(void) const { return(fd_rep); }
  virtual std::string parameter_names(void) const { return("label,device_name"); }

  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;
  
  virtual void open(void);
  virtual void close(void);

  virtual long int read_bytes(void* target_buffer, long int bytes);
  virtual long int write_bytes(void* target_buffer, long int bytes);

  virtual bool finished(void) const;

 private:

  int fd_rep;
  bool finished_rep;
  std::string device_name_rep;
};

#endif
