#ifndef INCLUDED_AUDIOIO_JACK_MANAGER_H
#define INCLUDED_AUDIOIO_JACK_MANAGER_H

#include <list>
#include <string>
#include <vector>
#include <map>

#include <pthread.h>
#include <jack/jack.h>

#include "sample-specs.h"
#include "dynamic-object.h"
#include "audioio-manager.h"
#include "eca-engine-driver.h"
#include "audioio_jack.h"

class AUDIO_IO;

using std::list;
using std::string;
using std::vector;

/**
 * Manager class for JACK client objects.
 *
 * Related design patterns:
 *     - Mediator (GoF273)
 *
 * @author Kai Vehmanen
 */
class AUDIO_IO_JACK_MANAGER : public AUDIO_IO_MANAGER, 
			      public ECA_ENGINE_DRIVER {

 public:

  friend int eca_jack_process_callback(jack_nframes_t nframes, void *arg);
#if ECA_JACK_TRANSPORT_API >= 3
  friend int eca_jack_sync_callback(jack_transport_state_t state, jack_position_t *pos, void *arg);
  friend void eca_jack_sync_start_seek_to(jack_transport_state_t state, jack_position_t *pos, void *arg);
  friend void eca_jack_sync_start_live_seek_to(jack_transport_state_t state, jack_position_t *pos, void *arg);
#endif
  friend void eca_jack_process_engine_iteration(jack_nframes_t nframes, void *arg);
  friend void eca_jack_process_mute(jack_nframes_t nframes, void *arg);
  friend void eca_jack_process_timebase_slave(jack_nframes_t nframes, void *arg);
  friend int eca_jack_bsize_cb(jack_nframes_t nframes, void *arg);
  friend int eca_jack_srate_cb(jack_nframes_t nframes, void *arg);
  friend void eca_jack_shutdown_cb(void *arg);

  static const int instance_limit;

public:

  typedef struct eca_jack_port_data {
    jack_port_t* jackport;
    string autoconnect_string;
    jack_nframes_t total_latency;
    jack_default_audio_sample_t* cb_buffer;
  } eca_jack_port_data_t;

  typedef struct eca_jack_node {
    AUDIO_IO_JACK* aobj;
    AUDIO_IO* origptr;
    list<eca_jack_port_data*> ports;
    int client_id;
  } eca_jack_node_t;

 private:

  typedef enum Operation_mode {
    Transport_none,
    Transport_receive,
    Transport_send,
    Transport_send_receive
  } Operation_mode_t;

 public:

  /** @name Constructors */
  /*@{*/

  AUDIO_IO_JACK_MANAGER(void);
  virtual ~AUDIO_IO_JACK_MANAGER(void);

  /*@}*/

  /** @name Functions reimplemented from AUDIO_IO_MANAGER */
  /*@{*/

  virtual bool is_managed_type(const AUDIO_IO* aobj) const;
  virtual void register_object(AUDIO_IO* aobj);
  virtual int get_object_id(const AUDIO_IO* aobj) const;
  virtual list<int> get_object_list(void) const;
  virtual void unregister_object(int id);

  /*@}*/

  /** @name Functions reimplemented from ECA_OBJECT */
  /*@{*/

  virtual string name(void) const { return("jack"); }
  virtual string description(void) const { return("JACK object manager"); }
 
  /*@}*/

  /** @name Function reimplemented from DYNAMIC_PARAMETERS */
  /*@{*/

  virtual std::string parameter_names(void) const { return("clientname,mode"); }
  virtual void set_parameter(int param, std::string value);
  virtual std::string get_parameter(int param) const;

  /*@}*/

  /** @name Function reimplemented from DYNAMIC_OBJECT */
  /*@{*/

  AUDIO_IO_JACK_MANAGER* clone(void) const { return new_expr(); }
  AUDIO_IO_JACK_MANAGER* new_expr(void) const { return new AUDIO_IO_JACK_MANAGER(); }  

  /*@}*/


  /** @name Functions reimplemented from ECA_ENGINE_DRIVER */
  /*@{*/

  virtual int exec(ECA_ENGINE* engine, ECA_CHAINSETUP* csetup);
  virtual void start(void);
  virtual void stop(void);
  virtual void exit(void);

  /*@}*/

  /** @name Public API for JACK clients */
  /*@{*/

  void register_jack_ports(int client_id, int ports, const string& portprefix);
  void unregister_jack_ports(int client_id);
  void auto_connect_jack_port(int client_id, int portnum, const string& portname);
  void auto_connect_jack_port_client(int client_id, const string& dst, int channels);

  long int client_latency(int client_id);

  void open(int client_id);
  void close(int client_id);
  
  long int read_samples(int client_id, void* target_buffer, long int samples);
  void write_samples(int client_id, void* target_buffer, long int samples);

  bool is_open(void) const { return(open_rep); }
  bool is_connection_active(void) const { return(activated_rep); }
  bool is_running(void) const;

  long int buffersize(void) const;
  SAMPLE_SPECS::sample_rate_t samples_per_second(void) const;

  /*@}*/

private:

  static void get_total_port_latency(jack_client_t* client, 
				     eca_jack_port_data_t* ports);

  void open_server_connection(void);
  void close_server_connection(void);

  void initial_seek(void);

  void activate_server_connection(void);
  void deactivate_server_connection(void);

  void set_node_connection(eca_jack_node_t* node, bool connect);
  void connect_all_nodes(void);
  void disconnect_all_nodes(void);
  eca_jack_node_t* get_node(int client_id);

  void wait_for_exit(void);
  void signal_exit(void);
  void wait_for_stop(void);
  void signal_stop(void);

  pthread_cond_t exit_cond_rep;
  pthread_mutex_t exit_mutex_rep;
  pthread_cond_t stop_cond_rep;
  pthread_mutex_t stop_mutex_rep;
  pthread_mutex_t engine_mod_lock_rep;

  Operation_mode_t mode_rep;

  bool open_rep;        /**< connection established to the JACK server  */
  bool activated_rep;   /**< JACK connection activated */

  bool shutdown_request_rep;   /**< jack->engine pending shutdown request */
  bool exit_request_rep;       /**< engine->engine exit request */

  int open_clients_rep;       /** number of active client nodes */
  int last_node_id_rep;
  int start_request_rep;      /**< number of jack->engine start requests pending */
  int stop_request_rep;       /**< number of jack->engine stop requests pending */
  int j_stopped_rounds_rep;   /**< number of iterations that JACK state has been stopped;
				   accessed only from proces thread */

  list<eca_jack_node_t*> node_list_rep;
  vector<eca_jack_port_data_t*> inports_rep;
  vector<eca_jack_port_data_t*> outports_rep;

  std::map<string, int> port_numbers_rep;   /** highest port number used for each prefix */

  int jackslave_seekahead_rep;
  long int jackslave_seekahead_target_rep;
  
  ECA_ENGINE* engine_repp;
  jack_client_t *client_repp;
  string jackname_rep;          /**< client name */

  SAMPLE_SPECS::sample_rate_t srate_rep;
  long int buffersize_rep;
  long int cb_allocated_frames_rep;
};

#endif
