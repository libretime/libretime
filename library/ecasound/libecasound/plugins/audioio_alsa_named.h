#ifndef INCLUDED_AUDIO_IO_ALSA_PCM_NAMED_H
#define INCLUDED_AUDIO_IO_ALSA_PCM_NAMED_H

#include "audioio_alsa.h"

/**
 * Class for handling named ALSA pcm-devices (Advanced Linux 
 * Sound Architecture).
 */
class AUDIO_IO_ALSA_PCM_NAMED : public AUDIO_IO_ALSA_PCM {

 public:

  virtual string name(void) const { return("ALSA named PCM device"); }
  virtual string description(void) const { return("ALSA named PCM device. Library versions 0.6.x and newer."); }

  virtual string parameter_names(void) const { return("label,pcm_name"); }
  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

  AUDIO_IO_ALSA_PCM_NAMED (void);
  virtual ~AUDIO_IO_ALSA_PCM_NAMED(void);
  AUDIO_IO_ALSA_PCM_NAMED* clone(void) const;
  AUDIO_IO_ALSA_PCM_NAMED* new_expr(void) const { return new AUDIO_IO_ALSA_PCM_NAMED(); }
  
 private:

  void print_status_debug(void);
  AUDIO_IO_ALSA_PCM_NAMED (const AUDIO_IO_ALSA_PCM_NAMED& x) { }
  AUDIO_IO_ALSA_PCM_NAMED& operator=(const AUDIO_IO_ALSA_PCM_NAMED& x) { return *this; }
};

#endif /* INCLUDED_AUDIO_IO_ALSA_PCM_NAMED */
