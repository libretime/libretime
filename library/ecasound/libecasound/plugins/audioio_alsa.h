#ifndef INCLUDED_AUDIO_IO_ALSA_PCM_H
#define INCLUDED_AUDIO_IO_ALSA_PCM_H

#include <string>
#include <iostream>

#include <sys/time.h>
#include <sys/types.h>
#include <sys/ioctl.h>
#include <unistd.h>
#include <fcntl.h>

#include "sample-specs.h"
#include "samplebuffer.h"
#include "audioio-device.h"

#include <alsa/asoundlib.h>

using namespace std;

/**
 * Class for handling ALSA pcm-devices (Advanced Linux Sound Architecture).
 */
class AUDIO_IO_ALSA_PCM : public AUDIO_IO_DEVICE {

 public:

  AUDIO_IO_ALSA_PCM (int card = 0, int device = 0, int subdevice = -1);
  virtual ~AUDIO_IO_ALSA_PCM(void);
  AUDIO_IO_ALSA_PCM* clone(void) const;
  AUDIO_IO_ALSA_PCM* new_expr(void) const { return new AUDIO_IO_ALSA_PCM(); }

  virtual string name(void) const { return("ALSA PCM device"); }
  virtual string description(void) const { return("ALSA PCM devices. Alsa-lib versions 0.9.0 and newer."); }

  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

  /** @name Function reimplemented from AUDIO_IO */
  /*@{*/

  virtual int supported_io_modes(void) const { return(io_read | io_write); }
  virtual string parameter_names(void) const { return("label,card,device,subdevice"); }

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);
  
  virtual long int read_samples(void* target_buffer, long int samples);
  virtual void write_samples(void* target_buffer, long int samples);

  /*@}*/

  /** @name Function reimplemented from AUDIO_IO_DEVICE */
  /*@{*/

  virtual void prepare(void);
  virtual void start(void);
  virtual void stop(void);

  virtual long int delay(void) const;
  virtual long int latency(void) const { return(buffersize()); }
  virtual long int prefill_space(void) const { if (io_mode() != io_read) return(buffer_size_rep); else return(0); }

  /*@}*/
 
private:

  void open_device(void);

  void allocate_structs(void);
  void deallocate_structs(void);

  void set_audio_format_params(void);
  void fill_and_set_hw_params(void);
  void fill_and_set_sw_params(void);
  void print_pcm_info(void);
  void handle_xrun_capture(void);
  void handle_xrun_playback(void);

private:

  snd_pcm_t *audio_fd_repp;
  snd_pcm_stream_t pcm_stream_rep;
  snd_pcm_format_t format_rep;
  snd_pcm_hw_params_t* pcm_hw_params_repp;
  snd_pcm_sw_params_t* pcm_sw_params_repp;

  snd_pcm_uframes_t period_size_rep; /**< current period size return by alsa-lib */
  snd_pcm_uframes_t buffer_size_rep; /**< current buffer size return by alsa-lib */
 
  int card_number_rep, device_number_rep, subdevice_number_rep;

  long underruns_rep, overruns_rep;
  unsigned char **nbufs_repp;

  string pcm_device_name_rep;
  static const string default_pcm_device_rep;

  bool using_plugin_rep;
  bool trigger_request_rep;

 protected:

  void set_pcm_device_name(const string& n);
  const string& pcm_device_name(void) const { return(pcm_device_name_rep); }
  
 private:

  AUDIO_IO_ALSA_PCM (const AUDIO_IO_ALSA_PCM& x) { }
  AUDIO_IO_ALSA_PCM& operator=(const AUDIO_IO_ALSA_PCM& x) { return *this; }
};

#ifdef ECA_ENABLE_AUDIOIO_PLUGINS
extern "C" {
AUDIO_IO* audio_io_descriptor(void);
int audio_io_interface_version(void);
const char* audio_io_keyword(void);
const char* audio_io_keyword_regex(void);
};
#endif

#endif /* INCLUDED_AUDIO_IO_ALSA_PCM_H */
