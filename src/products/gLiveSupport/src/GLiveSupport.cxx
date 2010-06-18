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
 
 
    Author   : $Author$
    Version  : $Revision$
    Location : $URL$

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#ifdef HAVE_PWD_H
#include <pwd.h>
#else
#error need pwd.h
#endif

#ifdef HAVE_SYS_STAT_H
#include <sys/stat.h>
#else
#error need sys/stat.h
#endif

#ifdef HAVE_STDLIB_H
#include <stdlib.h>
#else
#error need stdlib.h
#endif

#include <stdexcept>
#include <gtkmm/main.h>

#include "LiveSupport/Core/LocalizedObject.h"
#include "LiveSupport/Core/TimeConversion.h"
#include "LiveSupport/Core/XmlRpcInvalidDataException.h"
#include "LiveSupport/Authentication/AuthenticationClientFactory.h"
#include "LiveSupport/StorageClient/StorageClientFactory.h"
#include "LiveSupport/SchedulerClient/SchedulerClientFactory.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerFactory.h"

#include "MasterPanelWindow.h"
#include "GLiveSupport.h"


using namespace boost;
using namespace boost::posix_time;

using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::SchedulerClient;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

/*------------------------------------------------------------------------------
 *  The name of the config element for this class
 *----------------------------------------------------------------------------*/
const std::string
            LiveSupport :: GLiveSupport :: GLiveSupport :: configElementNameStr
                        = "gLiveSupport";

/*------------------------------------------------------------------------------
 *  The singleton instance of GLiveSupport
 *----------------------------------------------------------------------------*/
Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref
            LiveSupport :: GLiveSupport :: GLiveSupport :: singleton;

