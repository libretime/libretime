#ifndef INCLUDED_ECA_SAMPLERATE_AWARE_H
#define INCLUDED_ECA_SAMPLERATE_AWARE_H

#include "sample-specs.h"

/**
 * Interface class implemented by all types that 
 * require knowledge of system samplerate. Provides
 * funcitonality for setting and getting current 
 * samplerate, and mechanism for notifying subclasses
 * of a samplerate change.
 *
 * @author Kai Vehmanen
 */
class ECA_SAMPLERATE_AWARE {

public:

  /** @name Constructors and destructors */
  /*@{*/

  /**
   * Construtor.
   *
   * Note! The default is set to a very high value (8 * 48kHz)
   *       to ensure we retain precision when using (sample-count,srate)
   *       tuples for storing position and length information, even 
   *       in situations where the actual sample rate is not yet known.
   */
  ECA_SAMPLERATE_AWARE (SAMPLE_SPECS::sample_rate_t srate = 384000);
  virtual ~ECA_SAMPLERATE_AWARE(void);

  /*@}*/

  /** @name Public functions for getting audio format information */
  /*@{*/

  /**
   * Returns sampling rate in samples per second.
   * Note! Sometimes also called frames_per_second().
   */
  SAMPLE_SPECS::sample_rate_t samples_per_second(void) const { return(srate_rep); }

  /*@}*/

  /** @name Public virtual functions for setting audio format information */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);

  /*@}*/

private:

  SAMPLE_SPECS::sample_rate_t srate_rep;
};

#endif /* INCLUDED_ECA_SAMPLERATE_AWARE */
