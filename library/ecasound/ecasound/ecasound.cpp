// ------------------------------------------------------------------------
// ecasound.cpp: Console mode user interface to ecasound.
// Copyright (C) 2002-2010 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3 (see Ecasound Programmer's Guide)
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#include <cstdio>
#include <cstdlib>
#include <iostream>
#include <string>
#include <vector>

#include <signal.h>    /* POSIX: various signal functions */
#include <unistd.h>    /* POSIX: sleep() */

#include <kvu_dbc.h>
#include <kvu_com_line.h>
#include <kvu_utils.h>

#include <eca-control-main.h>
#include <eca-control-mt.h>
#include <eca-error.h>
#include <eca-logger.h>
#include <eca-logger-default.h>
#include <eca-session.h>
#include <eca-version.h>

#ifdef ECA_USE_LIBLO
#include <eca-osc.h>
#endif

#include "eca-comhelp.h"
#include "eca-console.h"
#include "eca-curses.h"
#include "eca-neteci-server.h"
#include "eca-plaintext.h"
#include "textdebug.h"
#include "ecasound.h"

/**
 * Check build time reqs
 */
#undef SIGNALS_CAN_BE_BLOCKED
#if defined(HAVE_SIGWAIT)
#  if defined(HAVE_SIGPROCMASK) || defined(HAVE_PTHREAD_SIGMASK)
#    define SIGNALS_CAN_BE_BLOCKED
#  else
#    error "Either pthread_sigmask() or sigprocmask() needs to be supported."
#    error "One option to try is to undefine HAVE_SIGWAIT and retry compilation "
#    error "with plain pause() support."
#  endif
#elif defined(HAVE_PAUSE)
   /* no op*/
#else
#  error ""
#endif

/**
 * Static/private function definitions
 */

static void ecasound_create_eca_objects(ECASOUND_RUN_STATE* state, COMMAND_LINE& cline);
static void ecasound_launch_neteci(ECASOUND_RUN_STATE* state);
static void ecasound_launch_osc(ECASOUND_RUN_STATE* state);
static int ecasound_pass_at_launch_commands(ECASOUND_RUN_STATE* state);
static void ecasound_main_loop_interactive(ECASOUND_RUN_STATE* state);
static void ecasound_main_loop_batch(ECASOUND_RUN_STATE* state);
void ecasound_parse_command_line(ECASOUND_RUN_STATE* state, 
				 const COMMAND_LINE& clinein,
				 COMMAND_LINE* clineout);
static void ecasound_print_usage(void);
static void ecasound_print_version_banner(void);
static void ecasound_signal_setup(ECASOUND_RUN_STATE* state);
static void ecasound_signal_unblock(ECASOUND_RUN_STATE* state);

extern "C" {
  static void* ecasound_watchdog_thread(void* arg); 
  static void ecasound_signal_handler(int signal);
}

/* Define to get exit debug traces */
// #define ENABLE_ECASOUND_EXIT_PROCESS_TRACES 1

/* Exit process has not yet started. */
#define ECASOUND_EXIT_PHASE_NONE           0

/* Termination will block until signal watchdog thread
 * has exited. */
#define ECASOUND_EXIT_PHASE_WAIT_FOR_WD    1

/* Watchdog has terminated, only a single thread
 * is left running. */
#define ECASOUND_EXIT_PHASE_ONE_THREAD     2

/* All resources freed, about to return from main(). */
#define ECASOUND_EXIT_PHASE_DONE           3

/**
 * Global variable that is set to one of 'ECASOUND_EXIT_PHASE_*' 
 * 
 * When ecasound's main()
 * function is about to exit (all engine threads have
 * been terminated and only single thread aside the 
 * watchdog is left, and to '2' when all running state 
 * has been clearer in ~ECASOUND_RUN_STATE().
 */
static sig_atomic_t glovar_ecasound_exit_phase = ECASOUND_EXIT_PHASE_NONE;

/* Global variable that is set to one, when common
 * POSIX signals are blocked and watchdog thread is
 * blocking on a call to pthread_sigmask(), sigprocmask(),
 * or pause(). */
static sig_atomic_t glovar_wd_signals_blocked = 0;

/* Global variable counting how many SIGINT signals
 * have been ignored. */
static sig_atomic_t glovar_soft_signals_ignored = 0;

/* Debuggin macros, see ENABLE_ECASOUND_EXIT_PROCESS_TRACES 
 * above */