namespace {

/*------------------------------------------------------------------------------
 *  The name of the configuration file for this class
 *----------------------------------------------------------------------------*/
const std::string   configFileDirStr = "/.campcaster/";

/*------------------------------------------------------------------------------
 *  The name of the configuration file for this class
 *----------------------------------------------------------------------------*/
const std::string   configFileNameStr = "campcaster-studio.xml";

/*------------------------------------------------------------------------------
 *  The name of the config element for the list of supported languages
 *----------------------------------------------------------------------------*/
const std::string   supportedLanguagesElementName = "supportedLanguages";

/*------------------------------------------------------------------------------
 *  The name of the config element for a supported language.
 *----------------------------------------------------------------------------*/
const std::string   languageElementName = "language";

/*------------------------------------------------------------------------------
 *  The name of the attribute for the locale id for a supported language
 *----------------------------------------------------------------------------*/
const std::string   localeAttrName = "locale";

/*------------------------------------------------------------------------------
 *  The name of the attribute for the name for a supported language
 *----------------------------------------------------------------------------*/
const std::string   nameAttrName = "name";

/*------------------------------------------------------------------------------
 *  The name of the config element for the directory where the Glade files are
 *----------------------------------------------------------------------------*/
const std::string   gladeDirConfigElementName = "gladeDirectory";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const std::string   gladeFileName = "GLiveSupport.glade";

/*------------------------------------------------------------------------------
 *  The name of the config element for the scheduler daemon start command
 *----------------------------------------------------------------------------*/
const std::string   schedulerDaemonCommandsElementName
                                            = "schedulerDaemonCommands";

/*------------------------------------------------------------------------------
 *  The name of the config element for the sound output player
 *----------------------------------------------------------------------------*/
const std::string   outputPlayerElementName = "outputPlayer";

/*------------------------------------------------------------------------------
 *  The name of the config element for the sound cue player
 *----------------------------------------------------------------------------*/
const std::string   cuePlayerElementName = "cuePlayer";

/*------------------------------------------------------------------------------
 *  The name of the config element for the station logo image
 *----------------------------------------------------------------------------*/
const std::string   stationLogoConfigElementName = "stationLogo";

/*------------------------------------------------------------------------------
 *  The name of the config element for the taskbar icon images
 *----------------------------------------------------------------------------*/
const std::string   taskbarIconsConfigElementName = "taskbarIcons";

/*------------------------------------------------------------------------------
 *  The name of the config element for the test audio file location
 *----------------------------------------------------------------------------*/
const std::string   testAudioUrlConfigElementName = "testAudioUrl";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing window positions
 *----------------------------------------------------------------------------*/
const std::string   windowPositionsKey = "windowPositions";

/*------------------------------------------------------------------------------
 *  The name of the user preference for storing the token of the edited p.l.
 *----------------------------------------------------------------------------*/
const std::string   editedPlaylistTokenKey = "editedPlaylistToken";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the scheduler not available error message
 *----------------------------------------------------------------------------*/
const std::string   schedulerNotReachableKey = "schedulerNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the storage not available error message
 *----------------------------------------------------------------------------*/
const std::string   storageNotReachableKey = "storageNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the authentication not available error msg
 *----------------------------------------------------------------------------*/
const std::string   authenticationNotReachableKey =
                                            "authenticationNotReachableMsg";

/*------------------------------------------------------------------------------
 *  Static constant for the key of the locale not available error message
 *----------------------------------------------------------------------------*/
const std::string   localeNotAvailableKey = "localeNotAvailableMsg";

/*------------------------------------------------------------------------------
 *  The name of the config element for the serial device
 *----------------------------------------------------------------------------*/
const std::string   serialPortConfigElementName = "serialPort";

/*------------------------------------------------------------------------------
 *  The default serial device
 *----------------------------------------------------------------------------*/
const std::string   serialPortDefaultDevice = "/dev/ttyS0";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Return the singleton instance
 *----------------------------------------------------------------------------*/
Ptr<LiveSupport::GLiveSupport::GLiveSupport>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getInstance(void)                                   throw ()
{
    if (!singleton.get()) {
        singleton.reset(new LiveSupport::GLiveSupport::GLiveSupport());
    }

    return singleton;
}


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
    configSupportedLanguages(
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()));

    // configure the resource bundle
    nodes = element.get_children(LocalizedObject::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no resourceBundle element");
    }
    LocalizedConfigurable::configure(
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()) );

    // configure the AuthenticationClientFactory
    nodes = element.get_children(
                        AuthenticationClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no authenticationClientFactory element");
    }
    Ptr<AuthenticationClientFactory>::Ref acf
                                = AuthenticationClientFactory::getInstance();
    acf->configure(*dynamic_cast<const xmlpp::Element*>(nodes.front()));

    authentication = acf->getAuthenticationClient();

    // configure the StorageClientFactory
    nodes = element.get_children(StorageClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no storageClientFactory element");
    }
    Ptr<StorageClientFactory>::Ref stcf = StorageClientFactory::getInstance();
    stcf->configure(*dynamic_cast<const xmlpp::Element*>(nodes.front()));

    storage = stcf->getStorageClient();

    // configure the directory where the Glade files are
    nodes = element.get_children(gladeDirConfigElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no gladeDirectory element");
    }
    const xmlpp::Element*  gladeDirElement 
                           = dynamic_cast<const xmlpp::Element*>(nodes.front());
    gladeDir = gladeDirElement->get_attribute("path")
                              ->get_value();
    glade = Gnome::Glade::Xml::create(gladeDir + gladeFileName);

    // configure the WidgetFactory
    nodes = element.get_children(WidgetFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no widgetFactory element");
    }
    widgetFactory = WidgetFactory::getInstance();
    widgetFactory->configure(
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()) );

    // configure the SchedulerClientFactory
    nodes = element.get_children(
                                SchedulerClientFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no schedulerClientFactory element");
    }
    Ptr<SchedulerClientFactory>::Ref schcf
                                        = SchedulerClientFactory::getInstance();
    schcf->configure(*dynamic_cast<const xmlpp::Element*>(nodes.front()));

    scheduler = schcf->getSchedulerClient();

    // configure the scheduler daemon start and stop command strings
    nodes = element.get_children(schedulerDaemonCommandsElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no scheduler daemon commands element");
    }
    const xmlpp::Element*  schedulerDaemonCommandsElement
                           = dynamic_cast<const xmlpp::Element*>(nodes.front());
    xmlpp::Attribute *     schedulerDaemonStartAttribute
                           = schedulerDaemonCommandsElement->get_attribute(
                                                                    "start");
    xmlpp::Attribute *     schedulerDaemonStopAttribute
                           = schedulerDaemonCommandsElement->get_attribute(
                                                                    "stop");
    if (!schedulerDaemonStartAttribute) {
        throw std::invalid_argument("missing scheduler start command");
    }
    if (!schedulerDaemonStopAttribute) {
        throw std::invalid_argument("missing scheduler stop command");
    }
    
    schedulerDaemonStartCommand.reset(new Glib::ustring(
                           schedulerDaemonStartAttribute->get_value()));
    
    schedulerDaemonStopCommand.reset(new Glib::ustring(
                           schedulerDaemonStopAttribute->get_value()));

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
    apf->configure(*dynamic_cast<const xmlpp::Element*>(nodes.front()));

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
    apf->configure(*dynamic_cast<const xmlpp::Element*>(nodes.front()));

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
    try {
        stationLogoPixbuf = Gdk::Pixbuf::create_from_file(stationLogoFileName);
    } catch (Glib::FileError &e) {
        Glib::ustring   errorMsg = "could not open station logo image file: ";
        errorMsg += e.what();
        throw std::invalid_argument(errorMsg);
    } catch (Gdk::PixbufError &e) {
        Glib::ustring   errorMsg = "could not create station logo image: ";
        errorMsg += e.what();
        throw std::invalid_argument(errorMsg);
    }

    // configure the taskbar icon images
    nodes = element.get_children(taskbarIconsConfigElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no taskbar icons element");
    }
    taskbarIcons.reset(new TaskbarIcons());
    taskbarIcons->configure(
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()) );

    // configure the MetadataTypeContainer
    nodes = element.get_children(MetadataTypeContainer::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no metadataTypeContainer element");
    }
    Ptr<ResourceBundle>::Ref    metadataBundle = getBundle("metadataTypes");
    metadataTypeContainer.reset(new MetadataTypeContainer(metadataBundle));
    metadataTypeContainer->configure( 
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()) );

    // configure the KeyboardShortcutList
    nodes = element.get_children(
                            KeyboardShortcutList::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no keyboardShortcutList element");
    }
    keyboardShortcutList.reset(new KeyboardShortcutList);
    keyboardShortcutList->configure( 
                        *dynamic_cast<const xmlpp::Element*>(nodes.front()) );
    
    // save the configuration so we can modify it later
    // TODO: move configuration code to the OptionsContainer class?
    Ptr<Glib::ustring>::Ref     configFileName(new Glib::ustring);
    configFileName->append(Glib::get_home_dir());
    configFileName->append(configFileDirStr);
    mkdir(configFileName->c_str(), 0700);   // create dir if does not exist
    configFileName->append(configFileNameStr);
    optionsContainer.reset(new OptionsContainer(element, configFileName));
    
    // read the test audio file location
    nodes = element.get_children(testAudioUrlConfigElementName);
    if (nodes.size() < 1) {
        throw std::invalid_argument("no test audio url element");
    }
    const xmlpp::Element*  testAudioUrlElement 
                           = dynamic_cast<const xmlpp::Element*>(nodes.front());
    testAudioUrl.reset(new Glib::ustring(
                           testAudioUrlElement->get_attribute("path")
                                              ->get_value() ));

    // read the serial port's file name
    nodes = element.get_children(serialPortConfigElementName);
    if (nodes.size() < 1) {
        Ptr<const Glib::ustring>::Ref   serialDevice(new const Glib::ustring(
                                                    serialPortDefaultDevice));
        optionsContainer->setOptionItem(OptionsContainer::serialDeviceName,
                                        serialDevice);
    }
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
GLiveSupport :: checkConfiguration(void)                            throw ()
{
    // === FATAL ERRORS ===

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
        displayMessageWindow(*msg);

        changeLocale("");
        return false;
    }

    // check if the authentication server is available
    try {
        authentication->getVersion();
    } catch (XmlRpcException &e) {
        displayAuthenticationServerMissingMessage();
        return false;
    }

    // === NON-FATAL ERRORS ===

    // check if the storage server is available
    try {
        storage->getVersion();
        storageAvailable = true;
    } catch (XmlRpcException &e) {
        storageAvailable = false;
        displayMessageWindow(*getResourceUstring(storageNotReachableKey));
    }

    // no need to check the widget factory

    // check the scheduler client
    checkSchedulerClient();
    if (!isSchedulerAvailable()) {
        displayMessageWindow(*getResourceUstring(schedulerNotReachableKey));
    }

    // TODO: check the audio player?

    return true;
}


