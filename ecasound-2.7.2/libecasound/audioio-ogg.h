#ifndef INCLUDED_AUDIOIO_OGG_H
#define INCLUDED_AUDIOIO_OGG_H

#include <string>
#include <cstdio>
#include "audioio-buffered.h"
#include "audioio-forked-stream.h"

/**
 * Interface for Ogg Vorbis decoders and encoders using UNIX 
 * pipe i/o. By default ogg123 and vorbize are used.
 *
 * @author Kai Vehmanen
 */
class OGG_VORBIS_INTERFACE : public AUDIO_IO_BUFFERED,
			     public AUDIO_IO_FORKED_STREAM {

 private:
  
  static std::string default_input_cmd;
  static std::string default_output_cmd;

 public:

  static void set_input_cmd(const std::string& value);
  static void set_output_cmd(const std::string& value);
  static long int default_output_default_bitrate;

 public:

  OGG_VORBIS_INTERFACE (const std::string& name = "");
  virtual ~OGG_VORBIS_INTERFACE(void);
    
  virtual OGG_VORBIS_INTERFACE* clone(void) const { return new OGG_VORBIS_INTERFACE(*this); }
  virtual OGG_VORBIS_INTERFACE* new_expr(void) const { return new OGG_VORBIS_INTERFACE(*this); }

  virtual std::string name(void) const { return "Ogg Vorbis stream"; }
  virtual std::string description(void) const { return "Interface for Ogg Vorbis decoders and encoders using UNIX pipe i/o."; }
  virtual std::string parameter_names(void) const { return "label,bitrate"; }
  virtual bool locked_audio_format(void) const { return true; }

  virtual int supported_io_modes(void) const { return io_read | io_write; }
  virtual bool supports_seeking(void) const { return false; }

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);
  
  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual bool finished(void) const { return(finished_rep); }

  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  virtual void start_io(void);
  virtual void stop_io(void);

 protected:

  /* functions called by AUDIO_IO_FORKED_STREAM that require
   * the use of AUDIO_IO methods */

  virtual bool do_supports_seeking(void) const { return supports_seeking(); }
  virtual void do_set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos) { set_position_in_samples(pos); }

 private:

  bool triggered_rep;
  bool finished_rep;
  long int bytes_rep;
  long int bitrate_rep;
  int filedes_rep;
  FILE* f1_rep;
  
  void fork_input_process(void);
  void fork_output_process(void);
};

#endif
