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
#ifndef GLiveSupport_h
#define GLiveSupport_h

#ifndef __cplusplus
#error This is a C++ include file
#endif


/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <string>
#include <list>
#include <map>
#include <boost/enable_shared_from_this.hpp>
#include <unicode/resbund.h>
#include <SerialStream.h>

#include "LiveSupport/Core/Ptr.h"
#include "LiveSupport/Core/LocalizedConfigurable.h"
#include "LiveSupport/Core/MetadataTypeContainer.h"
#include "LiveSupport/Core/OptionsContainer.h"
#include "LiveSupport/Authentication/AuthenticationClientInterface.h"
#include "LiveSupport/StorageClient/StorageClientInterface.h"
#include "LiveSupport/SchedulerClient/SchedulerClientInterface.h"
#include "LiveSupport/PlaylistExecutor/AudioPlayerInterface.h"
#include "LiveSupport/Widgets/WidgetFactory.h"
#include "KeyboardShortcutList.h"
#include "TaskbarIcons.h"
#include "ContentsStorable.h"
#include "GuiWindow.h"


namespace LiveSupport {
namespace GLiveSupport {

using namespace LiveSupport::Core;
using namespace LiveSupport::SchedulerClient;
using namespace LiveSupport::Authentication;
using namespace LiveSupport::StorageClient;
using namespace LiveSupport::PlaylistExecutor;
using namespace LiveSupport::Widgets;


/* ================================================================ constants */


/* =================================================================== macros */


/* =============================================================== data types */


class MasterPanelWindow;


/**
 *  The main application object for the gLiveSupport GUI.
 *
 *  The configuration file for this object is based on configuration
 *  elements used by the object. The DTD for the root element of the
 *  configuration file is:
 *
 *  <pre><code>
 *  <!ELEMENT gLiveSupport  (resourceBundle,
 *                           supportedLanguages,
 *                           widgetFactory,
 *                           authenticationClientFactory,
 *                           storageClientFactory,
 *                           schedulerClientFactory,
 *                           outputPlayer,
 *                           cuePlayer,
 *                           stationLogo,
 *                           metadataTypeContainer,
 *                           keyboardShortcutContainer*) >
 *  </code></pre>
 *
 *  These elements configure objects of the same name, except for
 *  outputPlayer and cuePlayer, which configure two instances of
 *  AudioPlayerFactory; and stationLogo, which is just a path to the
 *  station logo image file.
 *
 *  @author $Author$
 *  @version $Revision$
 *  @see LocalizedObject#getBundle(const xmlpp::Element &)
 *  @see AuthenticationClientFactory
 *  @see StorageClientFactory
 *  @see SchedulerClientFactory
 */
class GLiveSupport : public LocalizedConfigurable,
                     public boost::enable_shared_from_this<GLiveSupport>,
                     public AudioPlayerEventListener
{
    public:

        /**
         *  A type for the map of supported languages.
         *  This is an STL map, containing const Glib::ustring as keys, which
         *  are the language name of supported langauges. The values are
         *  const std::string, the names of the locales for the languages.
         */
        typedef std::map<const Glib::ustring,
                         const std::string>         LanguageMap;

        /**
         *  A type for having a map of AudioClip objects, with using
         *  the ids of the objects as keys.
         */
        typedef std::map<UniqueId::IdType,
                         Ptr<AudioClip>::Ref>       AudioClipMap;

        /**
         *  A type for having a map of Playlist objects, with using
         *  the ids of the objects as keys.
         */
        typedef std::map<UniqueId::IdType,
                         Ptr<Playlist>::Ref>        PlaylistMap;


    private:

        /**
         *  The name of the configuration XML elmenent used by this class.
         */
        static const std::string                    configElementNameStr;

        /**
         *  The singleton instance of this object.
         */
        static Ptr<GLiveSupport>::Ref               singleton;

        /**
         *  The authentication client used by the application.
         */
        Ptr<AuthenticationClientInterface>::Ref     authentication;

        /**
         *  The storage client used by the application.
         */
        Ptr<StorageClientInterface>::Ref            storage;

        /**
         *  The directory where the Glade files are.
         */
        Glib::ustring                               gladeDir;

        /**
         *  The Glade object, containing the visual design.
         *  For this class, it only contains some pop-up windows.
         */
        Glib::RefPtr<Gnome::Glade::Xml>             glade;

        /**
         *  The widget factory, containing our own widgets.
         */
        Ptr<WidgetFactory>::Ref                     widgetFactory;

        /**
         *  The scheduler client, used to access the scheduler daemon.
         */
        Ptr<SchedulerClientInterface>::Ref          scheduler;

        /**
         *  The output audio player.
         */
        Ptr<AudioPlayerInterface>::Ref              outputPlayer;

        /**
         *  The cue audio player.
         */
        Ptr<AudioPlayerInterface>::Ref              cuePlayer;

        /**
         *  The user id for the logged-in user.
         */
        Ptr<Glib::ustring>::Ref         userName;

        /**
         *  The session id for the logged-in user.
         */
        Ptr<SessionId>::Ref             sessionId;

        /**
         *  The map of supported languages.
         */
        Ptr<LanguageMap>::Ref           supportedLanguages;

        /**
         *  The container for all the possible metadata types.
         */
        Ptr<MetadataTypeContainer>::Ref metadataTypeContainer;

        /**
         *  The master panel window.
         */
        Ptr<MasterPanelWindow>::Ref     masterPanel;

        /**
         *  A map, holding references to all AudioClip objects that are
         *  opened.
         */
        Ptr<AudioClipMap>::Ref          openedAudioClips;

        /**
         *  A map, holding references to all Playlist objects that are
         *  opened.
         */
        Ptr<PlaylistMap>::Ref           openedPlaylists;

        /**
         *  The one and only playlist that may be edited at any one time.
         */
        Ptr<Playlist>::Ref              editedPlaylist;

        /**
         *  The playlist or audio clip that is being played on the
         *  live mode output sound card (may be null).
         */
        Ptr<Playable>::Ref              outputItemPlayingNow;

        /**
         *  The playlist or audio clip that is being played on the
         *  cue (preview) sound card (may be null).
         */
        Ptr<Playable>::Ref              cueItemPlayingNow;

        /**
         *  True if the output audio player has been paused.
         */
        bool                            outputPlayerIsPaused;

        /**
         *  True if the cue audio player has been paused.
         */
        bool                            cuePlayerIsPaused;

        /**
         *  The raw image containing the station logo.
         */
        Glib::RefPtr<Gdk::Pixbuf>       stationLogoPixbuf;

        /**
         *  The wrapper class containing the taskbar icon images.
         */
        Ptr<TaskbarIcons>::Ref          taskbarIcons;

        /**
         *  The location of the test audio file.
         */
        Ptr<Glib::ustring>::Ref         testAudioUrl;

        /**
         *  The command which starts the scheduler daemon.
         */
        Ptr<Glib::ustring>::Ref         schedulerDaemonStartCommand;

        /**
         *  The command which stops the scheduler daemon.
         */
        Ptr<Glib::ustring>::Ref         schedulerDaemonStopCommand;

        /**
         *  The serial stream object.
         */
        Ptr<LibSerial::SerialStream>::Ref       serialStream;

        /**
         *  Read a supportedLanguages configuration element,
         *  and fill the supportedLanguages map with its contents.
         *
         *  @param element a supportedLanguages element
         *  @exception std::invalid_argument if the supplied XML element
         *             is wrong
         */
        void
        configSupportedLanguages(const xmlpp::Element    & element)
                                                throw (std::invalid_argument);

        /**
         *  Emit the "edited playlist has been modified" signal.
         */
        void
        emitSignalEditedPlaylistModified(void)                      throw ()
        {
            signalEditedPlaylistModified().emit();
        }

        /**
         *  Remove a playlist from the playlist cache.
         *  The playlist will be released, if it has been acquired earlier.
         *  If the playlist wasn't in the cache, nothing happens.
         *
         *  @param id the id of the playlist to remove.
         */
        void
        uncachePlaylist(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);
        
        /**
         *  The list of keyboard shortcuts for the various windows.
         */
        Ptr<KeyboardShortcutList>::Ref  keyboardShortcutList;

        /**
         *  The type for a single window position.
         */
        typedef struct {
                    int x;
                    int y;
                    int width;
                    int height;
                }                   WindowPositionType;
        /**
         *  The type for storing the window positions.
         */
        typedef std::map<const Glib::ustring, WindowPositionType>
                                    WindowPositionsListType;

        /**
         *  The positions of the various windows.
         */
        WindowPositionsListType     windowPositions;
        
        /**
         *  An object containing the contents of the options file.
         */
        Ptr<OptionsContainer>::Ref  optionsContainer;

        /**
         *  Whether the storage component is available.
         */
        bool                        storageAvailable;

        /**
         *  Whether the scheduler component is available.
         */
        bool                        schedulerAvailable;

        /**
         *  Private constructor.
         */
        GLiveSupport(void)                                          throw ()
                : outputPlayerIsPaused(false),
                  cuePlayerIsPaused(false)
        {
            openedAudioClips.reset(new AudioClipMap());
            openedPlaylists.reset(new PlaylistMap());
            serialStream.reset(new LibSerial::SerialStream());
        }

        /**
         *  Display a message that the authentication server is not available.
         *  And offer a chance to edit the options to fix it.
         */
        void
        displayAuthenticationServerMissingMessage(void)             throw ();

        /**
         *  Refresh the playlist in the Live Mode window.
         *  Updates the playlist to the new copy supplied in the argument,
         *  if it is present in the Live Mode window.
         *  This is called by savePlaylist() after the playlist has been
         *  edited.
         *
         *  @param  playlist    the new version of the playlist.
         */
        void
        refreshPlaylistInLiveMode(Ptr<Playlist>::Ref    playlist)
                                                                    throw ();

        /**
         *  Replace the placeholders in the RDS settings with the
         *  current values.
         *
         *  @param  rdsString   the string with the placeholders;
         *                      they will be replaced in place.
         */
        void
        substituteRdsData(Ptr<Glib::ustring>::Ref   rdsString)
                                                                    throw ();

        /**
         *  Replace a single placeholders in the RDS settings.
         *  If the corresponding metadata is not found, an empty string
         *  is substituted instead.
         *
         *  @param  rdsString   the string with the placeholders;
         *                      they will be replaced in place.
         *  @param  placeholder the string to be substituted, e.g. "%t".
         *  @param  playable    the Playable object whose data is to be used.
         *  @param  metadataKay the kind of metadata to be substituted.
         */
        void
        substituteRdsItem(Ptr<Glib::ustring>::Ref   rdsString,
                          const std::string &       placeholder,
                          Ptr<Playable>::Ref        playable,
                          const std::string &       metadataKey)
                                                                    throw ();

        /**
         *  Write a string to the serial device.
         */
        void
        writeToSerial(Ptr<const Glib::ustring>::Ref     message)    throw ();

        /**
         *  Replace spaces with underscore characters.
         *
         *  @param  string the original string, eg: "one two three".
         *  @return the new string, eg: "one_two_three".
         */
        Glib::ustring
        replaceSpaces(Ptr<const Glib::ustring>::Ref     string)     throw ();

        /**
         *  Run a dialog window.
         *
         *  @param  dialogName  the type of the dialog; can be "noYesDialog"
         *                      or "okDialog".
         *  @param  message     the text to be displayed by the dialog.
         *  @return the response ID returned by the dialog.
         */
        Gtk::ResponseType
        runDialog(const Glib::ustring &     dialogName,
                  const Glib::ustring &     message)                throw ();


    protected:

        /**
         *  A signal object to notify people that the edited playlist changed.
         */
        sigc::signal<void>              signalEditedPlaylistModifiedObject;


    public:

        /**
         *  Virtual destructor.
         */
        virtual
        ~GLiveSupport(void)                                         throw ()
        {
            if (outputPlayer.get()) {
                outputPlayer->deInitialize();
            }
            if (cuePlayer.get()) {
                cuePlayer->deInitialize();
            }
            try {
                releaseOpenedAudioClips();
            } catch (XmlRpcException &e) {
            }
            try {
                releaseOpenedPlaylists();
            } catch(XmlRpcException &e) {
            }
        }

        /**
         *  Return the name of the XML element this object expects
         *  to be sent to a call to configure().
         *  
         *  @return the name of the expected XML configuration element.
         */
        static const std::string
        getConfigElementName(void)                                  throw ()
        {
            return configElementNameStr;
        }

        /**
         *  Returns the singleton instance of this object.
         *
         *  @return the singleton instance of this object.
         */
        static Ptr<GLiveSupport>::Ref
        getInstance()                                               throw ();

        /**
         *  Configure the scheduler daemon based on the XML element
         *  supplied.
         *
         *  @param element the XML element to configure the scheduler
         *                 daemon from.
         *  @exception std::invalid_argument if the supplied XML element
         *             contains bad configuraiton information
         *  @exception std::logic_error if the object has already
         *             been configured.
         */
        void
        configure(const xmlpp::Element    & element)
                                                throw (std::invalid_argument,
                                                       std::logic_error);

        /**
         *  A function to check the configuration of this object.
         *  Checks all resources configured by configure()
         *
         *  @retun true if all resources are accessible and working,
         *         false otherwise
         */
        bool
        checkConfiguration(void)                                    throw ();

        /**
         *  Display a message window.
         *  This function only returns after the message window is closed.
         *
         *  @param message the message to display
         */
        void
        displayMessageWindow(const Glib::ustring &      message)
                                                                    throw ();

        /**
         *  Run a dialog window with No and Yes buttons.
         *
         *  @param  message the text to be displayed by the dialog.
         *  @return the response ID returned by the dialog.
         */
        Gtk::ResponseType
        runNoYesDialog(const Glib::ustring &    message)            throw ();

        /**
         *  Run a dialog window with just an OK button.
         *
         *  @param  message the text to be displayed by the dialog.
         *  @return the response ID returned by the dialog.
         */
        Gtk::ResponseType
        runOkDialog(const Glib::ustring &    message)               throw ();

        /**
         *  Show the main window, and run the application.
         *  This call will only return after the main window has been closed.
         */
        void
        show(void)                                                  throw ();

        /**
         *  Change the language of the application.
         *
         *  @param locale the new locale of the appliction.
         *  @exception std::invalid_argument if the specified locale is not
         *             available
         */
        void
        changeLanguage(Ptr<const std::string>::Ref  locale)
                                                throw (std::invalid_argument);

        /**
         *  Perform authentication for the user of the application.
         *  As a result, the user will be authenticated for later
         *  actions, that need authentication.
         *  The user has to be logged out later.
         *
         *  @param login the login ID of the user
         *  @param password the password of the user
         *  @return true if the authentication was successful,
         *          false otherwise
         *  @see #logout
         */
        bool
        login(const std::string & login,
              const std::string & password)                         throw ();

        /**
         *  Return the session id for the user.
         *
         *  @return the session id for the user, if he has been already
         *          successfully authenticated with a call to login(),
         *          and not yet logged out.
         *          otherwise, a reference to 0.
         *  @see #login
         */
        Ptr<SessionId>::Ref
        getSessionId(void) const                                    throw ()
        {
            return sessionId;
        }

        /**
         *  Log out the user, if he has been authenticated previously
         *  with a successful call to login().
         *
         *  @return true if the logout was successful, false if not
         *          (e.g., if the user is editing a playlist, and
         *          presses "cancel" at the "Save playlist?" dialog.)
         *  @see #logout
         */
        bool
        logout(void)                                                throw ();

        /**
         *  Accessor function to the scheduler client held by this object.
         *
         *  @return the scheduler client held by this object.
         */
        Ptr<SchedulerClientInterface>::Ref
        getScheduler(void)                                          throw ()
        {
            return scheduler;
        }

        /**
         *  Get the map of supported languages.
         *
         *  @return the map of supported languages.
         */
        Ptr<const LanguageMap>::Ref
        getSupportedLanguages(void) const                           throw ()
        {
            return supportedLanguages;
        }

        /**
         *  Return a container with all supported metadata types.
         *
         *  @return the metadata type container
         */
        Ptr<MetadataTypeContainer>::Ref
        getMetadataTypeContainer(void) const                        throw ()
        {
            return metadataTypeContainer;
        }

        /**
         *  Upload an audio clip to the storage.
         *
         *  @param  audioClip   the audio clip to upload.
         *  @exception XmlRpcException on upload failures.
         */
        void
        uploadAudioClip(Ptr<AudioClip>::Ref     audioClip)
                                                    throw (XmlRpcException);

        /**
         *  Upload a playlist archive to the storage.
         *
         *  @param  path        the path of the file to upload.
         *  @exception XmlRpcException on upload failures.
         */
        Ptr<Playlist>::Ref
        uploadPlaylistArchive(Ptr<const Glib::ustring>::Ref     path)
                                                    throw (XmlRpcException);

        /**
         *  Add an item to the Scratchpad, and update it.
         *  If the item is already in the scratchpad, it gets pushed to the top.
         *
         *  @param playable the audio clip or playlist to be added
         */
        void
        addToScratchpad(Ptr<Playable>::Ref  playable)
                                                    throw (XmlRpcException);

        /**
         *  Reset the storage behind GLiveSupport.
         *  Used for testing only.
         *
         *  @exception XmlRpcException on communication problems.
         */
        void
        resetStorage(void)                          throw (XmlRpcException)
        {
            storage->reset();
        }

        /**
         *  Tell if an audio clip specified by an id exists.
         *
         *  @param id the id of the audio clip to check for.
         *  @return true if the audio clip by the specified id exists,
         *          false otherwise.
         *  @exception XmlRpcException on communication problems.
         */
        bool
        existsAudioClip(Ptr<const UniqueId>::Ref   id)  throw (XmlRpcException)
        {
            return storage->existsAudioClip(sessionId, id);
        }

        /**
         *  Open an audio clip, for reading only.
         *  The audio clip will be put into the internal cache of the
         *  GLiveSupport object.
         *
         *  @param id the audio clip id.
         *  @return the audio clip opened.
         *  @exception XmlRpcException if no audio clip with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<AudioClip>::Ref
        getAudioClip(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Acquire an audio clip, for random file access.
         *  The audio clip will be put into the internal cache of the
         *  GLiveSupport object.
         *
         *  @param id the audio clip id.
         *  @return the AudioClip acquired.
         *          note that the returned AudioClip does not have to be
         *          released, this will be done by the caching system
         *          automatically.
         *  @exception XmlRpcException if no audio clip with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<AudioClip>::Ref
        acquireAudioClip(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Tell if a playlist specified by an id exists.
         *
         *  @param id the id of the playlist to check for.
         *  @return true if the playlist by the specified id exists,
         *          false otherwise.
         *  @exception XmlRpcException on communication problems.
         */
        bool
        existsPlaylist(Ptr<const UniqueId>::Ref   id)   throw (XmlRpcException)
        {
            return storage->existsPlaylist(sessionId, id);
        }

        /**
         *  Open a playlist, for reading only.
         *  The playlist will be put into the internal cache of the
         *  GLiveSupport object.
         *
         *  @param id the playlist id.
         *  @return the playlist opened.
         *  @exception XmlRpcException if no playlist with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<Playlist>::Ref
        getPlaylist(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Acquire a playlist, for random file access.
         *  The playlist will be put into the internal cache of the
         *  GLiveSupport object.
         *
         *  @param id the playlist id.
         *  @return the playlist acquired.
         *          note that the returned Playlist does not have to be
         *          released, this will be done by the caching system
         *          automatically.
         *  @exception XmlRpcException if no playlist with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<Playlist>::Ref
        acquirePlaylist(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Tell if a playable object specified by an id exists.
         *
         *  @param id the id of the playable to check for.
         *  @return true if the playable by the specified id exists,
         *          false otherwise.
         *  @exception XmlRpcException on communication problems.
         */
        bool
        existsPlayable(Ptr<const UniqueId>::Ref   id)   throw (XmlRpcException)
        {
            return storage->existsAudioClip(sessionId, id)
                        || storage->existsPlaylist(sessionId, id);
        }

        /**
         *  Open a playable object, for reading only.
         *  Calls either getAudioClip() or getPlaylist().
         *  You do not need to release the returned Playable object.
         *
         *  @param id the id of the playable object.
         *  @return the playable object opened.
         *  @exception XmlRpcException if no Playable with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<Playable>::Ref
        getPlayable(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Acquire a playable object.
         *  Calls either acquireAudioClip() or acquirePlaylist().
         *
         *  @param id the id of the playable object.
         *  @return the playable object acquired.
         *          note that the returned Playable does not have to be
         *          released, this will be done by the caching system
         *          automatically.
         *  @exception XmlRpcException if no Playable with the specified
         *             id exists, or there was a communication problem.
         */
        Ptr<Playable>::Ref
        acquirePlayable(Ptr<const UniqueId>::Ref  id)
                                                    throw (XmlRpcException);

        /**
         *  Release all opened audio clips.
         */
        void
        releaseOpenedAudioClips(void)              throw (XmlRpcException);

        /**
         *  Release all opened playlists.
         */
        void
        releaseOpenedPlaylists(void)               throw (XmlRpcException);

        /**
         *  Add a file to the Live Mode, and update it.
         *
         *  @param playable the audio clip or playlist to be added
         */
        void
        addToLiveMode(Ptr<Playable>::Ref  playable)                 throw ();

        /**
         *  Return the currently edited playlist.
         *
         *  @return the currenlty edited playlist, or a reference to 0
         *          if no playlist is edited
         */
        Ptr<Playlist>::Ref
        getEditedPlaylist(void)                                     throw ()
        {
            return editedPlaylist;
        }

        /**
         *  Create a new playlist or Open a playlist for editing.
         *  The opened playlist can be later accessed by getEditedPlaylist().
         *  Always release the opened playlist by calling
         *  releaseEditedPlaylist().
         *
         *  If the argument is 0, a new playlist is created in the storage.
         *
         *  After a call to this function, getEditedPlaylist() is guaranteed
         *  to return a non-0 value.
         *
         *  If there is a playlist being edited, the
         *  PlaylistWindow's confirmation message is displayed.
         *  If the user presses "Cancel", then this function does nothing.
         *
         *  @param playlistId the id of the playlist to open for editing.
         *         if a reference to 0, create a new playlist.
         *  @return the new playlist object, which is opened for editing.
         *  @exception XmlRpcException on XMl-RPC errors.
         *  @see #getEditedPlaylist
         *  @see #releaseEditedPlaylist
         */
        void
        openPlaylistForEditing(
                    Ptr<const UniqueId>::Ref    playlistId
                                                = Ptr<const UniqueId>::Ref())
                                                      throw (XmlRpcException);

        /**
         *  Add a playable item to the currently open playlist.
         *  If there is no currently open playlist, open the simple playlist
         *  management window with a new playlist, holding only this one
         *  entry.
         *  Always release the opened playlist by calling
         *  releaseEditedPlaylist()
         *
         *  @param id the id of the playable object to add to the playlist.
         *  @exception XmlRpcException on XMl-RPC errors.
         *  @see #releaseEditedPlaylist
         */
        void
        addToPlaylist(Ptr<UniqueId>::Ref  id)
                                                      throw (XmlRpcException);
        /**
         *  Save the currently edited playlist in storage.
         *  This call has to be preceeded by a call to openPlaylistForEditing()
         *  or addToPlaylist().
         *  After this call, the playlist is no longer being edited.  If you 
         *  want to continue editing, open the playlist for editing again.
         *
         *  @exception XmlRpcException on upload failures.
         *  @see #openPlaylistForEditing
         *  @see #addToPlaylist
         */
        void
        savePlaylist(void)                          throw (XmlRpcException);

        /**
         *  Cancel the edited playlist: undo changes and release the lock.
         *
         *  @exception XmlRpcException on XML-RPC errors.
         *  @see #openPlaylistForEditing
         */
        void
        cancelEditedPlaylist(void)                  throw (XmlRpcException);

        /**
         *  Return the scheduled entries for a specified time interval.
         *
         *  @param from the start of the interval, inclusive
         *  @param to the end of the interval, exclusive
         *  @return a vector of the schedule entries for the time period.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual Ptr<std::vector<Ptr<ScheduleEntry>::Ref> >::Ref
        displaySchedule(Ptr<boost::posix_time::ptime>::Ref  from,
                        Ptr<boost::posix_time::ptime>::Ref  to)
                                                    throw (XmlRpcException)
        {
std::cout << "calling GLiveSupport :: displaySchedule !!!!!!!!!!!!!!!!!" << std::endl;
            return scheduler->displaySchedule(sessionId, from, to);
        }

        /**
         *  Schedule a playlist.
         *  This will schedule the plalyist, and show the scheduler window
         *  at the time of the scheduled playlist.
         *
         *  @param playlist the playlist to schedule.
         *  @param playtime the time for when to schedule.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual void
        schedulePlaylist(Ptr<Playlist>::Ref                 playlist,
                         Ptr<boost::posix_time::ptime>::Ref playtime)
                                                    throw (XmlRpcException);
        

        /**
         *  Remove a scheduled item.
         *
         *  @param sessionId a valid, authenticated session id.
         *  @param scheduledEntryId the id of the scheduled entry to remove.
         *  @exception XmlRpcException in case of XML-RPC errors.
         */
        virtual void
        removeFromSchedule(Ptr<const UniqueId>::Ref   scheduleEntryId)
                                                    throw (XmlRpcException);

        /**
         *  Preload the item in the output audio player.
         *  This is to shorten the time a playlist takes to start.
         *
         *  @param  playable    the Playable object to be preloaded.
         */
        void
        preload(Ptr<const Playable>::Ref    playable)
                                                                    throw ();

        /**
         *  Play a Playable object using the output audio player.
         *
         *  @param playable the Playable object to play.
         *  @exception std::logic_error in case of audio player errors.
         *  @exception std::runtime_error in case of audio player errors.
         */
        virtual bool
        playOutputAudio(Ptr<Playable>::Ref   playable)
                                                throw (std::logic_error,
                                                       std::runtime_error);

        /**
         *  Stop the output audio player.
         *
         *  @exception std::logic_error in case of audio player errors.
         */
        virtual void
        stopOutputAudio(void)
                                                throw (std::logic_error);

        /**
         *  Pause the output audio player.
         *
         *  @exception std::logic_error in case of audio player errors.
         */
        virtual void
        pauseOutputAudio(void)
                                                throw (std::logic_error);

        /**
         *  Determine the time elapsed in the current Playable object
         *  played by the output audio player.
         *
         *  @return     the current time position in the currently open
         *                  Playable object.
         *  @exception  std::logic_error if there is no Playable object open.
         */
        virtual Ptr<time_duration>::Ref
        getOutputAudioPosition(void)
                                                throw (std::logic_error)
        {
            return outputPlayer->getPosition();
        }

        /**
         *  Play a Playable object using the cue audio player.
         *
         *  @param playable the Playable object to play.
         *  @exception std::logic_error in case of audio player errors.
         *  @exception std::runtime_error in case of audio player errors.
         */
        virtual void
        playCueAudio(Ptr<Playable>::Ref   playable)
                                                throw (std::logic_error,
                                                       std::runtime_error);

        /**
         *  Stop the cue audio player.
         *
         *  @exception XmlRpcException in case of storage server errors.
         *  @exception std::logic_error in case of audio player errors.
         */
        virtual void
        stopCueAudio(void)
                                                throw (std::logic_error);

        /**
         *  Pause the cue audio player.
         *
         *  @exception std::logic_error in case of audio player errors.
         */
        virtual void
        pauseCueAudio(void)
                                                throw (std::logic_error);

        /**
         *  Attach a listener for the cue audio player (the listener
         *  will be notified when the cue audio player has stopped playing).
         *
         *  @param listener the event listener to register.
         */
        void
        attachCueAudioListener(AudioPlayerEventListener *   listener)
                                                throw ();

        /**
         *  Detach the listener for the cue audio player.
         *
         *  @param listener the event listener to unregister.
         *  @exception std::invalid_argument if the supplied event listener 
         *             has not been previously registered.
         */
        void
        detachCueAudioListener(AudioPlayerEventListener *   listener)
                                                throw (std::invalid_argument);

        /**
         *  Set the device for the cue audio player.
         *
         *  @param  deviceName  the name of the new device
         */
        void
        setCueAudioDevice(Ptr<const Glib::ustring>::Ref     deviceName)
                                                throw ();

        /**
         *  Play a test sound on the cue audio player.
         *
         *  @param  oldDevice   the name of the current audio device.
         *  @param  oldDevice   the name of the audio device to be tested.
         */
        void
        playTestSoundOnCue(Ptr<const Glib::ustring>::Ref    oldDevice,
                           Ptr<const Glib::ustring>::Ref    newDevice)
                                                                    throw ();

        /**
         *  Search in the local storage.
         *  Note that the return value (number of items found) will not be
         *  the same as the size of getSearchResults() if the limit and offset
         *  fields in the criteria parameter are not zero.
         *
         *  @param criteria the search conditions to use.
         *  @return the number of audio clips and playlists found.
         *  @exception XmlRpcException thrown by 
         *                             StorageClientInterface::search()
         */
        int
        search(Ptr<SearchCriteria>::Ref     criteria)
                                                throw (XmlRpcException);

        /**
         *  Event handler for the "output audio player has stopped" event.
         *
         *  @param errorMessage is a 0 pointer if the player stopped normally
         */
        virtual void
        onStop(Ptr<const Glib::ustring>::Ref  errorMessage
                                              = Ptr<const Glib::ustring>::Ref())
                                                                    throw ();

        /**
         *  Event handler for the "output audio player has started" event.
         *
         *  @param fileName
         */
        virtual void
        onStart(gint64 id)
                                                                    throw ();

        /**
         *  Display the playable item on the master panel as "now playing".
         */
        void
        setNowPlaying(Ptr<Playable>::Ref    playable)
                                                                    throw ();

        /**
         *  Return a pixbuf containing the radio station logo.
         *
         *  @return a pixbuf containing the station logo image.
         */
        Glib::RefPtr<Gdk::Pixbuf>
        getStationLogoPixbuf()                                      throw ();

        /**
         *  The signal raised when the edited playlist is modified.
         *
         *  @return the signal object (a protected member of this class)
         */
        sigc::signal<void>
        signalEditedPlaylistModified(void)                          throw ()
        {
            return signalEditedPlaylistModifiedObject;
        }
        
        /**
         *  Find the action triggered by the given key in the given window.
         *
         *  @param  windowName  a string identifying the window (not localized).
         *  @param  modifiers   the gdktypes code for the Shift, Ctrl etc.
         *                          modifier keys which are pressed.
         *  @param  key         the gdkkeysyms code for the key pressed.
         *  @return the associated action; or noAction, if none is found.
         */
        KeyboardShortcut::Action
        findAction(const Glib::ustring &    windowName,
                   Gdk::ModifierType        modifiers,
                   guint                    key) const              throw ()
        {
            return keyboardShortcutList->findAction(windowName, modifiers, key);
        }

        /**
         *  The list of all KeyboardShortcutContainer objects.
         *  Used in the Key bindings section of the OptionsWindow class.
         *
         *  @return a const pointer to the list (implemented as a std::map).
         */
        Ptr<const KeyboardShortcutList>::Ref
        getKeyboardShortcutList(void)                               throw ()
        {
            return keyboardShortcutList;
        }
        
        /**
         *  Get the localized name of the window.
         *  Used in the Key bindings section of the OptionsWindow class.
         *
         *  @param      windowName  the name of the window.
         *  @return     the localized name.
         *  @exception  std::invalid_argument   if the resource bundle is
         *                                      not found
         */
        Ptr<const Glib::ustring>::Ref
        getLocalizedWindowName(Ptr<const Glib::ustring>::Ref    windowName)
                                                throw (std::invalid_argument);
        
        /**
         *  Get the localized name of the keyboard shortcut action.
         *  Used in the Key bindings section of the OptionsWindow class.
         *
         *  @param  actionName  the name of the action.
         *  @return the localized name.
         *  @exception  std::invalid_argument   if the resource bundle is
         *                                      not found
         *  @see    KeyboardShortcut::getActionString()
         */
        Ptr<const Glib::ustring>::Ref
        getLocalizedKeyboardActionName(
                                Ptr<const Glib::ustring>::Ref   actionName)
                                                throw (std::invalid_argument);

        /**
         *  Save the position and size of the window.
         *
         *  The coordinates of the window's North-West corner and the
         *  size of the window are read, and stored in a variable of the
         *  GLiveSupport object, indexed by the window's get_name().
         *
         *  @param  window   the window to save the position and size of.
         *  @see    getWindowPosition()
         */
        void
        putWindowPosition(const GuiWindow *     window)             throw ();
        
        /**
         *  Apply saved position and size data to the window.
         *
         *  If position and size data were previously saved for a window
         *  with the same get_name(), then these data are read and applied to
         *  the window, restoring its position and size.
         *
         *  @param  window   the window to apply the position and size info to.
         *  @see    putWindowPosition()
         */
        void
        getWindowPosition(GuiWindow *           window)             throw ();

        /**
         *  Store the saved window positions.
         *
         *  The window positions (and sizes) are stored in a user preference
         *  item.  This is called when the user logs out.
         *
         *  @see    loadWindowPositions()
         */
        void
        storeWindowPositions(void)                                  throw ();
        
        /**
         *  Load the window positions.
         *
         *  The window positions (and sizes) are retrieved from the user
         *  preference item they were stored in.  This is called when the
         *  user logs in.
         *
         *  @see    storeWindowPosition()
         */
        void
        loadWindowPositions(void)                                   throw ();
        
        /**
         *  Access the OptionsContainer object containing the options.
         */
        Ptr<OptionsContainer>::Ref
        getOptionsContainer(void)                                   throw()
        {
            return optionsContainer;
        }

        /**
         *  Store the contents of a window as a user preference.
         *
         *  @param  window  the window to get the contents of.
         */
        void
        storeWindowContents(Ptr<ContentsStorable>::Ref  window)
                                                                    throw ()
        {
            storeWindowContents(window.get());
        }

        /**
         *  Load the contents of a window as a user preference.
         *
         *  @param  window  the window to restore the contents of.
         */
        void
        loadWindowContents(Ptr<ContentsStorable>::Ref   window)
                                                                    throw ()
        {
            loadWindowContents(window.get());
        }

        /**
         *  Store the contents of a window as a user preference.
         *
         *  @param  window  the window to get the contents of.
         */
        void
        storeWindowContents(ContentsStorable *      window)         throw ();

        /**
         *  Load the contents of a window as a user preference.
         *
         *  @param  window  the window to restore the contents of.
         */
        void
        loadWindowContents(ContentsStorable *       window)         throw ();

        /**
         *  Return whether the storage component is available.
         */
        bool
        isStorageAvailable(void)                                    throw()
        {
            return storageAvailable;
        }

        /**
         *  Return whether the scheduler component is available.
         */
        bool
        isSchedulerAvailable(void)                                  throw()
        {
            return schedulerAvailable;
        }

        /**
         *  Access the StorageClientInterface object.
         */
        Ptr<StorageClientInterface>::Ref
        getStorageClient(void)                                      throw()
        {
            return storage;
        }
        
        /**
         *  Check if the scheduler is available.
         *  This updates the schedulerAvailable variable accordingly.
         */
        void
        checkSchedulerClient(void)                                  throw();
        
        /**
         *  Start the scheduler client.
         */
        void
        startSchedulerClient(void)                                  throw();
        
        /**
         *  Stop the scheduler client.
         */
        void
        stopSchedulerClient(void)                                   throw();

        /**
         *  Upload a Playable object to the network hub.
         *
         *  This opens the Transports tab in the Search window, and adds the
         *  new upload task to it.
         *
         *  @param playable the audio clip or playlist to be uploaded.
         */
        void
        uploadToHub(Ptr<Playable>::Ref  playable)                   throw ();
        
        /**
         *  Take a break.
         *  This will perform all pending redraws, by giving the control back
         *  to the main loop for a while.
         *  Call this occasionally in the middle of long computations, to
         *  make sure your window gets redrawn.
         */
        void
        runMainLoop(void)                                           throw ()
        {
            while (Gtk::Main::events_pending()) {
                Gtk::Main::iteration();
            }
        }

        /**
         *  Preload the Scratchpad window during login.
         */
        void
        createScratchpadWindow(void)                                throw ();

        /**
         *  Read the RDS settings, and send them to the serial port.
         *
         *  The following RDS placeholders will be substituted:
         *
         *  <ul>
         *      <li>"%c" ---> "dc:creator" (Creator)</li>
         *      <li>"%t" ---> "dc:title" (Title)</li>
         *      <li>"%d" ---> "dc:format:extent" (Duration)</li>
         *      <li>"%s" ---> "dc:source" (Album)</li>
         *      <li>"%y" ---> "ls:year" (Year)</li>
         *  </ul>
         *
         *  @see substituteRdsData()
         */
        void
        updateRds(void)                                             throw ();

        /**
         *  Return the directory where the Glade files are.
         *
         *  @return the directory where the Glade files are.
         */
        Glib::ustring
        getGladeDir(void)                                           throw ()
        {
            return gladeDir;
        }
};

/* ================================================= external data structures */


/* ====================================================== function prototypes */


} // namespace GLiveSupport
} // namespace LiveSupport

#endif // GLiveSupport_h

