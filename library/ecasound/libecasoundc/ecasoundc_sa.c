/** 
 * @file ecasoundc_sa.cpp Standalone C implementation of the 
 *                        ecasound control interface
 */

/* FIXME: add check for big sync-error -> ecasound probably 
 *        died so better to give an error */
/* FIXME: add check for msgsize errors */

/** ------------------------------------------------------------------------
 * ecasoundc.cpp: Standalone C implementation of the 
 *                ecasound control interface
 * Copyright (C) 2000-2006,2008,2009 Kai Vehmanen
 * Copyright (C) 2003 Michael Ewe
 * Copyright (C) 2001 Aymeric Jeanneau
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * -------------------------------------------------------------------------
 * History of major changes:
 *
 * 2009-02-08 Kai Vehmanen
 *     - Finally got rid of the fixed-size parsing buffers.
 *     - Added handling (or proper ignoring) of SIGPIPE signals.
 * 2006-12-06 Kai Vehmanen 
 *     - Fixed severe string termination bug in handling lists of
 *       strings.
 *     - Fixed mechanism for waiting on grandchild ecasound process to exit.
 * 2003-12-24 Michael Ewe
 *     - Fixed signaling issues on FreeBSD. Modified to perform a
 *       double-fork to better decouple ECI stack and the ecasound
 *       engine process.
 * 2002-10-04 Kai Vehmanen
 *     - Rewritten as a standalone implementation.
 * 2001-06-04 Aymeric Jeanneau
 *     - Added reentrant versions of all public ECI functions.
 * 2000-12-06 Kai Vehmanen
 *     - Initial version.
 *
 * -------------------------------------------------------------------------
 */

#include <assert.h>
#include <stdio.h>        /* ANSI-C: printf(), ... */
#include <stdlib.h>       /* ANSI-C: calloc(), free() */
#include <string.h>       /* ANSI-C: strlen() */
#include <errno.h>        /* ANSI-C: errno */
#include <stdbool.h>

#include <fcntl.h>        /* POSIX: fcntl() */
#include <sys/poll.h>     /* XPG4-UNIX: poll() */
#include <unistd.h>       /* POSIX: pipe(), fork() */
#include <sys/stat.h>     /* POSIX: stat() */
#include <sys/types.h>    /* POSIX: fork() */
#include <sys/wait.h>     /* POSIX: wait() */
#include <signal.h>       /* POSIX: signal handling */

#include "ecasoundc.h"

/* --------------------------------------------------------------------- 
 * Options
 */

// #define ECI_ENABLE_DEBUG

/* --------------------------------------------------------------------- 
 * Definitions and constants
 */

#define ECI_PARSER_BUF_SIZE        65536

#define ECI_MAX_DYN_ALLOC_SIZE     16777216 /* assert if reached */
#define ECI_MAX_FLOAT_BUF_SIZE     32
#define ECI_MAX_RETURN_TYPE_SIZE   4
#define ECI_INI_STRING_SIZE        64
#define ECI_MAX_RESYNC_ATTEMPTS    9
#define ECI_MAX_LAST_COMMAND_SIZE  64

#define ECI_READ_TIMEOUT_MS        5000
#define ECI_READ_RETVAL_TIMEOUT_MS 30000

#define ECI_STATE_INIT             0
#define ECI_STATE_LOGLEVEL         1
#define ECI_STATE_MSGSIZE          2
#define ECI_STATE_COMMON_CR_1      3
#define ECI_STATE_COMMON_LF_1      4
#define ECI_STATE_RET_TYPE         5
#define ECI_STATE_COMMON_CONTENT   6
#define ECI_STATE_COMMON_CR_2      7
#define ECI_STATE_COMMON_LF_2      8
#define ECI_STATE_COMMON_CR_3      9
#define ECI_STATE_COMMON_LF_3      10
#define ECI_STATE_SEEK_TO_LF       11

#define ECI_STATE_MSG_GEN          0
#define ECI_STATE_MSG_RETURN       1

#define ECI_TOKEN_PHASE_NONE       0
#define ECI_TOKEN_PHASE_READING    1
#define ECI_TOKEN_PHASE_VALIDATE   2

#define ECI_RETURN_TYPE_LOGLEVEL   256

#ifdef ECI_ENABLE_DEBUG
#define ECI_DEBUG(x) fprintf(stderr,x)
#define ECI_DEBUG_1(x,y) fprintf(stderr,x,y)
#define ECI_DEBUG_2(x,y,z) fprintf(stderr,x,y,z)
#define ECI_DEBUG_3(x,y,z,t) fprintf(stderr,x,y,z,t)
#else
#define ECI_DEBUG(x) ((void) 0)
#define ECI_DEBUG_1(x,y) ((void) 0)
#define ECI_DEBUG_2(x,y,z) ((void) 0)
#define ECI_DEBUG_3(x,y,z,t) ((void) 0)
#endif

