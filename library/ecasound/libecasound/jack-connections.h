#ifndef INCLUDED_JACK_CONNECTIONS_H
#define INCLUDED_JACK_CONNECTIONS_H

#include <string>

/**
 * Utility class to manage JACK port connections.
 */
class JACK_CONNECTIONS {

public:

  static bool connect(const char* src, const char* dest);
  static bool disconnect(const char* src, const char* dest);
  static bool list_connections(std::string* output);

private:

  JACK_CONNECTIONS(void);
  ~JACK_CONNECTIONS(void);

};

#endif /* INCLUDED_JACK_CONNECTIONS_H */
