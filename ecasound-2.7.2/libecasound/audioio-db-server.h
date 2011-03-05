#ifndef INCLUDED_AUDIOIO_DB_SERVER_H
#define INCLUDED_AUDIOIO_DB_SERVER_H

#include <map>
#include "audioio.h"
#include "audioio-db-buffer.h"

class AUDIO_IO_DB_SERVER_impl;

/**
 * Audio i/o engine. Meant for serving all double-buffered client
 * audio objects (AUDIO_IO_DB_CLIENT). 
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_DB_SERVER {

  friend void* start_db_server_io_thread(void *ptr);

 public:

  /** @name Constructors and dtors */
  /*@{*/

  AUDIO_IO_DB_SERVER (void); 
  ~AUDIO_IO_DB_SERVER(void);

  /*@}*/

  /** @name Public functions for acquiring status information */
  /*@{*/

  bool is_running(void) const;
  bool is_full(void) const;

  /*@}*/

  /** @name Public functions for transport control */
  /*@{*/

  void start(void);
  void stop(void);
  void flush(void);

  /*@}*/

  /** @name Public functions for reporting client activity */
  /*@{*/

  void signal_client_activity(void);

  /*@}*/

  /** @name Public functions for waiting on server conditions */
  /*@{*/

  void wait_for_full(void);
  void wait_for_stop(void);
  void wait_for_flush(void);

  /*@}*/

  /** @name Public functions for configuration */
  /*@{*/

  void set_buffer_defaults(int buffers, long int buffersize);
  void register_client(AUDIO_IO* abject);
  void unregister_client(AUDIO_IO* abject);
  AUDIO_IO_DB_BUFFER* get_client_buffer(AUDIO_IO* abject);

  /*@}*/


 private:

  static const int buffercount_default;
  static const long int buffersize_default;

  std::vector<AUDIO_IO_DB_BUFFER*> buffers_rep;
  std::vector<AUDIO_IO*> clients_rep;
  std::map<AUDIO_IO*, int> client_map_rep;

  AUDIO_IO_DB_SERVER_impl* impl_repp;

  bool thread_running_rep;

  ATOMIC_INTEGER exit_ok_rep;
  ATOMIC_INTEGER exit_request_rep;
  ATOMIC_INTEGER stop_request_rep;
  ATOMIC_INTEGER running_rep;
  ATOMIC_INTEGER full_rep;
  
  int buffercount_rep;
  long int buffersize_rep;
  int schedpriority_rep;

  AUDIO_IO_DB_SERVER& operator=(const AUDIO_IO_DB_SERVER& x) { return *this; }
  AUDIO_IO_DB_SERVER (const AUDIO_IO_DB_SERVER& x) { }

  void io_thread(void);

  void wait_for_client_activity(void);

  void signal_full(void);
  void signal_stop(void);
  void signal_flush(void);

  void dump_profile_counters(void);

};

#endif
