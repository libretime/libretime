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
    Version  : $Revision: 1.9 $
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

#include "MasterPanelWindow.h"
#include "GLiveSupport.h"


using namespace LiveSupport::Core;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::Storage;
using namespace LiveSupport::SchedulerClient;
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
        throw std::invalid_argument("no StorageClientFactory element");
    }
    Ptr<StorageClientFactory>::Ref stcf = StorageClientFactory::getInstance();
    stcf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    storage = stcf->getStorageClient();

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

    // configure the AudioPlayerFactory
    nodes = element.get_children(AudioPlayerFactory::getConfigElementName());
    if (nodes.size() < 1) {
        throw std::invalid_argument("no audioPlayer element");
    }
    Ptr<AudioPlayerFactory>::Ref    apf = AudioPlayerFactory::getInstance();
    apf->configure( *((const xmlpp::Element*) *(nodes.begin())) );

    audioPlayer = apf->getAudioPlayer();
    audioPlayer->initialize();
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

        std::string             locale = localeAttr->get_value().raw();
        Ptr<Glib::ustring>::Ref uName(new Glib::ustring(nameAttr->get_value()));
        Ptr<UnicodeString>::Ref name   = 
                                LocalizedObject::ustringToUnicodeString(uName);

        supportedLanguages->insert(std::make_pair(locale, name));

        begin++;
    }
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
}


/*------------------------------------------------------------------------------
 *  Change the language of the application
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: changeLanguage(Ptr<const std::string>::Ref  locale)
                                                                    throw ()
{
    changeLocale(*locale);

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
    sessionId = authentication->login(login, password);

    return sessionId.get() != 0;
}


/*------------------------------------------------------------------------------
 *  Log the user out.
 *----------------------------------------------------------------------------*/
void
LiveSupport :: GLiveSupport ::
GLiveSupport :: logout(void)                                throw ()
{
    if (sessionId.get() != 0) {
        authentication->logout(sessionId);
        sessionId.reset();
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
Ptr<AudioClip>::Ref
LiveSupport :: GLiveSupport ::
GLiveSupport :: uploadFile(Ptr<const Glib::ustring>::Ref    title,
                           Ptr<const std::string>::Ref      fileName)
                                                    throw (StorageException)
{
    // create a URI from the file name
    Ptr<std::string>::Ref   uri(new std::string("file://"));
    *uri += *fileName;

    // determine the playlength of the audio clip
    Ptr<time_duration>::Ref     playlength;
    try {
        audioPlayer->open(*uri);
        playlength = audioPlayer->getPlaylength();
        audioPlayer->close();
    } catch (std::invalid_argument &e) {
        throw StorageException(e.what());
    }

    // get a unique id
    Ptr<UniqueId>::Ref      uid = UniqueId::generateId();

    // create and upload an AudioClip object
    Ptr<AudioClip>::Ref     audioClip(new AudioClip(uid,
                                                    title,
                                                    playlength,
                                                    uri));
    storage->storeAudioClip(sessionId, audioClip);

    return audioClip;
}

