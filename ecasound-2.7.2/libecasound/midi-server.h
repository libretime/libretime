#ifndef INCLUDED_MIDI_SERVER_H
#define INCLUDED_MIDI_SERVER_H

#include <deque>
#include <vector>
#include <list>
#include <string>
#include <map>

#include <pthread.h>
#include <kvu_locks.h>
#include "midiio.h"

/**
 * Interface class for specifying custom MIDI-handlers.
 */
class MIDI_HANDLER {

 public:
  
  virtual void insert(unsigned char byte) = 0;

  virtual ~MIDI_HANDLER(void) {}
};

/**
 * MIDI i/o engine.
 *
 * @author Kai Vehmanen
 */
class MIDI_SERVER {

  friend void* start_midi_server_io_thread(void *ptr);

 public:

  static const unsigned int max_queue_size_rep;

 public:

  bool is_running(void) const;
  bool is_enabled(void) const;

  void enable(void);
  void disable(void);

  void init(void);
  void start(void);
  void stop(void);

  void set_schedrealtime(bool v) { schedrealtime_rep = v; }
  void set_schedpriority(int v) { schedpriority_rep = v; }

  void register_client(MIDI_IO* mobject);
  void unregister_client(MIDI_IO* mobject);

  void register_handler(MIDI_HANDLER* handler);
  void unregister_handler(MIDI_HANDLER* handler);

  void add_mmc_send_id(int id);
  void remove_mmc_send_id(int id);
  void set_mmc_receive_id(int id) { mmc_receive_id_rep = id; }
  int mmc_receive_id(int id) const { return(mmc_receive_id_rep); }

  void toggle_midi_sync_send(bool v) { midi_sync_send_rep = v; }
  void toggle_midi_sync_receive(bool v) { midi_sync_receive_rep = v; }
  bool is_midi_sync_send_enabled(void) const { return(midi_sync_send_rep); }
  bool is_midi_sync_receive_enabled(void) const { return(midi_sync_receive_rep); }

  void send_midi_bytes(int dev_id, unsigned char* buf, int bytes);

  void add_controller_trace(int channel, int ctrl, int initial_value = 0);
  void remove_controller_trace(int channel, int ctrl);
  int last_controller_value(int channel, int ctrl) const;

  MIDI_SERVER (void);
  ~MIDI_SERVER(void);

 private:

  std::deque<unsigned char> buffer_rep;
  mutable std::map<std::pair<int,int>,int> controller_values_rep;
  unsigned char running_status_rep;
  int current_ctrl_channel_rep;
  int current_ctrl_number;

  std::list<int> mmc_send_ids_rep;
  int mmc_receive_id_rep;
  std::vector<MIDI_IO*> clients_rep;
  bool midi_sync_send_rep;
  bool midi_sync_receive_rep;
  std::vector<MIDI_HANDLER*> handlers_rep;

  pthread_t io_thread_rep;
  bool thread_running_rep;
  bool schedrealtime_rep;
  int schedpriority_rep;
  ATOMIC_INTEGER exit_request_rep;
  ATOMIC_INTEGER stop_request_rep;
  ATOMIC_INTEGER running_rep;

  MIDI_SERVER& operator=(const MIDI_SERVER& x) { return *this; }
  MIDI_SERVER (const MIDI_SERVER& x) { }

  void io_thread(void);
  void parse_receive_queue(void);

  void send_mmc_command(unsigned int cmd);
  void send_mmc_start(void);
  void send_mmc_stop(void);
  void send_midi_start(void);
  void send_midi_continue(void);
  void send_midi_stop(void);
};

#endif
