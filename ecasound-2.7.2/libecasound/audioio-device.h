#ifndef INCLUDED_AUDIOIO_DEVICE_H
#define INCLUDED_AUDIOIO_DEVICE_H

#include "audioio-buffered.h"

/**
 * Virtual base class for real-time devices.
 *
 * A realtime device...
 *
 * - is disabled after device is opened
 *
 * - is enabled with start()
 *
 * - once enabled, will handle I/O at a constant speed
 *   based on the sample format paremeters
 *
 * - is disabled with stop()
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_DEVICE : public AUDIO_IO_BUFFERED {
 
 public:

  /** @name Public static functions */
  /*@{*/

  /**
   * Whether given object is an AUDIO_IO_DEVICE object.
   */
  static bool is_realtime_object(const AUDIO_IO* aobj);

  /*@}*/

  /** @name Constructors and destructors */
  /*@{*/

  AUDIO_IO_DEVICE(void);
  virtual ~AUDIO_IO_DEVICE(void);

  /*@}*/

  /** @name Configuration 
   * 
   * For setting and getting configuration parameters.
   */
  /*@{*/

  /**
   * Whether to ignore possible under- and overrun 
   * situations. If enabled, device should try to
   * recover from these situations, ie. keep on 
   * running. If disabled, processing should be aborted
   * if an xrun occurs. Should be set before opening 
   * the device. Defaults to 'true'.
   *
   * @pre is_open() != true
   */
  virtual void toggle_ignore_xruns(bool v) { ignore_xruns_rep = v; }

  /** 
   * Whether the device should maximize the use 
   * of internal buffering. If disabled, the device 
   * should use minimal amount of internal buffering. The 
   * recommended size is  two or three fragments, each 
   * buffersize() sample frames in size. If enabled, 
   * device is free to use as much as buffering as 
   * is possible. The default state is enabled.
   * 
   * The exact amount of buffering can be checked 
   * with the latency() function.
   *
   * @pre is_open() != true
   */
  virtual void toggle_max_buffers(bool v) { max_buffers_rep = v; }
  
  /**
   * Returns the current setting for xrun handling.
   */
  virtual bool ignore_xruns(void) const { return ignore_xruns_rep; }

  /**
   * Returns the current setting for how internal 
   * buffering is used.
   */
  virtual bool max_buffers(void) const { return max_buffers_rep; }

  /**
   * Returns the systematic latency in sample frames. 
   * This value is usually a multiple of buffersize().
   * Note that the latency introduced by prefilling 
   * outputs is not included in this figure.
   *
   * @see delay()
   * @see prefill_space()
   *
   * @pre is_open() == true
   */
  virtual long int latency(void) const { return 0; }

  /**
   * How much data in sample frames can be prefilled 
   * to a output device before processing is started 
   * with start() (after prepare())?
   *
   * Note! Prefilling will have an affect to
   *       output latency.
   *
   * @see latency()
   */
  virtual long int prefill_space(void) const { return 0; }

  /*@}*/

  /** @name Main functionality */
  /*@{*/

  /**
   * Prepare device for processing. After this call, device is 
   * ready for input/output (buffer can be pre-filled).
   *
   * require:
   *  is_running() != true 
   *
   * ensure:
   *  (io_mode() == si_read && readable() == true) || writable()
   */
  virtual void prepare(void) { is_prepared_rep = true; }

  /**
   * Start prosessing sample data. Underruns will occur if the 
   * calling program can't handle data at the speed of the 
   * source device. Write_buffer() calls are blocked if necessary.
   *
   * Note! For output devices, at least one buffer of data 
   *       must have been written before issuing start()!
   *
   * require:
   *  is_running() != true
   *  is_prepared() == true 
   *
   * ensure:
   *  is_running() == true
   */
  virtual void start(void) { is_running_rep = true; }

  /**
   * Stop processing. Doesn't usually concern non-realtime devices.
   * I/O is not allowed after this call. This should be used when 
   * audio object is not going to be used for a while.
   *
   * require:
   *  is_running() == true
   * 
   * ensure:
   *  is_running() != true
   *  is_prepared() != true
   *  readable() == false
   *  writable() == false
   */
  virtual void stop(void) { is_running_rep = false; is_prepared_rep = false; }

  /*@}*/

  /** @name Runtime information */
  /*@{*/

  /**
   * Returns the delay between current read/write position 
   * and the exact hardware i/o location. For instance 
   * with soundcard hardware this value tells the distance 
   * to the exact audio frame currently being played or 
   * recorded.
   *
   * @see latency()
   * @see position_in_samples()
   *
   * @pre is_running() == true
   * @post delay() <= latency()
   */
  virtual long int delay(void) const { return 0; }

  /**
   * Whether device has been started?
   */
  bool is_running(void) const { return is_running_rep; }

  /**
   * Whether device has been prepared for processing?
   */
  bool is_prepared(void) const { return is_prepared_rep; }

  /*@}*/

  /** @name Functions reimplemented from AUDIO_IO */
  /*@{*/

  virtual bool supports_seeking(void) const { return true; }
  virtual bool finished(void) const { return is_open() == false; }
  virtual std::string status(void) const;

  /*@}*/

 private:
  
  bool is_running_rep;
  bool is_prepared_rep;
  bool ignore_xruns_rep;
  bool max_buffers_rep;
};

#endif
