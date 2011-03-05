// ------------------------------------------------------------------------
// eca-osc.cpp: Class implementing the Ecasound OSC interface
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
//
// References:
//     - liblo:
//       http://liblo.sourceforge.net/
//       http://liblo.sourceforge.net/docs/
//     - Open Source Control (OSC):
//       http://opensoundcontrol.org/
//       http://archive.cnmat.berkeley.edu/OpenSoundControl/
//       http://en.wikipedia.org/wiki/OpenSound_Control
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
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
// ------------------------------------------------------------------------

#ifdef HAVE_CONFIG_H
#include <config.h>
#endif

#ifdef ECA_USE_LIBLO

#include <cstdio>
#include <cstdlib>
#include <string>

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "eca-control-mt.h"
#include "eca-chainsetup-edit.h"
#include "eca-chainsetup.h"
#include "eca-logger.h"

#include "eca-osc.h"

// #define VERBOSE_FOR_DEBUGGING 1
#undef VERBOSE_FOR_DEBUGGING

using namespace std;

const std::string eca_osc_interface_namespace_const ("ecasound");

static void cb_lo_err_handler(int num, const char *msg, const char *where)
{
  std::fprintf(stderr, 
	       "lo_error: num=%d, msg=%s, where=%s.\n",
	       num, msg, where);
}

int cb_lo_method_handler(const char *path, const char *types, lo_arg **argv, int argc, lo_message msg, void *user_data)
{
  int retval;

  ECA_OSC_INTERFACE* self = 
    reinterpret_cast<ECA_OSC_INTERFACE*>(user_data);

#ifdef VERBOSE_FOR_DEBUGGING
  std::fprintf(stdout, 
	       "lo_method: path=%s, types=%s, args=%d, userdata=%p.\n",
	       path, types, argc, user_data);
#endif

  retval = self->handle_osc_message(path, types, argv, argc);

#ifdef VERBOSE_FOR_DEBUGGING
  for(int n = 0; n < argc; n++) {
    switch (types[n]) {
    case 'i':
      std::fprintf(stdout, 
		   "lo_arg #%d %c=<%d>\n",
		   n, types[n], argv[n]->i);
      break;
    case 'f':
      std::fprintf(stdout, 
		   "lo_arg #%d %c=<%.03f>\n",
		   n, types[n], argv[n]->f);
      break;
    default:
      std::fprintf(stdout, 
		   "lo_arg #%d %c=<raw:%08X>\n",
		   n, types[n], *(uint32_t*)argv[n]);
      break;
    }
  }
#endif
   
  /* note: doesn't seem to work in 0.23 (at least when messages 
     are sent with oscsend from 0.26) */
  // lo_message_pp(msg);

  return 0;
}

/**
 * Constructor
 *
 * If any other object may use the control object, passed to 
 * ECA_OSC_INTERFACE, at the same time, ECA_CONTROL_MT should
 * be used to guarantee thread-safety access to control
 * functions.
 *
 * @param ecacontrol if the control object is u
 */
ECA_OSC_INTERFACE::ECA_OSC_INTERFACE(ECA_CONTROL_MT *ecacontrol, int port)
  : ec_repp(ecacontrol),
    running_rep(false),
    udp_port_rep(port),
    lo_thr_repp(0)
{

}

ECA_OSC_INTERFACE::~ECA_OSC_INTERFACE(void)
{
  if (is_running() == true) {
    stop();
  }
}

void ECA_OSC_INTERFACE::start(void)
{
  const char* port_str = 0;
  if (udp_port_rep > 0) {
    port_str = kvu_numtostr(udp_port_rep).c_str();
  }

  /* FIXME: move to after opening and acquire port
   *        with lo_server_thread_get_port */
  ECA_LOG_MSG(ECA_LOGGER::info,
	      "started OSC interface at UDP port "
	      + kvu_numtostr(udp_port_rep));

  lo_thr_repp =
    lo_server_thread_new(port_str, cb_lo_err_handler);
  if (lo_thr_repp) {

    /* note: in liblo 0.23 function returns voide, but 
     *       in 0.26 in returns an int */
    lo_server_thread_start(lo_thr_repp);

    method_all_repp 
      = lo_server_thread_add_method(lo_thr_repp,
				    NULL,
				    NULL,
				    cb_lo_method_handler,
				    this);
				
    running_rep = true;
  }

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "server thread started with status " 
	      + kvu_numtostr(running_rep));
}

void ECA_OSC_INTERFACE::stop(void)
{
  if (running_rep == true) {
    /* note: in liblo 0.23 function returns voide, but 
     *       in 0.26 in returns an int */
    lo_server_thread_stop(lo_thr_repp);
    lo_server_thread_free(lo_thr_repp);
    method_all_repp = 0;
    lo_thr_repp = 0;
    running_rep = false;
  }
}

bool ECA_OSC_INTERFACE::is_running(void) const
{
  return running_rep;
}

/** For "foo/bar/yeah", returns "foo" */
static string priv_path_get_front(const string &path)
{
  return string (path, 0, path.find("/"));
}

/** For "foo/bar/yeah", returns "bar/yeah" */
static string priv_path_pop_front(const std::string &path)
{
  size_t marker = path.find("/");
  if (marker != string::npos)
    return string(path, marker + 1);

  return path;
}

