#ifndef INCLUDED_AUDIOIO_SEQUENCER_BASE_H
#define INCLUDED_AUDIOIO_SEQUENCER_BASE_H

#include <string>

#include "audioio-proxy.h"
#include "samplebuffer.h"
#include "eca-audio-time.h"

/**
 * Base class for audio sequencer objects. 
 *
 * Audio sequencer objects open one or more child objects
 * and alter the sequence of audio that is read from them.
 * Common operations are changing the position (inserting
 * silence at start, slicing), and looping of segments.
 *
 * The child objects also implement the AUDIO_IO interface,
 * or one of the more specialized subclasses.
 *
 * Related design patterns:
 *     - Proxy (GoF207)
 *
 * @author Kai Vehmanen
 */
class AUDIO_SEQUENCER_BASE : public AUDIO_IO_PROXY {

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_SEQUENCER_BASE ();
  virtual ~AUDIO_SEQUENCER_BASE(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/
 
  /* Pure virtual class, not implemented */

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  /* none */

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  virtual AUDIO_SEQUENCER_BASE* clone(void) const;
  virtual AUDIO_SEQUENCER_BASE* new_expr(void) const { return new AUDIO_SEQUENCER_BASE(); }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual bool finite_length_stream(void) const;
  virtual bool finished(void) const;

  virtual void read_buffer(SAMPLE_BUFFER* sbuf);
  virtual void write_buffer(SAMPLE_BUFFER* sbuf);

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  /*@}*/

  /** @name New functions */
  /*@{*/

  /**
   * Sets the child object to open. Argument 'v' should 
   * be a string suitable for passing to ECA_OBJECT_MAP::object(),
   * i.e. a Ecasound Option Syntax (EOS) string.
   */
  void set_child_object_string(const std::string& v);

  const std::string& child_object_string(void) const { return child_object_str_rep; }

  /**
   * Sets start offset for the child object. 
   *
   * At this offset, samples from the child start to
   * be read.
   *
   * If not set, defaults to zero offset (start 
   * consuming child object samples from the beginning).
   */
  void set_child_offset(const ECA_AUDIO_TIME& v);

  const ECA_AUDIO_TIME& child_offset(void) const { return child_offset_rep; }

  /**
   * Set start position inside child object.
   *
   * This is the position, within the child object, where
   * first samples will be read.
   *
   * If not set, defaults to zero (read from start of
   * child object).
   */
  void set_child_start_position(const ECA_AUDIO_TIME& v);

  const ECA_AUDIO_TIME& child_start_position(void) const { return child_start_pos_rep; }

  /**
   * Set the child length. 
   * 
   * If not set, defaults to the total length of 
   * the child object.
   * 
   * @see reset_child_length(void);
   */
  void set_child_length(const ECA_AUDIO_TIME& v);

  /**
   * Returns the child length.
   * 
   * Note that in the case that child length is infinite,
   * the returned object may be invalid (ECA_AUDIO_TIME::valid()
   * returns false). So caller must check validity of 
   * the returned value before using it.
   */
  ECA_AUDIO_TIME child_length(void) const;

  /**
   * Toggle whether child object data is looped.
   */
  void toggle_looping(bool v) { child_looping_rep = v; }

  bool child_looping(void) const { return child_looping_rep; }    

  /*@}*/

protected:

  void dump_child_debug(const char *tag);    
  void set_child_length_private(const ECA_AUDIO_TIME& v);
  SAMPLE_SPECS::sample_pos_t priv_public_to_child_pos(SAMPLE_SPECS::sample_pos_t pubpos) const;

private:

  SAMPLE_BUFFER tmp_buffer;

  bool child_looping_rep;
  ECA_AUDIO_TIME child_offset_rep,
                 child_start_pos_rep,
                 child_length_rep;
  bool child_length_set_by_client_rep;
  std::string child_object_str_rep;
  long int buffersize_rep;
  bool child_write_started;
  bool init_rep;

  void change_child_name(const string& child_name) throw(AUDIO_IO::SETUP_ERROR &);
  
  AUDIO_SEQUENCER_BASE& operator=(const AUDIO_SEQUENCER_BASE& x) { return *this; }
  AUDIO_SEQUENCER_BASE (const AUDIO_SEQUENCER_BASE& x) { }

};

#endif
