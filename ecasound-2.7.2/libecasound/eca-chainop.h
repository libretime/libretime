#ifndef INCLUDED_CHAINOP_H
#define INCLUDED_CHAINOP_H

#include <map>
#include <string>

#include "eca-operator.h"
#include "eca-audio-format.h"
#include "sample-specs.h"

class SAMPLE_BUFFER;

/**
 * Virtual base class for chain operators. 
 * @author Kai Vehmanen
 */
class CHAIN_OPERATOR : public OPERATOR {

 public:

  /**
   * Virtual destructor.
   */
  virtual ~CHAIN_OPERATOR (void) { }

  /**
   * Prepares chain operator for processing. 
   *
   * This function is called at least once before 
   * the first call to process().
   * 
   * Whenever attributes of the sample buffer pointed 
   * by 'sbuf' are changed, chain operator should
   * be reinitialized with a new call to init().
   *
   * @param sbuf pointer to a sample buffer object
   *
   * @see release
   */
  virtual void init(SAMPLE_BUFFER* sbuf) = 0;

  /**
   * Releases the buffer that was used to initialize
   * the chain operator.
   *
   * This function is called after the last call
   * to process().
   *
   * After release(), chain operator is not 
   * allowed to access the sample buffer given 
   * to init().
   * 
   * @see init()
   */
  virtual void release(void) { }

  /**
   * Processes sample data in the buffer passed
   * to init().
   */
  virtual void process(void) = 0;

  /**
   * Returns a string describing chain operator's
   * current status.
   *
   * @param single_sample pointer to a single sample
   */
  virtual std::string status(void) const { return(""); }

  /** 
   * Returns the maximum length of the sample buffer after
   * a call to process(), if the buffer's original 
   * length was 'i_samples'.
   *
   * This function should be reimplemented by chain 
   * operator types that add or remove samples 
   * from the input data stream.
   *
   * @see process()
   */
  virtual long int max_output_samples(long int i_samples) const { return(i_samples); }

  /** 
   * Returns number of channels of the sample buffer
   * after a call to process(), if the buffer originally
   * had 'i_channels' channels.
   *
   * This function should be reimplemented by chain
   * operator types that change the channel count
   * during processing.
   *
   * @see process()
   */
  virtual int output_channels(int i_channels) const { return(i_channels); }
};

#endif
