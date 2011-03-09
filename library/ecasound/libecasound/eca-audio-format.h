#ifndef INCLUDED_ECA_AUDIO_FORMAT_H
#define INCLUDED_ECA_AUDIO_FORMAT_H

#include <string>

#include "sample-specs.h"
#include "eca-samplerate-aware.h"
#include "eca-error.h"

/**
 * Class that represents audio format parameters. Audio format
 * is based on:
 *
 * - number of channels
 *
 * - channels interleaving
 *
 * - representation of individual sample
 */
class ECA_AUDIO_FORMAT : public ECA_SAMPLERATE_AWARE {

 public:

  /** @name Public type definitions and constants */
  /*@{*/

  enum Sample_format {
    sfmt_none,

    sfmt_u8,
    sfmt_s8,

    sfmt_s16,
    sfmt_s16_le,
    sfmt_s16_be,
     
    sfmt_s24,     
    sfmt_s24_le,     
    sfmt_s24_be,    

    sfmt_s32,     
    sfmt_s32_le,     
    sfmt_s32_be,    

    sfmt_f32,
    sfmt_f32_le,
    sfmt_f32_be,

    sfmt_f64, 
    sfmt_f64_le,
    sfmt_f64_be
  };    

  enum Sample_endianess {
    se_native,
    se_big,
    se_little
  };

  enum Sample_coding {
    sc_signed,
    sc_unsigned,
    sc_float
  };

  /*@}*/

 public:

  /*@}*/

  /** @name Constructors and destructors */
  /*@{*/

  ECA_AUDIO_FORMAT (int ch, long int srate, Sample_format format, bool ileaved = false);
  ECA_AUDIO_FORMAT (void);
  virtual ~ECA_AUDIO_FORMAT (void);

  /*@}*/

  /** @name Public functions for getting audio format information */
  /*@{*/

  /**
   * Returns frame size in bytes (sample size * channels)
   */
  int frame_size(void) const { return align_rep * channels_rep; }

  /**
   * Returns sample size in bytes (size of individual sample value)
   */
  int sample_size(void) const { return align_rep; }

  /**
   * How many bits are used to represent one sample value. 
   * Notice! This isn't necessarily sample_size() / 8. For example,
   * 24bit samples are represented with 32bit values.
   */
  int bits(void) const;
  
  /**
   * Returns sample format specification. See @ref Sample_format
   */
  Sample_format sample_format(void) const;

  /**
   * Returns sample coding. See @ref Sample_coding
   */
  Sample_coding sample_coding(void) const { return sc_rep ; }

  /**
   * Returns sample endianess. See @ref Sample_endianess
   */
  Sample_endianess sample_endianess(void) const { return se_rep; }

  /**
   * Returns sampling rate in bytes per second (data transfer 
   * rate).
   */
  long int bytes_per_second(void) const { return samples_per_second() * align_rep * channels_rep; }

  /** 
   * Returns number of channels.
   */
  SAMPLE_SPECS::channel_t channels(void) const { return channels_rep; }

  /**
   * Are channels interleaved?
   */
  bool interleaved_channels(void) const { return ileaved_rep; }

  /** 
   * Returns an identical audio format object
   */
  ECA_AUDIO_FORMAT audio_format(void) const;

  /**
   * Return the current sample format as a formatted std::string.
   *
   * @see set_sample_format
   */
  std::string format_string(void) const;

  /*@}*/

  /** @name Public virtual functions for setting audio format information */
  /*@{*/

  virtual void set_channels(SAMPLE_SPECS::channel_t v);
  virtual void set_sample_format(Sample_format v) throw(ECA_ERROR&);
  virtual void set_audio_format(const ECA_AUDIO_FORMAT& f_str);
  virtual void toggle_interleaved_channels(bool v);
  
  /*@}*/

  /** @name Public functions for setting audio format information */
  /*@{*/

  /**
   * Sets sample type based on the formatted std::string given as 
   * argument.
   *
   * The first letter is either "u", "s" and "f" (unsigned, 
   * signed, floating point).
   *
   * The following number specifies sample size in bits.
   *
   * If sample is little endian, "_le" is added to the end.
   * Similarly if big endian, "_be" is added. This postfix
   * can be omitted if applicable. 
   */
  void set_sample_format_string(const std::string& f_str) throw(ECA_ERROR&);

  /**
   * Sets the sample endianess to big endian.
   */
  void set_sample_coding(Sample_coding v);

  /**
   * Sets the sample endianess to big endian.
   */
  void set_sample_endianess(Sample_endianess v);

  /*@}*/

 private:

  Sample_format string_to_sample_format(const std::string& str) const throw(ECA_ERROR&);
  void update_sample_endianess(Sample_endianess v);

  bool ileaved_rep;
  SAMPLE_SPECS::channel_t channels_rep;
  size_t align_rep;            // the size of one sample value in bytes
  Sample_coding sc_rep;
  Sample_endianess se_rep;
};

#endif
