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
 
 
    Author   : $Author: fgerlits $
    Version  : $Revision: 1.12 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixPlayer.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include "LiveSupport/Core/TimeConversion.h"
#include "HelixEventHandlerThread.h"
#include "HelixPlayer.h"
#include <hxausvc.h>


using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */

/**
 *  Function pointer type for the SetDLLAccessPath function.
 */
typedef HX_RESULT (HXEXPORT_PTR FPRMSETDLLACCESSPATH) (const char*);


/* ================================================  local constants & macros */

/**
 *  The shared object access path.
 */
static DLLAccessPath        accessPath;


/**
 *  The name of the config element for this class
 */
const std::string HelixPlayer::configElementNameStr = "helixPlayer";


/**
 *  The name of the attribute to get shared object path.
 */
static const std::string    dllPathName = "dllPath";

/**
 *  The name of the client core shared object, as found under dllPath
 */
static const std::string    clntcoreName = "/clntcore.so";


/**
 *  Magic number #1: max time to wait for an audio stream, in milliseconds
 */
static const int            getAudioStreamTimeOut = 10;

/**
 *  Magic number #2: time to wait after getting crossfade interface, 
 *  and before setting crossfade values, in milliseconds
 */
static const int            crossFadeWaitingTime = 50;


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the Helix Player.
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute;

    if (!(attribute = element.get_attribute(dllPathName))) {
        std::string eMsg = "Missing attribute ";
        eMsg += dllPathName;
        throw std::invalid_argument(eMsg);
    }
    dllPath = attribute->get_value();
}


/*------------------------------------------------------------------------------
 *  Initialize the Helix Player
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: initialize(void)                 throw (std::exception)
{
    if (initialized) {
        return;
    }

    // open the Helix Client Core shared object
    std::string     staticLibPath(dllPath);
    staticLibPath += clntcoreName;
    if (DLLAccess::DLL_OK != dllAccess.open(staticLibPath.c_str())) {
        throw std::exception();
    }

    // get the main entry function pointers
    FPRMSETDLLACCESSPATH    setDLLAccessPath;

    createEngine     = (FPRMCREATEENGINE) dllAccess.getSymbol("CreateEngine");
    closeEngine      = (FPRMCLOSEENGINE) dllAccess.getSymbol("CloseEngine");
    setDLLAccessPath =
                (FPRMSETDLLACCESSPATH) dllAccess.getSymbol("SetDLLAccessPath");

    if (!createEngine || !closeEngine || !setDLLAccessPath) {
        throw std::exception();
    }

    // set the DLL access path
    std::string     str = "";
    str += "DT_Common=";
    str += dllPath;
    str += '\0';
    str += "DT_Plugins=";
    str += dllPath;
    str += '\0';
    str += "DT_Codecs=";
    str += dllPath;

    setDLLAccessPath(str.c_str());

    // create the client engine and the player
    if (HXR_OK != createEngine(&clientEngine)) {
        throw std::exception();
    }

    if (HXR_OK != clientEngine->CreatePlayer(player)) {
        throw std::exception();
    }

    // create and attach the client context
    clientContext = new ClientContext(shared_from_this());
    clientContext->AddRef();

    IHXPreferences    * preferences = 0;
    player->QueryInterface(IID_IHXPreferences, (void**) &preferences);
    clientContext->Init(player, preferences, "");
    player->SetClientContext(clientContext);
    HX_RELEASE(preferences);

    // create and attach the error sink
    IHXErrorSinkControl   * errorSinkControl;
    player->QueryInterface(IID_IHXErrorSinkControl,
                           (void**) &errorSinkControl);
    if (errorSinkControl) {
        IHXErrorSink          * errorSink;
        clientContext->QueryInterface(IID_IHXErrorSink, (void**) &errorSink);
        if (errorSink) {
            errorSinkControl->AddErrorSink(errorSink,
                                           HXLOG_EMERG,
                                           HXLOG_DEBUG);
            HX_RELEASE(errorSink);
        }
        HX_RELEASE(errorSinkControl);
    }

    // start the event handling thread
    Ptr<time_duration>::Ref   granurality(new time_duration(microseconds(10)));
    Ptr<RunnableInterface>::Ref handler(new HelixEventHandlerThread(
                                                            clientEngine,
                                                            granurality));
    eventHandlerThread.reset(new Thread(handler));
    eventHandlerThread->start();

    // set up other variables
    playing     = false;
    initialized = true;
}


/*------------------------------------------------------------------------------
 *  De-initialize the Helix Player
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: deInitialize(void)                       throw ()
{
    if (initialized) {
        // signal stop to and wait for the event handling thread to stop
        eventHandlerThread->stop();
        eventHandlerThread->join();

        // release Helix resources
        clientContext->Release();

        clientEngine->ClosePlayer(player);
        player->Release();

        closeEngine(clientEngine);

        dllAccess.close();

        initialized = false;
    }
}


/*------------------------------------------------------------------------------
 *  Specify which file to play
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: open(const std::string   fileUrl)
                                                throw (std::invalid_argument)
{
    playlength = 0UL;
    // the only way to check if this is a valid URL is to see if the
    // source count increases for the player.
    UINT16  sourceCount = player->GetSourceCount();
    if (HXR_OK != player->OpenURL(fileUrl.c_str())) {
        throw std::invalid_argument("can't open URL");
    }
    if (sourceCount == player->GetSourceCount()) {
        throw std::invalid_argument("can't open URL successfully");
    }
}


/*------------------------------------------------------------------------------
 *  Get the length of the current audio clip.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
HelixPlayer :: getPlaylength(void)                      throw ()
{
    Ptr<time_duration>::Ref   length;

    // only bother if there is something to check for.
    if (player->GetSourceCount() > 0) {
        Ptr<time_duration>::Ref   sleepT(new time_duration(microseconds(100)));

        // wait until the playlength is set to a sensible value
        // by the advise sink. this may take a while
        while (playlength == 0) {
            TimeConversion::sleep(sleepT);
        }

        unsigned long       secs      = playlength / 1000UL;
        unsigned long       millisecs = playlength - (secs * 1000UL);
        length.reset(new time_duration(seconds(secs) + millisec(millisecs)));
    }

    return length;
}


/*------------------------------------------------------------------------------
 *  Start playing
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: start(void)                      throw (std::logic_error)
{
    if (player->GetSourceCount() == 0) {
        throw std::logic_error("HelixPlayer::open() not called yet");
    }
    player->Begin();
    playing = true;
}


/*------------------------------------------------------------------------------
 *  Tell if we're playing
 *----------------------------------------------------------------------------*/
