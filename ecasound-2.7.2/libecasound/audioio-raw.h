#ifndef INCLUDE_AUDIOIO_RAW_H
#define INCLUDE_AUDIOIO_RAW_H

#include <string>
#include <iostream>

#include "eca-fileio.h"
#include "eca-fileio-mmap.h"
#include "eca-fileio-stream.h"

/**
 * Class for handling raw/headerless audio files
 *
 * - multiple channels are interleaved, left channel first
 *
 * @author Kai Vehmanen
 */
class RAWFILE : public AUDIO_IO_BUFFERED {

 public:

  RAWFILE (const std::string& name = "");
  virtual ~RAWFILE(void);
    
  virtual RAWFILE* clone(void) const;
  virtual RAWFILE* new_expr(void) const { return new RAWFILE(); }    

  virtual std::string name(void) const { return("Raw audio file"); }
  virtual std::string parameter_names(void) const { return("label,toggle_mmap"); }
  
  virtual void open(void) throw (AUDIO_IO::SETUP_ERROR &);
  virtual void close(void);

  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  virtual bool finished(void) const;
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

private:

  ECA_FILE_IO* fio_repp;
  std::string mmaptoggle_rep;

  RAWFILE(const RAWFILE& x) { }
  RAWFILE& operator=(const RAWFILE& x) { return *this; }

  void set_length_in_bytes(void);
};

#endif
