/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/
#ifndef LiveSupport_Core_AsyncState_h
#define LiveSupport_Core_AsyncState_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#include <ostream>
#include <string>
#include "LiveSupport/Core/Ptr.h"

namespace LiveSupport {
namespace Core {

/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A class representing the state of an asynchronous process.
 *
 *  It provides some constants, plus conversion methods to and from
 *  strings (used when sending through XML-RPC methods).
 *
 *  There are two sets of conversion methods, because the states have
 *  different names in the storage server for backup-related stuff and
 *  general transport stuff.
 *
 *  @author  $Author$
 *  @version $Revision$
 */
class AsyncState
{
    private:
    
        /**
         *  The possible states of an asynchronous process.
         */
        typedef enum {  innerInitState,
                        innerPendingState,
                        innerFinishedState,
                        innerClosedState,
                        innerFailedState,
                        innerInvalidState }     InnerState;
       /**
         *  The value of this state.
         */
        InnerState      value;

        /**
         *  A private constructor.
         */
        AsyncState(InnerState   state)                              throw ()
              : value(state)
        {
        }


    public:
    
        /**
         *  Default constructor; sets the state to "invalid".
         */
        AsyncState(void)                                            throw ()
              : value(innerInvalidState)
        {
        }
        
        /**
         *  Constant instance: init.
         */
        static const AsyncState     initState;
        
        /**
         *  Constant instance: pending.
         */
        static const AsyncState     pendingState;
        
        /**
         *  Constant instance: finished.
         */
        static const AsyncState     finishedState;
        
        /**
         *  Constant instance: closed.
         */
        static const AsyncState     closedState;
        
        /**
         *  Constant instance: failed.
         */
        static const AsyncState     failedState;
        
        /**
         *  Constant instance: invalid.
         */
        static const AsyncState     invalidState;

        /**
         *  Construct from a transport string.
         *
         *  @param  transportString a string used by the getTransportInfo
         *                          method of the storage server.
         *  @return an AsyncState with the corresponding value.
         */
        static AsyncState
        fromTransportString(const std::string &     transportString)
                                                                    throw ();
        
        /**
         *  Construct from a backup string.
         *
         *  @param  backupString    a string used by the xxxxBackupCheck
         *                          method of the storage server.
         *  @return an AsyncState with the corresponding value.
         */
        static AsyncState
        fromBackupString(const std::string &        backupString)
                                                                    throw ();
        
        /**
         *  Convert to a transport string.
         *
         *  @return a string used by the getTransportInfo method of the
         *          storage server.
         */
        Ptr<std::string>::Ref
        toTransportString(void) const                               throw ();
        
        /**
         *  Convert to a backup string.
         *
         *  @return a string used by the xxxxBackupCheck method of the
         *          storage server.
         */
        Ptr<std::string>::Ref
        toBackupString(void) const                                  throw ();
        
        /**
         *  Check for equality.
         *
         *  @param  other   the other AsyncState to compare with.
         *  @return true    if the two states are equal.
         */
        bool
        operator==(const AsyncState &   other) const                throw ()
        {
            return (value == other.value);
        }
        
        /**
         *  Check for inequality.
         *
         *  @param  other   the other AsyncState to compare with.
         *  @return true    if the two states are not equal.
         */
        bool
        operator!=(const AsyncState &   other) const                throw ()
        {
            return (value != other.value);
        }
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace Core
} // namespace LiveSupport

/**
 *  Print to an ostream.
 *
 *  @param  ostream     the ostream to print to.
 *  @param  state       the AsyncState to print.
 *  @return a reference to the same ostream object.
 */
std::ostream&
operator<<(std::ostream& ostream, const LiveSupport::Core::AsyncState   state)
                                                                    throw ();

#endif // LiveSupport_Core_AsyncState_h