#define DBC_REQUIRE(expr)						      \
   (expr) ? (void)(0) :	(void)(fprintf(stderr, "Warning: DBC_REQUIRE failed - \"%s\", %s, %d.\n", #expr,__FILE__, __LINE__))
#define DBC_ENSURE(expr)							      \
   (expr) ? (void)(0) :	(void)(fprintf(stderr, "Warning: DBC_ENSURE failed - \"%s\", %s, %d.\n", #expr,__FILE__, __LINE__))
#define DBC_CHECK(expr)							      \
   (expr) ? (void)(0) :	(void)(fprintf(stderr, "Warning: DBC_CHECK failed - \"%s\", %s, %d.\n", #expr,__FILE__, __LINE__))
#define DBC_DECLARE(expr)               expr

/* --------------------------------------------------------------------- 
 * Data structures 
 */

struct eci_string_s {
  char *d;   /* buffer to string contents */
  int slen;     /* string length in octets, including terminating null */
  int size;     /* buffer length in octets, including terminating null */
};
typedef struct eci_string_s eci_string;

struct eci_los_list {
  struct eci_los_list* prev_repp;
  struct eci_los_list* next_repp;
  eci_string data_repp;
};

struct eci_parser { 

  int state_rep;
  int state_msg_rep;

  double last_f_rep;
  long int last_li_rep;
  int last_i_rep;
  int last_counter_rep;
  char last_type_repp[ECI_MAX_RETURN_TYPE_SIZE];
  struct eci_los_list* last_los_repp;
  eci_string last_error_repp;
  eci_string last_s_repp;

  eci_string buffer_rep;

  int msgsize_rep;
  int loglevel_rep;

  int token_phase_rep;
  int buffer_current_rep;

  bool sync_lost_rep;
};

struct eci_internal { 
  int pid_of_child_rep;
  int pid_of_parent_rep;
  int cmd_read_fd_rep;
  int cmd_write_fd_rep;

  char last_command_repp[ECI_MAX_LAST_COMMAND_SIZE];
  int commands_counter_rep;

  struct eci_parser* parser_repp;

  char farg_buf_repp[ECI_MAX_FLOAT_BUF_SIZE];
  char raw_buffer_repp[ECI_PARSER_BUF_SIZE];
};

/* --------------------------------------------------------------------- 
 * Global variables
 */

static eci_handle_t static_eci_rep = 0;

/**
 * Message shown if ECASOUND is not defined.
 */
const char* eci_str_no_ecasound_env = 
    "\n"
    "***********************************************************************\n"
    "* Message from libecasoundc:\n"
    "* \n"
    "* 'ECASOUND' environment variable not set. Using the default value \n"
    "* value 'ECASOUND=ecasound'.\n"
    "***********************************************************************\n"
    "\n";

const char* eci_str_null_handle = 
    "\n"
    "***********************************************************************\n"
    "* Message from libecasoundc:\n"
    "* \n"
    "* A null client handle detected. This is usually caused by a bug \n"
    "* in the ECI application. Please report this bug to the author of\n"
    "* the program.\n"
    "***********************************************************************\n"
    "\n";

const char* eci_str_sync_lost =
    "\n"
    "***********************************************************************\n"
    "* Message from libecasoundc:\n"
    "* \n"
    "* Connection to the processing engine was lost. Check that ecasound \n"
    "* is correctly installed. Also make sure that ecasound is either \n"
    "* in some directory listed in PATH, or the environment variable\n"
    "* 'ECASOUND' contains the path to a working ecasound executable.\n"
    "***********************************************************************\n"
    "\n";

/* --------------------------------------------------------------------- 
 * Declarations of static functions
 */

static void eci_impl_check_handle(struct eci_internal* eci_rep);
static void eci_impl_free_parser(struct eci_internal* eci_rep);
static void eci_impl_clean_last_values(struct eci_parser* parser);
static void eci_impl_dump_parser_state(eci_handle_t ptr, const char* message);
static ssize_t eci_impl_fd_read(int fd, void *buf, size_t count, int timeout);
static const char* eci_impl_get_ecasound_path(void);
static struct eci_los_list *eci_impl_los_list_add_item(struct eci_los_list* headptr, char* stmp, int len);
static struct eci_los_list *eci_impl_los_list_alloc_item(void);
static void eci_impl_los_list_clear(struct eci_los_list *ptr);
static void eci_impl_read_return_value(struct eci_internal* eci_rep, int timeout);
static void eci_impl_set_last_los_value(struct eci_parser* parser);
static void eci_impl_set_last_values(struct eci_parser* parser);
static void eci_impl_update_state(struct eci_parser* eci_rep, char c);

/* ---------------------------------------------------------------------
 * Constructing and destructing                                       
 */

/**
 * Initializes session. This call clears all status info and
 * prepares ecasound for processing. Can be used to "restart"
 * the library.
 */
void eci_init(void)
{
  DBC_CHECK(static_eci_rep == NULL);
  static_eci_rep = eci_init_r();
}

/**
 * Initializes session. This call creates a new ecasound
 * instance and prepares it for processing. 
 *
 * @return NULL if initialization fails
 */
eci_handle_t eci_init_r(void)
{
  struct eci_internal* eci_rep = NULL;
  int cmd_send_pipe[2], cmd_receive_pipe[2];
  const char* ecasound_exec = eci_impl_get_ecasound_path();

  /* step: launch ecasound process and setup two-way communication */
  if (ecasound_exec != NULL &&
      (pipe(cmd_receive_pipe) == 0 && pipe(cmd_send_pipe) == 0)) {
    int fork_pid = fork();
    /* step: 1st fork */
    if (fork_pid == 0) { 
      /* first child (phase-1) */

      /* -c = interactive mode, -D = direct prompts and banners to stderr */
      const char* args[4] = { NULL, "-c", "-D", NULL };
      int res = 0;
      struct sigaction sa;
      pid_t pid;

      sa.sa_handler=SIG_IGN;
      sigemptyset(&sa.sa_mask);
      sa.sa_flags=0;
      sigaction(SIGHUP, &sa, NULL);
      setsid();

      /* step: 2nd fork (to detach from parent) */
      if (fork() != 0)
	  _exit(0);    /* first child terminates here (phase-2) */

      /* second child continues (phase-2) */
     
      args[0] = ecasound_exec;

      /* close all unused descriptors and resources */

      close(0);
      close(1);

      dup2(cmd_send_pipe[0], 0);
      dup2(cmd_receive_pipe[1], 1);

      close(cmd_receive_pipe[0]);
      close(cmd_receive_pipe[1]);
      close(cmd_send_pipe[0]);
      close(cmd_send_pipe[1]);

      freopen("/dev/null", "w", stderr);

      /* step: write second child's pid to the (grand) parent */
      pid = getpid();
      write(1, &pid, sizeof(pid));
      
      /* step: notify the parent that we're up */
      res = write(1, args, 1); 

      res = execvp(args[0], (char**)args);
      if (res < 0) printf("(ecasoundc_sa) launching ecasound FAILED!\n");

      close(0);
      close(1);

      _exit(res);
      ECI_DEBUG("(ecasoundc_sa) You shouldn't see this!\n");
    }
    else { 
      /* step: parent (phase-1) */
      int res;
      char buf[1];
      int status;
      int pid;

      /* set up signal handling */
      struct sigaction ign_handler;
      ign_handler.sa_handler = SIG_IGN;
      sigemptyset(&ign_handler.sa_mask);
      ign_handler.sa_flags = 0;
      /* ignore the following signals */
      sigaction(SIGPIPE, &ign_handler, 0);
      sigaction(SIGFPE, &ign_handler, 0);

      eci_rep = (struct eci_internal*)calloc(1, sizeof(struct eci_internal));
      eci_rep->parser_repp = (struct eci_parser*)calloc(1, sizeof(struct eci_parser));

      /* step: initialize variables */
      eci_rep->pid_of_child_rep = fork_pid;
      eci_rep->commands_counter_rep = 0;
      eci_rep->parser_repp->last_counter_rep = 0;
      eci_rep->parser_repp->token_phase_rep = ECI_TOKEN_PHASE_NONE;
      eci_rep->parser_repp->buffer_current_rep = 0;
      eci_rep->parser_repp->sync_lost_rep = false;
      eci_impl_clean_last_values(eci_rep->parser_repp);

      /*
	waits for first child to prevent the zombie
	read grand child prozess id from pipe
      */
      waitpid(eci_rep->pid_of_child_rep, &status, 0);
      res = read(cmd_receive_pipe[0], &pid, sizeof(pid));
      if ( res != sizeof(pid) ) {
	  ECI_DEBUG_1("(ecasoundc_sa) fork() of %s FAILED!\n", ecasound_exec);
	  eci_impl_free_parser(eci_rep);
	  free(eci_rep);
	  eci_rep = NULL;
      }
      eci_rep->pid_of_child_rep = pid;
      eci_rep->pid_of_parent_rep = getpid();

      eci_rep->cmd_read_fd_rep = cmd_receive_pipe[0];
      close(cmd_receive_pipe[1]);
      eci_rep->cmd_write_fd_rep = cmd_send_pipe[1];
      close(cmd_send_pipe[0]);

      /* step: switch to non-blocking mode for read */
      fcntl(eci_rep->cmd_read_fd_rep, F_SETFL, O_NONBLOCK);
      fcntl(eci_rep->cmd_write_fd_rep, F_SETFL, O_NONBLOCK);

      /* step: check that fork succeeded() */
      res = eci_impl_fd_read(eci_rep->cmd_read_fd_rep, buf, 1, ECI_READ_TIMEOUT_MS);
      if (res != 1) {
	ECI_DEBUG_1("(ecasoundc_sa) fork() of %s FAILED!\n", ecasound_exec);
	eci_impl_free_parser(eci_rep);
	free(eci_rep);
	eci_rep = NULL;
      }
      else {
	write(eci_rep->cmd_write_fd_rep, "debug 256\n", strlen("debug 256\n"));
	write(eci_rep->cmd_write_fd_rep, "int-set-float-to-string-precision 17\n", strlen("int-set-float-to-string-precision 17\n"));
	write(eci_rep->cmd_write_fd_rep, "int-output-mode-wellformed\n", strlen("int-output-mode-wellformed\n"));
	eci_rep->commands_counter_rep ++;
      
	/* step: check that exec() succeeded */
	eci_impl_read_return_value(eci_rep, ECI_READ_TIMEOUT_MS);
	if (eci_rep->commands_counter_rep != eci_rep->parser_repp->last_counter_rep) {
	  ECI_DEBUG_3("(ecasoundc_sa) exec() of %s FAILED (%d=%d)!\n", ecasound_exec, eci_rep->commands_counter_rep, eci_rep->parser_repp->last_counter_rep);
	  eci_impl_free_parser(eci_rep);
	  free(eci_rep);
	  eci_rep = NULL;
	}
      }
    }
  }

  return (eci_handle_t)eci_rep;
}

/**
 * Checks whether ECI is ready for use.
 *
 * @return non-zero if ready, zero otherwise
 */
int eci_ready(void)
{
  return eci_ready_r(static_eci_rep);
}

/**
 * Checks whether ECI is ready for use.
 *
 * @return non-zero if ready, zero otherwise
 */
int eci_ready_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  if (!ptr)
    return 0;

  if (eci_rep->pid_of_child_rep <= 0 ||
      eci_rep->cmd_read_fd_rep < 0 ||
      eci_rep->cmd_write_fd_rep < 0)
    return 0;
      
  return 1;
}

/**
 * Frees all resources.
 */
void eci_cleanup(void)
{
  if (static_eci_rep != NULL) {
    eci_cleanup_r(static_eci_rep);
    static_eci_rep = NULL;
  }
}

/**
 * Frees all resources.
 */
void eci_cleanup_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;
  ssize_t resread = 1, respoll;
  char buf[1];
  struct pollfd fds[1];

  eci_impl_check_handle(eci_rep);

  ECI_DEBUG("\n(ecasoundc_sa) requesting to terminatte ecasound process.\n");

  write(eci_rep->cmd_write_fd_rep, "quit\n", strlen("quit\n"));
  eci_rep->commands_counter_rep++;
  
  /* as we use double-fork, we cannot use waitpid() --
   * to block until ecasound grandchild has exited, we 
   * use a combination of poll+read(), 
   * ref:  http://www.greenend.org.uk/rjk/2001/06/poll.html
   */

  ECI_DEBUG_1("\n(ecasoundc_sa) cleaning up. waiting for grandchild ecasound process %d.\n", eci_rep->pid_of_child_rep);
  
  while (resread > 0) {
    fds[0].fd = eci_rep->cmd_read_fd_rep;
    fds[0].events = POLLIN;
    fds[0].revents = 0;
    respoll = poll(fds, 1, ECI_READ_RETVAL_TIMEOUT_MS);
    if (fds[0].revents & (POLLIN | POLLHUP))
      resread = read(eci_rep->cmd_read_fd_rep, buf, 1);
    else if (fds[0].revents & POLLERR)
      resread = -2;
    
    ECI_DEBUG_3("(ecasoundc_sa) waiting for ecasound, poll=%d, read=%d, revents=0x%02x)\n", respoll, resread, fds[0].revents);
  }
  
  ECI_DEBUG("(ecasoundc_sa) child exited\n");

  if (eci_rep != 0) {
    /* close descriptors */
    close(eci_rep->cmd_read_fd_rep);
    close(eci_rep->cmd_write_fd_rep);

    /* free lists of strings, if any */
    eci_impl_clean_last_values(eci_rep->parser_repp);

    eci_impl_free_parser(eci_rep);
    free(eci_rep);
  }
}

/* ---------------------------------------------------------------------
 * Issuing EIAM commands 
 */

/**
 * Sends a command to the ecasound engine. See ecasound-iam(5) for
 * more info.
 */
void eci_command(const char* command) { eci_command_r(static_eci_rep, command); }

/**
 * Sends a command to the ecasound engine. See ecasound-iam(5) for
 * more info.
 */
void eci_command_r(eci_handle_t ptr, const char* command)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;
  int timeout = ECI_READ_RETVAL_TIMEOUT_MS;

  eci_impl_check_handle(eci_rep);

  if (eci_ready_r(ptr) == 0) {
    ECI_DEBUG("(ecasoundc_sa) not ready, unable to process commands\n");
    return;
  }

  ECI_DEBUG_2("(ecasoundc_sa) writing command '%s' (cmd-counter=%d).\n", 
	      command, eci_rep->commands_counter_rep + 1);

  memcpy(eci_rep->last_command_repp, command, ECI_MAX_LAST_COMMAND_SIZE);

  eci_impl_clean_last_values(eci_rep->parser_repp);

  write(eci_rep->cmd_write_fd_rep, command, strlen(command));
  write(eci_rep->cmd_write_fd_rep, "\n", 1);

  /* 'run' is the only blocking function */
  if (strncmp(command, "run", 3) == 0) {
    ECI_DEBUG("(ecasoundc_sa) 'run' detected; disabling reply timeout!\n");
    timeout = -1;
  }

  eci_rep->commands_counter_rep++;
    
  if (eci_rep->commands_counter_rep - 1 !=
      eci_rep->parser_repp->last_counter_rep) {
    eci_impl_dump_parser_state(ptr, "sync error");
    eci_rep->parser_repp->sync_lost_rep = true;
  }
  
  if (eci_rep->commands_counter_rep >=
      eci_rep->parser_repp->last_counter_rep) {
    eci_impl_read_return_value(eci_rep, timeout);
  }

  ECI_DEBUG_2("(ecasoundc_sa) set return value type='%s' (read-counter=%d).\n", 
	      eci_rep->parser_repp->last_type_repp, eci_rep->parser_repp->last_counter_rep);
  
  if (eci_rep->commands_counter_rep >
      eci_rep->parser_repp->last_counter_rep) {
    fprintf(stderr, "%s", eci_str_sync_lost);
    eci_rep->parser_repp->sync_lost_rep = true;
  }
}

/** 
 * A specialized version of 'eci_command()' taking a double value
 * as the 2nd argument.
 */
void eci_command_float_arg(const char* command, double arg) { eci_command_float_arg_r(static_eci_rep, command, arg); }

/** 
 * A specialized version of 'eci_command()' taking a double value
 * as the 2nd argument.
 */
void eci_command_float_arg_r(eci_handle_t ptr, const char* command, double arg)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  snprintf(eci_rep->farg_buf_repp, ECI_MAX_FLOAT_BUF_SIZE-1, "%s %.32f", command, arg);
  eci_command_r(ptr, eci_rep->farg_buf_repp);
}

/* ---------------------------------------------------------------------
 * Getting return values 
 */

/**
 * Returns the number of strings returned by the 
 * last ECI command.
 */
int eci_last_string_list_count(void) { return(eci_last_string_list_count_r(static_eci_rep)); }

/**
 * Returns the number of strings returned by the 
 * last ECI command.
 */
int eci_last_string_list_count_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;
  struct eci_los_list* i;
  int count = 0;

  eci_impl_check_handle(eci_rep);

  for(i = eci_rep->parser_repp->last_los_repp; 
      i != NULL; 
      i = i->next_repp) {
    ++count;
  }

  return count;
}

/**
 * Returns the nth item of the list containing 
 * strings returned by the last ECI command.
 *
 * require:
 *  n >= 0 && n < eci_last_string_list_count()
 */
const char* eci_last_string_list_item(int n) { return(eci_last_string_list_item_r(static_eci_rep, n)); }

/**
 * Returns the nth item of the list containing 
 * strings returned by the last ECI command.
 *
 * require:
 *  n >= 0 && n < eci_last_string_list_count()
 */
const char* eci_last_string_list_item_r(eci_handle_t ptr, int n)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;
  struct eci_los_list* i;
  int count = 0;

  eci_impl_check_handle(eci_rep);

  for(i = eci_rep->parser_repp->last_los_repp;  
      i != NULL; 
      i = i->next_repp) {
    if (count++ == n) {
      return i->data_repp.d;
    }
  }

  return NULL;
}

