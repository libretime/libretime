#ifndef INCLUDED_SAMPLEBUFFER_IMPL_H
#define INCLUDED_SAMPLEBUFFER_IMPL_H

#include <vector>
#include "samplebuffer.h" 

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#ifdef ECA_COMPILE_SAMPLERATE
#include <samplerate.h>
#endif

class SAMPLE_BUFFER_impl {

 public:

  friend class SAMPLE_BUFFER;

 private:

  /** @name Misc member variables */
  /*@{*/
  
  bool rt_lock_rep;
  int lockref_rep;
  int quality_rep;
  int event_tags_rep;

  SAMPLE_BUFFER::sample_t* old_buffer_repp; // for resampling
  std::vector<SAMPLE_BUFFER::sample_t> resample_memory_rep;
#ifdef ECA_COMPILE_SAMPLERATE
  int src_state_channels_rep;
  std::vector<SRC_STATE*> src_state_rep;
#endif

  /*@}*/
};

#endif