bool
HelixPlayer :: isPlaying(void)                  throw ()
{
    if (playing) {
        playing = !player->IsDone();
    }

    return playing;
}


/*------------------------------------------------------------------------------
 *  Stop playing
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: stop(void)                       throw (std::logic_error)
{
    if (!isPlaying()) {
        throw std::logic_error("HelixPlayer is not yet playing, can't stop it");
    }
    player->Stop();

    playing = false;
    // TODO: gather error info from the ErrorSink
}
 

/*------------------------------------------------------------------------------
 *  Close the currently opened audio file.
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: close(void)                       throw ()
{
    if (isPlaying()) {
        stop();
    } else {
        // else, call IHXPlayer->Stop(), to clean up things...
        player->Stop();
    }
}


/*------------------------------------------------------------------------------
 *  Get the volume of the player.
 *----------------------------------------------------------------------------*/
unsigned int
HelixPlayer :: getVolume(void)                                  throw ()
{
    IHXAudioPlayer    * audioPlayer = 0;
    player->QueryInterface(IID_IHXAudioPlayer, (void**) &audioPlayer);
    if (!audioPlayer) {
        std::cerr << "can't get IHXAudioPlayer interface" << std::endl;
        return 0;
    }

    IHXVolume * ihxVolume = audioPlayer->GetAudioVolume();
    return ihxVolume->GetVolume();
}