const char* eci_last_string(void) { return(eci_last_string_r(static_eci_rep)); }

const char* eci_last_string_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  return eci_rep->parser_repp->last_s_repp.d;
}

double eci_last_float(void) { return(eci_last_float_r(static_eci_rep)); }

double eci_last_float_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  return eci_rep->parser_repp->last_f_rep;
}

int eci_last_integer(void) { return(eci_last_integer_r(static_eci_rep)); }

int eci_last_integer_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  return eci_rep->parser_repp->last_i_rep;
}

long int eci_last_long_integer(void) { return(eci_last_long_integer_r(static_eci_rep)); }

long int eci_last_long_integer_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  return eci_rep->parser_repp->last_li_rep;
}

/**
 * Returns pointer to a null-terminated string containing 
 * information about the last occured error.
 */
const char* eci_last_error(void) { return(eci_last_error_r(static_eci_rep)); }

/**
 * Returns pointer to a null-terminated string containing 
 * information about the last occured error.
 */
const char* eci_last_error_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);
  
  return eci_rep->parser_repp->last_error_repp.d;
}


const char* eci_last_type(void) { return(eci_last_type_r(static_eci_rep)); }

const char* eci_last_type_r(eci_handle_t ptr)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  eci_impl_check_handle(eci_rep);

  return eci_rep->parser_repp->last_type_repp;
}