#ifdef ENABLE_ECASOUND_EXIT_PROCESS_TRACES
#define TRACE_EXIT(x) do { x; } while(0)
#else
#define TRACE_EXIT(x) while(0) { x; }
#endif

/**
 * Namespace imports
 */
using namespace std;

ECASOUND_RUN_STATE::ECASOUND_RUN_STATE(void)
  : console(0),
    control(0),
    logger(0),
    eciserver(0),
    osc(0),
    session(0),
    launchcmds(0),
    neteci_thread(0),
    watchdog_thread(0),
    lock(0),
    exit_request(0),
    signalset(0),
    retval(ECASOUND_RETVAL_SUCCESS),
    neteci_mode(false),
    neteci_tcp_port(2868),
    osc_mode(false),
    osc_udp_port(-1),
    keep_running_mode(false),
    cerr_output_only_mode(false),
    interactive_mode(false),
    quiet_mode(false)
{
}

ECASOUND_RUN_STATE::~ECASOUND_RUN_STATE(void)
{
  /* note: global variable */
  DBC_CHECK(glovar_ecasound_exit_phase == ECASOUND_EXIT_PHASE_ONE_THREAD);

  if (control != 0) { delete control; control = 0; }
  if (session != 0) { delete session; session = 0; }

  if (launchcmds != 0) { delete launchcmds; launchcmds = 0; }
  if (eciserver != 0) { delete eciserver; eciserver = 0; }
#ifdef ECA_USE_LIBLO
  if (osc != 0) { delete osc; osc = 0; }
#endif
  if (console != 0) { delete console; console = 0; }
  if (neteci_thread != 0) { delete neteci_thread; neteci_thread = 0; }
  if (watchdog_thread != 0) { delete watchdog_thread; watchdog_thread = 0; }
  if (lock != 0) { delete lock; lock = 0; }
  if (signalset != 0) { delete signalset; signalset = 0; }

  glovar_ecasound_exit_phase = ECASOUND_EXIT_PHASE_DONE;
}

/**
 * Function definitions
 */
int main(int argc, char *argv[])
{
  ECASOUND_RUN_STATE state;
  bool have_curses =
#if defined(ECA_PLATFORM_CURSES) 
    true
#else
    false
#endif
    ;

  /* 1. setup signals and the signal watchdog thread */
  ecasound_signal_setup(&state);

  /* 2. parse command-line args */
  COMMAND_LINE* cline = new COMMAND_LINE(argc, argv);
  COMMAND_LINE* clineout = new COMMAND_LINE();
  ecasound_parse_command_line(&state, *cline, clineout); 
  delete cline; cline = 0;

  /* 3. create console interface */
  if (state.retval == ECASOUND_RETVAL_SUCCESS) {

    if (have_curses == true &&
	state.quiet_mode != true &&
	state.cerr_output_only_mode != true) {
      state.console = new ECA_CURSES();
      state.logger = new TEXTDEBUG();
    }
    else {
      ostream* ostr = (state.cerr_output_only_mode == true) ? &cerr : &cout;
      state.console = new ECA_PLAIN_TEXT(ostr);
      state.logger = new ECA_LOGGER_DEFAULT(*ostr);
    }
    ECA_LOGGER::attach_logger(state.logger);
    
    if (state.quiet_mode != true) {
      /* 4. print banner */
      state.console->print_banner();
    }

    /* 5. set default debug levels */
    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::errors, true);
    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::info, true);
    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::subsystems, true);
    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::eiam_return_values, true);
    ECA_LOGGER::instance().set_log_level(ECA_LOGGER::module_names, true);
    
    /* 6. create eca objects */
    ecasound_create_eca_objects(&state, *clineout);
    delete clineout; clineout = 0;

    /* 7. enable remote control over socket connection  */
    if (state.retval == ECASOUND_RETVAL_SUCCESS) {
      /* 7.a) ... ECI over socket connection */
      if (state.neteci_mode == true) {
	ecasound_launch_neteci(&state);
      }
      /* 7.b) ... over OSC */
      if (state.osc_mode == true) {
	ecasound_launch_osc(&state);
      }
    }

    /* 8. pass launch commands */
    ecasound_pass_at_launch_commands(&state);

    /* 9. start processing */
    if (state.retval == ECASOUND_RETVAL_SUCCESS) {
      if (state.interactive_mode == true)
	ecasound_main_loop_interactive(&state);
      else
	ecasound_main_loop_batch(&state);
    }
  }

  TRACE_EXIT(cerr << endl << "ecasound: out of mainloop..." << endl);

  /* step: terminate neteci thread */
  if (state.neteci_mode == true) {
    /* wait until the NetECI thread has exited */
    state.exit_request = 1;
    if (state.neteci_thread)
      pthread_join(*state.neteci_thread, NULL);
  }

  /* step: terminate the engine thread */
  if (state.control != 0) {
    if (state.control->is_running() == true) {
      state.control->stop_on_condition();
    }
    if (state.control->is_connected() == true) {
      state.control->disconnect_chainsetup();
    }
  }

  glovar_ecasound_exit_phase = ECASOUND_EXIT_PHASE_WAIT_FOR_WD;

  TRACE_EXIT(cerr << endl << "ecasound: joining watchdog..." << endl);

  /* step: Send a signal to the watchdog thread to wake it uP */
  pthread_kill(*state.watchdog_thread, SIGHUP);

  /* step: Unblock signals for the main thread as well. At this
   *       point the engine threads have been already terminated,
   *       so we don't have to anymore worry about which thread
   *       gets the signals. */
  ecasound_signal_unblock(&state);

  pthread_join(*state.watchdog_thread, NULL);

  TRACE_EXIT(cerr << endl << "ecasound: joined watchdog..." << endl);

  glovar_ecasound_exit_phase = ECASOUND_EXIT_PHASE_ONE_THREAD;

  DBC_CHECK(state.retval == ECASOUND_RETVAL_SUCCESS ||
	    state.retval == ECASOUND_RETVAL_INIT_FAILURE ||
	    state.retval == ECASOUND_RETVAL_START_ERROR ||
	    state.retval == ECASOUND_RETVAL_RUNTIME_ERROR);

  TRACE_EXIT(cerr << endl << "ecasound: main() exiting with code " << state.retval << endl);

  return state.retval;
}