/*------------------------------------------------------------------------------
 *  Display a message window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: displayMessageWindow(const Glib::ustring &      message)
                                                                    throw ()
{
    runOkDialog(message);
}


/*------------------------------------------------------------------------------
 *  Run a dialog window with No and Yes buttons.
 *----------------------------------------------------------------------------*/
Gtk::ResponseType
LiveSupport :: GLiveSupport ::
GLiveSupport :: runNoYesDialog(const Glib::ustring &    message)
                                                                    throw ()
{
    return runDialog("noYesDialog", message);
}


/*------------------------------------------------------------------------------
 *  Run a dialog window with just an OK button.
 *----------------------------------------------------------------------------*/
Gtk::ResponseType
LiveSupport :: GLiveSupport ::
GLiveSupport :: runOkDialog(const Glib::ustring &       message)
                                                                    throw ()
{
    return runDialog("okDialog", message);
}


/*------------------------------------------------------------------------------
 *  Run a dialog window.
 *----------------------------------------------------------------------------*/
Gtk::ResponseType
LiveSupport :: GLiveSupport ::
GLiveSupport :: runDialog(const Glib::ustring &         dialogName,
                          const Glib::ustring &         message)
                                                                    throw ()
{
    Gtk::Dialog *       dialog;
    Gtk::Label *        dialogLabel;
    glade->get_widget(dialogName + "1", dialog);
    glade->get_widget(dialogName + "Label1", dialogLabel);
    
    Glib::ustring       formattedMessage = "<span weight=\"bold\" ";
    formattedMessage += " size=\"larger\">";
    formattedMessage += message;
    formattedMessage += "</span>";
    dialogLabel->set_label(formattedMessage);

    Gtk::ResponseType   response = Gtk::ResponseType(dialog->run());
    dialog->hide();
    return response;
}


/*------------------------------------------------------------------------------
 *  Show the main window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: show(void)                                          throw ()
{
    masterPanel.reset(new MasterPanelWindow());

    masterPanel->getWindow()->set_icon_list(taskbarIcons->getIconList());
    masterPanel->getWindow()->set_default_icon_list(
                                            taskbarIcons->getIconList());

    // Shows the window and returns when it is closed.
    Gtk::Main::run(*masterPanel->getWindow());

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
        masterPanel->changeLanguage();
    }
}


/*------------------------------------------------------------------------------
 *  Authenticate the user
 *----------------------------------------------------------------------------*/
bool
LiveSupport :: GLiveSupport ::
GLiveSupport :: login(const std::string &   login,
                      const std::string &   password)               throw ()
{
    try {
        sessionId = authentication->login(login, password);
    } catch (XmlRpcException &e) {
        return false;
    }

    userName.reset(new Glib::ustring(login));

    Ptr<const Glib::ustring>::Ref   editedPlaylistToken;
    Ptr<const std::string>::Ref     editedPlaylistTokenString;
    try {
        editedPlaylistToken = authentication->loadPreferencesItem(
                                                    sessionId,
                                                    editedPlaylistTokenKey);
        editedPlaylistTokenString.reset(new const std::string(
                                                    *editedPlaylistToken ));
    } catch (std::invalid_argument &e) {
        // no stuck playlist token found; that's OK
    } catch (XmlRpcException &e) {
        std::cerr << "Problem loading "
                  << editedPlaylistTokenKey
                  << " user preference item:"
                  << std::endl
                  << e.what();
    }

    if (editedPlaylistTokenString) {
        try {
            storage->revertPlaylist(editedPlaylistTokenString);
        } catch (XmlRpcException &e) {
            // sometimes this throws; we don't care
        }

        try {
            authentication->deletePreferencesItem(sessionId,
                                                    editedPlaylistTokenKey);
        } catch (XmlRpcException &e) {
            std::cerr << "Problem deleting "
                      << editedPlaylistTokenKey
                      << " user preference item at login:"
                      << std::endl
                      << e.what();
        }
    }

    loadWindowPositions();
    
    return true;
}


/*------------------------------------------------------------------------------
 *  Log the user out.
 *----------------------------------------------------------------------------*/
bool
LiveSupport :: GLiveSupport ::
GLiveSupport :: logout(void)                                        throw ()
{
    if (!sessionId) {
        return false;
    }
    
    if (!masterPanel->cancelEditedPlaylist()) {
        return false;   // do nothing if the user presses the cancel button
    }
    
    stopCueAudio();
    masterPanel->showAnonymousUI();
    
    storeWindowPositions();
    windowPositions.clear();
    
    try {
        authentication->logout(sessionId);
    } catch (XmlRpcException &e) {
        std::cerr << "error in GLiveSupport::logout: " 
                  << e.what() << std::endl;
    }
    sessionId.reset();
    
    return true;
}