/**
 * Whether an error has occured?
 *
 * @return zero if not in error state
 */
int eci_error(void) { return(eci_error_r(static_eci_rep)); }

/**
 * Whether an error has occured?
 *
 * @return zero if not in error state
 */
int eci_error_r(eci_handle_t ptr)
{ 
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;
  int res;

  eci_impl_check_handle(eci_rep);

  if (eci_ready_r(ptr) == 0) {
    ECI_DEBUG("(ecasoundc_sa) not ready, raising an error\n");
    return 1;
  }

  if (eci_rep->parser_repp->sync_lost_rep == true) {
    ECI_DEBUG("(ecasoundc_sa) sync lost, raising an error\n");
    return 1;
  }

  res = (eci_rep->parser_repp->last_type_repp[0] == 'e') ? 1 : 0;

  ECI_DEBUG_1("(ecasoundc_sa) checking for error, returning %d", res);

  return res;
}
 
/* --------------------------------------------------------------------- 
 * Events 
 */

int eci_events_available(void) { return(eci_events_available_r(static_eci_rep)); }
int eci_events_available_r(eci_handle_t ptr) { return(0); }
void eci_next_event(void) { eci_next_event_r(static_eci_rep); }
void eci_next_event_r(eci_handle_t ptr) { }
const char* eci_current_event(void) { return(eci_current_event_r(static_eci_rep)); }
const char* eci_current_event_r(eci_handle_t ptr) { return(0); }

