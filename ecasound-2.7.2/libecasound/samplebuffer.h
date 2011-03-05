#ifndef INCLUDED_SAMPLEBUFFER_H
#define INCLUDED_SAMPLEBUFFER_H

#include <vector>

#include "eca-audio-format.h"
#include "sample-specs.h"

class SAMPLE_BUFFER_FUNCTIONS;
class SAMPLE_BUFFER_impl;

/**
 * A dynamic container for storing blocks of 
 * audio data.
 * 
 * Static attributes are:
 *  - samples of type 'sample_t' (usually 32bit float)
 *
 * Dynamic attributes are:
 *  - number of channels
 *  - length in samples
 *  - event tags 
 *
 * Provided services:
 *  - copying from/to other samplebuffer objects
 *  - basic audio operations
 *  - importing and exporting data from/to\n
 *    raw buffers of audio data
 *  - changing channel count and length
 *  - reserving space before-hand
 *  - realtime-safety and pointer locking
 *  - access to event tags
 */
class SAMPLE_BUFFER {

  friend class SAMPLE_BUFFER_FUNCTIONS;
  friend class SAMPLE_ITERATOR;
  friend class SAMPLE_ITERATOR_CHANNEL;
  friend class SAMPLE_ITERATOR_CHANNELS;
  friend class SAMPLE_ITERATOR_INTERLEAVED;

 public:

  /** @name Public type definitions */
  /*@{*/

  typedef SAMPLE_SPECS::channel_t channel_size_t;
  typedef long int buf_size_t;
  typedef SAMPLE_SPECS::sample_t sample_t;

  enum Tag_name {
    /* buffer contains last samples of a stream */
    tag_end_of_stream = 1,
    /* buffer contains samples from multiple inputs */
    tag_mixed_content = (1 << 1),
    /* internal: placeholder */
    tag_last =  (1 << 30),
    /* internal: matches all tags */
    tag_all = 0xffffffff
  };

  /*@}*/

 public:

  /** @name Constructors/destructors */
  /*@{*/

  SAMPLE_BUFFER (buf_size_t buffersize = 0, channel_size_t channels = 0);
  ~SAMPLE_BUFFER(void);

  /*@}*/

 public:
    
  /** @name Copying from/to other samplebuffer objects */
  /*@{*/

  void add_matching_channels(const SAMPLE_BUFFER& x);
  void add_matching_channels_ref(const SAMPLE_BUFFER& x);
  void add_with_weight(const SAMPLE_BUFFER& x, int weight);
  void copy_matching_channels(const SAMPLE_BUFFER& x);
  void copy_all_content(const SAMPLE_BUFFER& x);
  void copy_range(const SAMPLE_BUFFER& x, buf_size_t start_pos, buf_size_t end_pos, buf_size_t to_pos);

  /*@}*/

  /** @name Basic audio operations */
  /*@{*/ 

  void divide_by(sample_t dvalue);
  void divide_by_ref(sample_t dvalue);
  void multiply_by(sample_t factor);
  void multiply_by(sample_t factor, int channel);
  void multiply_by_ref(sample_t factor);
  void multiply_by_ref(sample_t factor, int channel);
  void limit_values(void);
  void limit_values_ref(void);
  void make_empty(void);
  bool is_empty(void) const { return buffersize_rep == 0; }
  void make_silent(void);
  void make_silent(int channel);
  void make_silent_ref(int channel);
  void make_silent_range(buf_size_t start_pos, buf_size_t end_pos);
  void make_silent_range_ref(buf_size_t start_pos, buf_size_t end_pos);
  void resample(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);
  void resample_set_quality(int quality);
  int resample_get_quality(void) const;

  /*@}*/

  /** 
   * @name Importing and exporting data from/to raw buffers of audio data */
  /*@{*/

  void import_interleaved(unsigned char* source, buf_size_t samples, ECA_AUDIO_FORMAT::Sample_format fmt, channel_size_t ch);
  void import_noninterleaved(unsigned char* source, buf_size_t samples, ECA_AUDIO_FORMAT::Sample_format fmt, channel_size_t ch);
  void export_interleaved(unsigned char* target, ECA_AUDIO_FORMAT::Sample_format fmt, channel_size_t ch);
  void export_noninterleaved(unsigned char* target, ECA_AUDIO_FORMAT::Sample_format fmt, channel_size_t ch);
  
  /*@}*/
        
 public:

  /** @name Changing channel count, length and sample-rate. */
  /*@{*/

  void number_of_channels(channel_size_t num);
  inline channel_size_t number_of_channels(void) const { return(channel_count_rep); }

  void length_in_samples(buf_size_t len);
  inline buf_size_t length_in_samples(void) const { return(buffersize_rep); }

  /*@}*/

  /** @name Reserving space before-hand */
  /*@{*/

  void resample_init_memory(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);
  void reserve_channels(channel_size_t num);
  void reserve_length_in_samples(buf_size_t len);

  /*@}*/

  /** @name Realtime-safety and pointer locking */
  /*@{*/

  void set_rt_lock(bool state);
  void get_pointer_reflock(void);
  void release_pointer_reflock(void);

  /*@}*/

  /** @name Event tags - for relaying additional info about the buffer */
  /*@{*/

  void event_tags_add(const SAMPLE_BUFFER& sbuf);
  void event_tags_set(const SAMPLE_BUFFER& sbuf);
  void event_tags_clear(Tag_name tagmask = tag_all);
  void event_tag_set(Tag_name tag, bool val = true);
  bool event_tag_test(Tag_name tag);

  /*@}*/

 private:

  void resample_extfilter(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);
  void resample_secret_rabbit_code(SAMPLE_SPECS::sample_rate_t from_srate, SAMPLE_SPECS::sample_rate_t to_srate);
  void resample_simplefilter(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);
  void resample_nofilter(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);
  void resample_with_memory(SAMPLE_SPECS::sample_rate_t from_rate, SAMPLE_SPECS::sample_rate_t to_rate);

  static void import_helper(const unsigned char *ibuffer,
			    buf_size_t* iptr,
			    sample_t* obuffer,
			    buf_size_t optr,
			    ECA_AUDIO_FORMAT::Sample_format fmt);
  static void export_helper(unsigned char* obuffer, 
			    buf_size_t* optr,
			    sample_t value,
			    ECA_AUDIO_FORMAT::Sample_format fmt);

 public:

  /** @name Data representation */

  /**
   * WARNING! Although 'buffer' is a public member, you should only 
   * use it directly for a very, very good reason. All normal 
   * input/output should be done via the SAMPLEBUFFER_ITERATORS 
   * class. Representation of 'buffer' may change at any time, 
   * and this will break all code using direct-access.
   *
   * If you do use direct access, then you must also 
   * use the get_pointer_reflock() and release_pointer_reflock()
   * calls so that reference counting is possible.
   */
  std::vector<sample_t*> buffer;

  /*@}*/

 private:

  /** @name Private data */
  /*@{*/

  channel_size_t channel_count_rep;
  buf_size_t buffersize_rep;
  buf_size_t reserved_samples_rep;

  /*@}*/

  SAMPLE_BUFFER_impl* impl_repp;

private:

  SAMPLE_BUFFER& operator= (const SAMPLE_BUFFER& t);
  SAMPLE_BUFFER (const SAMPLE_BUFFER& x);

};

#endif
