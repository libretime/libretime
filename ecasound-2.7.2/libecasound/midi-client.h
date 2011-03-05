#ifndef INCLUDED_MIDI_CLIENT_H
#define INCLUDED_MIDI_CLIENT_H

class MIDI_SERVER;

/**
 * Top-level interface for MIDI-clients
 */
class MIDI_CLIENT {
  
 public:

  int id(void) const;
  void register_server(MIDI_SERVER* server);

  MIDI_CLIENT(void);

 protected:

  void set_id(int n);
  MIDI_SERVER* server(void) const { return(server_repp); }

 private:

  int id_rep;
  bool id_set_rep;
  MIDI_SERVER* server_repp;
};

#endif