/* --------------------------------------------------------------------- 
 * Implementation of static functions
 */

static void eci_string_add(eci_string *dst, int at, char const *src, int len);

/**
 * Clears the string contents.
 *
 * @post eci_string_len(str)==0
 * @post strlen(str->d)==0
 */
static void eci_string_clear(eci_string *str)
{
  DBC_CHECK(str);
  str->slen = 0;
  if (str->size == 0) 
    eci_string_add(str, 0, NULL, 0);
  else
    str->d[0] = 0;
  DBC_CHECK(str->d[0] == 0);
}

/**
 * Initializes the string object for use.
 * This must only be called after initial 
 * object allocation.
 */
static void eci_string_init(eci_string *str)
{
  DBC_CHECK(str);
  str->slen = 0;
  str->size = 0;
  str->d = 0;
}

static void eci_string_free(eci_string *str)
{
  DBC_CHECK(str);
  free(str->d);
  str->size = 0;
  str->slen = 0;
}

/**
 * Returns the string length.
 */
static int eci_string_len(eci_string *str)
{
  DBC_CHECK(str);
  return str->slen;
}

/**
 * Adds 'len' octets from buffer 'src' to the string
 * at position 'at' (position 0 being the first character).
 */
static void eci_string_add(eci_string *dst, int at, char const *src, int len)
{
  int space_needed = at + len + 1;

  DBC_CHECK(dst);

  if (space_needed > dst->size) {
    int newsize = 
      dst->size ? dst->size * 2 : ECI_INI_STRING_SIZE;
    char *newbuf;
    while (space_needed > newsize) {
      newsize *= 2;
    }
    assert(newsize <= ECI_MAX_DYN_ALLOC_SIZE);
    newbuf = realloc(dst->d, newsize);
    assert(newbuf);
    dst->size = newsize;
    dst->d = newbuf;
  }
  
  DBC_CHECK(space_needed <= dst->size);

  memcpy(&dst->d[at], src, len);
  dst->d[at + len] = 0;
}

static void eci_impl_check_handle(struct eci_internal* eci_rep)
{
  if (eci_rep == NULL) {
    fprintf(stderr, "%s", eci_str_null_handle);
    DBC_CHECK(eci_rep != NULL);
    exit(-1);
  }
}

