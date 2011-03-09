#ifndef INCLUDED_AUDIO_IO_PROXY_H
#define INCLUDED_AUDIO_IO_PROXY_H

#include <kvu_dbc.h>

#include "audioio.h"
#include "audioio-barrier.h"

class SAMPLE_BUFFER;

/**
 * Generic interface for objects that act as
 * proxies for other objects of type AUDIO_IO.
 *
 * Related design patterns:
 *     - Proxy (GoF207
 *
 * Guide lines for subclassing:
 *     - reimplement (or explicitly use the existing implementation)
 *       all public getter and setter functions
 *     -> rationale: otherwise the class might return invalid
 *        data not related to the proxied child object, or setting 
 *        parameters never affect the proxy target
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_PROXY
  : public AUDIO_IO,
    public AUDIO_IO_BARRIER
{

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_IO_PROXY (void); 
  virtual ~AUDIO_IO_PROXY(void);

  /*@}*/

  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(string("Proxy => ") + child_repp->name()); }
  virtual std::string description(void) const { return(child_repp->description()); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS */
  /*@{*/

  virtual bool variable_params(void) const { return true; }
  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_IO_PROXY* clone(void) const { return(new AUDIO_IO_PROXY()); }
  virtual AUDIO_IO_PROXY* new_expr(void) const { return(new AUDIO_IO_PROXY()); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos) { return child_repp->seek_position(pos); }
  virtual bool supports_seeking(void) const { return child_repp->supports_seeking(); }
  virtual bool supports_seeking_sample_accurate(void) const { return child_repp->supports_seeking_sample_accurate(); }

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual int supported_io_modes(void) const { return(child_repp->supported_io_modes()); }
  virtual bool supports_nonblocking_mode(void) const { return(child_repp->supports_nonblocking_mode()); }
  virtual bool finite_length_stream(void) const { return( child_repp->finite_length_stream()); }
  virtual bool locked_audio_format(void) const { return(child_repp->locked_audio_format()); }

  virtual void set_buffersize(long int samples);
  virtual long int buffersize(void) const { return(buffersize_rep); }

  virtual void read_buffer(SAMPLE_BUFFER* sbuf) { child_repp->read_buffer(sbuf); }
  virtual void write_buffer(SAMPLE_BUFFER* sbuf) { child_repp->write_buffer(sbuf); }

  virtual bool finished(void) const { return(child_repp->finished()); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_FORMAT */
  /*@{*/

  virtual void set_channels(SAMPLE_SPECS::channel_t v);
  virtual void set_sample_format(Sample_format v) throw(ECA_ERROR&);
  virtual void set_audio_format(const ECA_AUDIO_FORMAT& f_str);
  virtual void toggle_interleaved_channels(bool v);

  /*@}*/

  /** @name Reimplemented functions from ECA_SAMPLERATE_AWARE */
  /*@{*/
  
  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO_BARRIER */
  /*@{*/

  virtual void start_io(void);
  virtual void stop_io(void);

  /*@}*/

 protected: 

  void set_child(AUDIO_IO* v);
  void release_child_no_delete(void);
  void pre_child_open(void);
  void post_child_open(void);
  bool is_child_initialized(void) const { return child_initialized_rep; }

  std::string child_params_as_string(int first, std::vector<std::string>* params);

  AUDIO_IO* child(void) const { return child_repp; }

 private:

  AUDIO_IO* child_repp;
  long int buffersize_rep;
  bool child_initialized_rep;
};

#endif // INCLUDED_AUDIO_IO_PROXY
