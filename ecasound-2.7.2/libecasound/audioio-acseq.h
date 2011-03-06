#ifndef INCLUDED_AUDIOIO_CLIP_SEQUENCER_H
#define INCLUDED_AUDIOIO_CLIP_SEQUENCER_H

#include "audioio-seqbase.h"

/**
 * Audio clip sequencer class. Allows to loop, play parts
 * of, and play files at a specific moment of time.
 *
 * Related design patterns:
 *     - Proxy (GoF207
 *
 * @author Kai Vehmanen
 */
class AUDIO_CLIP_SEQUENCER : public AUDIO_SEQUENCER_BASE {

 public:

  enum { cseq_none = 0, cseq_loop = 1, cseq_select = 2, cseq_play_at = 3 };

  /** @name Public functions */
  /*@{*/

  AUDIO_CLIP_SEQUENCER (void);
  virtual ~AUDIO_CLIP_SEQUENCER(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return("Audio clip sequencer"); }
  virtual std::string description(void) const { return("Audio clip sequencer. Supports looping and slicing of audio file segments."); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  virtual std::string parameter_names(void) const;
  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_CLIP_SEQUENCER* clone(void) const;
  virtual AUDIO_CLIP_SEQUENCER* new_expr(void) const { return new AUDIO_CLIP_SEQUENCER(); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/


  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  /*@}*/

  /** @name New functions */
  /*@{*/

    
  /*@}*/

private:

  mutable std::vector<std::string> params_rep;
  int child_param_offset_rep;
  int cseq_mode_rep;

  AUDIO_CLIP_SEQUENCER& operator=(const AUDIO_CLIP_SEQUENCER& x) { return *this; }
  AUDIO_CLIP_SEQUENCER (const AUDIO_CLIP_SEQUENCER& x) { }

};

#endif