static void eci_impl_free_parser(struct eci_internal* eci_rep)
{
  DBC_CHECK(eci_rep);
  eci_string_free(&eci_rep->parser_repp->last_error_repp);
  eci_string_free(&eci_rep->parser_repp->last_s_repp);
  eci_string_free(&eci_rep->parser_repp->buffer_rep);
  free(eci_rep->parser_repp);
  eci_rep->parser_repp = 0;
}

static void eci_impl_clean_last_values(struct eci_parser* parser)
{
  DBC_CHECK(parser != 0);

  eci_impl_los_list_clear(parser->last_los_repp);
  parser->last_los_repp = NULL;
  parser->last_i_rep = 0;
  parser->last_li_rep = 0;
  parser->last_f_rep = 0.0f;
  eci_string_clear(&parser->last_error_repp);
  eci_string_clear(&parser->last_s_repp);
}

static void eci_impl_dump_parser_state(eci_handle_t ptr, const char* message)
{
  struct eci_internal* eci_rep = (struct eci_internal*)ptr;

  fprintf(stderr, "\n(ecasoundc_sa) Error='%s', cmd='%s' last_error='%s' cmd_cnt=%d last_cnt=%d.\n", 
	  message,
	  eci_rep->last_command_repp,
	  eci_last_error_r(ptr),
	  eci_rep->commands_counter_rep,
	  eci_rep->parser_repp->last_counter_rep);
}


/**
 * Attempts to read up to 'count' bytes from file descriptor 'fd' 
 * into the buffer starting at 'buf'. If no data is available
 * for reading, up to 'timeout' milliseconds will be waited. 
 * A negative value means infinite timeout.
 */
static ssize_t eci_impl_fd_read(int fd, void *buf, size_t count, int timeout)
{
  int nfds = 1;
  struct pollfd ufds;
  ssize_t rescount = 0;
  int ret;

  ufds.fd = fd;
  ufds.events = POLLIN | POLLPRI;
  ufds.revents = 0;
  
  ret = poll(&ufds, nfds, timeout);
  if (ret > 0) {
    if (ufds.revents & POLLIN ||
	ufds.revents & POLLPRI) {
      rescount = read(fd, buf, count);
    }
  }
  else if (ret == 0) {
    /* timeout */
    rescount = -1;
  }
  return rescount;
}

static const char* eci_impl_get_ecasound_path(void)
{
  const char* result = getenv("ECASOUND");

  if (result == NULL) {
    fprintf(stderr, "%s", eci_str_no_ecasound_env);
    result = "ecasound";
  }

  return result;
}


static struct eci_los_list *eci_impl_los_list_add_item(struct eci_los_list* head, char* stmp, int len)
{
  struct eci_los_list* i = head;
  struct eci_los_list* last = NULL;
  
  /* find end of list */
  while(i != NULL) {
    last = i;
    i = i->next_repp;
  }

  /* add to the end, copy data */
  i = eci_impl_los_list_alloc_item();
  eci_string_add(&i->data_repp, 0, stmp, len);
  if (last != NULL) last->next_repp = i;
  
  /* ECI_DEBUG_3("(ecasoundc_sa) adding item '%s' to los list; head=%p, i=%p\n", stmp, (void*)head, (void*)i); */

  /* created a new list, return the new item */
  if (head == NULL) 
    return i;

  /* return the old head */
  return head;
}

struct eci_los_list *eci_impl_los_list_alloc_item(void)
{
  struct eci_los_list *item;
  /* ECI_DEBUG("(ecasoundc_sa) list alloc item\n"); */
  item = (struct eci_los_list*)calloc(1, sizeof(struct eci_los_list));
  DBC_CHECK(item != NULL);
  item->next_repp = item->prev_repp = NULL;
  eci_string_clear(&item->data_repp);

  return item;
}

static void eci_impl_los_list_clear(struct eci_los_list *ptr)
{
  struct eci_los_list *i = ptr;

  ECI_DEBUG_1("(ecasoundc_sa) clearing list, i=%p\n", (void*)i);

  while(i != NULL) {
    /* ECI_DEBUG_1("(ecasoundc_sa) freeing list item %p\n", (void*)i); */
    struct eci_los_list* next = i->next_repp;
    eci_string_free(&i->data_repp);
    free(i);
    i = next;
  }
}

static void eci_impl_read_return_value(struct eci_internal* eci_rep, int timeout)
{
  char* raw_buffer = eci_rep->raw_buffer_repp;
  int attempts = 0;

  DBC_CHECK(eci_rep->commands_counter_rep >=
	    eci_rep->parser_repp->last_counter_rep);

  while(attempts < ECI_MAX_RESYNC_ATTEMPTS) {
    int res = eci_impl_fd_read(eci_rep->cmd_read_fd_rep, raw_buffer, ECI_PARSER_BUF_SIZE-1, timeout);
    if (res > 0) {
      int n;

      raw_buffer[res] = 0;
      /* ECI_DEBUG_2("\n(ecasoundc_sa) read %u bytes:\n--cut--\n%s\n--cut--\n", res, raw_buffer); */

      for(n = 0; n < res; n++) {
	/* int old = eci_rep->parser_repp->state_rep; */
	eci_impl_update_state(eci_rep->parser_repp, raw_buffer[n]);
	/* if (old != eci_rep->parser_repp->state_rep) ECI_DEBUG_3("state change %d-%d, c=[%02X].\n", old, eci_rep->parser_repp->state_rep, raw_buffer[n]); */
      }

      if (eci_rep->commands_counter_rep ==
	  eci_rep->parser_repp->last_counter_rep) break;

      /* read return values until the correct one is found */
    }
    else {
      if (res < 0) {
	ECI_DEBUG_1("(ecasoundc_sa) timeout when reading return values (attempts=%d)!\n", attempts);
	eci_rep->parser_repp->sync_lost_rep = true;
	break;
      }
    }
    ++attempts;
  }

  if (eci_rep->commands_counter_rep !=
      eci_rep->parser_repp->last_counter_rep) {
    eci_impl_dump_parser_state(eci_rep, "read() error");
    eci_rep->parser_repp->sync_lost_rep = true;
  }
}