/**
 * Enters the main processing loop.
 */
void ecasound_create_eca_objects(ECASOUND_RUN_STATE* state, 
				 COMMAND_LINE& cline)
{
  DBC_REQUIRE(state != 0);
  DBC_REQUIRE(state->console != 0);

  try {
    state->session = new ECA_SESSION(cline);
    state->control = new ECA_CONTROL_MT(state->session);

    DBC_ENSURE(state->session != 0);
    DBC_ENSURE(state->control != 0);
  }
  catch(ECA_ERROR& e) {
    state->console->print("---\necasound: ERROR: [" + e.error_section() + "] : \"" + e.error_message() + "\"\n");
    state->retval = ECASOUND_RETVAL_INIT_FAILURE;
  }
}

/**
 * Launches a background thread that allows NetECI 
 * clients to connect to the current ecasound
 * session.
 */
void ecasound_launch_neteci(ECASOUND_RUN_STATE* state)
{
  DBC_REQUIRE(state != 0);
  // DBC_REQUIRE(state->console != 0);

  // state->console->print("ecasound: starting the NetECI server.");

  state->neteci_thread = new pthread_t;
  state->lock = new pthread_mutex_t;
  pthread_mutex_init(state->lock, NULL);
  state->eciserver = new ECA_NETECI_SERVER(state);

  int res = pthread_create(state->neteci_thread, 
			   NULL,
			   ECA_NETECI_SERVER::launch_server_thread, 
			   reinterpret_cast<void*>(state->eciserver));
  if (res != 0) {
    cerr << "ecasound: Warning! Unable to create a thread for control over socket connection (NetECI)." << endl;
    delete state->neteci_thread;  state->neteci_thread = 0;
    delete state->lock;  state->lock = 0;
    delete state->eciserver; state->eciserver = 0;
  }

  // state->console->print("ecasound: NetECI server started");
}

/**
 * Sets up and activates Ecasound OSC interface
 */
void ecasound_launch_osc(ECASOUND_RUN_STATE* state)
{
#ifdef ECA_USE_LIBLO
  DBC_REQUIRE(state != 0);
  state->osc = new ECA_OSC_INTERFACE (state->control, state->osc_udp_port);
  if (state->osc)
    state->osc->start();
#endif
}

