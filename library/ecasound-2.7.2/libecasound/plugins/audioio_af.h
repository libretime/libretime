#ifndef INCLUDED_AUDIOIO_AF_H
#define INCLUDED_AUDIOIO_AF_H

#include <string>
#include <audiofile.h>
#include "samplebuffer.h"

#include "audioio-buffered.h"
#include "samplebuffer.h"

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

/**
 * Interface to SGI audiofile library.
 * @author Kai Vehmanen
 */
class AUDIOFILE_INTERFACE : public AUDIO_IO_BUFFERED {

  std::string opt_filename_rep;
  long samples_read;
  bool finished_rep;
  AFfilehandle afhandle;

  AUDIOFILE_INTERFACE& operator=(const AUDIOFILE_INTERFACE& x) {
    return *this; }
  void debug_print_type(void);
  
 public:

  virtual string name(void) const { return "SGI libaudiofile object"; }
  virtual string description(void) const { return "SGI libaudiofile object. Supports AIFF (.aiff, .aifc, .aif) and Sun/NeXT audio files (.au, .snd)."; }

  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

  virtual int supported_io_modes(void) const { return (io_read | io_write); }
  virtual bool supports_seeking(void) const { return (io_mode() == io_read); }
  virtual string parameter_names(void) const { return "filename,opt_filename"; }
  virtual bool locked_audio_format(void) const { return true; }
  
  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);
  
  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual bool finished(void) const;
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);
    
  AUDIOFILE_INTERFACE* clone(void) const;
  AUDIOFILE_INTERFACE* new_expr(void) const { return new AUDIOFILE_INTERFACE(); }  

  AUDIOFILE_INTERFACE (const string& name = "");
  ~AUDIOFILE_INTERFACE(void);
};

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
extern "C" {
AUDIO_IO* audio_io_descriptor(void) { return(new AUDIOFILE_INTERFACE()); }
int audio_io_interface_version(void);
const char* audio_io_keyword(void);
const char* audio_io_keyword_regex(void);
};
#endif

#endif
