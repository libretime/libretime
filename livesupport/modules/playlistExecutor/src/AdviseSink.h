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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/AdviseSink.h,v $

------------------------------------------------------------------------------*/
#ifndef AdviseSink_h
#define AdviseSink_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <hxtypes.h>
#include <hxcore.h>
#include <hxmon.h>
#include <hxengin.h>
#include <hxclsnk.h>


namespace LiveSupport {
namespace PlaylistExecutor {

using namespace LiveSupport;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */

/**
 *  A Helix client advise sink, receiving notifications on the status
 *  of the client playing.
 *
 *  @author  $Author: maroy $
 *  @version $Revision: 1.1 $
 */
class AdviseSink : public IHXClientAdviseSink
{
    private:
        /**
         *  The reference count for this object.
         */
        LONG32          lRefCount;

        /**
         *  The pointer to the object this is an advise sink for.
         */
        IUnknown      * pUnknown;

        /**
         *  Pointer to the registry of pUnkown.
         */
        IHXRegistry   * pRegistry;

        /**
         *  Pointer to the scheduler of pUnkown.
         */
        IHXScheduler  * pScheduler;
    
        /**
         *  The time playing is started.
         */
        UINT32          ulStartTime;

        /**
         *  The time playing os stopped.
         */
        UINT32          ulStopTime;
    
 
    public:

        /**
         *  Constructor
         */
        AdviseSink(IUnknown   * pUnknown)               throw ();


        /**
         *  Destructor.
         */
        virtual
        ~AdviseSink(void)                               throw ();

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

        // IHXClientAdviseSink methods

        /**
         *  Called to advise the client that the position or length of the
         *  current playback context has changed.
         *
         *  @param ulPosition the new position of the playback.
         *  @param ulLength the new length of the playback.
         */
        STDMETHOD(OnPosLength) (THIS_
                                UINT32    ulPosition,
                                UINT32    ulLength)     throw ();
    
        /**
         *  Called to advise the client a presentation has been opened.
         */
        STDMETHOD(OnPresentationOpened) (THIS)          throw ();

        /**
         *  Called to advise the client a presentation has been closed.
         */
        STDMETHOD(OnPresentationClosed) (THIS)          throw ();

        /**
         *  Called to advise the client that the presentation statistics
         *  have changed. 
         */
        STDMETHOD(OnStatisticsChanged) (THIS)           throw ();

        /**
         *  Called by client engine to inform the client that a seek is
         *  about to occur. The render is informed the last time for the 
         *  stream's time line before the seek, as well as the first new
         *  time for the stream's time line after the seek will be completed.
         *
         *  @param ulOldTime the end of the stream's time line before the
         *         current seek.
         *  @param ulNewTime the beginning of the stream's time line after the
         *         current seek.
         */
        STDMETHOD (OnPreSeek) (THIS_
                               ULONG32  ulOldTime,
                               ULONG32  ulNewTime)      throw ();

        /**
         *  Called by client engine to inform the client that a seek has
         *  just occured. The render is informed the last time for the 
         *  stream's time line before the seek, as well as the first new
         *  time for the stream's time line after the seek.
         *
         *  @param ulOldTime the end of the stream's time line before the
         *         current seek.
         *  @param ulNewTime the beginning of the stream's time line after the
         *         current seek.
         */
        STDMETHOD (OnPostSeek) (THIS_
                                ULONG32 ulOldTime,
                                ULONG32 ulNewTime)      throw ();

        /**
         *  Called by client engine to inform the client that a stop has
         *  just occured. 
         */
        STDMETHOD (OnStop) (THIS)                       throw ();

        /**
         *  Called by client engine to inform the client that a pause has
         *  just occured. The render is informed the last time for the 
         *  stream's time line before the pause.
         *
         *  @param ulTime the time in the streams time line before being
         *         paused.
         */
        STDMETHOD (OnPause) (THIS_
                             ULONG32 ulTime)            throw ();

        /**
         *  Called by client engine to inform the client that a begin or
         *  resume has just occured. The render is informed the first time 
         *  for the stream's time line after the resume.
         *
         *  The time in the streams time line from which to begin or resume.
         */
        STDMETHOD (OnBegin) (THIS_
                             ULONG32 ulTime)            throw ();

        /**
         *  Called by client engine to inform the client that buffering
         *  of data is occuring. The render is informed of the reason for
         *  the buffering (start-up of stream, seek has occured, network
         *  congestion, etc.), as well as percentage complete of the 
         *  buffering process.
         *
         *  @param ulFlags the reason for the buffering, one of:
         *         BUFFERING_START_UP, BUFFERING_SEEK, BUFFERING_CONGESTION
         *         or BUFFERING_LIVE_PAUSE
         *  @param unPercentComplete the percentage of the buffering that has
         *         completed.
         */
        STDMETHOD (OnBuffering) (THIS_
                                 ULONG32    ulFlags,
                                 UINT16     unPercentComplete)      throw ();


        /**
         *  Called by client engine to inform the client is contacting
         *  hosts(s).
         *
         *  @param pHostName the name of the host being contacted.
         */
        STDMETHOD (OnContacting) (THIS_
                                  const char* pHostName)            throw ();

};


/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace PlaylistExecutor
} // namespace LiveSupport

#endif // AdviseSink_h

