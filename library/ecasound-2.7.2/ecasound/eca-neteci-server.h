#ifndef INCLUDED_ECA_NETECI_SERVER_H
#define INCLUDED_ECA_NETECI_SERVER_H

#include <list>
#include <string>

#include <sys/socket.h>   /* Generic socket definitions */
#include <sys/un.h>       /* UNIX socket definitions */
#include <netinet/in.h>   /* IP socket definitions */

struct ecasound_state;
class ECASOUND_RUN_STATE;

struct ecasound_neteci_server_client {
  std::string peername;
  char* buffer;
  int fd;
  int buffer_current_ptr;
  int buffer_length;
};

/**
 * NetECI server implementation.
 *
 * @author Kai Vehmanen
 */
class ECA_NETECI_SERVER {

 public:

  /**
   * Constructor.
   */
  ECA_NETECI_SERVER(ECASOUND_RUN_STATE* state);

  /**
   * Virtual destructor.
   */
  ~ECA_NETECI_SERVER(void);

  static void* launch_server_thread(void* arg);

 private:

  void run(void);

  void create_server_socket(void);
  void open_server_socket(void);
  void close_server_socket(void);
  void listen_for_events(void);
  void check_for_events(int timeout);
  void handle_connection(int fd);
  void handle_client_messages(struct ecasound_neteci_server_client* client);
  void handle_eci_command(const std::string& cmd, struct ecasound_neteci_server_client* client);
  void parse_raw_incoming_data(const char* buffer, 
			       ssize_t bytes,
			       struct ecasound_neteci_server_client* client);
  void remove_client(struct ecasound_neteci_server_client* client);
  void clean_removed_clients(void);

  struct sockaddr_un addr_un_rep;
  struct sockaddr_in addr_in_rep;
  struct sockaddr* addr_repp;
  ECASOUND_RUN_STATE* state_repp;

  std::list<struct ecasound_neteci_server_client*> clients_rep;
  /* FIXME: turn into a buffer of pointers to allow ptr-fields */
  std::list<std::string> parsed_cmd_queue_rep;
  std::string socketpath_rep;

  int srvfd_rep;
  bool server_listening_rep;
  bool unix_sockets_rep;
  bool cleanup_request_rep;

};

#endif /* INCLUDED_ECA_NETECI_SERVER_H */
