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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/ErrorSink.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include <hxcom.h>

#include "ErrorSink.h"

using namespace LiveSupport::PlaylistExecutor;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the Error Sink
 *----------------------------------------------------------------------------*/
ErrorSink::ErrorSink(IUnknown* pUnknown)                throw ()
    : lRefCount(0),
      pPlayer(NULL),
      lastHelixErrorCode(0)
{
    IHXClientEngine* pEngine = NULL;
    pUnknown->QueryInterface(IID_IHXClientEngine, (void**)&pEngine );
    if( pEngine ) {
        IUnknown* pTmp = NULL;
        pEngine->GetPlayer(0, pTmp);
        pPlayer = (IHXPlayer*)pTmp;
    }
    
    HX_RELEASE( pEngine );
//    HX_ASSERT(pPlayer);
}


/*------------------------------------------------------------------------------
 *  Destruct the Error Sink
 *----------------------------------------------------------------------------*/
ErrorSink::~ErrorSink()                                         throw ()
{
    HX_RELEASE(pPlayer);
}

// IUnknown methods

/*------------------------------------------------------------------------------
 *  Implement this to export the interfaces supported by your 
 *  object.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
ErrorSink::QueryInterface(REFIID    riid,
                          void   ** ppvObj)                     throw ()
{
    if (IsEqualIID(riid, IID_IUnknown)) {
        AddRef();
        *ppvObj = (IUnknown*)(IHXErrorSink*)this;
        return HXR_OK;
    } else if (IsEqualIID(riid, IID_IHXErrorSink)) {
        AddRef();
        *ppvObj = (IHXErrorSink*) this;
        return HXR_OK;
    }

    *ppvObj = NULL;
    return HXR_NOINTERFACE;
}


/*------------------------------------------------------------------------------
 *  Increase the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
ErrorSink::AddRef()                                             throw ()
{
    return InterlockedIncrement(&lRefCount);
}


/*------------------------------------------------------------------------------
 *  Decreaese the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
ErrorSink::Release()                                            throw ()
{
    if (InterlockedDecrement(&lRefCount) > 0) {
        return lRefCount;
    }

    delete this;
    return 0;
}

// IHXErrorSink methods

/*------------------------------------------------------------------------------
 *  Handle an error event.
 *----------------------------------------------------------------------------*/
STDMETHODIMP 
ErrorSink::ErrorOccurred(const UINT8    unSeverity,  
                         const ULONG32  ulHXCode,
                         const ULONG32  ulUserCode,
                         const char   * pUserString,
                         const char   * pMoreInfoURL)           throw ()
{
    lastHelixErrorCode = ulHXCode;

    return HXR_OK;
}

