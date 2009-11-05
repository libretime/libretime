/*------------------------------------------------------------------------------

    Copyright (c) 2004 Media Development Loan Fund
 
    This file is part of the Campcaster project.
    http://campcaster.campware.org/
    To report bugs, send an e-mail to bugs@campware.org
 
    Campcaster is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
  
    Campcaster is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with Campcaster; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 
 
    Author   : $Author: Kapil Agrawal$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#define DEBUG_PREFIX "GstreamerPlayer"
#include "LiveSupport/Core/Debug.h"

#include "LiveSupport/Core/TimeConversion.h"

#include "GstreamerPlayer.h"


using namespace boost::posix_time;
using namespace LiveSupport::Core;
using namespace LiveSupport::PlaylistExecutor;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/**
 *  The name of the config element for this class
 */
const std::string GstreamerPlayer::configElementNameStr = "gstreamerPlayer";

/**
 *  The name of the audio device attribute.
 */
static const std::string    audioDeviceName = "audioDevice";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

static gboolean my_bus_callback (GstBus *bus, GstMessage *message, gpointer data)
{

    GstreamerPlayer* const player = (GstreamerPlayer*) data;

    switch (GST_MESSAGE_TYPE (message)) {
		//we shall handle errors as non critical events as we should not stop playback in any case
        case GST_MESSAGE_ERROR:
        case GST_MESSAGE_EOS:
            if(player->playNextSmil()){
                break;
            }
            player->close();
			// Important: We *must* use an idle function call here, so that the signal handler returns 
			// before fireOnStopEvent() is executed.
			g_idle_add(GstreamerPlayer::fireOnStopEvent, data);
            break;
    }
}


/*------------------------------------------------------------------------------
 *  Configure the Audio Player.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: configure(const xmlpp::Element   &  element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    DEBUG_FUNC_INFO

    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    const xmlpp::Attribute    * attribute = 0;

    if ((attribute = element.get_attribute(audioDeviceName))) {
        m_audioDevice = attribute->get_value();
    }
}


/*------------------------------------------------------------------------------
 *  Initialize the Audio Player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: initialize(void)                 throw (std::exception)
{
    DEBUG_FUNC_INFO

    if (m_initialized) {
        return;
    }

    // initialize the gstreamer library
    if (!gst_init_check(0, 0, 0)) {
        throw std::runtime_error("couldn't initialize the gstreamer library");
    }

    m_playContext = new GstreamerPlayContext();

    m_playContext->setParentData(this);

    // set up other variables
    m_initialized = true;
}


/*------------------------------------------------------------------------------
 *  De-initialize the Gstreamer Player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: deInitialize(void)                       throw ()
{
    DEBUG_FUNC_INFO

    if (m_initialized) {
        m_playContext->closeContext();
        delete m_playContext;
        m_playContext = NULL;
        m_initialized = false;
    }
}

/*------------------------------------------------------------------------------
 *  Attach an event listener.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: attachListener(AudioPlayerEventListener*     eventListener)
                                                                    throw ()
{
    m_listeners.push_back(eventListener);
}


/*------------------------------------------------------------------------------
 *  Detach an event listener.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: detachListener(AudioPlayerEventListener*     eventListener)
                                                throw (std::invalid_argument)
{
    ListenerVector::iterator    it  = m_listeners.begin();
    ListenerVector::iterator    end = m_listeners.end();

    while (it != end) {
        if (*it == eventListener) {
            m_listeners.erase(it);
            return;
        }
        ++it;
    }

    throw std::invalid_argument("supplied event listener not found");
}


/*------------------------------------------------------------------------------
 *  Send the onStop event to all attached listeners.
 *----------------------------------------------------------------------------*/
gboolean
GstreamerPlayer :: fireOnStopEvent(gpointer self)                       throw ()
{
    DEBUG_BLOCK


    GstreamerPlayer* const player = (GstreamerPlayer*) self;

    ListenerVector::iterator    it  = player->m_listeners.begin();
    ListenerVector::iterator    end = player->m_listeners.end();
    while (it != end) {
        (*it)->onStop(player->m_errorMessage);
        ++it;
    }

    player->m_errorMessage.reset();

    // false == Don't call this idle function again
    return false;
}

/*------------------------------------------------------------------------------
 *  Send the onStart event to all attached listeners.
 *----------------------------------------------------------------------------*/
gboolean
GstreamerPlayer :: fireOnStartEvent(gpointer self)                       throw ()
{
    DEBUG_BLOCK


    GstreamerPlayer* const player = (GstreamerPlayer*) self;
	
    ListenerVector::iterator    it  = player->m_listeners.begin();
    ListenerVector::iterator    end = player->m_listeners.end();
    while (it != end) {
        (*it)->onStart(player->m_Id);
        ++it;
    }

    // false == Don't call this idle function again
    return false;
}


