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
    Location : $Source: /home/paul/cvs2svn-livesupport/newcvsrepo/livesupport/products/gLiveSupport/src/LiveModeWindow.cxx,v $

------------------------------------------------------------------------------*/

/* ============================================================ include files */

#ifdef HAVE_CONFIG_H
#include "configure.h"
#endif

#include <iostream>
#include <stdexcept>

#include "LiveSupport/Widgets/WidgetFactory.h"
#include "SchedulePlaylistWindow.h"
#include "LiveModeWindow.h"


using namespace Glib;

using namespace LiveSupport::Core;
using namespace LiveSupport::Widgets;
using namespace LiveSupport::GLiveSupport;

/* ===================================================  local data structures */


/* ================================================  local constants & macros */


/* ===============================================  local function prototypes */


/* =============================================================  module code */

/*------------------------------------------------------------------------------
 *  Constructor.
 *----------------------------------------------------------------------------*/
LiveModeWindow :: LiveModeWindow (Ptr<GLiveSupport>::Ref      gLiveSupport,
                                      Ptr<ResourceBundle>::Ref    bundle)
                                                                    throw ()
          : WhiteWindow(WidgetFactory::liveModeWindowTitleImage,
                        Colors::White,
                        WidgetFactory::getInstance()->getWhiteWindowCorners()),
            LocalizedObject(bundle),
            gLiveSupport(gLiveSupport)
{
    Ptr<WidgetFactory>::Ref     widgetFactory = WidgetFactory::getInstance();
    
    // Create the Tree model:
    treeModel = Gtk::ListStore::create(modelColumns);
    treeView = Gtk::manage(widgetFactory->createTreeView(treeModel));

    // Add the TreeView's view columns:
    try {
        treeView->appendColumn(*getResourceUstring("titleColumnLabel"),
                               modelColumns.titleColumn, 200);
        treeView->appendColumn(*getResourceUstring("creatorColumnLabel"),
                               modelColumns.creatorColumn, 200);
        treeView->appendColumn(*getResourceUstring("lengthColumnLabel"),
                               modelColumns.lengthColumn, 120);
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }
    
    treeView->columns_autosize();
    

    // register the signal handler for treeview entries being clicked
    treeView->signal_button_press_event().connect_notify(sigc::mem_fun(*this,
                                            &LiveModeWindow::onEntryClicked));

    // Add the TreeView, inside a ScrolledWindow, with the button underneath:
    scrolledWindow.add(*treeView);

    // Only show the scrollbars when they are necessary:
    scrolledWindow.set_policy(Gtk::POLICY_AUTOMATIC, Gtk::POLICY_AUTOMATIC);

    vBox.pack_start(scrolledWindow);
    add(vBox);

    // create the right-click entry context menu for audio clips
    contextMenu = Gtk::manage(new Gtk::Menu());
    Gtk::Menu::MenuList& contextMenuList = contextMenu->items();
    // register the signal handlers for the popup menu
    try {
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("cueMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onCueMenuOption)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("upMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onUpMenuOption)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("downMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onDownMenuOption)));
        contextMenuList.push_back(Gtk::Menu_Helpers::MenuElem(
                                 *getResourceUstring("removeMenuItem"),
                                  sigc::mem_fun(*this,
                                        &LiveModeWindow::onRemoveMenuOption)));
    } catch (std::invalid_argument &e) {
        std::cerr << e.what() << std::endl;
        std::exit(1);
    }

    contextMenu->accelerate(*this);

    // show
    set_name("liveModeWindow");
    set_default_size(530, 300);
    set_modal(false);
    property_window_position().set_value(Gtk::WIN_POS_NONE);
    set_resizable(true);
    
    showContents();
    show_all_children();
}


/*------------------------------------------------------------------------------
 *  Show all audio clips
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: showContents(void)                                throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    liveModeContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;
    Ptr<Playable>::Ref                      playable;
    Gtk::TreeModel::Row                     row;

    liveModeContents = gLiveSupport->getLiveModeContents();
    it  = liveModeContents->begin();
    end = liveModeContents->end();
    treeModel->clear();
    int     rowNumber = 0;
    
    while (it != end) {
        playable  = *it;
        row       = *(treeModel->append());

        row[modelColumns.playableColumn]  = playable;
        row[modelColumns.titleColumn]     = *playable->getTitle();
        Ptr<Glib::ustring>::Ref 
                            creator = playable->getMetadata("dc:creator");
        row[modelColumns.creatorColumn]   = creator ? *creator : "";
        row[modelColumns.lengthColumn]    = to_simple_string(
                                                *playable->getPlaylength() );
        row[modelColumns.rowNumberColumn] = rowNumber;

        ++it;
        ++rowNumber;
    }
}


/*------------------------------------------------------------------------------
 *  Destructor.
 *----------------------------------------------------------------------------*/
LiveModeWindow :: ~LiveModeWindow (void)                            throw ()
{
}


/*------------------------------------------------------------------------------
 *  Event handler for an entry being clicked in the list
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onEntryClicked (GdkEventButton     * event)       throw ()
{
    if (event->type == GDK_BUTTON_PRESS && event->button == 3) {
        Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                      treeView->get_selection();
        Gtk::TreeModel::iterator iter = refSelection->get_selected();
        
        // if nothing is currently selected, select row at mouse pointer
        if (!iter) {
            Gtk::TreeModel::Path    path;
            Gtk::TreeViewColumn *   column;
            int     cell_x,
                    cell_y;
            if (treeView->get_path_at_pos(int(event->x), int(event->y),
                                          path, column,
                                          cell_x, cell_y)) {
                refSelection->select(path);
                iter = refSelection->get_selected();
            }
        }

        if (iter) {
            Ptr<Playable>::Ref  playable =
                                        (*iter)[modelColumns.playableColumn];
            contextMenu->popup(event->button, event->time);
        }
    }
}


/*------------------------------------------------------------------------------
 *  Signal handler for the cue menu option selected.
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onCueMenuOption(void)                             throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

        try {
            gLiveSupport->playOutputAudio(playable);
        } catch (XmlRpcException &e) {
            std::cerr << "GLiveSupport::playOutputAudio() error:" << std::endl
                        << e.what() << std::endl;
        } catch (std::exception &e) {
            std::cerr << "GLiveSupport::playOutputAudio() error:" << std::endl
                        << e.what() << std::endl;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Up menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onUpMenuOption(void)                              throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();

    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

        Ptr<GLiveSupport::PlayableList>::Ref    liveModeContents;
        GLiveSupport::PlayableList::iterator    it;
        GLiveSupport::PlayableList::iterator    end;

        liveModeContents = gLiveSupport->getLiveModeContents();
        it  = liveModeContents->begin();
        end = liveModeContents->end();
        while (it != end) {
            Ptr<Playable>::Ref      p= *it;

            if (*p->getId() == *playable->getId()) {
                // move one up, and insert the same before that
                if (it == liveModeContents->begin()) {
                    break;
                }
                liveModeContents->insert(--it, playable);
                // move back to what we've found, and erase it
                liveModeContents->erase(++it);

                showContents();
                break;
            }

            it++;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Down menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onDownMenuOption(void)                            throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

        Ptr<GLiveSupport::PlayableList>::Ref    liveModeContents;
        GLiveSupport::PlayableList::iterator    it;
        GLiveSupport::PlayableList::iterator    end;

        liveModeContents = gLiveSupport->getLiveModeContents();
        it  = liveModeContents->begin();
        end = liveModeContents->end();
        while (it != end) {
            Ptr<Playable>::Ref      p= *it;

            if (*p->getId() == *playable->getId()) {
                // move two down, and insert the same before that
                ++it;
                if (it == end) {
                    break;
                }
                liveModeContents->insert(++it, playable);
                // move back to what we've found, and erase it
                --it;
                --it;
                liveModeContents->erase(--it);

                showContents();
                break;
            }

            it++;
        }
    }
}


/*------------------------------------------------------------------------------
 *  Event handler for the Remove menu item selected from the entry conext menu
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: onRemoveMenuOption(void)                          throw ()
{
    Glib::RefPtr<Gtk::TreeView::Selection> refSelection =
                                                    treeView->get_selection();
    Gtk::TreeModel::iterator iter = refSelection->get_selected();
    
    if (iter) {
        Ptr<Playable>::Ref  playable = (*iter)[modelColumns.playableColumn];

        removeItem(playable->getId());
        showContents();
    }
}


/*------------------------------------------------------------------------------
 *  Remove an item from the Scratchpad
 *----------------------------------------------------------------------------*/
void
LiveModeWindow :: removeItem(Ptr<const UniqueId>::Ref    id)        throw ()
{
    Ptr<GLiveSupport::PlayableList>::Ref    liveModeContents;
    GLiveSupport::PlayableList::iterator    it;
    GLiveSupport::PlayableList::iterator    end;

    liveModeContents = gLiveSupport->getLiveModeContents();
    it  = liveModeContents->begin();
    end = liveModeContents->end();
    while (it != end) {
        Ptr<Playable>::Ref      playable = *it;

        if (*playable->getId() == *id) {
            liveModeContents->erase(it);
            break;
        }

        it++;
    }
}

