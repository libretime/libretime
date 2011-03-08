// ------------------------------------------------------------------------
// eca-osc.h: Class implementing the Ecasound OSC interface
// Copyright (C) 2009 Kai Vehmanen
//
// Attributes:
//     eca-style-version: 3
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

#ifndef INCLUDED_ECA_OSC_H
#define INCLUDED_ECA_OSC_H

class ECA_CONTROL_MT;

#ifdef ECA_USE_LIBLO

#include <lo/lo.h>

/**
 * Ecasound Open Source Control Control (OSC) interface 
 *
 * The interface is documented in Ecasound codebase:
 *   - ecasound/Documentation/ecasound_osc_interface.txt
 *   - http://ecasound.git.sourceforge.net/git/gitweb.cgi?p=ecasound;a=blob;f=Documentation/ecasound_osc_interface.txt;hb=HEAD
 */
class ECA_OSC_INTERFACE {

  friend int cb_lo_method_handler(const char *path, const char *types, lo_arg **argv, int argc, lo_message msg, void *user_data);

  public:

  ECA_OSC_INTERFACE(ECA_CONTROL_MT *ecacontrol, int port = -1);
  ~ECA_OSC_INTERFACE(void);

  void start(void);
  void stop(void);
  bool is_running(void) const;
  
 private:

  int parse_path_param(const std::string &path, int *param);
  int handle_chain_message(const string &path, const char *types, lo_arg **argv, int argc);
  int handle_osc_message(const char *path, const char *types, lo_arg **argv, int argc);

  ECA_CONTROL_MT* ec_repp;
  bool running_rep;
  int udp_port_rep;
  lo_server_thread lo_thr_repp;
  lo_method method_all_repp;
};

#else  /* !ECA_USE_LIBLO */

class ECA_OSC_INTERFACE;

#endif /* ECA_USE_LIBLO */

#endif /* INCLUDED_ECA_OSC_H */
