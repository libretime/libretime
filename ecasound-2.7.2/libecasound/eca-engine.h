#ifndef INCLUDED_ECA_ENGINE_H
#define INCLUDED_ECA_ENGINE_H

#include <vector>
#include "sample-specs.h"
#include "eca-engine-driver.h"
#include "eca-chainsetup-edit.h"

class AUDIO_IO;
class AUDIO_IO_DB_CLIENT;
class AUDIO_IO_DEVICE;
class CHAIN;
class CHAIN_OPERATOR;
class ECA_CHAINSETUP;
class ECA_ENGINE;
class ECA_ENGINE_impl;
class SAMPLE_BUFFER;

/**
 * Default engine driver
 */
class ECA_ENGINE_DEFAULT_DRIVER : public ECA_ENGINE_DRIVER {

 public:

  virtual int exec(ECA_ENGINE* engine, ECA_CHAINSETUP* csetup);
  virtual void start(void);
  virtual void stop(void);
  virtual void exit(void);

 private:

  ECA_ENGINE* engine_repp;
  bool exit_request_rep;

};

/**
 * ECA_ENGINE is the actual processing engine. 
 * It is initialized with a pointer to a 
 * ECA_CHAINSETUP object, which has all information 
 * needed at runtime. In other words ECA_ENGINE is 
 * used to execute the chainsetup. You could say 
 * ECA_ENGINE renders the final product according 
 * to instruction given in ECA_CHAINSETUP. 
 *
 * In most use cases ECA_ENGINE operation 
 * involves multiple threads. The main thread
 * contexts are:
 *
 *     - control context in which
 *       ECA_ENGINE::exec() is executed
 *
 *     - driver context; depending on the 
 *       used driver, this can be either
 *       a separate thread or same as the
 *       control thread
 *
 *     - external context; other threads
 *       sending commands to the engine
 *
 * Notes: This class is closely tied to 
 *        ECA_CHAINSETUP. Its private data and
 *        function members can be accessed by 
 *        ECA_ENGINE through friend-access.
 */
class ECA_ENGINE {

 public:

  /** @name Public type definitions and constants */
  /*@{*/

  /** 
   * Engine operation states
   */
  enum Engine_status { engine_status_running,
		       engine_status_stopped, 
		       engine_status_finished,
		       engine_status_error,
		       engine_status_notready };
  typedef enum Engine_status Engine_status_t;

  /**
   * Commands used in ECA_ENGINE<->ECA_CONTROL communication.
   */
  enum Engine_command {
    ep_prepare = 0,
    ep_start,
    ep_stop,
    ep_debug,
    ep_exit,
    // --
    ep_exec_edit,
    // --
    ep_c_muting,
    ep_c_bypass,
    ep_c_select,
    // --
    ep_rewind,
    ep_forward,
    ep_setpos,
    ep_setpos_samples,
    ep_setpos_live_samples,
    // --
    ep_edit_lock,
    ep_edit_unlock
  };
  typedef enum Engine_command Engine_command_t;

  struct complex_command {
    Engine_command_t type;
    union {
      struct {
	double value;
      } engine;

      ECA::chainsetup_edit_t cs;

      struct {
	int chain;
	int op;
	int param;
	double value;
      } legacy;

    } m;
  };
  typedef struct complex_command complex_command_t;

  /*@}*/

  /** @name Public functions */
  /*@{*/

  ECA_ENGINE(ECA_CHAINSETUP* eparam);
  ~ECA_ENGINE(void);

  int exec(bool batch_mode);
  void command(Engine_command_t cmd, double arg);
  void command(complex_command_t ccmd);
  void wait_for_stop(int timeout);
  void wait_for_exit(int timeout);

  /*@}*/

  /** @name Public functions for observing engine status information */
  /*@{*/

  bool is_valid(void) const;
  bool is_finite_length(void) const;
  Engine_status_t status(void) const;

  /*@}*/

  /** @name API for engine driver objects (@see ECA_ENGINE_DRIVER) */
  /*@{*/

  void check_command_queue(void);
  void wait_for_commands(void);
  void init_engine_state(void);
  void update_engine_state(void);
  void engine_iteration(void);

  void prepare_operation(void);
  void start_operation(void);
  void stop_operation(void);

  void update_cache_chain_connections(void);
  void update_cache_latency_values(void);

  bool is_prepared(void) const;
  bool is_running(void) const;
  bool batch_mode(void) const { return(batchmode_enabled_rep); }
  bool is_locked_for_editing(void) const { return(edit_lock_rep); }

  SAMPLE_SPECS::sample_pos_t current_position_in_samples(void) const;
  double current_position_in_seconds_exact(void) const;

