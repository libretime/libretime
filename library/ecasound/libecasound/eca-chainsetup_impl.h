#ifndef INCLUDED_ECA_CHAINSETUP_IMPL_H
#define INCLUDED_ECA_CHAINSETUP_IMPL_H

#include "eca-chainsetup-bufparams.h"
#include "audio-stamp.h"
#include "midi-server.h"
#include "audioio-db-client.h"

class ECA_CHAINSETUP_impl {

 public:

  friend class ECA_CHAINSETUP;

 private:

  /** @name Aggregate objects */
  /*@{*/

  ECA_AUDIO_FORMAT default_audio_format_rep;

  AUDIO_STAMP_SERVER stamp_server_rep;
  AUDIO_IO_DB_SERVER pserver_rep;
  MIDI_SERVER midi_server_rep;

  ECA_CHAINSETUP_BUFPARAMS bmode_active_rep;
  ECA_CHAINSETUP_BUFPARAMS bmode_override_rep;
  ECA_CHAINSETUP_BUFPARAMS bmode_nonrt_rep;
  ECA_CHAINSETUP_BUFPARAMS bmode_rt_rep;
  ECA_CHAINSETUP_BUFPARAMS bmode_rtlowlatency_rep;

  /*@}*/


};

#endif /* INCLUDED_ECA_CHAINSETUP_IMPL_H */