/*------------------------------------------------------------------------------
 *  Store the contents of a window as a user preference
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: storeWindowContents(ContentsStorable *  window)
                                                                    throw ()
{
    Ptr<const Glib::ustring>::Ref   userPreferencesKey       
                                            = window->getUserPreferencesKey();
    Ptr<const Glib::ustring>::Ref   windowContents
                                            = window->getContents();
    
    try {
        authentication->savePreferencesItem(sessionId,
                                            *userPreferencesKey,
                                            windowContents);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error saving user preferences: " 
                    << e.what() 
                    << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Load the contents of a window from a user preference
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: loadWindowContents(ContentsStorable *   window)
                                                                    throw ()
{
    Ptr<const Glib::ustring>::Ref   userPreferencesKey       
                                            = window->getUserPreferencesKey();
    Ptr<const Glib::ustring>::Ref   windowContents;

    try {
        windowContents = authentication->loadPreferencesItem(
                                                        sessionId,
                                                        *userPreferencesKey);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error loading user preferences: " << e.what()
                  << std::endl;
        return;
    } catch (std::invalid_argument &e) {
        // no preferences stored for this user yet; no problem
        return;
    }
    
    window->setContents(windowContents);
}


/*------------------------------------------------------------------------------
 *  Open an audio clip, and put it into the internal cache of the GLiveSupport
 *  object.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getAudioClip(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<AudioClip>::Ref     clip;

    AudioClipMap::iterator  it = openedAudioClips->find(id->getId());
    if (it != openedAudioClips->end()) {
        clip = it->second;
    } else {
        clip = storage->getAudioClip(sessionId, id);
        (*openedAudioClips)[id->getId()] = clip;
    }

    return clip;
}


/*------------------------------------------------------------------------------
 *  Acquire an audio clip, and put it into the internal cache of
 *  the GLiveSupport object.
 *----------------------------------------------------------------------------*/
Ptr<AudioClip>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: acquireAudioClip(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<AudioClip>::Ref     clip;

    AudioClipMap::iterator  it = openedAudioClips->find(id->getId());
    if (it != openedAudioClips->end()) {
        clip = it->second;
    }
    
    if (!clip || !clip->getToken()) {
        clip = storage->acquireAudioClip(sessionId, id);
        (*openedAudioClips)[id->getId()] = clip;
    }

    return clip;
}


/*------------------------------------------------------------------------------
 *  Open an playlist, and put it into the internal cache of the GLiveSupport
 *  object.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getPlaylist(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<Playlist>::Ref      playlist;

    PlaylistMap::iterator  it = openedPlaylists->find(id->getId());
    if (it != openedPlaylists->end()) {
        playlist = it->second;
    } else {
        playlist = storage->getPlaylist(sessionId, id);
        (*openedPlaylists)[id->getId()] = playlist;
    }

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Acquire an playlist, and put it into the internal cache of
 *  the GLiveSupport object.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: acquirePlaylist(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<Playlist>::Ref      playlist;

    PlaylistMap::iterator  it = openedPlaylists->find(id->getId());
    if (it != openedPlaylists->end()) {
        playlist = it->second;
    }
    
    if (!playlist || !playlist->getUri()) {
        playlist = storage->acquirePlaylist(sessionId, id);
        (*openedPlaylists)[id->getId()] = playlist;
    }

    return playlist;
}


/*------------------------------------------------------------------------------
 *  Open a Playable object.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getPlayable(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<Playable>::Ref  playable;
    
    if (existsPlaylist(id)) {
        playable = getPlaylist(id);

    } else if (existsAudioClip(id)) {
        playable = getAudioClip(id);

    } else {
        throw XmlRpcInvalidArgumentException(
                                "invalid ID in GLiveSupport::getPlayable");
    }
    
    return playable;
}


/*------------------------------------------------------------------------------
 *  Acquire a Playable object.
 *----------------------------------------------------------------------------*/
Ptr<Playable>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: acquirePlayable(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<Playable>::Ref  playable;
    
    if (existsPlaylist(id)) {
        playable = acquirePlaylist(id);

    } else if (existsAudioClip(id)) {
        playable = acquireAudioClip(id);

    } else {
        throw XmlRpcInvalidArgumentException(
                                "invalid ID in GLiveSupport::acquirePlayable");
    }
    
    return playable;
}


/*------------------------------------------------------------------------------
 *  Remove a playlist from the playlist cache.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: uncachePlaylist(Ptr<const UniqueId>::Ref  id)
                                                        throw (XmlRpcException)
{
    Ptr<Playlist>::Ref      playlist;
    PlaylistMap::iterator   it;
    PlaylistMap::iterator   end = openedPlaylists->end();

    if ((it = openedPlaylists->find(id->getId())) != end) {
        playlist = (*openedPlaylists)[id->getId()];
        if (playlist->getUri()) {
            storage->releasePlaylist(playlist);
        }

        openedPlaylists->erase(it);
    }
}


/*-----------------------------------------------------------------------------
 *  Release all opened audio clips.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: releaseOpenedAudioClips(void)           throw (XmlRpcException)
{
    AudioClipMap::iterator   it  = openedAudioClips->begin();
    AudioClipMap::iterator   end = openedAudioClips->end();

    while (it != end) {
        Ptr<AudioClip>::Ref clip = it->second;

        if (clip->getToken().get()) {
            storage->releaseAudioClip(clip);
        }

        ++it;
    }

    openedAudioClips->clear();
}


/*------------------------------------------------------------------------------
 *  Release all opened playlists.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: releaseOpenedPlaylists(void)            throw (XmlRpcException)
{
    PlaylistMap::iterator   it  = openedPlaylists->begin();
    PlaylistMap::iterator   end = openedPlaylists->end();

    while (it != end) {
        Ptr<Playlist>::Ref playlist = it->second;

        if (playlist->getUri()) {
            storage->releasePlaylist(playlist);
        }

        ++it;
    }

    openedPlaylists->clear();
}


/*------------------------------------------------------------------------------
 *  Upload an audio clip to the local storage.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: uploadAudioClip(Ptr<AudioClip>::Ref     audioClip)
                                                    throw (XmlRpcException)
{
    storage->storeAudioClip(sessionId, audioClip);

    // this will also add it to the local cache
    addToScratchpad(audioClip);
}


/*------------------------------------------------------------------------------
 *  Upload a playlist archive to the local storage.
 *----------------------------------------------------------------------------*/
