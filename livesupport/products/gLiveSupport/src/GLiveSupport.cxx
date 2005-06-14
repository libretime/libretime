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
    Version  : $Revision: 1.50 $
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/GLiveSupport.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <stdexcept>
#include <gtkmm/main.h>

#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/Storage/StorageClientFactory.h"
#include "LiveSupport/SchedulerClient/SchedulerClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"
#include "LiveSupport/Core/TimeConversion.h"

#include "MasterPanelWindow.h"
#include "GLiveSupport.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;
using namespace LiveSupport::SchedulerClient;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;


/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string LiveSupport :: GLiveSupport ::
                  GLiveSupport :: configElementNameStr = "gLiveSupport";

/*------------------------------------------------------------------------------
 *  The name of the config element for the list of supported languages
 *----------------------------------------------------------------------------*/
static const std::string supportedLanguagesElementName = "supportedLanguages";

/*------------------------------------------------------------------------------
 *  The name of the config element for a supported language.
 *----------------------------------------------------------------------------*/
static const std::string languageElementName = "language";

/*------------------------------------------------------------------------------
 *  The name of the attribute for the locale id for a supported language
 *----------------------------------------------------------------------------*/
static const std::string localeAttrName = "locale";

/*------------------------------------------------------------------------------
 *  The name of the attribute for the name for a supported language
 *----------------------------------------------------------------------------*/
static const std::string nameAttrName = "name";

/*------------------------------------------------------------------------------
 *  The name of the config element for the sound output player
 *----------------------------------------------------------------------------*/
static const std::string outputPlayerElementName = "outputPlayer";

/*------------------------------------------------------------------------------
 *  The name of the config element for the sound cue player
 *----------------------------------------------------------------------------*/
static const std::string cuePlayerElementName = "cuePlayer";

/*------------------------------------------------------------------------------
 *  The name of the config element for the station logo image
 *----------------------------------------------------------------------------*/
static const std::string stationLogoConfigElementName = "stationLogo";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing Scratchpad contents
 *----------------------------------------------------------------------------*/
static const std::string scratchpadContentsKey = "scratchpadContents";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the scheduler not available key
 *----------------------------------------------------------------------------*/
static const std::string schedulerNotReachableKey = "schedulerNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the storage not available key
 *----------------------------------------------------------------------------*/
static const std::string storageNotReachableKey = "storageNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the authentication not available key
 *----------------------------------------------------------------------------*/
static const std::string authenticationNotReachableKey =
                                            "authenticationNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the locale not available key
 *----------------------------------------------------------------------------*/
