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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/AuthenticationManager.h,v $

------------------------------------------------------------------------------*/
#ifndef AuthenticationManager_h
#define AuthenticationManager_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <hxauth.h>


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Helix client authentication manager.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class AuthenticationManager : public IHXAuthenticationManager
{
    private:
        /**
         *  The reference count of this object.
         */
        INT32   lRefCount;


    public:
        /**
         *  Constructor.
         */
        AuthenticationManager()                     throw ();

        /**
         *  Destructor.
         */
        virtual
        ~AuthenticationManager()                    throw ();

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

 
        // IHXAuthenticationManager methods
 
        /**
         *  Get a user name and password, or other authentication parameters.
         *
         *  @param pResponse Ponter to an IHXAuthenticationManagerResponse
         *         interface that manages the response.
         */
        STDMETHOD(HandleAuthenticationRequest)
                            (IHXAuthenticationManagerResponse* pResponse)
                                                                    throw ();
};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // AuthenticationManager_h

