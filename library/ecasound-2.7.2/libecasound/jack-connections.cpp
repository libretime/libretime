// ------------------------------------------------------------------------
// jack-connections.cpp: Utility class to manage JACK port connections 
// Copyright (C) 2008 Kai Vehmanen
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

#include <string>
#include <iostream>

#include <jack/jack.h>
#include <sys/types.h>
#include <unistd.h>
#include <stdlib.h>

#include <kvu_numtostr.h>

#include "eca-logger.h"
#include "jack-connections.h"

using std::string;

JACK_CONNECTIONS::JACK_CONNECTIONS(void)
{
}

JACK_CONNECTIONS::~JACK_CONNECTIONS(void)
{
}

static jack_client_t *priv_prepare(void)
{
 int pid = getpid();
 std::string clntname = "libecasound-ctrl-" + kvu_numtostr(pid);
 jack_client_t *client = jack_client_new (clntname.c_str());
 return client;
}

static void priv_cleanup(jack_client_t *client)
{
  jack_client_close(client);
}

bool JACK_CONNECTIONS::connect(const char* src, const char* dest)
{
  
  int result = -1;
  jack_client_t *client = priv_prepare();
  if (client != 0) {
    result = jack_connect(client, src, dest);

    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		std::string("Connected JACK ports ") +
		src +
		" and " +
		dest +
		" with result of " +
		kvu_numtostr(result));

    priv_cleanup(client);

  }

  return result == 0;
}

bool JACK_CONNECTIONS::disconnect(const char* src, const char* dest)
{
  int result = -1;
  jack_client_t *client = priv_prepare();
  if (client != 0) {
    result = jack_disconnect(client, src, dest);

    ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		std::string("Connected JACK ports ") +
		src +
		" and " +
		dest +
		" with result of " +
		kvu_numtostr(result));

    priv_cleanup(client);
  }

  return result == 0;
}

bool JACK_CONNECTIONS::list_connections(std::string* output)
{
  jack_client_t *client = priv_prepare();
  if (client != 0) {
    const char **next, **ports =
      jack_get_ports(client, NULL, NULL, 0);

    if (ports) {

      *output += "\n";

      for (next = ports; *next; next++) {
	jack_port_t *port;

	*output += string(*next);

	port = jack_port_by_name(client, *next);

	const char **nextconn, **conns =
	  jack_port_get_all_connections(client, port);

	if (conns) {
	  for(nextconn = conns; *nextconn; nextconn++) {
	    *output += string("\n\t") + string(*nextconn) + string("\n");
	  }
	  free(conns);
	}
	else {
	  *output += "\n";
	}
      }
      free(ports);
    }

    priv_cleanup(client);
  }

  return client != 0;
}
