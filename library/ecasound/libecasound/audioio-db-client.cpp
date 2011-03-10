// ------------------------------------------------------------------------
// audioio-db-client.cpp: Client class for double-buffering providing 
//                        additional layer of buffering for objects
//                        derived from AUDIO_IO.
// Copyright (C) 2000-2005,2009 Kai Vehmanen
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
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
// ------------------------------------------------------------------------

#include <unistd.h> /* open(), close() */

#include <kvu_dbc.h>
#include <kvu_numtostr.h>

#include "samplebuffer.h"
#include "eca-logger.h"
#include "audioio-db-client.h"

/**
 * Constructor. The given client object is registered to 
 * the given db server as a client object.
 *
 * Ownership of 'aobject' is transfered to this db client
 * object if 'transfer_ownership' is true.
 */
AUDIO_IO_DB_CLIENT::AUDIO_IO_DB_CLIENT (AUDIO_IO_DB_SERVER *pserver, 
					AUDIO_IO* aobject,
					bool transfer_ownership) 
  : pserver_repp(pserver),
    free_child_rep(transfer_ownership) 
{
  set_child(aobject);
  pbuffer_repp = 0;
  xruns_rep = 0;
  finished_rep = false;
  recursing_rep = false;

  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
		std::string("DB-client created for ") +
		child()->label() +
		".");

  // just in case the child object has already been configured
  fetch_initial_child_data();
}

/**
 * Copy attributes from the proxied (child) object.
 */
void AUDIO_IO_DB_CLIENT::fetch_initial_child_data(void)
{
  // note! the child object is one that is configured
  //       at this point
  set_audio_format(child()->audio_format());
  set_position_in_samples(child()->position_in_samples());
  set_length_in_samples(child()->length_in_samples());
  set_buffersize(child()->buffersize());
  set_io_mode(child()->io_mode());
  set_label(child()->label());
  toggle_nonblocking_mode(child()->nonblocking_mode());
}

/**
 * Desctructor. Unregisters the client from the db
 * server.
 */
AUDIO_IO_DB_CLIENT::~AUDIO_IO_DB_CLIENT(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "destructor " + label() + ".");

  if (is_open() == true) {
    close();
  }

  if (pserver_repp != 0) {
    bool was_running = false;
    if (pserver_repp->is_running() == true) {
      was_running = true;
      pserver_repp->stop();
      pserver_repp->wait_for_stop();
      DBC_CHECK(pserver_repp->is_running() != true);
    }

    pserver_repp->unregister_client(child());
    pbuffer_repp = 0;

    if (was_running == true) {
      pserver_repp->start();
    }
  }
  
  if (free_child_rep != true) {
    /* to avoid deleting the original registered child */
    release_child_no_delete();
  }
    
  if (xruns_rep > 0) 
    std::cerr << "(audioio-db-client) There were total " << xruns_rep << " xruns." << std::endl;
}

/**
 * Whether all data has been processed? If opened in mode 'io_read', 
 * this means that end of stream has been reached. If opened in 
 * 'io_write' or 'io_readwrite' modes, finished status usually
 * means that an error has occured (no space left, etc). After 
 * finished() has returned 'true', further calls to read_buffer() 
 * and/or write_buffer() won't process any data.
 */
bool AUDIO_IO_DB_CLIENT::finished(void) const { return finished_rep; }

/**
 * Reads samples to buffer pointed by 'sbuf'. If necessary, the target 
 * buffer will be resized.
 */
void AUDIO_IO_DB_CLIENT::read_buffer(SAMPLE_BUFFER* sbuf)
{
  DBC_CHECK(pbuffer_repp != 0);

  if (pbuffer_repp->read_space() > 0) {
    SAMPLE_BUFFER* source = pbuffer_repp->sbufs_rep[pbuffer_repp->readptr_rep.get()];
    sbuf->copy_all_content(*source);
    pbuffer_repp->advance_read_pointer();
    pserver_repp->signal_client_activity();
    change_position_in_samples(sbuf->length_in_samples());
  }
  else {
    sbuf->number_of_channels(channels());
    if (pbuffer_repp->finished_rep.get() == 1) {
      finished_rep = true;
      sbuf->length_in_samples(0);
    }
    else {
      xruns_rep++;
      sbuf->length_in_samples(0);

      std::cerr << "(audioio-db-client) WARNING: Underrun in reading from \"" 
		<< child()->label() 
		<< "\". Trying to recover." << std::endl;
    }
  }

  // --------
  DBC_ENSURE(sbuf->number_of_channels() == channels());
  // --------
}

/**
 * Writes all data from sample buffer pointed by 'sbuf'. Notes
 * concerning read_buffer() also apply to this routine.
 */
