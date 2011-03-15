#ifndef INCLUDED_MIDIIO_ASEQ_H
#define INCLUDED_MIDIIO_ASEQ_H

#include <string>
#include <alsa/asoundlib.h>
#include "midiio.h"

/**
 * Input and output of raw MIDI streams using
 * ALSA sequencer
 *
 * @author Pedro Lopez-Cabanillas
 */
class MIDI_IO_ASEQ : public MIDI_IO {

 public:

  MIDI_IO_ASEQ (const std::string& name = "");
  virtual ~MIDI_IO_ASEQ(void);
    
  virtual MIDI_IO_ASEQ* clone(void) const { return(new MIDI_IO_ASEQ(*this)); }
  virtual MIDI_IO_ASEQ* new_expr(void) const { return new MIDI_IO_ASEQ(); }    

  virtual std::string name(void) const { return("ALSA Sequencer MIDI"); }
  virtual int supported_io_modes(void) const { return(io_read | io_write | io_readwrite); }
  virtual bool supports_nonblocking_mode(void) const { return(true); }
  virtual std::string parameter_names(void) const { return("label,device_name"); }

  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;
  
  virtual void open(void);
  virtual void close(void);

  virtual int poll_descriptor(void) const;

  virtual long int read_bytes(void* target_buffer, long int bytes);
  virtual long int write_bytes(void* target_buffer, long int bytes);

  virtual bool finished(void) const;
  virtual bool pending_messages(unsigned long timeout) const;
  
 private:

  snd_seq_t* seq_handle_repp;
  snd_midi_event_t* coder_repp;
  int buffer_size_rep;
  int port_rep;
  bool finished_rep;
  std::string device_name_rep;
};

#endif
