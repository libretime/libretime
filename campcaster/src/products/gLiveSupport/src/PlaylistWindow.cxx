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

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Core/TimeConversion.h"

#include "PlaylistWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */

namespace {

/*------------------------------------------------------------------------------
 *  The name of the localization resource bundle.
 *----------------------------------------------------------------------------*/
const Glib::ustring     bundleName = "playlistWindow";

/*------------------------------------------------------------------------------
 *  The name of the glade file.
 *----------------------------------------------------------------------------*/
const Glib::ustring     gladeFileName = "PlaylistWindow.glade";

}

/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
PlaylistWindow :: PlaylistWindow(Gtk::ToggleButton *         windowOpenerButton)
                                                                    throw ()
          : GuiWindow(bundleName,
                      gladeFileName,
                      windowOpenerButton),
            isPlaylistModified(false)
{
    // set up the file name entry
    Gtk::Label *        nameLabel;
    glade->get_widget("nameLabel1", nameLabel);
    nameLabel->set_label(*getResourceUstring("nameLabel"));
    glade->get_widget("nameEntry1", nameEntry);
    nameEntry->signal_changed().connect(sigc::mem_fun(*this,
                                        &PlaylistWindow::onTitleEdited));

    // set up the entries tree view
    entriesModel = Gtk::ListStore::create(modelColumns);
    glade->get_widget_derived("entriesView1", entriesView);
    entriesView->set_model(entriesModel);
    entriesView->connectModelSignals(entriesModel);
    
    entriesView->appendColumn(*getResourceUstring("startColumnLabel"),
                                modelColumns.startColumn,
                                60);
    entriesView->appendColumn(*getResourceUstring("titleColumnLabel"),
                                modelColumns.titleColumn,
                                200);
    entriesView->appendEditableColumn(
                                *getResourceUstring("fadeInColumnLabel"),
                                modelColumns.fadeInColumn,
                                fadeInColumnId,
                                60);
    entriesView->appendColumn(*getResourceUstring("lengthColumnLabel"),
                                modelColumns.lengthColumn,
                                60);
    entriesView->appendEditableColumn(
                                *getResourceUstring("fadeOutColumnLabel"),
                                modelColumns.fadeOutColumn,
                                fadeOutColumnId,
                                60);

    entriesView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                        &PlaylistWindow::onEntryClicked));
    entriesView->signalCellEdited().connect(sigc::mem_fun(*this,
                                        &PlaylistWindow::onFadeInfoEdited ));
    entriesView->signal_key_press_event().connect(sigc::mem_fun(*this,
                                        &PlaylistWindow::onKeyPressed));

    // set up the status bar
    glade->get_widget("statusBar1", statusBar);
    statusBar->set_label("");

    // create the right-click entry context menu
    rightClickMenu.reset(new Gtk::Menu());
    Gtk::Menu::MenuList &       rightClickMenuList = rightClickMenu->items();

    rightClickMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
            *getResourceUstring("upMenuItem"),
            sigc::mem_fun(*this,
                          &PlaylistWindow::onUpItem)));
    rightClickMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
            *getResourceUstring("downMenuItem"),
            sigc::mem_fun(*this,
                          &PlaylistWindow::onDownItem)));
    rightClickMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
            *getResourceUstring("removeMenuItem"),
            sigc::mem_fun(*this,
                          &PlaylistWindow::onRemoveItem)));

    // set up the "lock fades" check button
    Gtk::CheckButton *      lockFadesCheckButton;
    glade->get_widget("lockFadesCheckButton1", lockFadesCheckButton);
    lockFadesCheckButton->set_label(*getResourceUstring(
                                                "lockFadesCheckButtonLabel"));
    lockFadesCheckButton->set_active(true);
    areFadesLocked = true;
    lockFadesCheckButton->signal_toggled().connect(sigc::mem_fun(*this, 
                            &PlaylistWindow::onLockFadesCheckButtonClicked));
    
    // set up the "total time" display
    Gtk::Label *        lengthTextLabel;
    glade->get_widget("lengthTextLabel1", lengthTextLabel);
    lengthTextLabel->set_label(*getResourceUstring("lengthLabel"));
    
    glade->get_widget("lengthValueLabel1", lengthValueLabel);
    lengthValueLabel->set_label("00:00:00");
    
    // register the signal handlers for the buttons
    Gtk::Button *       closeButton;
    glade->get_widget("saveButton1", saveButton);
    glade->get_widget("closeButton1", closeButton);
    saveButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &PlaylistWindow::onSaveButtonClicked));
    closeButton->signal_clicked().connect(sigc::mem_fun(*this,
                            &PlaylistWindow::onBottomCloseButtonClicked));

    // get notified when the playlist is modified outside of the window
    gLiveSupport->signalEditedPlaylistModified().connect(sigc::mem_fun(*this,
                            &PlaylistWindow::onPlaylistModified ));
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
PlaylistWindow :: ~PlaylistWindow (void)
                                                                    throw ()
{
}