void AUDIO_IO_DB_CLIENT::write_buffer(SAMPLE_BUFFER* sbuf)
{
  DBC_CHECK(pbuffer_repp != 0);

  if (pbuffer_repp->write_space() > 0) {
    SAMPLE_BUFFER* target = pbuffer_repp->sbufs_rep[pbuffer_repp->writeptr_rep.get()];
    target->copy_all_content(*sbuf);
    target->number_of_channels(channels());
    pbuffer_repp->advance_write_pointer();
    pserver_repp->signal_client_activity();
    change_position_in_samples(sbuf->length_in_samples());
    extend_position();
  }
  else {
    if (pbuffer_repp->finished_rep.get() == 1) finished_rep = true;
    else {
      /* NOTE: not always rt-safe, but it's better to break rt-safety than
       *       to lose recorded data */

      std::cerr << "(audioio-db-client) WARNING: Overrun in writing to \"" 
		<< child()->label() 
		<< "\". Trying to recover." << std::endl;

      xruns_rep++;

      pserver_repp->wait_for_full();
      if (recursing_rep != true && pbuffer_repp->write_space() > 0) {
	recursing_rep = true;
	this->write_buffer(sbuf);
	recursing_rep = false;
      }
      else {
	seek_position(position_in_samples()); // hack to force a restart of the db server
	std::cerr << "(audioio-db-client) Serious trouble with the disk-io subsystem! (output)" << std::endl;
      }
    }
  }
}

/**
 * Stops the DB server in case it's running. 
 * Returns true if server was running. The return
 * value should be passed to restore_db_server_state()
 * function.
 */
bool AUDIO_IO_DB_CLIENT::pause_db_server_if_running(void)
{
  bool was_running = false;
  if (pserver_repp->is_running() == true) {
    was_running = true;
    pserver_repp->stop();
    pserver_repp->wait_for_stop();
    DBC_CHECK(pserver_repp->is_running() != true);
  }

  return was_running;
}

void AUDIO_IO_DB_CLIENT::restore_db_server_state(bool was_running)
{
  if (was_running == true) {
    pserver_repp->start();
    pserver_repp->wait_for_full();
    DBC_CHECK(pserver_repp->is_running() == true);
  }
}

/**
 * Seeks to the current position.
 *
 * Note! Seeking involves stopping the whole db 
 *       server, so it's a costly operation.
 */
SAMPLE_SPECS::sample_pos_t AUDIO_IO_DB_CLIENT::seek_position(SAMPLE_SPECS::sample_pos_t pos)
{ 
  ECA_LOG_MSG(ECA_LOGGER::user_objects, 
	      "seek " + label() + 
	      " to pos " + kvu_numtostr(pos) + ".");
  SAMPLE_SPECS::sample_pos_t res =
    child()->position_in_samples();
  
  if (child()->supports_seeking() == true) {
    bool was_running = pause_db_server_if_running();

    child()->seek_position_in_samples(pos);
    res = child()->position_in_samples();
    if (pbuffer_repp != 0) {
      pbuffer_repp->reset();
    }

    finished_rep = false;

    restore_db_server_state(was_running);
  }

  return AUDIO_IO_PROXY::seek_position(res);
}

/**
 * Opens the child audio object (possibly in exclusive mode).
 * This routine is meant for opening files and devices,
 * loading libraries, etc. 
 */
void AUDIO_IO_DB_CLIENT::open(void) throw(AUDIO_IO::SETUP_ERROR&) 
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "open " + label() + ".");

  if (child()->is_open() != true) {
    child()->open();
  }

  set_audio_format(child()->audio_format());
  set_length_in_samples(child()->length_in_samples());

  if (pbuffer_repp == 0) {
    pserver_repp->register_client(child());
    pbuffer_repp = pserver_repp->get_client_buffer(child());

    for(unsigned int n = 0; n < pbuffer_repp->sbufs_rep.size(); n++) {
      pbuffer_repp->sbufs_rep[n]->number_of_channels(channels());
      pbuffer_repp->sbufs_rep[n]->length_in_samples(buffersize());
    }

    if (io_mode() == AUDIO_IO::io_read) 
      pbuffer_repp->io_mode_rep = AUDIO_IO::io_read;
    else
      pbuffer_repp->io_mode_rep = AUDIO_IO::io_write;
  }

  AUDIO_IO::open();
}

/**
 * Closes the child audio object. After calling this routine, 
 * all resources (ie. soundcard) must be freed
 * (they can be used by other processes).
 */
void AUDIO_IO_DB_CLIENT::close(void)
{
  ECA_LOG_MSG(ECA_LOGGER::user_objects, "close " + label() + ".");

  if (child()->is_open() == true) child()->close();

  AUDIO_IO::close();
}

void AUDIO_IO_DB_CLIENT::start_io(void)
{
  AUDIO_IO_PROXY::start_io();

  /* note: child may have changed its position after 
   *       start_io() is issued (via AUDIO_IO_PROXY::start_io() */

  if (child()->supports_seeking() != true) {
    bool was_running = pause_db_server_if_running();

    set_position_in_samples(child()->position_in_samples());

    /* as position might have changed, flush the buffers */
    if (pbuffer_repp != 0) {
      pbuffer_repp->reset();
    }

    restore_db_server_state(was_running);
  }
}

void AUDIO_IO_DB_CLIENT::stop_io(void)
{
  AUDIO_IO_PROXY::stop_io();
}