static int ecasound_pass_at_launch_commands(ECASOUND_RUN_STATE* state)
{
  if (state->launchcmds) {
    std::vector<std::string>::const_iterator p = state->launchcmds->begin();

    while(p != state->launchcmds->end()) {
      struct eci_return_value retval;
      state->control->command(*p, &retval);
      state->control->print_last_value(&retval);
      ++p;
    }
  }

  return 0;
}

static void ecasound_check_for_quit(ECASOUND_RUN_STATE* state, const string& cmd)
{
  if (cmd == "quit" || cmd == "q") {
    state->console->print("---\necasound: Exiting...");
    state->exit_request = 1;
    ECA_LOGGER::instance().flush();
  }
}

/**
 * The main processing loop for interactive use.
 */
void ecasound_main_loop_interactive(ECASOUND_RUN_STATE* state)
{
  DBC_REQUIRE(state != 0);
  DBC_REQUIRE(state->console != 0);

  ECA_CONTROL_MAIN* ctrl = state->control;

  while(state->exit_request == 0) {
    state->console->read_command("ecasound ('h' for help)> ");
    const string& cmd = state->console->last_command();
    if (cmd.size() > 0 && state->exit_request == 0) {
      
      struct eci_return_value retval;
      ctrl->command(cmd, &retval);
      ctrl->print_last_value(&retval);

      ecasound_check_for_quit(state, cmd);
    }
  }
}

/**
 * The main processing loop for noninteractive use.
 */
void ecasound_main_loop_batch(ECASOUND_RUN_STATE* state)
{
  DBC_REQUIRE(state != 0);
  DBC_REQUIRE(state->console != 0);

  ECA_CONTROL_MAIN* ctrl = state->control;

  struct eci_return_value connect_retval;

  if (ctrl->is_selected() == true && 
      ctrl->is_valid() == true) {
    ctrl->connect_chainsetup(&connect_retval);
  }
  
  if (state->neteci_mode != true &&
      state->osc_mode != true) {

    /* case: 2.1: non-interactive, neither NetECI or OSC is used,
     *            so this thread can use 'ctrl' exclusively */
    
    if (ctrl->is_connected() == true) {
      if (!state->exit_request) {
	int res = ctrl->run(!state->keep_running_mode);
	if (res < 0) {
	  state->retval = ECASOUND_RETVAL_RUNTIME_ERROR;
	  cerr << "ecasound: Warning! Errors detected during processing." << endl;
	}
      }
    }
    else {
      ctrl->print_last_value(&connect_retval);
      state->retval = ECASOUND_RETVAL_START_ERROR;
    }
  }
  else {
    
      /* case: 2.2: non-interactive, NetECI active 
       *
       *             (special handling is needed as NetECI needs
       *             to submit atomic bundles of ECI commands and thus
       *             needs to be able to lock the ECA_CONTROL object
       *             for itself) */
    
    int res = -1;
    
    if (ctrl->is_connected() == true) {
      res = ctrl->start();
    }
    
    /* note: if keep_running_mode is enabled, we do not 
     *       exit even if there are errors during startup */
    if (state->keep_running_mode != true &&
	res < 0) {
      state->retval = ECASOUND_RETVAL_START_ERROR;
      state->exit_request = 1;
    }

    while(state->exit_request == 0) {
      
      if (state->keep_running_mode != true &&
	  ctrl->is_finished() == true)
	break;
      
      /* note: sleep for one second and let the NetECI thread
       *       access the ECA_CONTROL object for a while */
      kvu_sleep(1, 0);
    }
    
    ecasound_check_for_quit(state, "quit");
  }
  // cerr << endl << "ecasound: mainloop exiting..." << endl;
}

/**
 * Parses the command lines options in 'cline'.
 */