/*------------------------------------------------------------------------------
 * Preload a file, to speed up the subsequent open() call. 
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: preload(const std::string   fileUrl)
                                                throw (std::invalid_argument,
                                                       std::runtime_error)
{
    DEBUG_BLOCK
    //According to the Gstreamer documentation, stream buffering happens
    //automatically when the pipeline is set to GST_STATE_PAUSED.
    //As this state is now set automatically in the open function,
    //we no longer have a need for preloading.
}


/*------------------------------------------------------------------------------
 *  Specify which file to play
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: open(const std::string   fileUri, gint64 id, gint64 offset)
										throw (std::invalid_argument, std::runtime_error)
{
    DEBUG_BLOCK

    if (isOpen()) {
        close();
    }
    
    m_smilOffset = 0L;

    debug() << "Opening URL: " << fileUri << endl;
    debug() << "Timestamp: " << *TimeConversion::now() << endl;

    m_errorMessage.reset();
    m_errorWasRaised = false;

    m_playContext->setAudioDevice(m_audioDevice);
    if (fileUri.find(std::string(".smil")) != std::string::npos) {
        m_smilHandler = new SmilHandler();
        m_smilHandler->openSmilFile(fileUri.c_str(), offset);
        AudioDescription *audioDescription = m_smilHandler->getNext();
		gint64 clipOffset = m_smilHandler->getClipOffset();
        m_playContext->setClipOffset(clipOffset);
		m_Id = audioDescription->m_Id;
        m_open=m_playContext->openSource(audioDescription);
		m_url = (const char*) audioDescription->m_src;
    }else{
        m_open=m_playContext->openSource(fileUri.c_str());
		m_url = fileUri;
		m_Id = id;
    }

    if(!m_open){
	  close();
	  deInitialize();
	  initialize();
	  m_playContext->forceEOS();
	  return false;
    }
	g_idle_add(GstreamerPlayer::fireOnStartEvent, this);
	return true;
}

bool 
GstreamerPlayer :: playNextSmil(void)                                    throw ()
{
    DEBUG_BLOCK
	if(NULL == m_playContext)
	{
		return false;
	}
    m_playContext->closeContext();
    if(m_smilHandler == NULL){
        return false;
    }
    AudioDescription *audioDescription = m_smilHandler->getNext();
    if(audioDescription == NULL){//no more audio entries to play
        delete m_smilHandler;
        m_smilHandler = NULL;
        return false;
    }
    if(!m_playContext->openSource(audioDescription)){
		m_playContext->stopContext();
		m_playContext->closeContext();
		m_open            = false;
		deInitialize();
		initialize();
		m_playContext->forceEOS();
        return true;
    }
	m_Id = audioDescription->m_Id;
	m_url = (const char*) audioDescription->m_src;
	g_idle_add(GstreamerPlayer::fireOnStartEvent, this);
	m_smilOffset = audioDescription->m_begin;
    m_playContext->playContext();
    return true;
}


/*------------------------------------------------------------------------------
 *  Tell if we've been opened.
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: isOpen(void)                                 throw ()
{
    return m_open;
}


/*------------------------------------------------------------------------------
 *  Get the length of the current audio clip.
 *  Currently not used by the Studio, but may be used later on.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
GstreamerPlayer :: getPlaylength(void)              throw (std::logic_error)
{
    DEBUG_BLOCK
    
    if (!isOpen()) {
        throw std::logic_error("player not open");
    }
    
    Ptr<time_duration>::Ref   length;
    
    gint64 ns = m_playContext->getPlayLength();
 
    length.reset(new time_duration(microsec(ns / 1000LL)));

    debug() << "playlength is: " << *length << endl; 
    return length;
}


/*------------------------------------------------------------------------------
 *  Get the current position of the current audio clip.
 *----------------------------------------------------------------------------*/
Ptr<time_duration>::Ref
GstreamerPlayer :: getPosition(void)                throw (std::logic_error)
{
    Ptr<time_duration>::Ref   length;

    if (!isOpen()) {
        throw std::logic_error("player not open");
    }

    gint64 ns = m_playContext->getPosition();
    length.reset(new time_duration(microseconds((m_smilOffset + ns) / 1000LL)));

    return length;
}

/*------------------------------------------------------------------------------
 *  Start playing
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: start()                      throw (std::logic_error)
{
    DEBUG_BLOCK
    if (!isOpen()) {
        throw std::logic_error("GstreamerPlayer not opened yet");
    }

    if (!isPlaying()) {
        m_playContext->playContext();
    }else{
        error() << "Already playing!" << endl;
    }
}


/*------------------------------------------------------------------------------
 *  Pause the player
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: pause(void)                      throw (std::logic_error)
{
    DEBUG_BLOCK

    if (isPlaying()) {
        m_playContext->pauseContext();
    }
}


/*------------------------------------------------------------------------------
 *  Tell if we're playing
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: isPlaying(void)                  throw ()
{
    return m_playContext->isPlaying();
}


/*------------------------------------------------------------------------------
 *  Stop playing
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: stop(void)                       throw (std::logic_error)
{
    DEBUG_BLOCK

    if (!isOpen()) {
        throw std::logic_error("GstreamerPlayer not opened yet");
    }

    if (isPlaying()) {
        m_playContext->stopContext();
    }
}
 

/*------------------------------------------------------------------------------
 *  Close the currently opened audio file.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: close(void)                       throw (std::logic_error)
{
    DEBUG_BLOCK
    m_playContext->stopContext();
    m_playContext->closeContext();
    if(m_smilHandler != NULL){
        delete m_smilHandler;
        m_smilHandler = NULL;
    }
    m_open            = false;
}


/*------------------------------------------------------------------------------
 *  Get the volume of the player. *Unimplemented*: Feature is currently not used.
 *----------------------------------------------------------------------------*/
unsigned int
GstreamerPlayer :: getVolume(void)                                  throw ()
{
    return 0;
}


/*------------------------------------------------------------------------------
 *  Set the volume of the player. *Unimplemented*: Feature is currently not used.
 *----------------------------------------------------------------------------*/
void
GstreamerPlayer :: setVolume(unsigned int   volume)                 throw ()
{}


/*------------------------------------------------------------------------------
 *  Set the audio device.
 *----------------------------------------------------------------------------*/
bool
GstreamerPlayer :: setAudioDevice(const std::string &deviceName)       
                                                                throw ()
{
    DEBUG_BLOCK

    debug() << "Using device: " << deviceName << endl;

    if (deviceName.size() == 0) {
        return false;
    }

    m_playContext->setAudioDevice(deviceName);

    return true;
}

