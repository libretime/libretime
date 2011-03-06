#ifndef INCLUDED_AUDIO_STAMP_H
#define INCLUDED_AUDIO_STAMP_H

#include <map>
#include "samplebuffer.h"

class AUDIO_STAMP {

 public:

  int id(void) const;
  void fetch_stamp(SAMPLE_BUFFER* x);

  AUDIO_STAMP(void);

 protected:

  void set_id(int n);
  void store(const SAMPLE_BUFFER* x);

 private:

  SAMPLE_BUFFER buffer_rep;
  int id_rep;
  bool id_set_rep;
};

class AUDIO_STAMP_SERVER {
  
 public:

  void register_stamp(AUDIO_STAMP* stamp);
  void fetch_stamp(int id, SAMPLE_BUFFER* x);

 private:

  std::map<int, AUDIO_STAMP*> stamp_map_rep;
};

class AUDIO_STAMP_CLIENT {
  
 public:

  int id(void) const;
  void register_server(AUDIO_STAMP_SERVER* server);

  AUDIO_STAMP_CLIENT(void);

 protected:

  void set_id(int n);
  void fetch_stamp(SAMPLE_BUFFER* x);

 private:

  int id_rep;
  bool id_set_rep;
  AUDIO_STAMP_SERVER* server_repp;
};

#endif
