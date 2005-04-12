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
    Version  : $Revision: 1.4 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/AdviseSink.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include <hxcom.h>

#include "HelixPlayer.h"
#include "AdviseSink.h"

using namespace LiveSupport::PlaylistExecutor;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Construct the advise sink
 *----------------------------------------------------------------------------*/
AdviseSink::AdviseSink(IUnknown               * pUnknown,
                       Ptr<HelixPlayer>::Ref    helixPlayer)            throw ()
    : lRefCount(0)
    , pUnknown(NULL)
    , pRegistry(NULL)
    , pScheduler(NULL)
{
    this->helixPlayer = helixPlayer;

    if (pUnknown) {
        this->pUnknown = pUnknown;
        this->pUnknown->AddRef();

        if (HXR_OK != this->pUnknown->QueryInterface(IID_IHXRegistry,
                                                     (void**)&pRegistry)) {
            pRegistry = NULL;
        }

        if (HXR_OK != this->pUnknown->QueryInterface(IID_IHXScheduler,
                                                     (void**)&pScheduler)) {
            pScheduler = NULL;
        }

        IHXPlayer* pPlayer;
        if (HXR_OK == this->pUnknown->QueryInterface(IID_IHXPlayer,
                                                     (void**)&pPlayer)) {
            pPlayer->AddAdviseSink(this);
            pPlayer->Release();
        }
    }
}


/*------------------------------------------------------------------------------
 *  Destruct the advise sink
 *----------------------------------------------------------------------------*/
AdviseSink::~AdviseSink(void)                       throw ()
{
    if (pScheduler) {
        pScheduler->Release();
        pScheduler = NULL;
    }

    if (pRegistry) {
        pRegistry->Release();
        pRegistry = NULL;
    }

    if (pUnknown) {
        pUnknown->Release();
        pUnknown = NULL;
    }
}


// IUnknown methods

/*------------------------------------------------------------------------------
 *  Implement this to export the interfaces supported by your 
 *  object.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::QueryInterface(REFIID   riid,
                           void  ** ppvObj)             throw ()
{
    if (IsEqualIID(riid, IID_IUnknown)) {
        AddRef();
        *ppvObj = (IUnknown*)(IHXClientAdviseSink*)this;
        return HXR_OK;
    } else if (IsEqualIID(riid, IID_IHXClientAdviseSink)) {
        AddRef();
        *ppvObj = (IHXClientAdviseSink*)this;
        return HXR_OK;
    }

    *ppvObj = NULL;
    return HXR_NOINTERFACE;
}


/*------------------------------------------------------------------------------
 *  Increase the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
AdviseSink::AddRef()                                    throw ()
{
    return InterlockedIncrement(&lRefCount);
}


/*------------------------------------------------------------------------------
 *  Decreaese the refence count.
 *----------------------------------------------------------------------------*/
STDMETHODIMP_(ULONG32)
AdviseSink::Release()                                   throw ()
{
    if (InterlockedDecrement(&lRefCount) > 0) {
        return lRefCount;
    }

    delete this;
    return 0;
}


//    IHXClientAdviseSink methods

/*------------------------------------------------------------------------------
 *  Called to advise the client that the position or length of the
 *  current playback context has changed.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPosLength(UINT32      ulPosition,
                        UINT32      ulLength)               throw ()
{
    helixPlayer->setPlaylength(ulLength);
    try {
        helixPlayer->implementFading(ulPosition);
    } catch (std::runtime_error) {
        // TODO: mark error; log it somewhere, maybe?
    }
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called to advise the client a presentation has been opened.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPresentationOpened()                          throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called to advise the client a presentation has been closed.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPresentationClosed()                          throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called to advise the client that the presentation statistics
 *  have changed. 
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnStatisticsChanged(void)                       throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that a seek is
 *  about to occur. The render is informed the last time for the 
 *  stream's time line before the seek, as well as the first new
 *  time for the stream's time line after the seek will be completed.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPreSeek(ULONG32    ulOldTime,
                      ULONG32    ulNewTime)                 throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that a seek has
 *  just occured. The render is informed the last time for the 
 *  stream's time line before the seek, as well as the first new
 *  time for the stream's time line after the seek.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPostSeek(ULONG32    ulOldTime,
                       ULONG32    ulNewTime)                throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that a stop has
 *  just occured. 
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnStop(void)                                    throw ()
{
    HXTimeval now;

    // Find out the current time and subtract the beginning time to
    // figure out how many seconds we played
    now = pScheduler->GetCurrentSchedulerTime();
    ulStopTime = now.tv_sec;

    helixPlayer->fireOnStopEvent();

// TODO: maybe save the number of seconds played?
//    GetGlobal()->g_ulNumSecondsPlayed = ulStopTime - ulStartTime;

    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that a pause has
 *  just occured. The render is informed the last time for the 
 *  stream's time line before the pause.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnPause(ULONG32 ulTime)                         throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that a begin or
 *  resume has just occured. The render is informed the first time 
 *  for the stream's time line after the resume.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnBegin(ULONG32 ulTime)                         throw ()
{
    HXTimeval now;

    // Record the current time, so we can figure out many seconds we played
    now = pScheduler->GetCurrentSchedulerTime();
    ulStartTime = now.tv_sec;

    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client that buffering
 *  of data is occuring. The render is informed of the reason for
 *  the buffering (start-up of stream, seek has occured, network
 *  congestion, etc.), as well as percentage complete of the 
 *  buffering process.
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnBuffering(ULONG32    ulFlags,
                        UINT16    unPercentComplete)            throw ()
{
    return HXR_OK;
}


/*------------------------------------------------------------------------------
 *  Called by client engine to inform the client is contacting
 *  hosts(s).
 *----------------------------------------------------------------------------*/
STDMETHODIMP
AdviseSink::OnContacting(const char* pHostName)                 throw ()
{
    return HXR_OK;
}

