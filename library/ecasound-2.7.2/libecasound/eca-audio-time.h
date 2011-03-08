#ifndef INCLUDED_ECA_AUDIO_TIME_H
#define INCLUDED_ECA_AUDIO_TIME_H

#include <string>
#include "sample-specs.h"

/**
 * Generic class for representing time in audio environment
 */
class ECA_AUDIO_TIME {

 private:

  SAMPLE_SPECS::sample_pos_t samples_rep;
  mutable SAMPLE_SPECS::sample_rate_t sample_rate_rep;
  mutable bool rate_set_rep;

  static const SAMPLE_SPECS::sample_rate_t default_srate = 384000;
  static const SAMPLE_SPECS::sample_rate_t invalid_srate = -1;

 public:

  enum format_type { format_hour_min_sec, format_min_sec, format_seconds, format_samples };

  ECA_AUDIO_TIME(SAMPLE_SPECS::sample_pos_t samples, SAMPLE_SPECS::sample_rate_t sample_rate);
  ECA_AUDIO_TIME(double time_in_seconds);
  ECA_AUDIO_TIME(format_type type, const std::string& time);
  ECA_AUDIO_TIME(const std::string& time);
  ECA_AUDIO_TIME(void);

  void set(format_type type, const std::string& time);
  void set_seconds(double seconds);
  void set_time_string(const std::string& time);
  void set_samples(SAMPLE_SPECS::sample_pos_t samples);
  void set_samples_per_second(long int srate);
  void set_samples_per_second_keeptime(long int srate);
  void mark_as_invalid(void);

  std::string to_string(format_type type) const;
  double seconds(void) const;
  SAMPLE_SPECS::sample_rate_t samples_per_second(void) const;
  SAMPLE_SPECS::sample_pos_t samples(void) const;
  bool valid(void) const;
};

#endif
