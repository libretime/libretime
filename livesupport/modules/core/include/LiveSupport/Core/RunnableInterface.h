/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the LiveSupport project.
    http://livesupport.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    LiveSupport is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    LiveSupport is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with LiveSupport; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: maroy $
    Version  : $Revision: 1.2 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/core/include/LiveSupport/Core/RunnableInterface.h,v $

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_RunnableInterface_h
#define LiveSupport_Core_RunnableInterface_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif


namespace LiveSupport {
namespace Core {


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Runnable object, that can form the main execution body of a thread.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.2 $
 *  @see Thread
 */
class RunnableInterface
{
    public:
        /**
         *  A virtual destructor, as this class has virtual functions.
         */
        virtual
        ~RunnableInterface(void)                        throw ()
        {
        }

        /**
         *  The main execution loop for the thread.
         */
        virtual void
        run(void)                                       throw ()        = 0;

        /**
         *  Send a signal to the runnable object.
         *
         *  @param userData user-specific parameter for the signal.
         */
        virtual void
        signal(int userData)                            throw ()        = 0;

        /**
         *  Signal the thread to stop, gracefully.
         *  This is just a call to signal the execution to stop, eventually.
         */
        virtual void
        stop(void)                                      throw ()        = 0;
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport


#endif // LiveSupport_Core_RunnableInterface_h