  const ECA_CHAINSETUP* connected_chainsetup(void) const { return(csetup_repp); }

  /*@}*/

private:

  /** @name Private data and functions */
  /*@{*/

  /**
   * Number of sample-frames of data is prefilled to 
   * rt-outputs before starting processing.
   */
  static const long int prefill_threshold_constant = 16348;
  static const int prefill_blocks_constant = 3;

  ECA_ENGINE_impl* impl_repp;

  bool use_midi_rep;
  bool batchmode_enabled_rep;
  bool processing_range_set_rep;

  bool prepared_rep;
  bool running_rep;
  bool was_running_rep;
  bool driver_local;
  bool edit_lock_rep;

  bool finished_rep;
  int outputs_finished_rep;
  int driver_errors_rep;
  int inputs_not_finished_rep;

  long int prefill_threshold_rep;
  long int preroll_samples_rep;
  long int recording_offset_rep;

  /*@}*/

  /** @name Pointers to connected chainsetup  */
  /*@{*/

  ECA_CHAINSETUP* csetup_repp;
  ECA_ENGINE_DRIVER* driver_repp;

  std::vector<CHAIN*>* chains_repp;
  std::vector<AUDIO_IO*>* inputs_repp;
  std::vector<AUDIO_IO*>* outputs_repp;

  /*@}*/

  /** @name Audio data buffers */
  /*@{*/

  SAMPLE_BUFFER* mixslot_repp;
  std::vector<SAMPLE_BUFFER*> cslots_rep;

  /*@}*/

  /** 
   * @name Various audio object maps.
   * 
   * The main purpose of these maps is to make 
   * it easier to iterate audio objects with 
   * certain attributes.
   */
  /*@{*/

  std::vector<AUDIO_IO_DEVICE*> realtime_inputs_rep;
  std::vector<AUDIO_IO_DEVICE*> realtime_outputs_rep;
  std::vector<AUDIO_IO_DEVICE*> realtime_objects_rep;
  std::vector<AUDIO_IO*> non_realtime_inputs_rep;
  std::vector<AUDIO_IO*> non_realtime_outputs_rep;
  std::vector<AUDIO_IO*> non_realtime_objects_rep;

  /*@}*/

  /** @name Cache objects for chainsetup and audio 
   * object information  */
  /*@{*/

  std::vector<int> input_chain_count_rep;
  std::vector<int> output_chain_count_rep;

  /** @name Attribute functions */
  /*@{*/

  long int buffersize(void) const;
  int max_channels(void) const;

  /*@}*/

  /** @name Private functions for transport control  */
  /*@{*/

  void request_start(void);
  void request_stop(void);
  void signal_stop(void);
  void signal_exit(void);
  void conditional_start(void);
  void conditional_stop(void);

  void start_servers(void);
  void stop_servers(void);

  void prepare_realtime_objects(void);
  void start_realtime_objects(void);
  void reset_realtime_devices(void);

  void start_forked_objects(void);
  void stop_forked_objects(void);

  void state_change_to_finished(void);

  /*@}*/

  /** @name Private functions for observing and modifying position  */
  /*@{*/

  void set_position(double seconds);
  void set_position(int seconds) { set_position((double)seconds); }
  void set_position_samples(SAMPLE_SPECS::sample_pos_t samples);
  void set_position_samples_live(SAMPLE_SPECS::sample_pos_t samples);
  void change_position(double seconds);

  void prehandle_control_position(void);
  void posthandle_control_position(void);

  /*@}*/

  /** @name Private functions for command queue handling  */
  /*@{*/

  void interpret_queue(void);

  /*@}*/

  /** @name Private functions for setup and cleanup  */
  /*@{*/

  void init_variables(void);
  void init_connection_to_chainsetup(void);
  void init_driver(void);
  void init_prefill(void);
  void init_servers(void);
  void init_chains(void);
  void cleanup(void);

  void create_cache_object_lists(void);

  void init_profiling(void);
  void dump_profile_info(void);

  /*@}*/

  /** @name Private functions for signal routing  */
  /*@{*/

  void inputs_to_chains(void);
  void process_chains(void);
  void mix_to_outputs(bool skip_realtime_target_outputs);

  /*@}*/

  /** @name Private functions for toggling engine features */
  /*@{*/

  void chain_muting(void);
  void chain_processing(void);

  /*@}*/

  /** @name Hidden/unimplemented functions */
  /*@{*/

  ECA_ENGINE& operator=(const ECA_ENGINE& x) { return *this; }

  /*@}*/
};

#endif