/**
 * Sets the last 'list of strings' values.
 *
 * @pre parser != 0
 * @pre parser->state_rep == ECI_STATE_COMMON_LF_3
 */
static void eci_impl_set_last_los_value(struct eci_parser* parser)
{
  struct eci_los_list* i = parser->last_los_repp;
  int quoteflag = 0, m = 0, n;
  eci_string stmp;
  eci_string_init(&stmp);

  DBC_CHECK(parser != 0);
  DBC_CHECK(parser->state_rep == ECI_STATE_COMMON_LF_3);

  ECI_DEBUG_2("(ecasoundc_sa) parsing a list '%s' (count=%d)\n", parser->buffer_rep.d, parser->buffer_current_rep);

  eci_impl_los_list_clear(i);
  parser->last_los_repp = NULL;

  for(n = 0; n < parser->buffer_current_rep && n < parser->msgsize_rep; n++) {
    char c = parser->buffer_rep.d[n];

    if (c == '\"') {
      quoteflag = !quoteflag;
    }
    else if (c == '\\') {
      n++;
      eci_string_add(&stmp, m++, &parser->buffer_rep.d[n], 1);
    }
    else if (c != ',' || quoteflag == 1) {
      eci_string_add(&stmp, m++, &parser->buffer_rep.d[n], 1);
    }
    else {
      if (m == 0) continue;
      i = eci_impl_los_list_add_item(i, stmp.d, m);
      m = 0;
    }
  }
  if (m > 0) {
    i = eci_impl_los_list_add_item(i, stmp.d, m);
  }

  parser->last_los_repp = i;

  eci_string_free(&stmp);
}

/**
 * Sets the 'last value' fields in the given 'parser'
 * object.
 *
 * @pre parser != 0
 * @pre parser->state_rep == ECI_STATE_COMMON_LF_3
 */
static void eci_impl_set_last_values(struct eci_parser* parser)
{
  DBC_CHECK(parser != 0);
  DBC_CHECK(parser->state_rep == ECI_STATE_COMMON_LF_3);

  switch(parser->last_type_repp[0])
    {
    case 's':
      eci_string_add(&parser->last_s_repp, 0, parser->buffer_rep.d, parser->buffer_current_rep);
      break;

    case 'S':
      eci_impl_set_last_los_value(parser);
      break;

    case 'i':
      parser->last_i_rep = atoi(parser->buffer_rep.d);
      break;

    case 'l':
      parser->last_li_rep = atol(parser->buffer_rep.d);
      break;

    case 'f':
      parser->last_f_rep = atof(parser->buffer_rep.d);
      break;

    case 'e':
      eci_string_add(&parser->last_error_repp, 0, parser->buffer_rep.d, parser->buffer_current_rep);
      break;

    default: {}

    }
}

