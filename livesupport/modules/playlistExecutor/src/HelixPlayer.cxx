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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/modules/playlistExecutor/src/Attic/HelixPlayer.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include "HelixDefs.h"

#include "HelixPlayer.h"


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


/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string HelixPlayer::configElementNameStr = "helixPlayer";


/**
 *  The name of the attribute to get shared object path.
 */
static const std::string    dllPathName = "dllPath";

/**
 *  The name of the client core shared object, as found under dllPath
 */
static const std::string    clntcoreName = "/clntcore.so";


/* ===============================================  local function prototypes */

/*------------------------------------------------------------------------------
 *  The main thread function for handling Helix events.
 *----------------------------------------------------------------------------*/
void *
LiveSupport::PlaylistExecutor::eventHandlerThread(void   * helixPlayer)
                                                                    throw ()
{
    HelixPlayer   * hPlayer = (HelixPlayer *) helixPlayer;

    while (hPlayer->handleEvents) {
        struct _HXxEvent  * event = 0;
        hPlayer->clientEngine->EventOccurred(event);
        usleep(10000);
    }

    pthread_exit(0);
}


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
    clientContext = new ClientContext();
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
    handleEvents  = true;
    int             ret;
    if ((ret = pthread_create(&eventHandlingThread,
                              NULL,
                              eventHandlerThread,
                              (void *) this))) {
        // TODO: signal return code
        throw std::exception();
    }

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
        handleEvents = false;
        pthread_join(eventHandlingThread, 0);

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
HelixPlayer :: playThis(const std::string   fileUrl)
                                                throw (std::invalid_argument)
{
    // the only way to check if this is a valid URL is to see if the
    // source count increases for the player.
    UINT16  sourceCount = player->GetSourceCount();
    if (HXR_OK != player->OpenURL(fileUrl.c_str())) {
        throw std::invalid_argument("can't open URL");
    }
    if (sourceCount == player->GetSourceCount()) {
        throw std::invalid_argument("can't open URL");
    }
}


/*------------------------------------------------------------------------------
 *  Start playing
 *----------------------------------------------------------------------------*/
void
HelixPlayer :: start(void)                      throw (std::logic_error)
{
    if (player->GetSourceCount() == 0) {
        throw std::logic_error("HelixPlayer::playThis() not called yet");
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
 *  A global function needed by the Helix library, this will return the
 *  access path to shared objects.
 *----------------------------------------------------------------------------*/
DLLAccessPath* GetDLLAccessPath(void)
{
    return &accessPath;
}


