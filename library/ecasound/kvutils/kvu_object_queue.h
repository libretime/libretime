// ------------------------------------------------------------------------
// object_queue.cpp: Thread-safe way to transmit generic objects (FIFO).
// Copyright (C) 1999-2000 Kai Vehmanen
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

#ifndef INCLUDED_OBJECT_QUEUE_H
#define INCLUDED_OBJECT_QUEUE_H

#include <pthread.h>
#include <deque>

/**
 * Thread-safe way to transmit generic objects (FIFO-queue).
 */
template<class T>
class OBJECT_QUEUE {
    
private:

    deque<T> cmds_rep;
    mutable pthread_mutex_t lock_rep;

public:
    
    /**
     * Inserts 'obj' into the queue. If some other process is 
     * accessing the queue, this call will block.
     */
    void push_back(const T& obj)  {
      pthread_mutex_lock(&lock_rep);
      cmds_rep.push_back(obj);
      pthread_mutex_unlock(&lock_rep);
    }

    /**
     * Pops the first item. If some other process is 
     * accessing the queue, this call will block.
     */
    void pop_front(void) {
      pthread_mutex_lock(&lock_rep);
      cmds_rep.pop_front();
      pthread_mutex_unlock(&lock_rep);
    }

    /**
     * Returns the first item. If some other process is 
     * accessing the queue, this call will block.
     */
    T front(void) const  {
      T temporary;
      pthread_mutex_lock(&lock_rep);
      if (cmds_rep.size() > 0) {
	temporary = cmds_rep.front();
      }
      pthread_mutex_unlock(&lock_rep);
      return(temporary);
    }

    /**
     * Returns true if the queue is empty. Unlike other calls,
     * this call will not block if some other process is 
     * accessing the queue. In this case, the returned result
     * will be 'true' even if the queue wasn't empty.
     */
    bool is_empty(void) const {
      if (pthread_mutex_trylock(&lock_rep) != 0)
	return(true);
  
      bool temp = true;
      if (cmds_rep.size() > 0) temp = false;
      pthread_mutex_unlock(&lock_rep);
      return(temp);
    }    
    
    /**
     * Clears the queue. If some other process is 
     * accessing the queue, this call will block.
     */
    void clear(void) {
      pthread_mutex_lock(&lock_rep);
      while (cmds_repsize() > 0) cmds_rep.pop_front();
      pthread_mutex_unlock(&lock_rep);
    }

    OBJECT_QUEUE(void) {
      pthread_mutex_init(&lock_rep, NULL);
    }

    ~OBJECT_QUEUE(void) {
      pthread_mutex_destroy(&lock_rep);      
    }
};

#endif