static void eci_impl_update_state(struct eci_parser* parser, char c)
{
  switch(parser->state_rep)
    {
    case ECI_STATE_INIT:
      if (c >= 0x30 && c <= 0x39) {
	parser->token_phase_rep = ECI_TOKEN_PHASE_READING;
	parser->buffer_current_rep = 0;
	eci_string_clear(&parser->buffer_rep);
	parser->state_rep = ECI_STATE_LOGLEVEL;
      }
      else {
	parser->token_phase_rep = ECI_TOKEN_PHASE_NONE;
      }
      break;

    case ECI_STATE_LOGLEVEL:
      if (c == ' ') {
	parser->loglevel_rep = atoi(parser->buffer_rep.d);

	if (parser->loglevel_rep == ECI_RETURN_TYPE_LOGLEVEL) {
	  /* ECI_DEBUG_3("\n(ecasoundc_sa) found rettype loglevel '%s' (i=%d,len=%d).\n", parser->buffer_repp, parser->loglevel_rep, parser->buffer_current_rep); */
	  parser->state_msg_rep = ECI_STATE_MSG_RETURN;
	}
	else {
	  /* ECI_DEBUG_3("\n(ecasoundc_sa) found loglevel '%s' (i=%d,parser->buffer_current_rep=%d).\n", buf, parser->loglevel_rep, parser->buffer_current_rep); */
	  parser->state_msg_rep = ECI_STATE_MSG_GEN;
	}
	  
	parser->state_rep = ECI_STATE_MSGSIZE;
	parser->token_phase_rep =  ECI_TOKEN_PHASE_NONE;
      }
      else if (c < 0x30 && c > 0x39) {
	parser->state_rep = ECI_STATE_SEEK_TO_LF;
      }

      break;

    case ECI_STATE_MSGSIZE:
      if ((c == ' ' && parser->state_msg_rep == ECI_STATE_MSG_RETURN) ||
	  (c == 0x0d && parser->state_msg_rep == ECI_STATE_MSG_GEN)) {

	parser->msgsize_rep = atoi(parser->buffer_rep.d);

	/* ECI_DEBUG_3("(ecasoundc_sa) found msgsize '%s' (i=%d,len=%d).\n", parser->buffer_repp, parser->msgsize_rep, parser->buffer_current_rep); */

	if (parser->state_msg_rep == ECI_STATE_MSG_GEN) {
	  parser->state_rep = ECI_STATE_COMMON_LF_1;
	}
	else {
	  parser->state_rep = ECI_STATE_RET_TYPE;
	}

	parser->token_phase_rep =  ECI_TOKEN_PHASE_NONE;
  
      }
      else if (c < 0x30 && c > 0x39) {
	parser->state_rep = ECI_STATE_SEEK_TO_LF;
      }
      else if (parser->token_phase_rep == ECI_TOKEN_PHASE_NONE) {
	parser->token_phase_rep =  ECI_TOKEN_PHASE_READING;
	parser->buffer_current_rep = 0;
	eci_string_clear(&parser->buffer_rep);
      }
      break;

    case ECI_STATE_COMMON_CR_1: 
      if (c == 0x0d) 
	parser->state_rep = ECI_STATE_COMMON_LF_1;
      else
	parser->state_rep = ECI_STATE_INIT;
      break;

    case ECI_STATE_COMMON_LF_1:
      if (c == 0x0a) {
	parser->state_rep = ECI_STATE_COMMON_CONTENT;
      }
      else
	parser->state_rep = ECI_STATE_INIT;
      break;

    case ECI_STATE_RET_TYPE:
      if (c == 0x0d) {
	/* parse return type */
	/* set 'parser->last_type_repp' */
	int len = (parser->buffer_current_rep < ECI_MAX_RETURN_TYPE_SIZE) ? parser->buffer_current_rep : (ECI_MAX_RETURN_TYPE_SIZE - 1);

	memcpy(parser->last_type_repp, parser->buffer_rep.d, len);
	parser->last_type_repp[len] = 0;
	
	ECI_DEBUG_2("(ecasoundc_sa) found rettype '%s' (len=%d)\n", parser->last_type_repp, parser->buffer_current_rep);

	parser->state_rep = ECI_STATE_COMMON_LF_1;
	parser->token_phase_rep =  ECI_TOKEN_PHASE_NONE;

      }
      else if (parser->token_phase_rep == ECI_TOKEN_PHASE_NONE) {
	parser->token_phase_rep =  ECI_TOKEN_PHASE_READING;
	parser->buffer_current_rep = 0;
	eci_string_clear(&parser->buffer_rep);
      }

      break;

    case ECI_STATE_COMMON_CONTENT:
      if (c == 0x0d) {
	/* parse return type */
	/* set 'parser->last_xxx_yyy' */

	/* handle empty content */
	if (parser->msgsize_rep == 0) 
	  eci_string_clear(&parser->buffer_rep);

	ECI_DEBUG_2("(ecasoundc_sa) found content, loglevel=%d, msgsize=%d", parser->loglevel_rep, parser->msgsize_rep);
	if (parser->state_msg_rep == ECI_STATE_MSG_GEN)
	  ECI_DEBUG(".\n");
	else
	  ECI_DEBUG_1(" type='%s'.\n", parser->last_type_repp);

	parser->state_rep = ECI_STATE_COMMON_LF_2;
	parser->token_phase_rep =  ECI_TOKEN_PHASE_VALIDATE;

      }
      else if (parser->token_phase_rep == ECI_TOKEN_PHASE_NONE) {
	parser->token_phase_rep = ECI_TOKEN_PHASE_READING;
	parser->buffer_current_rep = 0;
	eci_string_clear(&parser->buffer_rep);
      }
      break;

    case ECI_STATE_COMMON_CR_2:
      if (c == 0x0d)
	parser->state_rep = ECI_STATE_COMMON_LF_2; 
      else
	parser->state_rep = ECI_STATE_COMMON_CONTENT;
      break;
	
    case ECI_STATE_COMMON_LF_2:
      if (c == 0x0a)
	parser->state_rep = ECI_STATE_COMMON_CR_3; 
      else
	parser->state_rep = ECI_STATE_COMMON_CONTENT;
      break;

    case ECI_STATE_COMMON_CR_3:
      if (c == 0x0d)
	parser->state_rep = ECI_STATE_COMMON_LF_3; 
      else
	parser->state_rep = ECI_STATE_COMMON_CONTENT;
      break;

    case ECI_STATE_COMMON_LF_3:
      if (c == 0x0a) {
	if (parser->state_msg_rep == ECI_STATE_MSG_RETURN) {
	  ECI_DEBUG_1("(ecasoundc_sa) rettype-content validated: <<<%s>>>\n", parser->buffer_rep.d);
	  eci_impl_set_last_values(parser);
	  parser->last_counter_rep++;
	}
	else {
	  ECI_DEBUG_1("(ecasoundc_sa) gen-content validated: <<<%s>>>\n", parser->buffer_rep.d);
	}
	parser->state_rep = ECI_STATE_INIT; 
      }
      else
	parser->state_rep = ECI_STATE_COMMON_CONTENT;
      break;

    case ECI_STATE_SEEK_TO_LF: 
      if (c == 0x0a) {
	parser->token_phase_rep = ECI_TOKEN_PHASE_NONE;
	parser->state_rep = ECI_STATE_INIT;
      }
      break;

    default: {}

    } /* end of switch() */

  if (parser->token_phase_rep == ECI_TOKEN_PHASE_READING) {
    eci_string_add(&parser->buffer_rep, parser->buffer_current_rep, &c, 1);
    ++parser->buffer_current_rep;
  }

  //ECI_DEBUG_2("(ecasoundc_sa) parser buf contents: '%s' (cur=%d)\n.", parser->buffer_rep.d, parser->buffer_current_rep);
}