void ecasound_parse_command_line(ECASOUND_RUN_STATE* state, 
				 const COMMAND_LINE& cline,
				 COMMAND_LINE* clineout)
{
  if (cline.size() < 2) {
    ecasound_print_usage();
    state->retval = ECASOUND_RETVAL_INIT_FAILURE;
  }
  else {
   cline.begin();
    while(cline.end() != true) {

      if (cline.current() == "-o:stdout" ||
	  cline.current() == "stdout") {
	state->cerr_output_only_mode = true;
	/* pass option to libecasound */
	clineout->push_back(cline.current());
      }

      else if (cline.current() == "-d:0" ||
	       cline.current() == "-q") {
	state->quiet_mode = true;
	/* pass option to libecasound */
	clineout->push_back(cline.current());
      } 

      else if (cline.current() == "-c") {
	state->interactive_mode = true;
      }

      else if (cline.current() == "-C") {
	state->interactive_mode = false;
      }

      else if (cline.current() == "-D") {
	state->cerr_output_only_mode = true;
      }
      
      else if (cline.current() == "--server" ||
	       cline.current() == "--daemon") {
	/* note: --daemon* deprecated as of 2.6.0 */
	state->neteci_mode = true;
      }

      else if (cline.current().compare(0, 2, "-E") == 0) {
	cline.next();
	if (cline.end() != true) {
	  state->launchcmds = 
	    new std::vector<std::string>
 	      (kvu_string_to_vector(cline.current(), ';'));
	}
      }

      else if (cline.current().find("--server-tcp-port") != string::npos ||
	       cline.current().find("--daemon-port") != string::npos) {
	std::vector<std::string> argpair = 
	  kvu_string_to_vector(cline.current(), '=');
	if (argpair.size() > 1) {
	  /* --server-tcp-port=XXXX */
	  /* note: --daemon* deprecated as of 2.6.0 */
	  state->neteci_tcp_port = atoi(argpair[1].c_str());
	}
      }

      else if (cline.current() == "--no-server" ||
	       cline.current() == "--nodaemon") {
	/* note: --daemon deprecated as of 2.6.0 */
	state->neteci_mode = false;
      }

      else if (cline.current().find("--osc-udp-port") != string::npos) {
	std::vector<std::string> argpair = 
	  kvu_string_to_vector(cline.current(), '=');
	if (argpair.size() > 1) {
	  /* --osc-udp-port=XXXX */
	  state->osc_udp_port = atoi(argpair[1].c_str());
	  fprintf(stdout,
		  "set UDP port based on %s to %d.\n", cline.current().c_str(), state->osc_udp_port);
	}
#ifdef ECA_USE_LIBLO
	state->osc_mode = true;
#else
	state->osc_mode = false;
	cerr << "ERROR: ecasound was built without OSC support" << endl;
#endif

      }

      else if (cline.current() == "-h" ||
	       cline.current() == "--help") {
	ecasound_print_usage();
	state->retval = ECASOUND_RETVAL_INIT_FAILURE;
	break;
      }

      else if (cline.current() == "-K" ||
	       cline.current() == "--keep-running") {
	state->keep_running_mode = true;
      }

      else if (cline.current() == "--version") {
	ecasound_print_version_banner();
	state->retval = ECASOUND_RETVAL_INIT_FAILURE;
	break;
      }
      
      else {
	/* pass rest of the options to libecasound */
	clineout->push_back(cline.current());
      }

      cline.next();
    }
  }
}

void ecasound_print_usage(void)
{
  cout << ecasound_parameter_help();
}

void ecasound_print_version_banner(void)
{
  cout << "ecasound v" << ecasound_library_version << endl;
  cout << "Copyright (C) 1997-2010 Kai Vehmanen and others." << endl;
  cout << "Ecasound comes with ABSOLUTELY NO WARRANTY." << endl;
  cout << "You may redistribute copies of ecasound under the terms of the GNU" << endl;
  cout << "General Public License. For more information about these matters, see" << endl; 
  cout << "the file named COPYING." << endl;
}

