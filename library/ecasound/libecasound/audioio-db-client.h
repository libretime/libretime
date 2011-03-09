#ifndef INCLUDED_AUDIOIO_DB_CLIENT_H
#define INCLUDED_AUDIOIO_DB_CLIENT_H

#include <string>
#include <iostream>

#include "audioio-proxy.h"
#include "audioio-db-server.h"

class SAMPLE_BUFFER;

/**
 * Client class for double-buffering providing 
 * additional layer of buffering for objects
 * derived from AUDIO_IO.
 *
 * The buffering subsystem has been optimized for 
 * reliable streaming performance. Because of this some 
 * operations like random seeks are considerably slower 
 * than with direct access.
 *
 * Related design patterns:
 *     - Proxy (GoF207)
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_DB_CLIENT : public AUDIO_IO_PROXY {

 public:

  /** @name Public functions */
  /*@{*/

  AUDIO_IO_DB_CLIENT (AUDIO_IO_DB_SERVER *pserver, AUDIO_IO* aobject, bool transfer_ownership); 
  virtual ~AUDIO_IO_DB_CLIENT(void);

  /*@}*/
  
  /** @name Reimplemented functions from ECA_OBJECT */
  /*@{*/

  virtual std::string name(void) const { return(string("DB => ") + child()->name()); }
  virtual std::string description(void) const { return(child()->description()); }

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_PARAMETERS<string> */
  /*@{*/

  /* none */

  /*@}*/

  /** @name Reimplemented functions from DYNAMIC_OBJECT<string> */
  /*@{*/

  AUDIO_IO_DB_CLIENT* clone(void) const { std::cerr << __FILE__ << ": Not implemented!" << std::endl; return 0; }
  AUDIO_IO_DB_CLIENT* new_expr(void) const  { std::cerr << __FILE__ << ": Not implemented!" << std::endl; return 0; }

  /*@}*/

  /** @name Reimplemented functions from ECA_AUDIO_POSITION */
  /*@{*/

  virtual SAMPLE_SPECS::sample_pos_t seek_position(SAMPLE_SPECS::sample_pos_t pos);

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO_BARRIER */
  /*@{*/

  virtual void start_io(void);
  virtual void stop_io(void);

  /*@}*/

  /** @name Reimplemented functions from AUDIO_IO */
  /*@{*/

  virtual void read_buffer(SAMPLE_BUFFER* sbuf);
  virtual void write_buffer(SAMPLE_BUFFER* sbuf);

  virtual void open(void) throw(AUDIO_IO::SETUP_ERROR&);
  virtual void close(void);

  virtual bool finished(void) const;

  /*@}*/

  private:

  AUDIO_IO_DB_SERVER* pserver_repp;
  AUDIO_IO_DB_BUFFER* pbuffer_repp;

  AUDIO_IO_DB_CLIENT& operator=(const AUDIO_IO_DB_CLIENT& x) { return *this; }
  AUDIO_IO_DB_CLIENT (const AUDIO_IO_DB_CLIENT& x) { }

  int xruns_rep;
  bool finished_rep;
  bool free_child_rep;
  bool recursing_rep;

  void fetch_initial_child_data(void);

  bool pause_db_server_if_running(void);
  void restore_db_server_state(bool was_running);
};

#endif
