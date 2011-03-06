#ifndef INCLUDED_AUDIOIO_MP3_H
#define INCLUDED_AUDIOIO_MP3_H

#include <string>
#include <cstdio>
#include "audioio-buffered.h"
#include "audioio-forked-stream.h"
#include "sample-specs.h"

/**
 * Interface for mp3 decoders and encoders that support 
 * input/output using standard streams. Defaults to
 * mpg123 and lame.
 * @author Kai Vehmanen
 */
class MP3FILE : public AUDIO_IO_BUFFERED,
		public AUDIO_IO_FORKED_STREAM {

 private:
  
  static std::string conf_input_cmd;
  static std::string conf_output_cmd;
  static long int conf_default_output_bitrate;

 public:

  static void set_input_cmd(const std::string& value);
  static void set_output_cmd(const std::string& value);
 
 public:

  MP3FILE (const std::string& name = "");
  virtual ~MP3FILE(void);
    
  virtual MP3FILE* clone(void) const { return new MP3FILE(*this); }
  virtual MP3FILE* new_expr(void) const { return new MP3FILE(*this); }

  virtual std::string name(void) const { return("Mp3 stream"); }
  virtual std::string description(void) const { return("Interface for mp3 decoders and encoders that support input/output using standard streams."); }
  virtual std::string parameter_names(void) const { return("label,bitrate"); }
  virtual bool locked_audio_format(void) const { return(true); }

  virtual int supported_io_modes(void) const { return(io_read | io_write); }
  virtual bool supports_seeking(void) const { return io_mode() == io_read; }
  virtual bool supports_seeking_sample_accurate(void) const { return false; }

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);
  
  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual bool finished(void) const { return finished_rep; }
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

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

  bool finished_rep;
  bool triggered_rep;
  int pid_of_child_rep;
  long pcm_rep;
  long int bytes_rep;
  long int bitrate_rep;
  SAMPLE_SPECS::sample_pos_t last_position_rep;
  int filedes_rep;
  FILE* filehandle_rep;
  bool mono_input_rep;
  
  void process_mono_fix(char* target_buffer, long int bytes_rep);
  void get_mp3_params(const std::string& fname) throw(AUDIO_IO::SETUP_ERROR&);
  
  //  MP3FILE(const MP3FILE& x) { }
  MP3FILE& operator=(const MP3FILE& x) { return *this; }


  void fork_input_process(void);
  void fork_output_process(void);
};

#endif
