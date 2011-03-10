#ifndef INCLUDED_SAMPLEBUFFER_ITERATORS_H
#define INCLUDED_SAMPLEBUFFER_ITERATORS_H

#include "samplebuffer.h"

/**
 * Iterate through all samples. No specific order.
 *
 * Related design patterns:
 *     - Iterator (GoF257)
 */
class SAMPLE_ITERATOR {

 private:

  SAMPLE_BUFFER* target;
  SAMPLE_BUFFER::buf_size_t index;     // index of the current sample
  SAMPLE_BUFFER::channel_size_t channel_index;  // index of current channel

 public:

  /**
   * Prepare iterator for processing.
   */
  void init(SAMPLE_BUFFER* buf) { target = buf; }

  /**
   * Start iteration from the first audio item;
   */
  void begin(void);

  /**
   * True if iterator is past the last audio item.
   */
  inline bool end(void) { return(channel_index >= static_cast<int>(target->channel_count_rep)); }

  /**
   * Move iterator to the next audio item.
   */
  void next(void);

  /**
   * Returns a pointer to the current sample.
   */
  inline SAMPLE_SPECS::sample_t* current(void) { return(&(target->buffer[channel_index][index])); }
};

/**
 * Iterate through all samples of one channel. 
 *
 * Notice! This iterator can be used to add extra
 * channels to the sample data.
 *
 * Related design patterns:
 *     - Iterator (GoF257)
 */
class SAMPLE_ITERATOR_CHANNEL {

 private:

  SAMPLE_BUFFER* target;
  SAMPLE_BUFFER::buf_size_t index;     // index of the current sample
  SAMPLE_BUFFER::channel_size_t channel_index;  // index of current channel

 public:

  /**
   * Prepare iterator for processing.
   */
  void init(SAMPLE_BUFFER* buf, int channel = 0);

  /**
   * Start iteration from the first sample of 'channel'. More channels 
   * are allocated, if sample buffer has fewer channels than asked for.
   *
   * @param channel number of iterated channel (0, 1, ... , n)
   */
  void begin(int channel);

  /**
   * Start iteration from the first audio item (using the previously
   * set channel).
   */
  void begin(void) { index = 0; }
  
  /**
   * Move iterator to the next audio item.
   */
  void next(void) { ++index; }

  /**
   * True if iterator is past the last audio item.
   */
  inline bool end(void) { return(static_cast<long int>(index) >= target->buffersize_rep); }

  /**
   * Returns a pointer to the current sample.
   */
  inline SAMPLE_SPECS::sample_t* current(void) { return(&target->buffer[channel_index][index]); }
};

/**
 * Iterate through all samples, one channel at a time.
 *
 * Related design patterns:
 *     - Iterator (GoF257)
 */
class SAMPLE_ITERATOR_CHANNELS {

 private:

  SAMPLE_BUFFER* target;
  SAMPLE_BUFFER::buf_size_t index;     // index of the current sample
  SAMPLE_BUFFER::channel_size_t channel_index;  // index of current channel

 public:

  /**
   * Prepare iterator for processing.
   */
  void init(SAMPLE_BUFFER* buf) { target = buf; }

  /**
   * Start iteration from the first audio item;
   */
  void begin(void);

  /**
   * Move iterator to the next audio item.
   */
  void next(void);

  /**
   * True if iterator is past the last audio item.
   */
  inline bool end(void) { return(channel_index >= static_cast<int>(target->channel_count_rep)); }

  /**
   * Returns a pointer to the current sample.
   */
  inline SAMPLE_SPECS::sample_t* current(void) { return(&(target->buffer[channel_index][index])); }

  /**
   * Returns current channel index (starting from 0)
   */
  inline int channel(void) const { return(channel_index); }
};

/**
 * Iterate through all samples, one sample frame (interleaved) at a time.
 *
 * Related design patterns:
 *     - Iterator (GoF257)
 */
class SAMPLE_ITERATOR_INTERLEAVED {

 private:

  SAMPLE_BUFFER* target;
  SAMPLE_BUFFER::buf_size_t  index;     // index of the current sample

 public:

  /**
   * Prepare iterator for processing.
   */
  void init(SAMPLE_BUFFER* buf) { target = buf; }

  /**
   * Start iteration from the first audio item;
   */
  void begin(void) { index = 0; }

  /**
   * Move iterator to the next audio item.
   */
  inline void next(void) { ++index; }

  /**
   * True if iterator is past the last audio item.
   */
  inline bool end(void) { return(static_cast<long int>(index) >= target->buffersize_rep); }

  /**
   * Returns a pointer to the current sample.
   */
  inline SAMPLE_SPECS::sample_t* current(int channel) { return(&target->buffer[channel][index]); }
};

#endif
