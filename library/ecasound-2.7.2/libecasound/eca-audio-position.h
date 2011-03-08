#ifndef INCLUDED_ECA_AUDIO_POSITION_H
#define INCLUDED_ECA_AUDIO_POSITION_H

#include "sample-specs.h"
#include "eca-samplerate-aware.h"

/**
 * Position cursor for a finite length audio stream
 */
class ECA_AUDIO_POSITION : public ECA_SAMPLERATE_AWARE {

 public:

  /** @name Constructors and destructors */
  /*@{*/

  ECA_AUDIO_POSITION(void);
  virtual ~ECA_AUDIO_POSITION(void);

  /*@}*/

  /** @name Public functions for getting length information */
  /*@{*/
  
  SAMPLE_SPECS::sample_pos_t length_in_samples(void) const;
  int length_in_seconds(void) const;
  double length_in_seconds_exact(void) const;

  bool length_set(void) const { return(length_set_rep); }

  /*@}*/

 protected:

  /** @name Protected functions for setting length */
  /*@{*/

  void set_length_in_samples(SAMPLE_SPECS::sample_pos_t pos);
  void set_length_in_seconds(int pos_in_seconds);
  void set_length_in_seconds(double pos_in_seconds);

  /**
   * If current position is beyond the current total
   * length, sets length according to the current position.
   *
   * ensure:
   *  position_in_samples() == length_in_samples()
   */
  void extend_position(void) { 
    length_in_samples_rep = 
      (position_in_samples_rep > length_in_samples_rep)
      ? position_in_samples_rep : length_in_samples_rep; }

  /*@}*/

 public:

  /** @name Public functions for getting position information */
  /*@{*/

  SAMPLE_SPECS::sample_pos_t position_in_samples(void) const;
  int position_in_seconds(void) const;
  double position_in_seconds_exact(void) const;

  /*@}*/

 protected:

  /** @name Protected functions for setting position (without action) */
  /*@{*/

  void set_position_in_samples(SAMPLE_SPECS::sample_pos_t pos);
  void set_position_in_seconds(int pos_in_seconds);
  void set_position_in_seconds(double pos_in_seconds);
  void change_position_in_samples(SAMPLE_SPECS::sample_pos_t pos);
  void change_position_in_seconds(double pos_in_seconds);

  /*@}*/

 public:

  /** @name Public functions for setting position (with action) */
  /*@{*/

  void seek_position_in_samples(SAMPLE_SPECS::sample_pos_t pos_in_samples);
  void seek_position_in_samples_advance(SAMPLE_SPECS::sample_pos_t pos_in_samples);
  void seek_position_in_seconds(double pos_in_seconds);
  void seek_first(void);
  void seek_last(void);

public:
  virtual bool supports_seeking(void) const = 0;
  virtual bool supports_seeking_sample_accurate(void) const = 0;

protected:
  /**
   * Seeks to the current position.
   *
   * If the seek is succesful, seek_position() should return 
   * position given as argument 'pos'.
   *
   * If the seek is unsuccesful (seeking not supported at all,
   * or not with sample accuracy), the current actual position 
   * in samples should be returned.
   *
   * @param pos new position in samples
   * @return pos on success, actual current position otherwise
   */
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos) = 0;

  /*@}*/

public:

  /** @name Public utility functions */
  /*@{*/

  /**
   * True if current position is beyond the end position or
   * smaller than zero.
   */
  inline bool out_position(void) const { 
    return((( position_in_samples_rep < 0) &&
	    (position_in_samples_rep > length_in_samples_rep)) ? true : false); }

  /*@}*/

 public:

  /** @name Functions reimplemented from ECA_SAMPLERATE_AWARE */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value);

  /*@}*/

 private:

  SAMPLE_SPECS::sample_pos_t position_in_samples_rep;
  SAMPLE_SPECS::sample_pos_t length_in_samples_rep;
  bool length_set_rep;

};

#endif