Ptr<Playlist>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: uploadPlaylistArchive(Ptr<const Glib::ustring>::Ref     path)
                                                    throw (XmlRpcException)
{
    Ptr<UniqueId>::Ref  id = storage->importPlaylist(sessionId, path);
    Ptr<Playlist>::Ref  playlist = storage->getPlaylist(sessionId, id);
    
    // this will also add it to the local cache
    addToScratchpad(playlist);
    
    return playlist;
}


/*------------------------------------------------------------------------------
 *  Add a file to the Scratchpad, and update it.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToScratchpad(Ptr<Playable>::Ref  playable)
                                                    throw (XmlRpcException)
{
    if (playable->getType() == Playable::AudioClipType) {
        acquireAudioClip(playable->getId());
    } else if (playable->getType() == Playable::PlaylistType) {
        acquirePlaylist(playable->getId());
    }

    // this will also add it to the local cache
    masterPanel->updateScratchpadWindow(playable);
}


/*------------------------------------------------------------------------------
 *  Add a file to the Live Mode, and update it.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToLiveMode(Ptr<Playable>::Ref    playable)
                                                                    throw ()
{
    masterPanel->updateLiveModeWindow(playable);
}


/*------------------------------------------------------------------------------
 *  Display the playable item on the master panel as "now playing".
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: setNowPlaying(Ptr<Playable>::Ref    playable)
                                                                    throw ()
{
    // test needed: this gets called indirectly from ~MasterPanelWindow
    if (masterPanel) {
        masterPanel->setNowPlaying(playable);
    }
}


/*------------------------------------------------------------------------------
 *  Open a  playlist for editing.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: openPlaylistForEditing(Ptr<const UniqueId>::Ref   playlistId)
                                                    throw (XmlRpcException)
{
    if (masterPanel->cancelEditedPlaylist() == false) {
        return;                 // the user canceled the operation
    }
    
    if (!playlistId.get()) {
        playlistId     = storage->createPlaylist(sessionId);
    } else {
        uncachePlaylist(playlistId);
    }

    editedPlaylist = storage->editPlaylist(sessionId, playlistId);
    editedPlaylist->setMetadata(userName, "dc:creator");

    try {
        Ptr<const Glib::ustring>::Ref   editToken(new const Glib::ustring(
                                            *editedPlaylist->getEditToken() ));
        authentication->savePreferencesItem(sessionId,
                                            editedPlaylistTokenKey,
                                            editToken);
    } catch (XmlRpcException &e) {
        std::cerr << "Problem saving "
                  << editedPlaylistTokenKey
                  << " user preference item:"
                  << std::endl
                  << e.what();
    }
        
    editedPlaylist->createSavedCopy();

    masterPanel->updatePlaylistWindow();
}


/*------------------------------------------------------------------------------
 *  Cancel the edited playlist: undo changes and release the lock.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: cancelEditedPlaylist(void)
                                                    throw (XmlRpcException)
{
    if (editedPlaylist) {
        if (editedPlaylist->isLocked()) {
            editedPlaylist->revertToSavedCopy();
            storage->savePlaylist(sessionId, editedPlaylist);
            try {
                authentication->deletePreferencesItem(sessionId,
                                                      editedPlaylistTokenKey);
            } catch (XmlRpcException &e) {
                std::cerr << "Problem deleting "
                            << editedPlaylistTokenKey
                            << " user preference item at cancel:"
                            << std::endl
                            << e.what();
            }
        }
        editedPlaylist.reset();
    }
}


/*------------------------------------------------------------------------------
 *  Add a playlist to the currently edited playlist
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: addToPlaylist(Ptr<UniqueId>::Ref  id)
                                                    throw (XmlRpcException)
{
    if (!editedPlaylist.get()) {
        openPlaylistForEditing();
    }

    // append the appropriate playable object to the end of the playlist
    if (existsPlaylist(id)) {
        Ptr<Playlist>::Ref      playlist = getPlaylist(id);
        editedPlaylist->addPlaylist(playlist, editedPlaylist->getPlaylength());
    } else if (existsAudioClip(id)) {
        Ptr<AudioClip>::Ref clip = getAudioClip(id);
        Ptr<UniqueId>::Ref elid = editedPlaylist->addAudioClip(clip, editedPlaylist->getPlaylength());
		
		//TODO: for testing only!!!!!!!!!
//        editedPlaylist->setClipStart(elid, Ptr<time_duration>::Ref(new time_duration(seconds(5))));
//        editedPlaylist->setClipEnd(elid, Ptr<time_duration>::Ref(new time_duration(seconds(10))));
    }

    masterPanel->updatePlaylistWindow();
    emitSignalEditedPlaylistModified();
}


/*------------------------------------------------------------------------------
 *  Save the currently edited playlist in storage
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: savePlaylist(void)
                                                    throw (XmlRpcException)
{
    if (editedPlaylist) {
        if (editedPlaylist->isLocked()) {
            editedPlaylist->deleteSavedCopy();
            storage->savePlaylist(sessionId, editedPlaylist);
            try {
                authentication->deletePreferencesItem(sessionId,
                                                      editedPlaylistTokenKey);
            } catch (XmlRpcException &e) {
                std::cerr << "Problem deleting "
                            << editedPlaylistTokenKey
                            << " user preference item at save:"
                            << std::endl
                            << e.what();
            }
            // update with new version
            // this will also add it to the local cache
            uncachePlaylist(editedPlaylist->getId());
            addToScratchpad(editedPlaylist);
            refreshPlaylistInLiveMode(editedPlaylist);
        }
        editedPlaylist.reset();
    }
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
GLiveSupport :: removeFromSchedule(Ptr<const UniqueId>::Ref   scheduleEntryId)
                                                    throw (XmlRpcException)
{
    // for some weird reason, the schedule functions won't accept
    // Ptr<const UniqueId>::Ref, just a non-const version
    Ptr<UniqueId>::Ref  seid(new UniqueId(scheduleEntryId->getId()));

    scheduler->removeFromSchedule(sessionId, seid);
}


/*------------------------------------------------------------------------------
 *  Preload the item in the output audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: preload(Ptr<const Playable>::Ref    playable)
                                                                    throw ()
{
    Ptr<const std::string>::Ref     uri = playable->getUri();
    if (uri) {
        try {
            outputPlayer->preload(*uri);
            
        } catch (std::invalid_argument) {
            std::cerr << "gLiveSupport: invalid argument in preload("
                      << *uri 
                      << ")" << std::endl;
        } catch (std::runtime_error) {
            std::cerr << "gLiveSupport: runtime error in preload("
                      << *uri 
                      << ")" << std::endl;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Play a Playable object using the output audio player.
 *----------------------------------------------------------------------------*/
