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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/ClientContext.h,v $

------------------------------------------------------------------------------*/
#ifndef ClientContext_h
#define ClientContext_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <hxprefs.h>
#include <ihxpckts.h>

#include "AdviseSink.h"

namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

class ExampleErrorMessages;

/**
 *  A Helix client context.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class ClientContext : public IHXPreferences
{
    private:
        /**
         *  The reference count of this object.
         */
        LONG32                  lRefCount;

        /**
         *  The advise sink recieving event notifications.
         */
        AdviseSink            * pClientSink;

        /**
         *  The error sink, receiving error notifications.
         */
        ErrorSink             * pErrorSink;

        /**
         *  The authentication manager.
         */
        AuthenticationManager * pAuthMgr;
        
        /**
         *  The preferences.
         */
        IHXPreferences*         pDefaultPrefs;

        /**
         *  The GUID for this context.
         */
        char                    pszGUID[256]; /* Flawfinder: ignore */


    public:

        /**
         *  Constructor.
         */
        ClientContext()                                 throw ();

        /**
         *  Destructor.
         */
        virtual
        ~ClientContext()                                throw ();

        /**
         *  Initialize the client context.
         *  Always close a context by calling Close().
         *
         *  @param pUnknown pointer to the object this will be a context for.
         *  @param pPreferences pointer to the helix preferences used.
         *  @param pszGUID the globally unique ID, if any, for the context.
         *  @see #Close
         */
        void
        Init(IUnknown             * pUnknown,
              IHXPreferences      * pPreferences,
              char                * pszGUID)            throw ();

        /**
         *  De-Initialize the client context.
         *
         *  @see #Init
         */
        void Close()                                    throw ();

        /**
         *  Return the ErrorSink object for this client context.
         *
         *  @return the ErrorSink object for this client context.
         */
        ErrorSink *
        getErrorSink(void) const                        throw ()
        {
            return pErrorSink;
        }

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

        // IHXPreferences methods

        /**
         *  Reaad a preference from the registry or configuration file.
         *
         *  @param pPrekKey pointer to the name of the preference.
         *  @param pBuffer return a pointer to an IHXBuffer interface
         *         that manages the value of the preference.
         */
        STDMETHOD(ReadPref) (THIS_
                             const char   * pPrekKey,
                             IHXBuffer    *& pBuffer)           throw ();

        /**
         *  Writes a preference to the registry of configuration file.
         *
         *  @param pPrekKey pointer to the name of the preference.
         *  @param pBuffer return a pointer to an IHXBuffer interface
         *         that manages the value of the preference.
         */
        STDMETHOD(WritePref) (THIS_
                              const char   * pPrekKey,
                              IHXBuffer    * pBuffer)           throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // ClientContext_h

