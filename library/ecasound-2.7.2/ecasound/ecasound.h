#ifndef INCLUDED_ECASOUND_H
#define INCLUDED_ECASOUND_H

#include <string>
#include <vector>

#include <pthread.h>   /* POSIX: pthread_create() */
#include <signal.h>    /* POSIX: sigaction(), sigwait(), sig_atomic_t */

class ECA_CONSOLE;
class ECA_CONTROL_MT;
class ECA_LOGGER_INTERFACE;
class ECA_NETECI_SERVER;
class ECA_SESSION;
class ECA_OSC_INTERFACE;

/**
 * String constants
 */

#define ECASOUND_BANNER_ASTERISK_BAR "********************************************************************************\n"
#define ECASOUND_COPYRIGHT           " (C) 1997-2010 Kai Vehmanen and others    "

#define ECASOUND_RETVAL_SUCCESS         0    /**< Succesful run */
#define ECASOUND_RETVAL_INIT_FAILURE    1    /**< Unable to initialize */
#define ECASOUND_RETVAL_START_ERROR     2    /**< Unable to start processing */
#define ECASOUND_RETVAL_RUNTIME_ERROR   3    /**< Error during processing */
#define ECASOUND_RETVAL_CLEANUP_ERROR   4    /**< Error during cleanup/exit */

#define ECASOUND_TERM_WIDTH_DEFAULT     74

/**
 * Type definitions
 */

/* Note! Check the initialization in ecasound.cpp if
 *       you change the state struct! */

class ECASOUND_RUN_STATE {
 public:
  ECASOUND_RUN_STATE(void);
  ~ECASOUND_RUN_STATE(void);

  ECA_CONSOLE* console;
  ECA_CONTROL_MT* control;
  ECA_LOGGER_INTERFACE* logger;
  ECA_NETECI_SERVER* eciserver;
  ECA_OSC_INTERFACE* osc;
  ECA_SESSION* session;
  std::vector<std::string>* launchcmds;

  pthread_t* neteci_thread;
  pthread_t* watchdog_thread;
  pthread_mutex_t* lock;
  sig_atomic_t exit_request;
  sigset_t* signalset;

  int retval;

  bool neteci_mode;
  int neteci_tcp_port;

  bool osc_mode;
  int osc_udp_port;

  bool keep_running_mode;
  bool cerr_output_only_mode;
  bool interactive_mode;
  bool quiet_mode;
};

#endif /* INCLUDED_ECASOUND_H */
