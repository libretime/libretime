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

/* ============================================================ include files */

#include "LiveSupport/Core/AsyncState.h"


using namespace LiveSupport::Core;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  Constant instance: init.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::initState(innerInitState);

/*------------------------------------------------------------------------------
 *  Constant instance: pending.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::pendingState(innerPendingState);

/*------------------------------------------------------------------------------
 *  Constant instance: finished.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::finishedState(innerFinishedState);

/*------------------------------------------------------------------------------
 *  Constant instance: closed.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::closedState(innerClosedState);

/*------------------------------------------------------------------------------
 *  Constant instance: failed.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::failedState(innerFailedState);

/*------------------------------------------------------------------------------
 *  Constant instance: invalid.
 *----------------------------------------------------------------------------*/
const AsyncState    AsyncState::invalidState(innerInvalidState);


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct from a transport string.
 *----------------------------------------------------------------------------*/
AsyncState
AsyncState :: fromTransportString(const std::string &   transportString)
                                                                    throw ()
{
    if (transportString == "init") {
        return initState;
        
    } else if (transportString == "pending" || transportString == "waiting") {
        return pendingState;
        
    } else if (transportString == "finished") {
        return finishedState;
        
    } else if (transportString == "closed") {
        return closedState;
        
    } else if (transportString == "failed") {
        return failedState;
    }

    return invalidState;
}


/*------------------------------------------------------------------------------
 *  Construct from a backup string.
 *----------------------------------------------------------------------------*/
AsyncState
AsyncState :: fromBackupString(const std::string &      backupString)
                                                                    throw ()
{
    if (backupString == "working") {
        return pendingState;
        
    } else if (backupString == "success") {
        return finishedState;
        
    } else if (backupString == "fault") {
        return failedState;
    }
    
    return invalidState;
}


/*------------------------------------------------------------------------------
 *  Convert to a transport string.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
AsyncState :: toTransportString(void) const                         throw ()
{
    Ptr<std::string>::Ref   transportString(new std::string());    
    
    switch (value) {
        case innerInitState:        *transportString = "init";
                                    break;
        
        case innerPendingState:     *transportString = "pending";
                                    break;
        
        case innerFinishedState:    *transportString = "finished";
                                    break;
        
        case innerClosedState:      *transportString = "closed";
                                    break;
        
        case innerFailedState:      *transportString = "failed";
                                    break;
        
        case innerInvalidState:     *transportString = "(invalid)";
                                    break;
    }
    
    return transportString;
}


/*------------------------------------------------------------------------------
 *  Convert to a backup string.
 *----------------------------------------------------------------------------*/
Ptr<std::string>::Ref
AsyncState :: toBackupString(void) const                            throw ()
{
    Ptr<std::string>::Ref   backupString(new std::string());    
    
    switch (value) {
        case innerInitState:        *backupString = "(init)";
                                    break;
        
        case innerPendingState:     *backupString = "working";
                                    break;
        
        case innerFinishedState:    *backupString = "success";
                                    break;
        
        case innerClosedState:      *backupString = "(closed)";
                                    break;
        
        case innerFailedState:      *backupString = "fault";
                                    break;
        
        case innerInvalidState:     *backupString = "(invalid)";
                                    break;
    }
    
    return backupString;
}


/*------------------------------------------------------------------------------
 *  Print to an ostream.
 *----------------------------------------------------------------------------*/
std::ostream &
operator<<(std::ostream & ostream, const LiveSupport::Core::AsyncState  state)
                                                                    throw ()
{
    Ptr<std::string>::Ref   transportState = state.toTransportString();
    ostream << *transportState;
    return ostream;
}

