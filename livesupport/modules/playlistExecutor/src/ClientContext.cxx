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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/ClientContext.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include <hxbuffer.h>
#include <hxmangle.h>
#include <hxstrutl.h>

#include "ErrorSink.h"
#include "AuthenticationManager.h"
#include "ClientContext.h"
#include "HelixPlayer.h"

using namespace LiveSupport::PlaylistExecutor;



/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the Client Context
 *----------------------------------------------------------------------------*/
ClientContext::ClientContext(Ptr<HelixPlayer>::Ref  helixPlayer)    throw ()
    : lRefCount(0)
    , pClientSink(NULL)
    , pErrorSink(NULL)
    , pAuthMgr(NULL)
    , pDefaultPrefs(NULL)
{
    this->helixPlayer = helixPlayer;
}


/*------------------------------------------------------------------------------
 *  Destruct the Client Context
 *----------------------------------------------------------------------------*/
ClientContext::~ClientContext()                             throw ()
{
    Close();
};


/*------------------------------------------------------------------------------
 *  Initialize the Client Context
 *----------------------------------------------------------------------------*/
void
ClientContext::Init(IUnknown         * pUnknown,
                    IHXPreferences   * pPreferences,
                    char             * pszGUID)             throw ()
{
    char* pszCipher = NULL;

    pClientSink    = new AdviseSink(pUnknown, helixPlayer);
    pErrorSink     = new ErrorSink(pUnknown);
    pAuthMgr       = new AuthenticationManager();

    if (pClientSink) {
        pClientSink->AddRef();
    }
    
    if (pErrorSink) {
        pErrorSink->AddRef();
    }

    if(pAuthMgr) {
        pAuthMgr->AddRef();
    }

    if (pPreferences) {
        pDefaultPrefs = pPreferences;
        pDefaultPrefs->AddRef();
    }

    if (pszGUID && *pszGUID) {
        // Encode GUID
        pszCipher = Cipher(pszGUID);
        SafeStrCpy(this->pszGUID,  pszCipher, 256);
    } else {
        this->pszGUID[0] = '\0';
    }
}


/*------------------------------------------------------------------------------
 *  De-Initialize the Client Context
 *----------------------------------------------------------------------------*/
void ClientContext::Close()                                     throw ()
{
    HX_RELEASE(pClientSink);
    HX_RELEASE(pErrorSink);
    HX_RELEASE(pAuthMgr);
    HX_RELEASE(pDefaultPrefs);
}


// IUnknown methods

/*------------------------------------------------------------------------------
 *  Implement this to export the interfaces supported by your 
 *  object.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
ClientContext::QueryInterface(REFIID    riid,
                              void   ** ppvObj)                 throw ()
{
    if (IsEqualIID(riid, IID_IUnknown)) {
        AddRef();
        *ppvObj = this;
        return HXR_OK;
    } else if (IsEqualIID(riid, IID_IHXPreferences)) {
        AddRef();
        *ppvObj = (IHXPreferences*)this;
        return HXR_OK;
    } else if (pClientSink && 
               pClientSink->QueryInterface(riid, ppvObj) == HXR_OK) {
        pClientSink->AddRef();
        *ppvObj = pClientSink;
        return HXR_OK;
    } else if (pErrorSink && 
               pErrorSink->QueryInterface(riid, ppvObj) == HXR_OK) {
        pErrorSink->AddRef();
        *ppvObj = pErrorSink;
        return HXR_OK;
    } else if (pAuthMgr &&
               pAuthMgr->QueryInterface(riid, ppvObj) == HXR_OK) {
        pErrorSink->AddRef();
        *ppvObj = pAuthMgr;
        return HXR_OK;
    }

    *ppvObj = NULL;
    return HXR_NOINTERFACE;
}


/*------------------------------------------------------------------------------
 *  Increase the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
ClientContext::AddRef()                                         throw ()
{
    return InterlockedIncrement(&lRefCount);
}


/*------------------------------------------------------------------------------
 *  Decreaese the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
ClientContext::Release()                                        throw ()
{
    if (InterlockedDecrement(&lRefCount) > 0) {
        return lRefCount;
    }

    delete this;
    return 0;
}


// IHXPreferences methods

/*------------------------------------------------------------------------------
 *  Read a Preference from the registry.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
ClientContext::ReadPref(const char    * pPrekKey,
                        IHXBuffer     *& pBuffer)               throw ()
{
    HX_RESULT   hResult   = HXR_OK;
    
    if ((stricmp(pPrekKey, CLIENT_GUID_REGNAME) == 0) && (*pszGUID)) {
        // Create a Buffer 
        pBuffer = new CHXBuffer();
        pBuffer->AddRef();

        // Copy the encoded GUID into the pBuffer
        pBuffer->Set((UCHAR*)pszGUID, strlen(pszGUID) + 1);
    } else if (pDefaultPrefs) {
        hResult = pDefaultPrefs->ReadPref(pPrekKey, pBuffer);
    } else {
        hResult = HXR_NOTIMPL;
    }

    return hResult;
}


/*------------------------------------------------------------------------------
 *  Write a Preference to the registry.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
ClientContext::WritePref(const char   * pPrekKey,
                         IHXBuffer    * pBuffer)                throw ()
{
    if (pDefaultPrefs) {
        return pDefaultPrefs->WritePref(pPrekKey, pBuffer);
    } else    {
        return HXR_OK;
    }
}