/**
 * Parses path with syntax "param/X" and writes X to 'param'.
 *
 * @return 0 on success, negative error code otherwise
 */ 
int ECA_OSC_INTERFACE::parse_path_param(const std::string &path, int *param)
{
  if (priv_path_get_front(string(path)) == "param") {
    string left_s = priv_path_pop_front(path);
    string param_s = priv_path_get_front(left_s);
    if (param_s.size() > 0 &&
	param != 0) {
      *param = std::atoi(param_s.c_str());
      return 0;
    }
  }
  return -1;
}

int ECA_OSC_INTERFACE::handle_chain_message(const std::string &path, const char *types, lo_arg **argv, int argc)
{
  int retval = -1;
  string chain_s = priv_path_get_front(path);
  
  ec_repp->lock_control();

  const ECA_CHAINSETUP *cs = ec_repp->get_connected_chainsetup();
  int c_index = cs ? cs->get_chain_index(chain_s) : -1;
  if (c_index > 0) {
    ECA::chainsetup_edit_t edit;
    edit.cs_ptr = cs;

    string left_s = priv_path_pop_front(path);
    string action = priv_path_get_front(left_s);

    if (action == "op") {
      left_s = priv_path_pop_front(left_s);
      string op_s = priv_path_get_front(left_s);
      int param = -1;
      int p_res =
	parse_path_param(priv_path_pop_front(left_s), &param);

      if (argc > 0 &&
	  p_res == 0) {
	DBC_CHECK(types[0] == 'f');
	edit.type = ECA::edit_cop_set_param;

	edit.m.cop_set_param.chain = c_index;
	edit.m.cop_set_param.op = std::atoi(op_s.c_str());
	edit.m.cop_set_param.param = param;
	edit.m.cop_set_param.value = argv[0]->f;
	
#ifdef VERBOSE_FOR_DEBUGGING
	std::fprintf(stdout, 
		     "chain=%s (id=%d), op=%d, param=%d to %.03f\n",
		     chain_s.c_str(), 
		     c_index,
		     edit.m.cop_set_param.op,
		     edit.m.cop_set_param.param,
		     edit.m.cop_set_param.value);
#endif

	ec_repp->execute_edit_on_connected(edit);
	
	retval = 0;
      }
    }
    else if (action == "ctrl") {
      left_s = priv_path_pop_front(left_s);
      string ctrl_s = priv_path_get_front(left_s);
      int param = -1;
      int p_res =
	parse_path_param(priv_path_pop_front(left_s), &param);
      if (argc > 0 &&
	  p_res == 0) {
	DBC_CHECK(types[0] == 'f');
	edit.type = ECA::edit_ctrl_set_param;

	edit.m.ctrl_set_param.chain = c_index;
	edit.m.ctrl_set_param.op = std::atoi(ctrl_s.c_str());
	edit.m.ctrl_set_param.param = param;
	edit.m.ctrl_set_param.value = argv[0]->f;
	
#ifdef VERBOSE_FOR_DEBUGGING
	std::fprintf(stdout, 
		     "chain=%s (id=%d), ctrl=%d, param=%d to %.03f\n",
		     chain_s.c_str(), 
		     c_index,
		     edit.m.ctrl_set_param.op,
		     edit.m.ctrl_set_param.param,
		     edit.m.ctrl_set_param.value);
#endif

	ec_repp->execute_edit_on_connected(edit);
	
	retval = 0;
      }
    }
    else {
      DBC_NEVER_REACHED();
    }
  }
  else {
#ifdef VERBOSE_FOR_DEBUGGING
    std::fprintf(stdout, 
		 "full=%s, chain=%s (id=%d), op=-, param=- to -\n",
		 path.c_str(),chain_s.c_str(), 
		 c_index);
#endif
  }

  ec_repp->unlock_control();    

  if (cs == 0) {
    ECA_LOG_MSG(ECA_LOGGER::info,
		"WARNING: no chainsetup connected, so ignoring OSC message \""
		+ path + "\"");
  }	
  
  return retval;
}

int ECA_OSC_INTERFACE::handle_osc_message(const char *path, const char *types, lo_arg **argv, int argc)
{
  int retval = 0;
  
  DBC_CHECK(path[0] == '/');
  string left_s = priv_path_pop_front(string(path));
  string namespace_s = priv_path_get_front(left_s);
  if (namespace_s == eca_osc_interface_namespace_const) {
    left_s = priv_path_pop_front(left_s);
    string component_s = priv_path_get_front(left_s);

#ifdef VERBOSE_FOR_DEBUGGING
    std::fprintf(stdout, 
		 "full=%s, left=%s, component=%s\n",
		 path, left_s.c_str(), component_s.c_str());
#endif

    if (component_s == "chain") {
      left_s = priv_path_pop_front(left_s);
      retval = handle_chain_message(left_s, types, argv, argc);
    }
  }
  else {
#ifdef VERBOSE_FOR_DEBUGGING
    std::fprintf(stdout, 
		 "full=%s, left=%s, ignoring...\n",
		 path, left_s.c_str());
#endif

    retval = -1;
  }

  return retval;
}

#endif /* ECA_USE_LIBLO */
