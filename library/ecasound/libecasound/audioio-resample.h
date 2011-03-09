#ifndef INCLUDED_AUDIOIO_RESAMPLE_H
#define INCLUDED_AUDIOIO_RESAMPLE_H

#include <string>
#include <vector>
#include <iostream>

#include "samplebuffer.h"
#include "audioio-proxy.h"

/**
 * A proxy class that resamples the the child 
 * object's data.
 *
 * Related design patterns:
 *     - Proxy (GoF207
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_RESAMPLE : public AUDIO_IO_PROXY {

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_IO_RESAMPLE (void); 
  virtual ~AUDIO_IO_RESAMPLE(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(string("Resample => ") + child()->name()); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  virtual bool variable_params(void) const { return true; }
  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_IO_RESAMPLE* clone(void) const;
  virtual AUDIO_IO_RESAMPLE* new_expr(void) const { return(new AUDIO_IO_RESAMPLE()); }

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual int supported_io_modes(void) const { return(io_read); }
  virtual bool supports_seeking(void) const { return(true); }
  virtual bool finite_length_stream(void) const { return(true); }

  virtual void read_buffer(SAMPLE_BUFFER* sbuf);
  virtual void write_buffer(SAMPLE_BUFFER* sbuf);

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  virtual void set_buffersize(long int samples);
  virtual long int buffersize(void) const;

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_FORMAT */
  /*@{*/

  virtual void set_audio_format(const ECA_AUDIO_FORMAT& f_str);

  /*@}*/

  /** @name Reimplemented functions from ECA_SAMPLERATE_AWARE */
  /*@{*/
  
  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

 private:

  void recalculate_psfactor(void);

  mutable std::vector<std::string> params_rep;
  bool init_rep;
  SAMPLE_SPECS::sample_rate_t child_srate_conf_rep;
  float psfactor_rep;
  int quality_rep;
  SAMPLE_BUFFER sbuf_rep;
  SAMPLE_BUFFER leftoverbuf_rep;

  static const int child_parameter_offset = 2;

  AUDIO_IO_RESAMPLE& operator=(const AUDIO_IO_RESAMPLE& x) { return *this; }
  AUDIO_IO_RESAMPLE (const AUDIO_IO_RESAMPLE& x) { }

};

#endif
