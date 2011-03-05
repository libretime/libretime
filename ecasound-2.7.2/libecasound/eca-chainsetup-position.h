#ifndef INCLUDED_ECA_CONTROL_POSITION_H
#define INCLUDED_ECA_CONTROL_POSITION_H

#include "sample-specs.h"
#include "eca-audio-position.h"

/**
 * Virtual class implementing position and
 * length related chainsetup features.
 */
class ECA_CHAINSETUP_POSITION : public ECA_AUDIO_POSITION {

 public:

  /** @name Init and cleaup */
  /*@{*/

  ECA_CHAINSETUP_POSITION(void);
  virtual ~ECA_CHAINSETUP_POSITION(void);

  /*@}*/

  /** @name Public getter/setter functions for max length information.
   *        Note that this length information is different from
   *        that defined in ECA_AUDIO_POSITION. Max length can be
   *        set by the users of this class, and can be either shorter
   *        or longer than the actual length. */
  /*@{*/

  void set_max_length_in_samples(SAMPLE_SPECS::sample_pos_t pos);
  void set_max_length_in_seconds(double pos_in_seconds);

  inline bool is_over_max_length(void) const { return((position_in_samples() > max_length_in_samples() && max_length_set() == true) ? true : false); }
  SAMPLE_SPECS::sample_pos_t max_length_in_samples(void) const;
  double max_length_in_seconds_exact(void) const;
  bool max_length_set(void) const { return(max_length_set_rep); }

  /*@}*/

  /** @name Functions reimplemented from ECA_SAMPLERATE_AWARE */
  /*@{*/

  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t new_value);

  /*@}*/

  /** @name Functions implemented from ECA_AUDIO_POSITION */
  /*@{*/

  virtual bool supports_seeking(void) const { return true; }
  virtual bool supports_seeking_sample_accurate(void) const { return true; }

  /*@}*/

 protected:

  /** @name Protected functions for controlling looping */
  /*@{*/

  void toggle_looping(bool v) { looping_rep = v; }
  bool looping_enabled(void) const { return(looping_rep); }

  /*@}*/

 private:

  bool looping_rep, max_length_set_rep;
  SAMPLE_SPECS::sample_pos_t max_length_in_samples_rep;
};

#endif
