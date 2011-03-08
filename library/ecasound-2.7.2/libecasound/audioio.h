#ifndef INCLUDED_AUDIOIO_H
#define INCLUDED_AUDIOIO_H

#include <string>

#include "eca-audio-position.h"
#include "eca-audio-time.h"
#include "eca-audio-format.h"
#include "dynamic-object.h"

class SAMPLE_BUFFER;
class AUDIO_IO_MANAGER;

using std::string;

/**
 * Virtual base class for all audio I/O objects. Different types
 * of audio objects include files, audio devices, sound 
 * producing program modules, audio server clients, and so on.
 *
 * The class interface is divided into following sections:
 *
 *  - type definitions
 *
 *  - attributes
 *
 *  - configuration (setting and getting configuration parameters)
 *
 *  - functionality (control and runtime information)
 *
 *  - runtime information
 *
 *  - constructors and destructors
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO : public DYNAMIC_OBJECT<string>,
                 public ECA_AUDIO_FORMAT,
		 public ECA_AUDIO_POSITION {

 public:

  /** @name Public type definitions and constants */
  /*@{*/

  /**
   * Input/Output mode
   *
   * @see io_mode()
   *
   * io_read
   *
   * Device is opened for input. If opening a file, 
   * it must exist.
   *
   * io_write
   *
   * Device is opened for output. If opening a file and
   * and output exists, it is first truncated.
   * 
   * io_readwrite
   *
   * Device is opened for both reading and writing. If
   * opening a file, a new file is created if needed. 
   * When switching from read to write or vica versa,
   * position should be reset before using the device.
   **/
  enum Io_mode { io_read = 1, io_write = 2, io_readwrite = 4 };

  class SETUP_ERROR {
   public:
    enum Error_type {
      sample_format,    /* unsupported sample format */
      channels,         /* unsupported channel count */
      sample_rate,      /* unsupported sample_rate */
      interleaving,     /* non-interleaved or interleaved channel organization not supported */
      io_mode,          /* unsupported I/O mode */
      buffersize,       /* unsupported buffersize */
      blockmode,        /* non-blocking or blocking mode not supported */
      dynamic_params,   /* invalid dynamic parameters (for instance invalid label()) */
      unexpected        /* unexpected/unknown error */
    };
    
     const string& message(void) const;
     Error_type type(void) const;
     SETUP_ERROR(Error_type type, const string& message);

   private:
     Error_type type_rep;
     string message_rep;
  };

 public:

  /*@}*/

  /** @name Public functions for handling object managers */
  /*@{*/

  /**
   * Creates an object manager for this audio object type. 
   *
   * @return 0 if no manager objects are not supported
   */
  virtual AUDIO_IO_MANAGER* create_object_manager(void) const { return(0); }

  /*@}*/

  /** @name Constructors and destructors */
  /*@{*/

  virtual AUDIO_IO* clone(void) const = 0;
  virtual AUDIO_IO* new_expr(void) const = 0;
  virtual ~AUDIO_IO(void);
  AUDIO_IO(const string& name = "uninitialized", 
	   int mode = io_read);

  /*@}*/

  /** @name Attribute functions */
  /*@{*/

  virtual int supported_io_modes(void) const;
  virtual bool supports_nonblocking_mode(void) const;
  virtual bool finite_length_stream(void) const;
  virtual bool locked_audio_format(void) const;

  /*@}*/

  /** @name Configuration 
   * 
   * For setting and getting configuration parameters.
   */
  /*@{*/

  /**
   * Sets the sample buffer size in sample frames. 
   * 
   * When reading data with read_buffer(), buffersize()
   * determines how many sample frames of data is 
   * processed per call.
   *
   * Otherwise buffersize() is only used for initializing 
   * devices and data structures. 
   *
   * Device should always be able to write all sample 
   * data passed to write_buffer(), independently from current 
   * buffersize() value.
   *
   * @see buffersize()
   */
  virtual void set_buffersize(long int samples) = 0;

  /**
   * Returns the current buffersize in sample frames.
   *
   * @see set_buffersize()
   */
  virtual long int buffersize(void) const = 0;

  int io_mode(void) const;
  const string& label(void) const;
  string format_info(void) const;

  void set_io_mode(int mode);
  void set_label(const string& id_label);
  void toggle_nonblocking_mode(bool value);

  virtual string parameter_names(void) const { return("label"); }
  virtual void set_parameter(int param, string value);
  virtual string get_parameter(int param) const;

 public:

  /*@}*/

  /** @name Main functionality */
  /*@{*/

  /**
   * Reads samples and store them to buffer pointed by 'sbuf'. If 
   * necessary, the target buffer will be resized (both length 
   * and number of channels). 
   *
   * The sample buffer event tags may also be set during 
   * this method call (see SAMPLE_BUFFER documentation for more
   * details).
   *
   * It's important to note that SAMPLE_BUFFER audio format cannot be
   * changed during processing. This means that audio data must be converted
   * from audio object's internal format to that of 'sbuf' given as 
   * argument. SAMPLE_BUFFER class provides tools for all normal conversion 
   * operations. If you need direct access to object's data, a lower 
   * abstraction level should be used (@see AUDIO_IO_BUFFERED).
   *
   * Note! The implementations should call set_position_in_samples()
   *       or change_position_in_samples() in ECA_AUDIO_POSITION.
   *
   * @pre io_mode() == io_read || io_mode() == io_readwrite
   * @pre readable() == true
   * @pre sbuf != 0
   * @post sbuf->length_in_samples() <= buffersize()
   * @post sbuf->number_of_channels() == channels()
   */
  virtual void read_buffer(SAMPLE_BUFFER* sbuf) = 0;

  /**
   * Writes all data from sample buffer pointed by 'sbuf' to
   * this object. Notes concerning read_buffer() also apply to 
   * this routine.
   *
   * Note! The implementations should call set_position_in_samples()
   *       or change_position_in_samples() in ECA_AUDIO_POSITION.
   *
   * @pre io_mode() == io_write || io_mode() == io_readwrite
   * @pre writable() == true
   * @pre  sbuf != 0
   */
  virtual void write_buffer(SAMPLE_BUFFER* sbuf) = 0;

  /**
   * Opens the audio object (possibly in exclusive mode).
   * This routine is used for initializing external connections 
   * (opening files or devices, loading shared libraries, 
   * opening IPC connections). As it's impossible to know in 
   * advance what might happen, open() may throw an 
   * exception.  This way it becomes possible to provide 
   * more verbose information about the problem that caused 
   * open() to fail.
   *
   * At this point the various audio parameters are used
   * for the first time. Unless locked_audio_format() is 'true', 
   * object tries to use the audio format parameters set prior to 
   * this call. If object doesn't support the given parameter
   * combination, it can either try adjust them to closest
   * matching, or in the worst case, throw an SETUP_ERROR 
   * exception (see above).
   *
   * @pre is_open() != true
   * @post readable() == true || writable() == true || is_open() != true
   */
  virtual void open(void) throw (AUDIO_IO::SETUP_ERROR &);

  /**
   * Closes audio object. After calling this routine, 
   * all resources (for instance files and devices) must 
   * be freed so that they can be used by other processes.
   *
   * @pre is_open() == true
   * @post readable() != true
   * @post writable() != true
   */
  virtual void close(void);

  /*@}*/

  /** @name Runtime information */
  /*@{*/

  /**
   * Returns a file descriptor id suitable for poll() and 
   * select() system calls. If polling is not supported,
   * returns value of '-1'.
   */
  virtual int poll_descriptor(void) const { return(-1); }

  /**
   * If 'supports_nonblocking_mode() == true', this function returns
   * the number of samples frames that is available for reading, or 
   * alternatively, how many sample frames can be written without 
   * blocking. This function can be used for implementing nonblocking 
   * input and output with devices supporting it.
   *
   * Note, you should use set_buffersize() for setting how 
   * many sample frames read_buffer() will ask from the device.
   * 
   * require:
   *  supports_nonblocking_mode() == true
   */
  virtual long int samples_available(void) const { return(0); }

  /**
   * Has device been opened (with open())?
   */
  bool is_open(void) const { return(open_rep); }

  /**
   * Whether all data has been processed? If opened in mode 'io_read', 
   * this means that end of stream has been reached. If opened in 
   * 'io_write' or 'io_readwrite' modes, finished status usually
   * means that an error has occured (no space left, etc). After 
   * finished() has returned 'true', further calls to read_buffer() 
   * and/or write_buffer() won't process any data.
   *
   * For output for which 'finite_length_stream()' is true, when
   * 'finished()' returns true, that means an error has occured. 
   * Otherwise 'finished()' just tells that further attempts to do 
   * i/o will fail.
   */
  virtual bool finished(void) const = 0;

  virtual bool nonblocking_mode(void) const;
  virtual bool readable(void) const;
  virtual bool writable(void) const;
  virtual string status(void) const;

  ECA_AUDIO_TIME length(void) const;
  ECA_AUDIO_TIME position(void) const;

  /*@}*/

  /** @name Functions overridden and reimplemented from 
   *        ECA_AUDIO_POSITION and ECA_AUDIO_FORMAT */
  /*@{*/

  SAMPLE_SPECS::sample_rate_t samples_per_second(void) const;
  virtual void set_samples_per_second(SAMPLE_SPECS::sample_rate_t v);
  virtual void set_audio_format(const ECA_AUDIO_FORMAT& f_str);
  
  /*@}*/

  /** @name Functions implemented from ECA_AUDIO_POSITION */
  /*@{*/

  virtual bool supports_seeking(void) const;
  virtual bool supports_seeking_sample_accurate(void) const;
  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /*@}*/

 protected:

  /** @name Functions provided for subclasses. */
  /*@{*/

  void position(const ECA_AUDIO_TIME& v);
  void length(const ECA_AUDIO_TIME& v);

  std::string parameter_get_to_string(int param) const;
  std::string parameter_set_to_string(int param, std::string value) const;

  /*@{*/

 private:
  
  int io_mode_rep;
  string id_label_rep;
  bool nonblocking_rep;
  bool open_rep;
};

#endif
