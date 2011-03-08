#ifndef INCLUDED_AUDIOIO_MIKMOD_H
#define INCLUDED_AUDIOIO_MIKMOD_H

#include <string>
#include <cstdio>
#include "audioio-buffered.h"
#include "audioio-forked-stream.h"

/**
 * Interface to module players such as MikMod that support 
 * UNIX pipe i/o.
 *
 * @author Kai Vehmanen
 */
class MIKMOD_INTERFACE : public AUDIO_IO_BUFFERED,
			 public AUDIO_IO_FORKED_STREAM {

 private:
  
  static std::string default_mikmod_cmd;

 public:

  static void set_mikmod_cmd(const std::string& value);
 
 public:

  MIKMOD_INTERFACE (const std::string& name = "");
  virtual ~MIKMOD_INTERFACE(void);
    
  virtual MIKMOD_INTERFACE* clone(void) const { return new MIKMOD_INTERFACE(*this); }
  virtual MIKMOD_INTERFACE* new_expr(void) const { return new MIKMOD_INTERFACE(); }

  virtual std::string name(void) const { return("MikMod tracker module"); }
  virtual std::string description(void) const { return("Interface to module players that support UNIX pipe i/o."); }

  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

  virtual int supported_io_modes(void) const { return(io_read); }
  virtual string parameter_names(void) const { return("filename,opt_filename"); }
  virtual bool supports_seeking(void) const { return(false); }

  virtual void open(void) throw (AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);
  
  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples) { }

  virtual bool finished(void) const { return(finished_rep); }

  virtual void start_io(void);
  virtual void stop_io(void);

 protected:

  /* functions called by AUDIO_IO_FORKED_STREAM that require
   * the use of AUDIO_IO methods */

  virtual bool do_supports_seeking(void) const { return supports_seeking(); }
  virtual void do_set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos) { set_position_in_samples(pos); }

 private:

  std::string opt_filename_rep;
  bool triggered_rep;
  bool finished_rep;
  long int bytes_read_rep;
  int filedes_rep;
  FILE* f1_rep;
  
  void seek_position_in_samples(long pos);
  MIKMOD_INTERFACE& operator=(const MIKMOD_INTERFACE& x) { return *this; }

  void fork_mikmod(void);
  void kill_mikmod(void);
};

#endif