/*------------------------------------------------------------------------------
 *  Set the volume of the player.
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: setVolume(unsigned int   volume)                 throw ()
{
    IHXAudioPlayer    * audioPlayer = 0;
    player->QueryInterface(IID_IHXAudioPlayer, (void**) &audioPlayer);
    if (!audioPlayer) {
        std::cerr << "can't get IHXAudioPlayer interface" << std::endl;
        return;
    }

    IHXVolume * ihxVolume = audioPlayer->GetAudioVolume();
    ihxVolume->SetVolume(volume);
}


/*------------------------------------------------------------------------------
 *  A global function needed by the Helix library, this will return the
 *  access path to shared objects.
 *----------------------------------------------------------------------------*/
DLLAccessPath* GetDLLAccessPath(void)
{
    return &accessPath;
}


/*------------------------------------------------------------------------------
 *  Play a playlist, with simulated fading.
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: openAndStartPlaylist(Ptr<Playlist>::Ref  playlist)       
                                                throw (std::invalid_argument,
                                                       std::logic_error,
                                                       std::runtime_error)
{
    if (!playlist || !playlist->getUri()) {
        throw std::invalid_argument("no playlist SMIL file found");
    }
    open(*playlist->getUri());      // may throw invalid_argument

    bool    hasFadeInfo = false;
    int     numberOfPlaylistElements = 0;
    Playlist::const_iterator    it = playlist->begin();
    while (it != playlist->end()) {
        ++numberOfPlaylistElements;
        if (it->second->getFadeInfo()) {
            hasFadeInfo = true;
        }
        ++it;
    }

    start();                        // may throw logic_error
    if (!numberOfPlaylistElements || !hasFadeInfo) {
        return;
    }

    IHXAudioPlayer* audioPlayer = 0;
    if (player->QueryInterface(IID_IHXAudioPlayer, (void**)&audioPlayer)
                                                        != HXR_OK
            || !audioPlayer) {
        throw std::runtime_error("can't get IHXAudioPlayer interface");
    }

    IHXAudioCrossFade* crossFade = 0;
    if (audioPlayer->QueryInterface(IID_IHXAudioCrossFade, (void**)&crossFade)
                                                            != HXR_OK
            || !crossFade) {
        throw std::runtime_error("can't get IHXAudioCrossFade interface");
    }

    Ptr<time_duration>::Ref sleepT(new time_duration(microseconds(10)));

    IHXAudioStream*     audioStream[numberOfPlaylistElements + 1];
    for (int i = 0; i < numberOfPlaylistElements; i++) {
        int j = 0;
        do {
            TimeConversion::sleep(sleepT);
            audioStream[i] = audioPlayer->GetAudioStream(i);
            ++j;
            if (j > getAudioStreamTimeOut * 100) {
                std::stringstream   eMsg;
                eMsg << "can't get audio stream number " << i;
                throw std::runtime_error(eMsg.str());
            }
        } while (!audioStream[i]);
    }
    audioStream[numberOfPlaylistElements] = 0;  // fade out last clip into 0

    it = playlist->begin();
    
    sleepT.reset(new time_duration(milliseconds(crossFadeWaitingTime)));
    TimeConversion::sleep(sleepT);

    for (int i = 0; i < numberOfPlaylistElements; i++) {

        Ptr<PlaylistElement>::Ref   playlistElement = it->second;
        if (!playlistElement->getFadeInfo()) {
            ++it;
            continue;
        }
        
        // we assume i-th fade out is the same as (i+1)-st fade in
        unsigned long   crossFadeLength = playlistElement
                                                ->getFadeInfo()
                                                ->getFadeOut()
                                                ->total_milliseconds();
        unsigned long   fadeOutAt   = playlistElement->getRelativeOffset()
                                                     ->total_milliseconds()
                                    + playlistElement->getPlayable()
                                                     ->getPlaylength()
                                                     ->total_milliseconds()
                                    - crossFadeLength;

        if (crossFadeLength) {
//std::cerr << "fadeOutAt: " << fadeOutAt << "\n"
//          << "crossFadeLength: " << crossFadeLength << "\n";
            crossFade->CrossFade(audioStream[i], audioStream[i+1], 
                                fadeOutAt, fadeOutAt, crossFadeLength);
        }
    
        ++it;
    }

    for (int i = 0; i < numberOfPlaylistElements; i++) {
        HX_RELEASE(audioStream[i]);
    }
    HX_RELEASE(crossFade);
    HX_RELEASE(audioPlayer);
}