static void ecasound_signal_handler(int signal)
{
  /* note: If either sigprocmask() or pthread_sigmask() 
   *       is available, all relevant signals should be blocked
   *       and this handler should be never called until the final
   *       phase (ECASOUND_EXIT_PHASE_WAIT_FOR_WD) of the process 
   *       termination starts.
   */

#if defined(SIGNALS_CAN_BE_BLOCKED)

  if (glovar_wd_signals_blocked) {
    TRACE_EXIT(cerr << "WARNING: ecasound_signal_handler entered, this should _NOT_ happen!";
	       cerr << " pid=" << getpid() << endl);
  }

#else /* !SIGNALS_CAN_BE_BLOCKED */

 {
   static int ignored = 0;

   TRACE_EXIT(cerr << "ecasound_signal_handler, built with pause(), ignore count " 
	      << ignored << endl);

   /* note: When signal blocking is not possible, ignore
    *       the first two signal (1st is the user sent signal, 
    *       and second is the SIGHUP from ecasound main(). If
    *       we receive a third one, only then it's time for
    *       the emergency exit. */

   if (++ignored <= 2) {
     return;
   }
 }

#endif

  TRACE_EXIT(cerr << "Signal " 
	     << signal 
	     << " received in exit phase "
	     << glovar_ecasound_exit_phase << endl);

  /* note: In ECASOUND_EXIT_PHASE_WAIT_FOR_WD, the main() thread
   *       will send a signal to the watchdog and we need to 
   *       ignore this properly (as it's an internally generated
   *       signal, not sent from an external source. */

  if (glovar_ecasound_exit_phase == ECASOUND_EXIT_PHASE_NONE ||
      glovar_ecasound_exit_phase == ECASOUND_EXIT_PHASE_ONE_THREAD ||
      glovar_ecasound_exit_phase == ECASOUND_EXIT_PHASE_DONE) {

    if (signal == SIGINT && 
	glovar_soft_signals_ignored == 0) {
      cerr << endl
	   << "NOTICE: SIGINT (ctrl-c) was received while terminating ecasound. If\n"
	   << "        another signal is received, the normal cleanup procedure will\n"
	   << "        be skipped and process will terminate immediately.\n";
      glovar_soft_signals_ignored = 1;
      return;
    }

    /* note: user needs to see this, not using TRACE_EXIT() macro */
    cerr << endl
	 << "WARNING: Signal was received while terminating ecasound, so exiting immediately!\n"
	 << "         Normal exit process is skipped, which may have some side-effects\n"
	 << "         (e.g. file header information not updated).\n";


    /* step: Make sure the watchdog is woken up (a hack, but it seems
     *       exit() can in some cases be blocked when watchdog is
     *       still in sigwait() at this point. */
    if (signal != SIGHUP)
      kill(0, SIGHUP);
    
    exit(ECASOUND_RETVAL_CLEANUP_ERROR);
  }
}

/**
 * Sets up a signal mask with sigaction() that blocks 
 * all common signals, and then launces a signal watchdog
 * thread that waits on the blocked signals using
 * sigwait(). 
 * 
 * This design causes all non-fatal termination signals
 * to be routed through a single thread. This signal watchdog
 * in turn performs a clean exit upon receiving a signal.
 * Without this setup, interactions between threads when handling 
 * would be harder to control (especially considering that
 * ecasound needs to work on various different platforms).
 */
void ecasound_signal_setup(ECASOUND_RUN_STATE* state)
{
  sigset_t* signalset;

  /* man pthread_sigmask:
   *  "...signal actions and signal handlers, as set with
   *   sigaction(2), are shared between all threads"
   */

  /* handle the following signals explicitly */
  signalset = new sigset_t;
  state->signalset = signalset;
  sigemptyset(signalset);
  sigaddset(signalset, SIGTERM);
  sigaddset(signalset, SIGINT);
  sigaddset(signalset, SIGHUP);
  sigaddset(signalset, SIGPIPE);
  sigaddset(signalset, SIGQUIT);

  /* create a dummy signal handler */
  struct sigaction blockaction;
  blockaction.sa_handler = ecasound_signal_handler;
  sigemptyset(&blockaction.sa_mask);
  blockaction.sa_flags = 0;

  /* attach the dummy handler to the following signals */
  sigaction(SIGTERM, &blockaction, 0);
  sigaction(SIGINT, &blockaction, 0);
  sigaction(SIGHUP, &blockaction, 0);
  sigaction(SIGPIPE, &blockaction, 0);
  sigaction(SIGQUIT, &blockaction, 0);

#ifdef __FreeBSD__
  /* ignore signals instead of passing them to our handler */
  blockaction.sa_handler = SIG_IGN;
  sigaction(SIGFPE, &blockaction, 0);
#endif

  state->watchdog_thread = new pthread_t;
  int res = pthread_create(state->watchdog_thread, 
			   NULL, 
			   ecasound_watchdog_thread, 
			   reinterpret_cast<void*>(state));
  if (res != 0) {
    cerr << "ecasound: Warning! Unable to create watchdog thread." << endl;
  }

  /* block all signals in 'signalset' (see above) */
#if defined(HAVE_PTHREAD_SIGMASK)
  pthread_sigmask(SIG_BLOCK, signalset, NULL);
#elif defined(HAVE_SIGPROCMASK)
  sigprocmask(SIG_BLOCK, signalset, NULL);
#endif
}

