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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/AuthenticationManager.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include <hxcom.h>

#include "AuthenticationManager.h"

using namespace LiveSupport::PlaylistExecutor;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the Authentication manager
 *----------------------------------------------------------------------------*/
AuthenticationManager::AuthenticationManager()              throw ()
    : lRefCount(0)
{
}


/*------------------------------------------------------------------------------
 *  Destruct the Authentication manager
 *----------------------------------------------------------------------------*/
AuthenticationManager::~AuthenticationManager()             throw ()
{
}


// IUnknown methods

/*------------------------------------------------------------------------------
 *  Implement this to export the interfaces supported by your 
 *  object.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AuthenticationManager::QueryInterface(REFIID    riid,
                                      void   ** ppvObj)         throw ()
{
    if (IsEqualIID(riid, IID_IUnknown)) {
        AddRef();
        *ppvObj = (IUnknown*)(IHXAuthenticationManager*)this;
        return HXR_OK;
    } else if (IsEqualIID(riid, IID_IHXAuthenticationManager)) {
        AddRef();
        *ppvObj = (IHXAuthenticationManager*)this;
        return HXR_OK;
    }

    *ppvObj = NULL;
    return HXR_NOINTERFACE;
}


/*------------------------------------------------------------------------------
 *  Increase the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(UINT32)
AuthenticationManager::AddRef()                                 throw ()
{
    return InterlockedIncrement(&lRefCount);
}


/*------------------------------------------------------------------------------
 *  Decreaese the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(UINT32)
AuthenticationManager::Release()                                throw ()
{
    if (InterlockedDecrement(&lRefCount) > 0) {
        return lRefCount;
    }

    delete this;
    return 0;
}


/*------------------------------------------------------------------------------
 *  Handle an authentication request.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AuthenticationManager::HandleAuthenticationRequest(
                        IHXAuthenticationManagerResponse* pResponse)
                                                                throw ()
{
    // pass on anything
    return HXR_OK;
}