bool
LiveSupport :: GLiveSupport ::
GLiveSupport :: playOutputAudio(Ptr<Playable>::Ref playable)
                                                    throw (std::logic_error,
                                                           std::runtime_error)
{
    try {        
        switch (playable->getType()) {
            case Playable::AudioClipType:
                outputItemPlayingNow = acquireAudioClip(playable->getId());
                if(false == outputPlayer->open(*outputItemPlayingNow->getUri(), (gint64)outputItemPlayingNow->getId()->getId(), 0L))
				{
					return false;
				}
                outputPlayer->start();
                std::cerr << "gLiveSupport: Live Mode playing audio clip '"
                          << *playable->getTitle()
                          << "'" << std::endl;
                break;
    
            case Playable::PlaylistType:
                outputItemPlayingNow = acquirePlaylist(playable->getId());
                outputPlayer->open(*outputItemPlayingNow->getUri(), (gint64)outputItemPlayingNow->getId()->getId(), 0L);
                outputPlayer->start();
                std::cerr << "gLiveSupport: Live Mode playing playlist '"
                          << *playable->getTitle()
                          << "'" << std::endl;
                break;
    
            default:        // this never happens
                break;
        }
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
    } catch (std::invalid_argument &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
    } catch (std::runtime_error &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
    }

    outputPlayerIsPaused = false;
	return true;
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
        outputPlayerIsPaused = false;
        outputItemPlayingNow.reset();
        
        Ptr<Playable>::Ref  nullPointer;
        setNowPlaying(nullPointer);
        
        outputPlayer->close();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the "output audio player has stopped" event.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: onStop(Ptr<const Glib::ustring>::Ref      errorMessage)
                                                                    throw ()
{
    outputItemPlayingNow.reset();
    try {
        outputPlayer->close();

        Ptr<Playable>::Ref  playable = masterPanel->getNextItemToPlay();
        setNowPlaying(playable);
        if (playable) {
            playOutputAudio(playable);
        }
    } catch (std::logic_error) {
        std::cerr << "logic_error caught in GLiveSupport::onStop()\n";
        std::exit(1);
    }
    
    if (errorMessage) {
        displayMessageWindow(*errorMessage);
    }
}

/*------------------------------------------------------------------------------
 *  Event handler for the "output audio player has started" event.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: onStart(gint64 id)
                                                                    throw ()
{
	masterPanel->setCurrentInnerPlayable(id);
}


/*------------------------------------------------------------------------------
 *  Play a Playable object using the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: playCueAudio(Ptr<Playable>::Ref playable)
                                                throw (std::logic_error,
                                                       std::runtime_error)
{
    if (cueItemPlayingNow) {
        stopCueAudio();     // stop the audio player and
    }                       // release old resources

    try {        
        switch (playable->getType()) {
            case Playable::AudioClipType:
                cueItemPlayingNow = acquireAudioClip(playable->getId());
                cuePlayer->open(*cueItemPlayingNow->getUri(), (gint64)cueItemPlayingNow->getId()->getId(), 0L);
                cuePlayer->start();
                std::cerr << "gLiveSupport: Cue playing audio clip '"
                          << *playable->getTitle()
                          << "'" << std::endl;
                break;
    
            case Playable::PlaylistType:
                cueItemPlayingNow = acquirePlaylist(playable->getId());
                cuePlayer->open(*cueItemPlayingNow->getUri(), (gint64)cueItemPlayingNow->getId()->getId(), 0L);
                cuePlayer->start();
                std::cerr << "gLiveSupport: Cue playing playlist '"
                          << *playable->getTitle()
                          << "'" << std::endl;
                break;
    
            default:        // this never happens
                break;
        }
    } catch (XmlRpcException &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
    } catch (std::invalid_argument &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
    } catch (std::runtime_error &e) {
        Ptr<Glib::ustring>::Ref     eMsg 
                                    = getResourceUstring("audioErrorMsg");
        eMsg->append("\n");
        eMsg->append(e.what());
        displayMessageWindow(*eMsg);
        throw std::runtime_error(e.what());
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
        cuePlayerIsPaused = false;
        cueItemPlayingNow.reset();
        
        // test needed: this gets called indirectly from ~MasterPanelWindow
        if (masterPanel) {
            masterPanel->showCuePlayerStopped();
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
 *  Return an image containing the radio station logo.
 *----------------------------------------------------------------------------*/
Glib::RefPtr<Gdk::Pixbuf>
LiveSupport :: GLiveSupport ::
GLiveSupport :: getStationLogoPixbuf(void)                          throw()
{
    return stationLogoPixbuf;
}


/*------------------------------------------------------------------------------
 *  Get the localized name of the keyboard shortcut action.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getLocalizedKeyboardActionName(
                            Ptr<const Glib::ustring>::Ref    actionName)
                                                throw (std::invalid_argument)
{
    return getResourceUstring("keyboardShortcuts", actionName->c_str());
}


/*------------------------------------------------------------------------------
 *  Get the localized name of the window.
 *----------------------------------------------------------------------------*/
Ptr<const Glib::ustring>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: getLocalizedWindowName(
                            Ptr<const Glib::ustring>::Ref    windowName)
                                                throw (std::invalid_argument)
{
    return getResourceUstring(windowName->c_str(), "windowTitle");
}


/*------------------------------------------------------------------------------
 *  Save the position and size of the window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: putWindowPosition(const GuiWindow *     window)
                                                                    throw ()
{
    WindowPositionType  pos;
    window->getWindow()->get_position(pos.x, pos.y);
    window->getWindow()->get_size(pos.width, pos.height);

    windowPositions[replaceSpaces(window->getTitle())] = pos;
}


/*------------------------------------------------------------------------------
 *  Apply saved position and size data to the window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: getWindowPosition(GuiWindow *       window)
                                                                    throw ()
{
    WindowPositionsListType::const_iterator it = windowPositions.find(
                                        replaceSpaces(window->getTitle()));
    if (it != windowPositions.end()) {
        WindowPositionType  pos = it->second;
        window->getWindow()->move(pos.x, pos.y);
        window->getWindow()->resize(pos.width, pos.height);
    }
}


/*------------------------------------------------------------------------------
 *  Replace spaces with underscore characters.
 *----------------------------------------------------------------------------*/
Glib::ustring
LiveSupport :: GLiveSupport ::
GLiveSupport :: replaceSpaces(Ptr<const Glib::ustring>::Ref     string)
                                                                    throw ()
{
    Glib::ustring   copy = *string;

    for (unsigned int i = 0; i < copy.size(); ++i) {
        if (copy[i] == ' ') {
            copy.replace(i, 1, 1, '_');
        }
    }

    return copy;
}
        

/*------------------------------------------------------------------------------
 *  Store the saved window positions.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: storeWindowPositions(void)                          throw ()
{
    // just store this as a space-delimited list of window names and numbers
    std::ostringstream                      prefsString;
    WindowPositionsListType::iterator       it;
    WindowPositionsListType::iterator       end;
    WindowPositionType                      pos;

    it  = windowPositions.begin();
    end = windowPositions.end();
    while (it != end) {
        prefsString << it->first << " ";
        pos  = it->second;
        prefsString << pos.x << " "
                    << pos.y << " "
                    << pos.width << " "
                    << pos.height << " ";
        ++it;
    }

    Ptr<Glib::ustring>::Ref  prefsUstring(new Glib::ustring(prefsString.str()));
    try {
        authentication->savePreferencesItem(sessionId,
                                            windowPositionsKey,
                                            prefsUstring);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error saving user preferences: " << e.what() << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Load the window positions.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: loadWindowPositions(void)                           throw ()
{
    Ptr<Glib::ustring>::Ref     prefsUstring;

    try {
        prefsUstring = authentication->loadPreferencesItem(sessionId,
                                                           windowPositionsKey);
    } catch (XmlRpcException &e) {
        // TODO: signal error
        std::cerr << "error loading user preferences: " << e.what()
                  << std::endl;
        return;
    } catch (std::invalid_argument &e) {
        // no window positions were stored for this user yet; no problem
        return;
    }
    
    // load the prefs, which is a space-delimited list
    std::istringstream          prefsString(prefsUstring->raw());

    while (!prefsString.eof()) {
        Glib::ustring           windowName;
        prefsString >> windowName;
        if (prefsString.fail()) {
            break;
        }
        
        WindowPositionType      pos;
        prefsString >> pos.x;
        if (prefsString.fail()) {
            continue;
        }
        prefsString >> pos.y;
        if (prefsString.fail()) {
            continue;
        }
        prefsString >> pos.width;
        if (prefsString.fail()) {
            continue;
        }
        prefsString >> pos.height;
        if (prefsString.fail()) {
            continue;
        }
        
        windowPositions[windowName] = pos;
    }
}


/*------------------------------------------------------------------------------
 *  Set the device for the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: setCueAudioDevice(Ptr<const Glib::ustring>::Ref  deviceName)
                                                                    throw ()
{
    cuePlayer->setAudioDevice(*deviceName);
}


/*------------------------------------------------------------------------------
 *  Play a test sound on the cue audio player.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: playTestSoundOnCue(Ptr<const Glib::ustring>::Ref  oldDevice,
                                   Ptr<const Glib::ustring>::Ref  newDevice)
                                                                    throw ()
{
    if (cueItemPlayingNow) {
        stopCueAudio();     // stop the audio player and
    }                       // release old resources
    
    try {
        if (cuePlayer->isOpen()) {
            if (cuePlayer->isPlaying()) {
                cuePlayer->stop();
            }
            cuePlayer->close();
        }
        cuePlayer->setAudioDevice(*newDevice);
        cuePlayer->open(*testAudioUrl, 0L, 0L);
        cuePlayer->start();
        Ptr<time_duration>::Ref     sleepT(new time_duration(microseconds(10)));
        while (cuePlayer->isPlaying()) {
            runMainLoop();
            TimeConversion::sleep(sleepT);
        }
    } catch (std::runtime_error &e) {
        // "invalid device" error from open(); do nothing
    } catch (std::logic_error &e) {
        // some other error; do nothing
    }
    cuePlayer->close();
    cuePlayer->setAudioDevice(*oldDevice);
}


/*------------------------------------------------------------------------------
 *  Check if the scheduler is available.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: checkSchedulerClient(void)                          throw ()
{
    try {
        scheduler->getVersion();
        schedulerAvailable = true;
        if (masterPanel) {
            masterPanel->setSchedulerAvailable(true);
        }
    } catch (XmlRpcException &e) {
        schedulerAvailable = false;
        if (masterPanel) {
            masterPanel->setSchedulerAvailable(false);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Start the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: startSchedulerClient(void)                          throw ()
{
    system(schedulerDaemonStartCommand->c_str());
}


/*------------------------------------------------------------------------------
 *  Stop the scheduler daemon.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: stopSchedulerClient(void)                           throw ()
{
    system(schedulerDaemonStopCommand->c_str());
}


/*------------------------------------------------------------------------------
 *  Upload a Playable object to the network hub.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: uploadToHub(Ptr<Playable>::Ref      playable)
                                                                    throw ()
{
    masterPanel->uploadToHub(playable);
}


/*------------------------------------------------------------------------------
 *  Display a message that the authentication server is not available.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: displayAuthenticationServerMissingMessage(void)     throw ()
{
    // "authentication not available -- would you like to edit the options?"
    Gtk::ResponseType   answer = runNoYesDialog(*getResourceUstring(
                                            "authenticationNotReachableMsg"));
    
    if (answer == Gtk::RESPONSE_YES) {
/* DISABLED TEMPORARILY
        Ptr<OptionsWindow>::Ref     optionsWindow(new OptionsWindow(
                                                    shared_from_this(),
                                                    getBundle("optionsWindow"),
                                                    0,
                                                    gladeDir));
        optionsWindow->run();
        
        if (optionsContainer->isTouched()) {
            optionsContainer->writeToFile();
        }
*/
    }
}


