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
    Version  : $Revision: 1.1 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/ErrorSink.h,v $

------------------------------------------------------------------------------*/
#ifndef ErrorSink_h
#define ErrorSink_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <hxcore.h>
#include <hxerror.h>


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Helix error sink
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class ErrorSink : public IHXErrorSink
{
    protected:
        /**
         *  The reference count of this object.
         */
        LONG32      lRefCount;

        /**
         *  The player this sink gets errors from.
         */
        IHXPlayer * pPlayer;

        /**
         *  The last Helix error code receieved.
         */
        UINT32      lastHelixErrorCode;


    public:

        /**
         *  Constructor.
         *
         *  @param pUnkown pointer to the object this is an erro sink for.
         */
        ErrorSink(IUnknown* pUnknown)                       throw ();

        /**
         *  Destructor.
         */
        virtual
        ~ErrorSink()                                        throw ();

        // IUnknown methods

        /**
         *  Query the object to determine if it supports a specific interface.
         *
         *  @param riid the reference identifier for the interface queried.
         *  @param ppvObj points to an interface pointer, that is filled
         *         if the requested interface is implemented.
         */
        STDMETHOD(QueryInterface) (THIS_
                                   REFIID riid,
                                   void** ppvObj)       throw ();
    
        /**
         *  Increase the objects reference count by one.
         *
         *  @return the new reference count.
         */
        STDMETHOD_(ULONG32,AddRef) (THIS)               throw ();

        /**
         *  Decreases the objects reference count by one. If the count
         *  reaches 0, the object is destroyed.
         *
         *  @return the new value of the reference count.
         */
        STDMETHOD_(ULONG32,Release) (THIS)              throw ();

 
        // IHXErrorSink methods

        /**
         *  After you have registered your error sink with an
         *  IHXErrorSinkControl 
         *  (either in the server or player core) this method will be called to 
         *  report an error, event, or status message.
         *
         *  @param unSeverity the type of report.
         *  @param ulHXCode Helix Architecture error code.
         *  @param ulUserCode User-specific error code.
         *  @param pUserString User-specific error string.
         *  @param pMoreInfoURL pointer to a user-specific URL for more info.
         */
        STDMETHOD(ErrorOccurred) (THIS_
                                  const UINT8       unSeverity,  
                                  const ULONG32     ulHXCode,
                                  const ULONG32     ulUserCode,
                                  const char      * pUserString,
                                  const char      * pMoreInfoURL)
                                                                    throw ();

        /**
         *  Get the Helix error code for the last error occured.
         *
         *  @return the Helix error code for the last error occured.
         */
        ULONG32
        getLastErrorCode(void) const                                throw ()
        {
            return lastHelixErrorCode;
        }

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // ErrorSink_h