static void ecasound_wd_wait_for_signals(ECASOUND_RUN_STATE* state)
{
#ifdef HAVE_SIGWAIT
  {
  /********************************************/
  /* impl 1: sigwait()                        */
  /********************************************/

    int signalno = 0;

# if defined(HAVE_PTHREAD_SIGMASK)
    pthread_sigmask(SIG_BLOCK, state->signalset, NULL);
# elif defined(HAVE_SIGPROCMASK)
  /* the set of signals must be blocked before entering sigwait() */
    sigprocmask(SIG_BLOCK, state->signalset, NULL);
# else
#   error "Build environment error."
# endif

    /* note: specific to sigwait() logic */
    glovar_wd_signals_blocked = 1;

    sigwait(state->signalset, &signalno);

    TRACE_EXIT(cerr << endl << "(ecasound-watchdog) Received signal " << signalno << ". Cleaning up and exiting..." << endl);
  }

#elif HAVE_PAUSE /* !HAVE_SIGWAIT */

  /**************************************************/
  /* impl 2:  pause() (alternative to sigwait())
  ***************************************************/

  /* note: with pause() we don't set 'glovar_ecasound_signal_blocked' as
   * it is normal to get signals when watchdog is running */

  pause();

  TRACE_EXIT(cerr << endl << "(ecasound-watchdog) Received signal and returned from pause(). Cleaning up and Exiting..." << endl);

#else  /* !HAVE_SIGWAIT && !HAVE_PAUSE */
#   error "Build environment error."
#endif
}

/**
 * Unblocks signals defined for 'state' for the calling thread,
 * or with pthread_sigmask() is not supported, for the whole
 * process.
 */
static void ecasound_signal_unblock(ECASOUND_RUN_STATE* state)
{
#ifdef HAVE_SIGWAIT
# if defined(HAVE_PTHREAD_SIGMASK)
  pthread_sigmask(SIG_UNBLOCK, state->signalset, NULL);
# elif defined(HAVE_SIGPROCMASK)
  /* the set of signals must be blocked before entering sigwait() */
  sigprocmask(SIG_UNBLOCK, state->signalset, NULL);
# else
#   error "Build environment error."
# endif
#endif
}

/**
 * Runs a watchdog thread that centrally catches signals that
 * will cause ecasound to exit.
 */
void* ecasound_watchdog_thread(void* arg)
{
  ECASOUND_RUN_STATE* state = reinterpret_cast<ECASOUND_RUN_STATE*>(arg);

  /* step: announce we are alive */
  // cerr << "Watchdog-thread created, pid=" << getpid() << "." << endl;

  /* step: block until a signal is received */
  ecasound_wd_wait_for_signals(state);

  /* step: unblock signals for watchdog thread after process 
   *       termination has been started */
  ecasound_signal_unblock(state);
  glovar_wd_signals_blocked = 0;

  /* step: signal the mainloop that process should terminate */
  state->exit_request = 1;

  /* step: in case mainloop is blocked running a batch job, we signal
   *       the engine thread directly and force it to terminate */
  if (state->interactive_mode != true &&
      state->control)
    state->control->quit_async();

  TRACE_EXIT(cerr << endl << "(ecasound-watchdog) looping until main reaches join point..." << endl);

  while(glovar_ecasound_exit_phase != 1) {
    
    TRACE_EXIT(cerr << "(ecasound-watchdog) watchdog thread exiting (looping)..." << endl);

    /* sleep for one 200ms */
    kvu_sleep(0, 200000000);

    /* note: A race condition exists between ECA_CONTROL_BASE
     *       quit_async() and run(): if quit_async() is called
     *       after run() has been entered, but before run()
     *       has managed to start the engine, it is possible engine
     *       may still be started. 
     * 
     *       Thus we will keep checking the engine status until 
     *       shutdown is really completed. 
     *
     *       For robustness, this check is also done when in
     *       interactive mode (in case the mainloop does not for
     *       some reason react to our exit request).
     */
    if (state->control) {
      if (state->control->is_engine_running() == true) {
	state->control->quit_async();
      }
    }
  }

  /* note: this function should always exit before main() */
  DBC_CHECK(glovar_ecasound_exit_phase == ECASOUND_EXIT_PHASE_WAIT_FOR_WD);

  TRACE_EXIT(cerr << endl << "(ecasound-watchdog) thread exiting..." << endl);

  return 0;
}