static const std::string localeNotAvailableKey = "localeNotAvailableMsg";


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Configure the gLiveSupport object
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error)
{
    if (element.get_name() != configElementNameStr) {
        std::string eMsg = "Bad configuration element ";
        eMsg += element.get_name();
        throw std::invalid_argument(eMsg);
    }

    xmlpp::Node::NodeList   nodes;

    // read the list of supported languages
    nodes = element.get_children(supportedLanguagesElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no supportedLanguages element");
    }
    configSupportedLanguages(*((const xmlpp::Element*) *(nodes.begin())) );

    // configure the resource bundle
    nodes = element.get_children(LocalizedObject::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no resourceBundle element");
    }
    LocalizedConfigurable::configure(
                                  *((const xmlpp::Element*) *(nodes.begin())));

    // configure the AuthenticationClientFactory
    nodes = element.get_children(
                        AuthenticationClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no authenticationClientFactory element");
    }
    Ptr<AuthenticationClientFactory>::Ref acf
                                = AuthenticationClientFactory::getInstance();
    acf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    authentication = acf->getAuthenticationClient();

    // configure the StorageClientFactory
    nodes = element.get_children(StorageClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no storageClientFactory element");
    }
    Ptr<StorageClientFactory>::Ref stcf = StorageClientFactory::getInstance();
    stcf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    storage = stcf->getStorageClient();

    // configure the WidgetFactory
    nodes = element.get_children(WidgetFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no widgetFactory element");
    }
    widgetFactory = WidgetFactory::getInstance();
    widgetFactory->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    // configure the SchedulerClientFactory
    nodes = element.get_children(
                                SchedulerClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no schedulerClientFactory element");
    }
    Ptr<SchedulerClientFactory>::Ref schcf
                                        = SchedulerClientFactory::getInstance();
    schcf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    scheduler = schcf->getSchedulerClient();

    Ptr<AudioPlayerFactory>::Ref    apf;
    xmlpp::Element                * elem;
    // configure the outputPlayer AudioPlayerFactory
    nodes = element.get_children(outputPlayerElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no outputPlayer element");
    }
    elem  = (xmlpp::Element*) *(nodes.begin());
    nodes = elem->get_children(AudioPlayerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no audioPlayer element");
    }
    apf = AudioPlayerFactory::getInstance();
    apf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    outputPlayer = apf->getAudioPlayer();
    outputPlayer->initialize();
    outputPlayer->attachListener(this);

    // configure the cuePlayer AudioPlayerFactory
    nodes = element.get_children(cuePlayerElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no cuePlayer element");
    }
    elem  = (xmlpp::Element*) *(nodes.begin());
    nodes = elem->get_children(AudioPlayerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no audioPlayer element");
    }
    apf = AudioPlayerFactory::getInstance();
    apf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    cuePlayer = apf->getAudioPlayer();
    cuePlayer->initialize();

    // configure the station logo image
    nodes = element.get_children(stationLogoConfigElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no station logo element");
    }
    const xmlpp::Element*  stationLogoElement 
                           = dynamic_cast<const xmlpp::Element*>(nodes.front());
    const Glib::ustring    stationLogoFileName
                           = stationLogoElement->get_attribute("path")
                                               ->get_value();
    stationLogoPixbuf = Gdk::Pixbuf::create_from_file(stationLogoFileName);

    // configure the MetadataTypeContainer
    nodes = element.get_children(MetadataTypeContainer::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no metadataTypeContainer element");
    }
    Ptr<ResourceBundle>::Ref  metadataBundle;
    try {
        metadataBundle = getBundle("metadataTypes");
    } catch (std::invalid_argument &e) {
        throw std::invalid_argument(e.what());
    }
    metadataTypeContainer.reset(new MetadataTypeContainer(metadataBundle));
    metadataTypeContainer->configure( 
                                *((const xmlpp::Element*) *(nodes.begin())) );
}


/*------------------------------------------------------------------------------
 *  Configure the list of supported languages
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: configSupportedLanguages(const xmlpp::Element & element)
                                                throw (std::invalid_argument)
{
    xmlpp::Node::NodeList               nodes;
    xmlpp::Node::NodeList::iterator     begin;
    xmlpp::Node::NodeList::iterator     end;

    supportedLanguages.reset(new LanguageMap());

    // read the list of supported languages
    nodes = element.get_children(languageElementName);
    begin = nodes.begin();
    end   = nodes.end();

    while (begin != end) {
        xmlpp::Element    * elem = (xmlpp::Element *) *begin;
        xmlpp::Attribute  * localeAttr = elem->get_attribute(localeAttrName);
        xmlpp::Attribute  * nameAttr   = elem->get_attribute(nameAttrName);

        std::string     locale = localeAttr->get_value().raw();
        Glib::ustring   name   = nameAttr->get_value();

        supportedLanguages->insert(std::make_pair(name, locale));

        begin++;
    }
}

 
/*------------------------------------------------------------------------------
 *  Check all configured resources
 *----------------------------------------------------------------------------*/
bool
LiveSupport :: GLiveSupport ::
GLiveSupport :: checkConfiguration(void)                    throw ()
{
    // first, check if resources are available for all configured languages
    LanguageMap::iterator   it  = supportedLanguages->begin();
    try {
        LanguageMap::iterator   end = supportedLanguages->end();
        while (it != end) {
            changeLocale((*it).second);
            ++it;
        }
        changeLocale("");
    } catch (std::invalid_argument &e) {
        Ptr<Glib::ustring>::Ref language(new Glib::ustring((*it).first));
        Ptr<UnicodeString>::Ref uLanguage = ustringToUnicodeString(language);
        Ptr<Glib::ustring>::Ref msg = formatMessage(localeNotAvailableKey,
                                                    (*it).first);
        displayMessageWindow(msg);

        changeLocale("");
        return false;
    }

    // check if the authentication server is available
    try {
        authentication->getVersion();
    } catch (XmlRpcException &e) {
        displayMessageWindow(getResourceUstring(authenticationNotReachableKey));
        return false;
    }


    // check if the storage server is available
    try {
        storage->getVersion();
    } catch (XmlRpcException &e) {
        displayMessageWindow(getResourceUstring(storageNotReachableKey));
        return false;
    }

    // no need to check the widget factory

    // check the scheduler client
    try {
        scheduler->getVersion();
    } catch (XmlRpcException &e) {
        displayMessageWindow(getResourceUstring(schedulerNotReachableKey));
        return false;
    }

    // TODO: check the audio player?

    return true;
}