/*------------------------------------------------------------------------------
 *  Save the edited playlist.
 *----------------------------------------------------------------------------*/
bool
PlaylistWindow :: savePlaylist(bool reopen)                         throw ()
{
    try {
        Ptr<Playlist>::Ref              playlist
                                        = gLiveSupport->getEditedPlaylist();
        if (!playlist) {
            return false;
        }

        Ptr<const Glib::ustring>::Ref   title(new Glib::ustring(
                                                    nameEntry->get_text()));
        if (*title == "") {
            statusBar->set_text(*getResourceUstring("emptyTitleErrorMsg"));
            return false;
        }
        
        playlist->setTitle(title);
        gLiveSupport->savePlaylist();
        if (reopen) {
            gLiveSupport->openPlaylistForEditing(playlist->getId());
        }
        setPlaylistModified(false);

        Ptr<Glib::ustring>::Ref statusText = formatMessage(
                                                    "playlistSavedMsg",
                                                    *playlist->getTitle());
        statusBar->set_text(*statusText);
        return true;
        
    } catch (XmlRpcException &e) {
        statusBar->set_text(e.what());
        return false;
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the save button getting clicked.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onSaveButtonClicked(void)                         throw ()
{
    savePlaylist(true);
}


/*------------------------------------------------------------------------------
 *  Cancel the edited playlist, after asking for confirmation.
 *----------------------------------------------------------------------------*/
bool
PlaylistWindow :: cancelPlaylist(void)                              throw ()
{
    if (gLiveSupport->getEditedPlaylist()) {
        if (!isPlaylistModified) {
            gLiveSupport->cancelEditedPlaylist();
        } else {
            Gtk::ResponseType       result = runConfirmationDialog();
            switch (result) {
                case Gtk::RESPONSE_NO:
                                try {
                                    gLiveSupport->cancelEditedPlaylist();
                                } catch (XmlRpcException &e) {
                                    std::cerr << e.what() << std::endl;
                                    return false;
                                }
                                setPlaylistModified(false);
                                break;

                case Gtk::RESPONSE_YES:
                                if (!savePlaylist(false)) {
                                    return false;
                                }
                                break;

                case Gtk::RESPONSE_CANCEL:
                                return false;

                default :                       // can happen if the window
                                return false;   // is closed with Alt-F4
            }                                   // -- treated as cancel
        }
    }
    
    return true;
}


/*------------------------------------------------------------------------------
 *  Run the confirmation window.
 *----------------------------------------------------------------------------*/
Gtk::ResponseType
PlaylistWindow :: runConfirmationDialog(void)                       throw ()
{
    Gtk::Dialog *       confirmationDialog;
    Gtk::Label *        confirmationDialogLabel;
    Gtk::Button *       noButton;
    glade->get_widget("confirmationDialog1", confirmationDialog);
    glade->get_widget("confirmationDialogLabel1", confirmationDialogLabel);
    glade->get_widget("noButton1", noButton);
    
    Glib::ustring       message = "<span weight=\"bold\" ";
    message += " size=\"larger\">";
    message += *getResourceUstring("savePlaylistDialogMsg");
    message += "</span>";
    confirmationDialogLabel->set_label(message);
    noButton->set_label(*getResourceUstring("closeWithoutSavingButtonLabel"));

    Gtk::ResponseType   response = Gtk::ResponseType(
                                            confirmationDialog->run());
    confirmationDialog->hide();
    return response;
}


/*------------------------------------------------------------------------------
 *  Clean and close the window.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: closeWindow(void)                 throw ()
{
    statusBar->set_text("");
    nameEntry->set_text("");
    entriesModel->clear();
    setPlaylistModified(false);
    hide();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the save button getting clicked.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onBottomCloseButtonClicked(void)  throw ()
{
    if (cancelPlaylist()) {
        closeWindow();
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the "lock fades" check button toggled.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onLockFadesCheckButtonClicked(void)
                                                                    throw ()
{
    areFadesLocked = !areFadesLocked;
}


/*------------------------------------------------------------------------------
 *  Show the contents of the currently edited playlist.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: showContents(void)                throw ()
{
    Ptr<Playlist>::Ref          playlist;
    Playlist::const_iterator    it;
    Playlist::const_iterator    end;

    playlist = gLiveSupport->getEditedPlaylist();
    
    if (playlist) {
        nameEntry->set_text(*playlist->getTitle());
        
        Ptr<const std::string>::Ref
                lengthStr = TimeConversion::timeDurationToHhMmSsString(
                                                    playlist->getPlaylength());
        lengthValueLabel->set_text(*lengthStr);
        
        entriesModel->clear();
        for (it = playlist->begin(); it != playlist->end(); ++it) {
            Ptr<PlaylistElement>::Ref playlistElement
                                          = it->second;
            Ptr<Playable>::Ref   playable = playlistElement->getPlayable();
            Gtk::TreeModel::Row  row      = *(entriesModel->append());
    
            row[modelColumns.playlistElementColumn]
                        = playlistElement;
            row[modelColumns.startColumn]
                        = *TimeConversion::timeDurationToHhMmSsString(
                                        playlistElement->getRelativeOffset());
            row[modelColumns.titleColumn]
                        = Glib::Markup::escape_text(*playable->getTitle());
            row[modelColumns.lengthColumn]
                        = *TimeConversion::timeDurationToHhMmSsString(
                                        playable->getPlaylength());

            Ptr<FadeInfo>::Ref      fadeInfo = playlistElement->getFadeInfo();
            Ptr<time_duration>::Ref fadeIn, fadeOut;
            if (fadeInfo) {
                fadeIn  = fadeInfo->getFadeIn();
                fadeOut = fadeInfo->getFadeOut();
            }
            row[modelColumns.fadeInColumn]
                        = (fadeIn && fadeIn->total_microseconds() != 0)
                          ? *TimeConversion::timeDurationToShortString(fadeIn)
                          : "-   ";
            row[modelColumns.fadeOutColumn]
                        = (fadeOut && fadeOut->total_microseconds() != 0)
                          ? *TimeConversion::timeDurationToShortString(fadeOut)
                          : "-   ";
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the fade info being edited.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onTitleEdited(void)               throw()
{
    Ptr<Playlist>::Ref          playlist = gLiveSupport->getEditedPlaylist();
    if (!playlist) {
        try {
            gLiveSupport->openPlaylistForEditing();
            playlist = gLiveSupport->getEditedPlaylist();
            
        } catch (XmlRpcException &e) {
            std::cerr << "error in PlaylistWindow::"
                         "onTitleEdited(): "
                      << e.what() << std::endl;
            return;
        }
    }
    Ptr<Glib::ustring>::Ref     title(new Glib::ustring(
                                                    nameEntry->get_text()));
    if (*title != *playlist->getTitle()) {
        playlist->setTitle(title);
        setPlaylistModified(true);
    }
    
    showContents();
}


/*------------------------------------------------------------------------------
 *  Signal handler for the fade info being edited.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onFadeInfoEdited(
                                        const Glib::ustring &  pathString,
                                        int                    columnId,
                                        const Glib::ustring &  newText)
                                                                    throw()
{
    Gtk::TreeModel::Path        path(pathString);
    std::vector<int>            rowNumberVector = path.get_indices();
    int                         rowNumber = rowNumberVector.at(0);
    
    Ptr<time_duration>::Ref     newTime;
    try {
        Ptr<std::string>::Ref   newTextPtr(new std::string(newText));
        newTime = TimeConversion::parseTimeDuration(newTextPtr);
    } catch (boost::bad_lexical_cast &e) {
        showContents();         // bad time format; restore previous state
        return;
    }
    
    if (newTime->is_negative()) {
        showContents();
        return;
    }
    
    Ptr<Playlist>::Ref          playlist = gLiveSupport->getEditedPlaylist();
    Playlist::const_iterator    iter = playlist->begin();
    for (int i=0; i<rowNumber; ++i) {
        ++iter;
    }
    // TODO: add an at(n) access function to Playlist
    Ptr<PlaylistElement>::Ref   playlistElement = iter->second;
    
    switch (columnId) {
        case fadeInColumnId :
            setFadeIn(playlistElement, newTime);
            if (areFadesLocked && iter-- != playlist->begin()) {
                Ptr<PlaylistElement>::Ref   prevPlaylistElement = iter->second;
                setFadeOut(prevPlaylistElement, newTime);
            }
            break;
        case fadeOutColumnId :
            setFadeOut(playlistElement, newTime);
            if (areFadesLocked && ++iter != playlist->end()) {
                Ptr<PlaylistElement>::Ref   nextPlaylistElement = iter->second;
                setFadeIn(nextPlaylistElement, newTime);
            }
            break;
        default :
            return;         // should never happen
    }
    
    showContents();
}


/*------------------------------------------------------------------------------
 *  Auxilliary function: set the fade in of a playlist element.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: setFadeIn(
                          Ptr<PlaylistElement>::Ref     playlistElement,
                          Ptr<time_duration>::Ref       newFadeIn)
                                                                    throw()
{
    Ptr<FadeInfo>::Ref          oldFadeInfo = playlistElement->getFadeInfo();
    Ptr<time_duration>::Ref     oldFadeOut;
    if (oldFadeInfo) {
        if (*oldFadeInfo->getFadeIn() == *newFadeIn) {
            return;
        }
        oldFadeOut  = oldFadeInfo->getFadeOut();
    } else {
        oldFadeOut.reset(new time_duration(0,0,0,0));
    }
    Ptr<FadeInfo>::Ref          newFadeInfo(new FadeInfo(
                                                newFadeIn, oldFadeOut ));
    if (isLengthOkay(playlistElement, newFadeInfo)) {
        playlistElement->setFadeInfo(newFadeInfo);
        setPlaylistModified(true);
    }
}


/*------------------------------------------------------------------------------
 *  Auxilliary function: set the fade out of a playlist element.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: setFadeOut(
                            Ptr<PlaylistElement>::Ref     playlistElement,
                            Ptr<time_duration>::Ref       newFadeOut)
                                                                    throw()
{
    Ptr<FadeInfo>::Ref          oldFadeInfo = playlistElement->getFadeInfo();
    Ptr<time_duration>::Ref     oldFadeIn;
    if (oldFadeInfo) {
        if (*oldFadeInfo->getFadeOut() == *newFadeOut) {
            return;
        }
        oldFadeIn = oldFadeInfo->getFadeIn();
    } else {
        oldFadeIn.reset(new time_duration(0,0,0,0));
    }
    Ptr<FadeInfo>::Ref          newFadeInfo(new FadeInfo(
                                                    oldFadeIn, newFadeOut ));
    if (isLengthOkay(playlistElement, newFadeInfo)) {
        playlistElement->setFadeInfo(newFadeInfo);
        setPlaylistModified(true);
    }
}


/*------------------------------------------------------------------------------
 *  Auxilliary function: check that fades are not longer than the whole clip.
 *----------------------------------------------------------------------------*/
inline bool
PlaylistWindow :: isLengthOkay(
                            Ptr<PlaylistElement>::Ref     playlistElement,
                            Ptr<FadeInfo>::Ref            newFadeInfo)
                                                                    throw()
{
    time_duration   totalFades = *newFadeInfo->getFadeIn()
                               + *newFadeInfo->getFadeOut();
    return (totalFades <= *playlistElement->getPlayable()->getPlaylength());
}


/*------------------------------------------------------------------------------
 *  Signal handler for the playlist being modified outside the window.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onPlaylistModified(void)          throw()
{
    setPlaylistModified(true);
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onEntryClicked(GdkEventButton * event)
                                                                    throw()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Gtk::TreePath           currentPath;
        Gtk::TreeViewColumn *   column;
        int     cell_x,
                cell_y;
        bool foundValidRow = entriesView->get_path_at_pos(
                                            int(event->x), int(event->y),
                                            currentPath, column,
                                            cell_x, cell_y);

        if (foundValidRow) {
            currentItem = entriesModel->get_iter(currentPath);
            if (currentItem) {
                rightClickMenu->popup(event->button, event->time);
            }
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Up menu item selected from the context menu.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onUpItem(void)                    throw()
{
    if (currentItem && currentItem != entriesModel->children().begin()) {
        int             rowNumber    = (*currentItem)
                                            [modelColumns.rowNumberColumn];
        Gtk::TreeIter   previousItem = currentItem;
        --previousItem;
        swapPlaylistElements(previousItem, currentItem);
        setPlaylistModified(true);
        showContents();
        selectRow(--rowNumber);
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Down menu item selected from the context menu.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onDownItem(void)                  throw()
{
    if (currentItem) {
        Gtk::TreeIter   nextItem  = currentItem;
        ++nextItem;
        if (nextItem) {
            int         rowNumber = (*currentItem)
                                            [modelColumns.rowNumberColumn];
            swapPlaylistElements(currentItem, nextItem);
            setPlaylistModified(true);
            showContents();
            selectRow(++rowNumber);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Swap two playlist elements in the edited playlist.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: swapPlaylistElements(
                                        Gtk::TreeIter   firstIter,
                                        Gtk::TreeIter   secondIter)
                                                                    throw()
{
    Ptr<PlaylistElement>::Ref
            firstElement  = (*firstIter) [modelColumns.playlistElementColumn];
    Ptr<PlaylistElement>::Ref
            secondElement = (*secondIter)[modelColumns.playlistElementColumn];

    // remove the two playlist elements
    Ptr<Playlist>::Ref  playlist = gLiveSupport->getEditedPlaylist();
    playlist->removePlaylistElement(firstElement->getId());
    playlist->removePlaylistElement(secondElement->getId());

    // swap the relative offsets so that elt2.begin <-- elt1.begin
    //                               and elt1.end   <-- elt2.end   
    Ptr<time_duration>::Ref     firstStart = firstElement->getRelativeOffset();
    Ptr<time_duration>::Ref     secondStart(new time_duration(
                                        *secondElement->getRelativeOffset()
                                        + *secondElement->getPlayable()
                                                        ->getPlaylength()
                                        - *firstElement->getPlayable()
                                                       ->getPlaylength() ));
    firstElement->setRelativeOffset(secondStart);
    secondElement->setRelativeOffset(firstStart);

    // read the fade infos
    bool hasFadeInfo = false;
    Ptr<FadeInfo>::Ref          firstFadeInfo  = firstElement->getFadeInfo();
    Ptr<FadeInfo>::Ref          secondFadeInfo = secondElement->getFadeInfo();
    Ptr<time_duration>::Ref     beginFade,
                                midFade1,
                                midFade2,
                                endFade;

    if (firstFadeInfo) {
        hasFadeInfo = true;
        beginFade = firstFadeInfo->getFadeIn();
        midFade1  = firstFadeInfo->getFadeOut();
    } else {
        beginFade.reset(new time_duration(0,0,0,0));
        midFade1 .reset(new time_duration(0,0,0,0));
    }
    
    if (secondFadeInfo) {
        hasFadeInfo = true;
        midFade2  = secondFadeInfo->getFadeIn();
        endFade   = secondFadeInfo->getFadeOut();
    } else if (hasFadeInfo) {
        midFade2.reset(new time_duration(0,0,0,0));
        endFade .reset(new time_duration(0,0,0,0));
    }    

    // move fades around if they seem to be simple crossfades
    // otherwise, just leave them as they are
    if (hasFadeInfo && *midFade1 == *midFade2) {
        Ptr<FadeInfo>::Ref  firstFadeInfo (new FadeInfo(beginFade, midFade1));
        Ptr<FadeInfo>::Ref  secondFadeInfo(new FadeInfo(midFade1,  endFade ));

        firstElement->setFadeInfo(secondFadeInfo);
        secondElement->setFadeInfo(firstFadeInfo);
    }

    // add the playlist elements back in
    playlist->addPlaylistElement(firstElement);
    playlist->addPlaylistElement(secondElement);
    
    // Note:
    // removing and then adding is necessary to make sure that the playlist
    // elements are correctly indexed by their relative offset in the playlist.
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu item selected from the context menu.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: onRemoveItem(void)                throw()
{
    if (currentItem) {
        Ptr<Playlist>::Ref 
                playlist        = gLiveSupport->getEditedPlaylist();
        Ptr<PlaylistElement>::Ref 
                playlistElement = (*currentItem)
                                        [modelColumns.playlistElementColumn];
    
        playlist->removePlaylistElement(playlistElement->getId());
        playlist->eliminateGaps();

        setPlaylistModified(true);
        showContents();
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for a key pressed.
 *----------------------------------------------------------------------------*/
bool
PlaylistWindow :: onKeyPressed(GdkEventKey *    event)
                                                                    throw ()
{
    if (event->type == GDK_KEY_PRESS) {
        KeyboardShortcut::Action    action = gLiveSupport->findAction(
                                                "playlistWindow",
                                                Gdk::ModifierType(event->state),
                                                event->keyval);
        switch (action) {
            case KeyboardShortcut::moveItemUp :
                                    findCurrentItem();
                                    onUpItem();
                                    return true;

            case KeyboardShortcut::moveItemDown :
                                    findCurrentItem();
                                    onDownItem();
                                    return true;
            
            case KeyboardShortcut::removeItem :
                                    findCurrentItem();
                                    onRemoveItem();
                                    return true;
            
            default :               break;
        }
    }

    return false;
}


/*------------------------------------------------------------------------------
 *  Find (an iterator pointing to) the currently selected row.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: findCurrentItem(void)             throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                            = entriesView->get_selection();
    currentItem = selection->get_selected();
}


/*------------------------------------------------------------------------------
 *  Select (highlight) the nth row.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: selectRow(int rowNumber)          throw ()
{
    Gtk::TreeModel::iterator    iter = entriesModel->children().begin();
    for (; rowNumber > 0; --rowNumber) {
        ++iter;
    }
    if (iter) {
        Glib::RefPtr<Gtk::TreeView::Selection>  selection
                                                = entriesView->get_selection();
        selection->select(iter);
    }
}


/*------------------------------------------------------------------------------
 *  Set the value of the isPlaylistModified variable.
 *----------------------------------------------------------------------------*/
void
PlaylistWindow :: setPlaylistModified(bool  newValue)
                                                                    throw ()
{
    isPlaylistModified = newValue;
    saveButton->set_sensitive(newValue);
}