/*------------------------------------------------------------------------------
 *  Refresh the playlist in the Live Mode window.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: refreshPlaylistInLiveMode(Ptr<Playlist>::Ref    playlist)
                                                                    throw ()
{
    masterPanel->refreshPlaylistInLiveMode(playlist);
}


/*------------------------------------------------------------------------------
 *  Preload the Scratchpad window during login.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: createScratchpadWindow(void)
                                                                    throw ()
{
    if (masterPanel) {
        masterPanel->createScratchpadWindow();
    }
}    


/*------------------------------------------------------------------------------
 *  Write a string to the serial device.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: writeToSerial(Ptr<const Glib::ustring>::Ref     message)
                                                                    throw ()
{
    Ptr<const Glib::ustring>::Ref
        serialDevice = optionsContainer->getOptionItem(
                                            OptionsContainer::serialDeviceName);
    try {
        // TODO: move this to a separate class, and make it configurable
        serialStream->Open(*serialDevice);
        serialStream->SetBaudRate(LibSerial::SerialStreamBuf::BAUD_2400);
        serialStream->SetCharSize(LibSerial::SerialStreamBuf::CHAR_SIZE_8);
        serialStream->SetNumOfStopBits(1);
        serialStream->SetParity(LibSerial::SerialStreamBuf::PARITY_NONE);
        serialStream->SetFlowControl(
                                LibSerial::SerialStreamBuf::FLOW_CONTROL_NONE);
        (*serialStream) << *message;
        serialStream->flush();
        serialStream->Close();
    } catch (...) {
        // TODO: handle this somehow
        std::cerr << "IO error in GLiveSupport::writeToSerial()" << std::endl;
    }
}


/*------------------------------------------------------------------------------
 *  Replace the placeholders in the RDS settings with the current values.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: substituteRdsData(Ptr<Glib::ustring>::Ref   rdsString)
                                                                    throw ()
{
    Ptr<Playable>::Ref  playable = masterPanel->getCurrentInnerPlayable();
    
    // these substitutions are documented in the doxygen docs of the
    // public updateRds() function
    substituteRdsItem(rdsString, "%c", playable, "dc:creator");
    substituteRdsItem(rdsString, "%t", playable, "dc:title");
    substituteRdsItem(rdsString, "%d", playable, "dc:format:extent");
    substituteRdsItem(rdsString, "%s", playable, "dc:source");
    substituteRdsItem(rdsString, "%y", playable, "ls:year");
}


/*------------------------------------------------------------------------------
 *  Replace a single placeholders in the RDS settings.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: substituteRdsItem(Ptr<Glib::ustring>::Ref   rdsString,
                                  const std::string &       placeholder,
                                  Ptr<Playable>::Ref        playable,
                                  const std::string &       metadataKey)
                                                                    throw ()
{
    unsigned int    pos;
    while ((pos = rdsString->find(placeholder)) != std::string::npos) {
        Ptr<const Glib::ustring>::Ref   value;
        if (playable) {
            value = playable->getMetadata(metadataKey);
        }
        if (!value) {
            value.reset(new Glib::ustring(""));
        }
        rdsString->replace(pos, placeholder.length(), *value);
    }
}


/*------------------------------------------------------------------------------
 *  Read the RDS settings, and send them to the serial port.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: updateRds(void)                                     throw ()
{
    Ptr<Glib::ustring>::Ref
                        rdsString = optionsContainer->getCompleteRdsString();
    if (rdsString) {
        substituteRdsData(rdsString);
        writeToSerial(rdsString);
    }
}