/*------------------------------------------------------------------------------
 *  Display a message window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: displayMessageWindow(Ptr<Glib::ustring>::Ref    message)
                                                                    throw ()
{
    WhiteWindow   * window = widgetFactory->createMessageWindow(message);
    Gtk::Main::run(*window);
    delete window;
}


/*------------------------------------------------------------------------------
 *  Show the main window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: show(void)                              throw ()
{
    masterPanel.reset(new MasterPanelWindow(shared_from_this(), getBundle()));

    // Shows the window and returns when it is closed.
    Gtk::Main::run(*masterPanel);

    masterPanel.reset();
}


/*------------------------------------------------------------------------------
 *  Change the language of the application
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: changeLanguage(Ptr<const std::string>::Ref  locale)
                                                 throw (std::invalid_argument)
{
    changeLocale(*locale);

    metadataTypeContainer->setBundle(getBundle("metadataTypes"));

    if (masterPanel.get()) {
        masterPanel->changeLanguage(getBundle());
    }
}


/*------------------------------------------------------------------------------
 *  Authenticate the user
 *----------------------------------------------------------------------------*/
bool
LiveSupport :: GLiveSupport ::
GLiveSupport :: login(const std::string & login,
                      const std::string & password)          throw ()
{
    try {
        sessionId = authentication->login(login, password);
        loadScratchpadContents();
        return true;
    } catch (XmlRpcException &e) {
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Log the user out.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: logout(void)                                throw ()
{
    if (sessionId.get() != 0) {
        stopCueAudio();
        storeScratchpadContents();
        scratchpadContents->clear();
        authentication->logout(sessionId);
        sessionId.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Store the Scratchpad contents as a user preference
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: storeScratchpadContents(void)               throw ()
{
    // just store this as a space-delimited list of ids
    std::ostringstream                      prefsString;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;
    Ptr<Playable>::Ref                      playable;

    it  = scratchpadContents->begin();
    end = scratchpadContents->end();
    while (it != end) {
        playable  = *it;
        prefsString << playable->getId()->getId() << " ";

        ++it;
    }

    Ptr<Glib::ustring>::Ref  prefsUstring(new Glib::ustring(prefsString.str()));
    try {
        authentication->savePreferencesItem(sessionId,
                                            scratchpadContentsKey,
                                            prefsUstring);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error saving user preferences: " << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Load the Scratchpad contents from a user preference
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: loadScratchpadContents(void)                throw ()
{
    Ptr<Glib::ustring>::Ref     prefsUstring;

    try {
        prefsUstring = authentication->loadPreferencesItem(sessionId,
                                                        scratchpadContentsKey);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error loading user preferences: " << e.what()
                  << std::endl;
        return;
    } catch (std::invalid_argument &e) {
        // no scratchpad stored for this user yet; no problem
        return;
    }
    
    // just store this as a space-delimited list of ids
    std::istringstream          prefsString(prefsUstring->raw());
    Ptr<Playable>::Ref          playable;

    while (!prefsString.eof()) {
        UniqueId::IdType        idValue;
        Ptr<UniqueId>::Ref      id;

        prefsString >> idValue;
        if (prefsString.fail()) {
            break;
        }
        id.reset(new UniqueId(idValue));

        // now we have the id, get the corresponding playlist or audio clip from
        // the storage
        if (storage->existsPlaylist(sessionId, id)) {
            Ptr<Playlist>::Ref  playlist = storage->getPlaylist(sessionId, id);
            scratchpadContents->push_back(playlist);
        } else if (storage->existsAudioClip(sessionId, id)) {
            Ptr<AudioClip>::Ref clip = storage->getAudioClip(sessionId, id);
            scratchpadContents->push_back(clip);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Show the anonymous UI
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: showAnonymousUI(void)                       throw ()
{
    if (masterPanel.get()) {
        masterPanel->showAnonymousUI();
    }
}


/*------------------------------------------------------------------------------
 *  Show the UI when someone is logged in
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: showLoggedInUI(void)                        throw ()
{
    if (masterPanel.get()) {
        masterPanel->showLoggedInUI();
    }
}


/*------------------------------------------------------------------------------
 *  Upload a file to the server.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: uploadFile(Ptr<AudioClip>::Ref  audioClip)
                                                    throw (XmlRpcException)
{
    storage->storeAudioClip(sessionId, audioClip);

    addToScratchpad(audioClip);
}


/*------------------------------------------------------------------------------
 *  Add a file to the Scratchpad, and update it.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToScratchpad(Ptr<Playable>::Ref  playable)
                                                            throw ()
{
    PlayableList::iterator  it;
    for (it = scratchpadContents->begin(); it != scratchpadContents->end();
                                                                     ++it) {
        Ptr<Playable>::Ref  listElement = *it;
        if (*listElement->getId() == *playable->getId()) {      // found
            scratchpadContents->erase(it);
            scratchpadContents->push_front(listElement);
            masterPanel->updateScratchpadWindow();   
            return;
        }
    }
    
    scratchpadContents->push_front(playable);                   // not found
    masterPanel->updateScratchpadWindow();   
}


/*------------------------------------------------------------------------------
 *  Add a file to the Live Mode, and update it.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToLiveMode(Ptr<Playable>::Ref  playable)
                                                            throw ()
{
    if (outputItemPlayingNow) {
        masterPanel->updateLiveModeWindow(playable);
    } else {
        playOutputAudio(playable);
        setNowPlaying(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Display the playable item on the master panel as "now playing".
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: setNowPlaying(Ptr<Playable>::Ref    playable)
                                                            throw ()
{
    masterPanel->setNowPlaying(playable);
}


/*------------------------------------------------------------------------------
 *  Open a  playlist for editing.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: openPlaylistForEditing(Ptr<UniqueId>::Ref  playlistId)
                                                    throw (XmlRpcException)
{
    releaseEditedPlaylist();

    if (!playlistId.get()) {
        playlistId     = storage->createPlaylist(sessionId);
    }

    editedPlaylist = storage->editPlaylist(sessionId, playlistId);

    return editedPlaylist;
}


/*------------------------------------------------------------------------------
 *  Release the edited playlist.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: releaseEditedPlaylist(void)
                                                    throw (XmlRpcException)
{
    if (editedPlaylist.get()) {
        if (editedPlaylist->isLocked()) {
            storage->releasePlaylist(editedPlaylist);
        }
        editedPlaylist.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Add a playlist to the currently edited playlist
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToPlaylist(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException)
{
    if (!editedPlaylist.get()) {
        openPlaylistForEditing();
    }

    // for some weird reason, the storage functions won't accept
    // Ptr<const UniqueId>::Ref, just a non-const version
    Ptr<UniqueId>::Ref  uid(new UniqueId(id->getId()));

    // append the appropriate playable object to the end of the playlist
    if (storage->existsPlaylist(sessionId, uid)) {
        Ptr<Playlist>::Ref      playlist = storage->getPlaylist(sessionId, uid);
        editedPlaylist->addPlaylist(playlist, editedPlaylist->getPlaylength());
    } else if (storage->existsAudioClip(sessionId, uid)) {
        Ptr<AudioClip>::Ref clip = storage->getAudioClip(sessionId, uid);
        editedPlaylist->addAudioClip(clip, editedPlaylist->getPlaylength());
    }

    masterPanel->updateSimplePlaylistMgmtWindow();
}


/*------------------------------------------------------------------------------
 *  Save the currently edited playlist in storage
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: savePlaylist(void)
                                                    throw (XmlRpcException)
{
    storage->savePlaylist(sessionId, editedPlaylist);

    Ptr<Playlist>::Ref      playlist = storage->getPlaylist(sessionId,
                                                    editedPlaylist->getId());
    addToScratchpad(playlist);
    editedPlaylist.reset();

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Schedule a playlist, then show the scheduler at that timepoint
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: schedulePlaylist(Ptr<Playlist>::Ref             playlist,
                                 Ptr<posix_time::ptime>::Ref    playtime)
                                                    throw (XmlRpcException)
{
    scheduler->uploadPlaylist(sessionId, playlist->getId(), playtime);
    masterPanel->updateSchedulerWindow(playtime);
}


/*------------------------------------------------------------------------------
 *  Remove a scheduled entry.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: removeFromSchedule(Ptr<UniqueId>::Ref   scheduleEntryId)
                                                    throw (XmlRpcException)
{
    scheduler->removeFromSchedule(sessionId, scheduleEntryId);
}


/*------------------------------------------------------------------------------
 *  Delete a playable object from the storage.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: deletePlayable(Ptr<Playable>::Ref   playable)
                                                    throw (XmlRpcException)
{
    switch (playable->getType()) {
        case Playable::AudioClipType:
            storage->deleteAudioClip(sessionId, playable->getId());
            break;

        case Playable::PlaylistType:
            storage->deletePlaylist(sessionId, playable->getId());
            break;

        default:
            break;
    }
}


/*------------------------------------------------------------------------------
 *  Play a Playable object using the output audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: playOutputAudio(Ptr<Playable>::Ref playable)
                                                    throw (std::logic_error)
{
    try {        
        switch (playable->getType()) {
            case Playable::AudioClipType:
                outputItemPlayingNow = storage->acquireAudioClip(sessionId, 
                                                        playable->getId());
                outputPlayer->open(*outputItemPlayingNow->getUri());
                outputPlayer->start();
                break;
    
            case Playable::PlaylistType:
                outputItemPlayingNow = storage->acquirePlaylist(sessionId, 
                                                        playable->getId());
                outputPlayer->openAndStart(outputItemPlayingNow->getPlaylist());
                break;
    
            default:        // this never happens
                break;
        }
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    } catch (std::invalid_argument &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    } catch (std::runtime_error &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    }
    
    outputPlayerIsPaused = false;
}
    

/*------------------------------------------------------------------------------
 *  Pause the output audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: pauseOutputAudio(void)
                                                    throw (std::logic_error)
{
    if (!outputPlayerIsPaused && outputPlayer->isPlaying()) {
        outputPlayer->pause();
        outputPlayerIsPaused = true;

    } else if (outputPlayerIsPaused) {
        outputPlayer->start();
        outputPlayerIsPaused = false;
    }
}


/*------------------------------------------------------------------------------
 *  Stop the output audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: stopOutputAudio(void)
                                                    throw (std::logic_error)
{
    if (outputItemPlayingNow) {
        outputPlayer->close();              // triggers a call to onStop()
        outputPlayerIsPaused = false;
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the "output audio player has stopped" event.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: onStop(void)                                throw ()
{
    releaseOutputAudio();
    
    Ptr<Playable>::Ref  playable = masterPanel->getNextItemToPlay();
    if (playable) {
        playOutputAudio(playable);
    }
    setNowPlaying(playable);
}


/*------------------------------------------------------------------------------
 *  Release the resources used by the output audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: releaseOutputAudio(void)
                                                    throw (std::logic_error)
{
    if (outputItemPlayingNow) {
        try {
            switch (outputItemPlayingNow->getType()) {
                case Playable::AudioClipType:
                    storage->releaseAudioClip(
                                        outputItemPlayingNow->getAudioClip());
                    outputItemPlayingNow.reset();
                    break;
                case Playable::PlaylistType:
                    storage->releasePlaylist(
                                        outputItemPlayingNow->getPlaylist());
                    outputItemPlayingNow.reset();
                    break;
                default:    // this never happens
                    break;
            }
        } catch (XmlRpcException &e) {
            Ptr<Glib::ustring>::Ref     eMsg 
                                        = getResourceUstring("audioErrorMsg");
            eMsg->append(e.what());
            displayMessageWindow(eMsg);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Play a Playable object using the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: playCueAudio(Ptr<Playable>::Ref playable)
                                                throw (std::logic_error)
{
    if (cueItemPlayingNow) {
        stopCueAudio();     // stop the audio player and
    }                       // release old resources

    try {        
        switch (playable->getType()) {
            case Playable::AudioClipType:
                cueItemPlayingNow = storage->acquireAudioClip(sessionId, 
                                                              playable->getId());
                cuePlayer->open(*cueItemPlayingNow->getUri());
                cuePlayer->start();
                break;
    
            case Playable::PlaylistType:
                cueItemPlayingNow = storage->acquirePlaylist(sessionId, 
                                                             playable->getId());
                cuePlayer->openAndStart(cueItemPlayingNow->getPlaylist());
                break;
    
            default:        // this never happens
                break;
        }
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    } catch (std::invalid_argument &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    } catch (std::runtime_error &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append(e.what());
        displayMessageWindow(eMsg);
    }
    
    cuePlayerIsPaused = false;
}


/*------------------------------------------------------------------------------
 *  Pause the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: pauseCueAudio(void)
                                                    throw (std::logic_error)
{
    if (!cuePlayerIsPaused && cuePlayer->isPlaying()) {
        cuePlayer->pause();
        cuePlayerIsPaused = true;

    } else if (cuePlayerIsPaused) {
        cuePlayer->start();
        cuePlayerIsPaused = false;
    }
}


/*------------------------------------------------------------------------------
 *  Stop the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: stopCueAudio(void)
                                                    throw (std::logic_error)
{
    if (cueItemPlayingNow) {
        cuePlayer->close();
        releaseCueAudio();
        cuePlayerIsPaused = false;
    }
}

/*------------------------------------------------------------------------------
 *  Release the resources used by the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: releaseCueAudio(void)
                                                    throw (std::logic_error)
{
    if (cueItemPlayingNow) {
        try {
            switch (cueItemPlayingNow->getType()) {
                case Playable::AudioClipType:
                    storage->releaseAudioClip(cueItemPlayingNow->getAudioClip());
                    cueItemPlayingNow.reset();
                    break;
                case Playable::PlaylistType:
                    storage->releasePlaylist(cueItemPlayingNow->getPlaylist());
                    cueItemPlayingNow.reset();
                    break;
                default:    // this never happens
                    break;
            }
        } catch (XmlRpcException &e) {
            Ptr<Glib::ustring>::Ref     eMsg 
                                        = getResourceUstring("audioErrorMsg");
            eMsg->append(e.what());
            displayMessageWindow(eMsg);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Attach a listener for the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: attachCueAudioListener(AudioPlayerEventListener *   listener)
                                                throw ()
{
    cuePlayer->attachListener(listener);
}


/*------------------------------------------------------------------------------
 *  Detach the listener for the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: detachCueAudioListener(AudioPlayerEventListener *   listener)
                                                throw (std::invalid_argument)
{
    cuePlayer->detachListener(listener);
}


/*------------------------------------------------------------------------------
 *  Search in the local storage.
 *----------------------------------------------------------------------------*/
Ptr<LiveSupport::GLiveSupport::GLiveSupport::PlayableList>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: search(Ptr<SearchCriteria>::Ref     criteria)
                                                throw (XmlRpcException)
{
    Ptr<LiveSupport::GLiveSupport::GLiveSupport::PlayableList>::Ref
            results(new PlayableList);
    
    storage->search(sessionId, criteria);

    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref
            audioClipIds = storage->getAudioClipIds();
    std::vector<Ptr<UniqueId>::Ref>::const_iterator it;
    for (it = audioClipIds->begin(); it != audioClipIds->end(); ++it) {
        results->push_back(storage->getAudioClip(sessionId, *it));
    }
    
    Ptr<std::vector<Ptr<UniqueId>::Ref> >::Ref
            playlistIds = storage->getPlaylistIds();
    for (it = playlistIds->begin(); it != playlistIds->end(); ++it) {
        results->push_back(storage->getPlaylist(sessionId, *it));
    }
    
    return results;
}


/*------------------------------------------------------------------------------
 *  Browse in the local storage.
 *----------------------------------------------------------------------------*/
Ptr<std::vector<Glib::ustring> >::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: browse(Ptr<const Glib::ustring>::Ref    metadata,
                       Ptr<SearchCriteria>::Ref         criteria)
                                                throw (XmlRpcException)
{
    return storage->browse(sessionId, metadata, criteria);
}


/*------------------------------------------------------------------------------
 *  Return an image containing the radio station logo.
 *----------------------------------------------------------------------------*/
Gtk::Image *
LiveSupport :: GLiveSupport ::
GLiveSupport :: getStationLogoImage(void)       throw()
{
    return new Gtk::Image(stationLogoPixbuf);
}

